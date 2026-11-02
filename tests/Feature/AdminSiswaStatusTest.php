<?php

namespace Tests\Feature;

use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Semester;
use App\Models\StudentProfile;
use App\Models\TahunPelajaran;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AdminSiswaStatusTest extends TestCase
{
    use DatabaseTransactions;

    private User $admin;

    private User $guru;

    private Kelas $kelas;

    private MataPelajaran $mapel;

    private Semester $semester;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'password' => 'password123',
        ]);

        $this->guru = User::factory()->create([
            'role' => 'guru',
            'password' => 'password123',
        ]);

        $this->kelas = Kelas::firstOrCreate(['tingkat' => '8', 'nama_kelas' => 'StatusTest']);
        $this->mapel = MataPelajaran::firstOrCreate(
            ['kode_mapel' => 'MAPEL_TEST_ST'],
            ['nama_mapel' => 'Mapel Status Test', 'is_active' => true]
        );

        $this->semester = Semester::getActive() ?? Semester::first();
        if (! $this->semester) {
            $tp = TahunPelajaran::create([
                'nama' => '2026/2027',
                'tahun_mulai' => 2026,
                'tahun_selesai' => 2027,
                'tanggal_mulai' => '2026-07-01',
                'tanggal_selesai' => '2027-06-30',
                'is_active' => true,
            ]);

            $this->semester = Semester::create([
                'tahun_pelajaran_id' => $tp->id,
                'semester' => 'Ganjil',
                'tanggal_mulai' => '2026-07-01',
                'tanggal_selesai' => '2026-12-31',
                'is_active' => true,
            ]);
        }
    }

    private function createStudent(string $name, string $nis, string $status = 'AKTIF', bool $isActive = true): User
    {
        $user = User::factory()->create([
            'name' => $name,
            'role' => 'siswa',
            'email' => 'siswa_'.uniqid().'@siswa.id',
            'password' => 'password123',
        ]);

        StudentProfile::create([
            'user_id' => $user->id,
            'kelas_id' => $this->kelas->id,
            'nis' => $nis,
            'nisn' => 'NISN'.$nis,
            'status' => $status,
            'is_active' => $isActive,
        ]);

        return $user;
    }

    public function test_admin_can_update_student_status_to_tidak_aktif()
    {
        $studentUser = $this->createStudent('Siswa Mau Dinonaktifkan', 'NIS1001', 'AKTIF', true);

        $response = $this->actingAs($this->admin)->put(route('admin.siswa.update', $studentUser->id), [
            'name' => 'Siswa Sudah Nonaktif',
            'email' => $studentUser->email,
            'tingkat' => '8',
            'kelas' => 'StatusTest',
            'status' => 'TIDAK_AKTIF',
        ]);

        $response->assertRedirect(route('admin.siswa.show', $studentUser->id));

        $profile = StudentProfile::where('user_id', $studentUser->id)->first();
        $this->assertEquals('TIDAK_AKTIF', $profile->status);
        $this->assertFalse((bool) $profile->is_active);

        // Verify detail view displays "Tidak Aktif"
        $showResponse = $this->actingAs($this->admin)->get(route('admin.siswa.show', $studentUser->id));
        $showResponse->assertSee('Tidak Aktif');
    }

    public function test_admin_can_update_student_status_to_lulus()
    {
        $studentUser = $this->createStudent('Siswa Mau Lulus', 'NIS1002', 'AKTIF', true);

        $response = $this->actingAs($this->admin)->put(route('admin.siswa.update', $studentUser->id), [
            'name' => 'Siswa Sudah Lulus',
            'email' => $studentUser->email,
            'tingkat' => '8',
            'kelas' => 'StatusTest',
            'status' => 'LULUS',
        ]);

        $response->assertRedirect(route('admin.siswa.show', $studentUser->id));

        $profile = StudentProfile::where('user_id', $studentUser->id)->first();
        $this->assertEquals('LULUS', $profile->status);
        $this->assertFalse((bool) $profile->is_active);

        // Verify detail view displays "Lulus"
        $showResponse = $this->actingAs($this->admin)->get(route('admin.siswa.show', $studentUser->id));
        $showResponse->assertSee('Lulus');
    }

    public function test_admin_can_reactivate_student()
    {
        $studentUser = $this->createStudent('Siswa Mau Diaktifkan Lagi', 'NIS1003', 'TIDAK_AKTIF', false);

        $response = $this->actingAs($this->admin)->put(route('admin.siswa.update', $studentUser->id), [
            'name' => 'Siswa Aktif Kembali',
            'email' => $studentUser->email,
            'tingkat' => '8',
            'kelas' => 'StatusTest',
            'status' => 'AKTIF',
        ]);

        $response->assertRedirect(route('admin.siswa.show', $studentUser->id));

        $profile = StudentProfile::where('user_id', $studentUser->id)->first();
        $this->assertEquals('AKTIF', $profile->status);
        $this->assertTrue((bool) $profile->is_active);
    }

    public function test_inactive_student_cannot_login()
    {
        $studentUser = $this->createStudent('Siswa Nonaktif Login', 'NIS1004', 'TIDAK_AKTIF', false);

        $response = $this->post(route('login'), [
            'email' => $studentUser->email,
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_inactive_student_is_excluded_from_jurnal_attendance_and_rekap()
    {
        $activeStudent = $this->createStudent('Siswa Aktif 1', 'NIS2001', 'AKTIF', true);
        $inactiveStudent = $this->createStudent('Siswa Inaktif 1', 'NIS2002', 'TIDAK_AKTIF', false);

        // 1. Check Jurnal Mengajar createWizard
        $wizardResponse = $this->actingAs($this->guru)->get(route('guru.jurnal-mengajar.wizard', [
            'kelas_id' => $this->kelas->id,
            'mata_pelajaran_id' => $this->mapel->id,
            'tanggal' => now()->format('Y-m-d'),
            'jam_ke_mulai' => 1,
            'jam_ke_selesai' => 2,
        ]));

        $wizardResponse->assertStatus(200);
        $wizardResponse->assertSee($activeStudent->name);
        $wizardResponse->assertDontSee($inactiveStudent->name);

        // 2. Check Rekap Absensi Siswa
        $rekapResponse = $this->actingAs($this->admin)->get(route('admin.absensi.siswa.index', [
            'filter' => 'hari-ini',
            'kelas_id' => $this->kelas->id,
        ]));

        $rekapResponse->assertStatus(200);
        $rekapResponse->assertSee($activeStudent->name);
        $rekapResponse->assertDontSee($inactiveStudent->name);
    }
}
