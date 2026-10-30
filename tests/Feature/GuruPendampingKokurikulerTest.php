<?php

namespace Tests\Feature;

use App\Models\GuruProfile;
use App\Models\Kelas;
use App\Models\KokurikulerSession;
use App\Models\PenilaianKokurikuler;
use App\Models\Semester;
use App\Models\StudentProfile;
use App\Models\TahunPelajaran;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class GuruPendampingKokurikulerTest extends TestCase
{
    use DatabaseTransactions;

    protected $admin;

    protected $waliGuruUser;

    protected $waliGuruProfile;

    protected $pendampingGuruUser;

    protected $pendampingGuruProfile;

    protected $kelas;

    protected $activeSemester;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Admin
        $this->admin = User::firstOrCreate(
            ['email' => 'admin_test_pendamping@gmail.com'],
            ['name' => 'Admin Test', 'password' => bcrypt('password'), 'role' => 'admin']
        );

        // 2. Tahun Pelajaran & Semester
        $tp = TahunPelajaran::firstOrCreate(
            ['tahun_mulai' => 2026, 'tahun_selesai' => 2027],
            ['nama' => '2026/2027', 'is_active' => true]
        );

        $this->activeSemester = Semester::firstOrCreate(
            ['tahun_pelajaran_id' => $tp->id, 'semester_ke' => 1],
            ['nama' => 'Ganjil', 'is_active' => true, 'tanggal_mulai' => '2026-07-01', 'tanggal_selesai' => '2026-12-31']
        );

        // 3. Kelas
        $this->kelas = Kelas::create([
            'tingkat' => 8,
            'nama_kelas' => 'Z',
            'tahun_angkatan' => 2026,
            'tahun_pelajaran_id' => $tp->id,
        ]);

        // 4. Wali Guru
        $this->waliGuruUser = User::create([
            'name' => 'Guru Wali Test',
            'email' => 'wali_test_'.uniqid().'@example.com',
            'password' => bcrypt('password'),
            'role' => 'guru',
        ]);
        $this->waliGuruProfile = GuruProfile::create([
            'user_id' => $this->waliGuruUser->id,
            'nama' => 'Guru Wali Test',
            'nip' => 'NIP_WALI_'.uniqid(),
            'kelas_id' => $this->kelas->id,
            'is_active' => true,
        ]);

        // 5. Pendamping Guru (Non-Wali Kelas)
        $this->pendampingGuruUser = User::create([
            'name' => 'Guru Pendamping Test',
            'email' => 'pendamping_test_'.uniqid().'@example.com',
            'password' => bcrypt('password'),
            'role' => 'guru',
        ]);
        $this->pendampingGuruProfile = GuruProfile::create([
            'user_id' => $this->pendampingGuruUser->id,
            'nama' => 'Guru Pendamping Test',
            'nip' => 'NIP_PENDAMPING_'.uniqid(),
            'kelas_id' => null, // Non-Wali Kelas
            'is_active' => true,
        ]);
    }

    public function test_admin_can_assign_guru_pendamping_to_class()
    {
        $response = $this->actingAs($this->admin)->put(route('admin.kelas.update', $this->kelas->id), [
            'tingkat' => $this->kelas->tingkat,
            'nama_kelas' => $this->kelas->nama_kelas,
            'tahun_angkatan' => $this->kelas->tahun_angkatan,
            'wali_kelas_id' => $this->waliGuruProfile->id,
            'guru_pendamping_id' => $this->pendampingGuruProfile->id,
        ]);

        $response->assertRedirect(route('admin.kelas.index'));
        $this->assertDatabaseHas('kelas', [
            'id' => $this->kelas->id,
            'guru_pendamping_id' => $this->pendampingGuruProfile->id,
        ]);
    }

    public function test_admin_cannot_assign_wali_kelas_as_guru_pendamping()
    {
        $dummyClass = Kelas::create([
            'tingkat' => 7,
            'nama_kelas' => 'X',
            'tahun_angkatan' => 2026,
        ]);

        // Another wali guru
        $otherWaliUser = User::create([
            'name' => 'Other Wali',
            'email' => 'other_wali_'.uniqid().'@example.com',
            'password' => bcrypt('password'),
            'role' => 'guru',
        ]);
        $otherWaliProfile = GuruProfile::create([
            'user_id' => $otherWaliUser->id,
            'nama' => 'Other Wali',
            'nip' => 'NIP_OTHER_'.uniqid(),
            'kelas_id' => $dummyClass->id, // Already a Wali Kelas
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->put(route('admin.kelas.update', $this->kelas->id), [
            'tingkat' => $this->kelas->tingkat,
            'nama_kelas' => $this->kelas->nama_kelas,
            'tahun_angkatan' => $this->kelas->tahun_angkatan,
            'wali_kelas_id' => $this->waliGuruProfile->id,
            'guru_pendamping_id' => $otherWaliProfile->id,
        ]);

        $response->assertSessionHasErrors('guru_pendamping_id');
    }

    public function test_auto_release_guru_pendamping_when_promoted_to_wali_kelas()
    {
        // 1. Assign as pendamping
        $this->kelas->update(['guru_pendamping_id' => $this->pendampingGuruProfile->id]);
        $this->assertEquals($this->pendampingGuruProfile->id, $this->kelas->fresh()->guru_pendamping_id);

        // 2. Another class assigns this pendamping guru as Wali Kelas
        $anotherKelas = Kelas::create([
            'tingkat' => 9,
            'nama_kelas' => 'Y',
            'tahun_angkatan' => 2026,
        ]);

        $response = $this->actingAs($this->admin)->put(route('admin.kelas.update', $anotherKelas->id), [
            'tingkat' => $anotherKelas->tingkat,
            'nama_kelas' => $anotherKelas->nama_kelas,
            'tahun_angkatan' => $anotherKelas->tahun_angkatan,
            'wali_kelas_id' => $this->pendampingGuruProfile->id,
            'guru_pendamping_id' => null,
        ]);

        $response->assertRedirect(route('admin.kelas.index'));

        // Check that old assisted class has guru_pendamping_id released to NULL
        $this->assertNull($this->kelas->fresh()->guru_pendamping_id);
    }

    public function test_guru_pendamping_can_access_kokurikuler_and_cannot_access_rekap()
    {
        $this->kelas->update(['guru_pendamping_id' => $this->pendampingGuruProfile->id]);

        // Access index page
        $response = $this->actingAs($this->pendampingGuruUser)->get(route('guru.penilaian-kokurikuler.index', [
            'kelas_id' => $this->kelas->id,
        ]));
        $response->assertOk();
        $response->assertSee('Guru Pendamping');
        $response->assertDontSee('Lanjut ke Penilaian Siswa');
        $response->assertSee('Simpan Absensi & Dokumentasi', false);

        // Access Rekap (Must be 403 Forbidden)
        $rekapResponse = $this->actingAs($this->pendampingGuruUser)->get(route('guru.penilaian-kokurikuler.rekap', [
            'kelas_id' => $this->kelas->id,
        ]));
        $rekapResponse->assertForbidden();

        // Access Export (Must be 403 Forbidden)
        $exportResponse = $this->actingAs($this->pendampingGuruUser)->get(route('guru.penilaian-kokurikuler.export', [
            'kelas_id' => $this->kelas->id,
        ]));
        $exportResponse->assertForbidden();
    }

    public function test_guru_pendamping_preserves_wali_kelas_rubric_evaluations()
    {
        $this->kelas->update(['guru_pendamping_id' => $this->pendampingGuruProfile->id]);

        // Create student
        $studentUser = User::create([
            'name' => 'Siswa Test',
            'email' => 'siswa_'.uniqid().'@example.com',
            'password' => bcrypt('password'),
            'role' => 'siswa',
        ]);
        StudentProfile::create([
            'user_id' => $studentUser->id,
            'kelas_id' => $this->kelas->id,
            'nis' => '123456',
            'is_active' => true,
        ]);

        $date = '2026-08-11';

        // 1. Wali Kelas inputs rubric assessment
        $session = KokurikulerSession::create([
            'kelas_id' => $this->kelas->id,
            'semester_id' => $this->activeSemester->id,
            'penilai_user_id' => $this->waliGuruUser->id,
            'tanggal' => $date,
            'hari' => 'Selasa',
            'tema' => 'Tema Awal Wali',
            'foto_bukti' => 'kokurikuler_bukti/sample.webp',
        ]);

        PenilaianKokurikuler::create([
            'kokurikuler_session_id' => $session->id,
            'semester_id' => $this->activeSemester->id,
            'kelas_id' => $this->kelas->id,
            'tanggal' => $date,
            'hari' => 'Selasa',
            'tema' => 'Tema Awal Wali',
            'student_user_id' => $studentUser->id,
            'penilai_user_id' => $this->waliGuruUser->id,
            'status_absensi' => 'hadir',
            'predikat' => 'BSH',
            'catatan' => 'Catatan Rahasia Wali',
        ]);

        // 2. Guru Pendamping updates documentation & attendance without providing 'nilai'
        $response = $this->actingAs($this->pendampingGuruUser)->post(route('guru.penilaian-kokurikuler.store'), [
            'kelas_id' => $this->kelas->id,
            'tanggal' => $date,
            'tema' => 'Tema Diupdate Pendamping',
            'deskripsi' => 'Deskripsi kegiatan oleh pendamping',
            'absensi' => [
                $studentUser->id => 'hadir',
            ],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Verify rubric evaluation was NOT overwritten to NULL
        $assessment = PenilaianKokurikuler::where('kelas_id', $this->kelas->id)
            ->where('student_user_id', $studentUser->id)
            ->whereDate('tanggal', $date)
            ->first();

        $this->assertNotNull($assessment);
        $this->assertEquals('hadir', $assessment->status_absensi);
        $this->assertEquals('BSH', $assessment->predikat);
        $this->assertEquals('Catatan Rahasia Wali', $assessment->catatan);
        $this->assertEquals('Tema Diupdate Pendamping', $assessment->tema);
    }
}
