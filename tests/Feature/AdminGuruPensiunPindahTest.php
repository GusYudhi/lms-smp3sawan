<?php

namespace Tests\Feature;

use App\Models\GuruProfile;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Semester;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AdminGuruPensiunPindahTest extends TestCase
{
    use DatabaseTransactions;

    protected $admin;
    protected $mapel;
    protected $kelas;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::firstOrCreate(
            ['email' => 'admin_test_pensiun@admin.id'],
            [
                'name' => 'Admin Pensiun Test',
                'password' => bcrypt('password123'),
                'role' => 'admin',
            ]
        );

        $this->mapel = MataPelajaran::firstOrCreate(
            ['kode_mapel' => 'PEN1'],
            ['nama_mapel' => 'Pensiun Mapel Test', 'is_active' => true]
        );

        $this->kelas = Kelas::firstOrCreate(
            ['nama_kelas' => '9Z Test'],
            ['tingkat' => 9]
        );
    }

    public function test_admin_can_mark_teacher_as_pensiun_pindah_and_clears_wali_kelas()
    {
        $teacher = User::factory()->create([
            'email' => 'guru_pensiun_test_' . uniqid() . '@guru.id',
            'role' => 'guru',
            'password' => bcrypt('password123'),
        ]);

        $profile = GuruProfile::create([
            'user_id' => $teacher->id,
            'nama' => $teacher->name,
            'nip' => 'NIP-PEN-' . uniqid(),
            'kode_guru' => 'GPEN',
            'is_active' => true,
            'kelas_id' => $this->kelas->id,
            'mata_pelajaran_id' => $this->mapel->id,
        ]);

        $this->assertTrue($profile->is_active);
        $this->assertEquals($this->kelas->id, $profile->kelas_id);

        // Edit teacher to mark as pensiun
        $response = $this->actingAs($this->admin)->put(route('admin.guru.update', $teacher->id), [
            'name' => 'Guru Pensiun Berhasil',
            'nomor_induk' => $profile->nip,
            'kode_guru' => $profile->kode_guru,
            'email' => $teacher->email,
            'jenis_kelamin' => 'L',
            'mata_pelajaran' => [$this->mapel->id],
            'kelas_id' => $this->kelas->id, // Submits kelas_id, but should be cleared because pensiun
            'is_pensiun' => '1',
        ]);

        $response->assertRedirect(route('admin.guru.index'));

        $profile->refresh();
        $this->assertFalse((bool) $profile->is_active);
        $this->assertNull($profile->kelas_id, 'Wali kelas must be cleared when teacher is retired/transferred');
    }

    public function test_admin_can_reactivate_retired_teacher()
    {
        $teacher = User::factory()->create([
            'email' => 'guru_reactivate_test_' . uniqid() . '@guru.id',
            'role' => 'guru',
            'password' => bcrypt('password123'),
        ]);

        $profile = GuruProfile::create([
            'user_id' => $teacher->id,
            'nama' => $teacher->name,
            'nip' => 'NIP-REA-' . uniqid(),
            'kode_guru' => 'GREA',
            'is_active' => false,
            'kelas_id' => null,
            'mata_pelajaran_id' => $this->mapel->id,
        ]);

        $this->assertFalse((bool) $profile->is_active);

        // Update without is_pensiun
        $response = $this->actingAs($this->admin)->put(route('admin.guru.update', $teacher->id), [
            'name' => 'Guru Aktif Kembali',
            'nomor_induk' => $profile->nip,
            'kode_guru' => $profile->kode_guru,
            'email' => $teacher->email,
            'jenis_kelamin' => 'L',
            'mata_pelajaran' => [$this->mapel->id],
            'kelas_id' => $this->kelas->id,
            // is_pensiun is NOT passed or 0
        ]);

        $response->assertRedirect(route('admin.guru.index'));

        $profile->refresh();
        $this->assertTrue((bool) $profile->is_active);
        $this->assertEquals($this->kelas->id, $profile->kelas_id);
    }

    public function test_retired_teacher_does_not_appear_in_jadwal_teacher_dropdown()
    {
        $semester = Semester::firstOrCreate(
            ['is_active' => true],
            ['nama_semester' => 'Ganjil Test Pensiun', 'tahun_pelajaran_id' => 1]
        );

        $teacherRetired = User::factory()->create([
            'email' => 'retired_guru_' . uniqid() . '@guru.id',
            'role' => 'guru',
            'password' => bcrypt('password123'),
        ]);
        GuruProfile::create([
            'user_id' => $teacherRetired->id,
            'nama' => 'Pensiun Guru X',
            'nip' => 'NIP-RET-' . uniqid(),
            'kode_guru' => 'GRET',
            'is_active' => false,
            'mata_pelajaran_id' => $this->mapel->id,
        ]);

        $teacherActive = User::factory()->create([
            'email' => 'active_guru_' . uniqid() . '@guru.id',
            'role' => 'guru',
            'password' => bcrypt('password123'),
        ]);
        GuruProfile::create([
            'user_id' => $teacherActive->id,
            'nama' => 'Active Guru Y',
            'nip' => 'NIP-ACT-' . uniqid(),
            'kode_guru' => 'GACT',
            'is_active' => true,
            'mata_pelajaran_id' => $this->mapel->id,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.jadwal.index', $semester->id));
        $response->assertStatus(200);

        $gurus = $response->viewData('gurus');
        $this->assertTrue($gurus->contains('id', $teacherActive->id));
        $this->assertFalse($gurus->contains('id', $teacherRetired->id));
    }

    public function test_retired_teacher_cannot_login()
    {
        $password = 'secret12345';
        $teacher = User::factory()->create([
            'email' => 'login_retired_' . uniqid() . '@guru.id',
            'role' => 'guru',
            'password' => bcrypt($password),
        ]);

        GuruProfile::create([
            'user_id' => $teacher->id,
            'nama' => 'Guru Pensiun Login Test',
            'nip' => 'NIP-LOG-' . uniqid(),
            'kode_guru' => 'GLOG',
            'is_active' => false,
        ]);

        $response = $this->post(route('login'), [
            'email' => $teacher->email,
            'password' => $password,
        ]);

        // Should be logged out and redirected to login with error
        $this->assertGuest();
        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
    }

    public function test_admin_can_filter_teachers_by_status_aktif()
    {
        $activeTeacher = User::factory()->create([
            'name' => 'Alfa Aktif Teacher ' . uniqid(),
            'email' => 'alfa_aktif_' . uniqid() . '@guru.id',
            'role' => 'guru',
            'password' => bcrypt('password123'),
        ]);
        GuruProfile::create([
            'user_id' => $activeTeacher->id,
            'nama' => $activeTeacher->name,
            'nip' => 'NIP-ALF-' . uniqid(),
            'kode_guru' => 'ALFA',
            'is_active' => true,
        ]);

        $retiredTeacher = User::factory()->create([
            'name' => 'Beta Pensiun Teacher ' . uniqid(),
            'email' => 'beta_pensiun_' . uniqid() . '@guru.id',
            'role' => 'guru',
            'password' => bcrypt('password123'),
        ]);
        GuruProfile::create([
            'user_id' => $retiredTeacher->id,
            'nama' => $retiredTeacher->name,
            'nip' => 'NIP-BET-' . uniqid(),
            'kode_guru' => 'BETA',
            'is_active' => false,
        ]);

        // Filter status_aktif = 1 (Aktif)
        $responseAktif = $this->actingAs($this->admin)->get(route('admin.guru.index', ['status_aktif' => '1']));
        $responseAktif->assertStatus(200);
        $teachersAktif = $responseAktif->viewData('teachers');
        $this->assertTrue($teachersAktif->contains('id', $activeTeacher->id));
        $this->assertFalse($teachersAktif->contains('id', $retiredTeacher->id));

        // Filter status_aktif = 0 (Pensiun / Pindah)
        $responsePensiun = $this->actingAs($this->admin)->get(route('admin.guru.index', ['status_aktif' => '0']));
        $responsePensiun->assertStatus(200);
        $teachersPensiun = $responsePensiun->viewData('teachers');
        $this->assertFalse($teachersPensiun->contains('id', $activeTeacher->id));
        $this->assertTrue($teachersPensiun->contains('id', $retiredTeacher->id));
    }
}
