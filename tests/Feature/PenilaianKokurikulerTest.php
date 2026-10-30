<?php

namespace Tests\Feature;

use App\Models\FixedSchedule;
use App\Models\GuruProfile;
use App\Models\JamPelajaran;
use App\Models\Kelas;
use App\Models\KokurikulerSession;
use App\Models\PenilaianKokurikuler;
use App\Models\Semester;
use App\Models\StudentProfile;
use App\Models\TahunPelajaran;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PenilaianKokurikulerTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;

    protected User $waliKelas;

    protected User $regularGuru;

    protected User $student1;

    protected User $student2;

    protected Kelas $kelas;

    protected Semester $semester;

    // 1x1 valid transparent PNG base64
    protected string $sampleBase64Image = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->admin = User::create([
            'name' => 'Admin Kokurikuler Test',
            'email' => 'admin_koku_test@smp3sawan.sch.id',
            'password' => bcrypt('password123'),
            'role' => 'admin',
        ]);

        $tp = TahunPelajaran::create([
            'nama' => '2026/2027 Test Koku',
            'tahun_mulai' => 2026,
            'tahun_selesai' => 2027,
            'tanggal_mulai' => '2026-07-01',
            'tanggal_selesai' => '2027-06-30',
            'is_active' => true,
        ]);

        Semester::query()->update(['is_active' => false]);

        $this->semester = Semester::create([
            'tahun_pelajaran_id' => $tp->id,
            'nama' => 'Ganjil 2026/2027 Test Koku',
            'semester_ke' => 1,
            'tanggal_mulai' => '2026-07-01',
            'tanggal_selesai' => '2026-12-31',
            'is_active' => true,
        ]);

        JamPelajaran::create([
            'semester_id' => $this->semester->id,
            'jam_ke' => 0,
            'jam_mulai' => '06:45',
            'jam_selesai' => '07:30',
        ]);

        $this->kelas = Kelas::create([
            'tingkat' => '7',
            'nama_kelas' => 'Z',
            'tahun_angkatan' => 2026,
            'tahun_pelajaran_id' => $tp->id,
        ]);

        $this->waliKelas = User::create([
            'name' => 'Wali Kelas 7Z',
            'email' => 'walikelas7z@smp3sawan.sch.id',
            'password' => bcrypt('password123'),
            'role' => 'guru',
        ]);

        GuruProfile::create([
            'user_id' => $this->waliKelas->id,
            'nama' => $this->waliKelas->name,
            'nip' => '199001012020011099',
            'kode_guru' => 'WKZ',
            'email' => $this->waliKelas->email,
            'jenis_kelamin' => 'L',
            'kelas_id' => $this->kelas->id,
            'is_active' => true,
        ]);

        $this->regularGuru = User::create([
            'name' => 'Guru Biasa Non Wali',
            'email' => 'gurubiasa_koku@smp3sawan.sch.id',
            'password' => bcrypt('password123'),
            'role' => 'guru',
        ]);

        GuruProfile::create([
            'user_id' => $this->regularGuru->id,
            'nama' => $this->regularGuru->name,
            'nip' => '199001012020011098',
            'kode_guru' => 'GBN',
            'email' => $this->regularGuru->email,
            'jenis_kelamin' => 'P',
            'kelas_id' => null,
            'is_active' => true,
        ]);

        $this->student1 = User::create([
            'name' => 'Siswa Satu 7Z',
            'email' => 'siswa1_7z@smp3sawan.sch.id',
            'password' => bcrypt('password123'),
            'role' => 'siswa',
        ]);

        StudentProfile::create([
            'user_id' => $this->student1->id,
            'nis' => '99001',
            'nisn' => '0099001',
            'kelas_id' => $this->kelas->id,
            'jenis_kelamin' => 'L',
            'is_active' => true,
        ]);

        $this->student2 = User::create([
            'name' => 'Siswa Dua 7Z',
            'email' => 'siswa2_7z@smp3sawan.sch.id',
            'password' => bcrypt('password123'),
            'role' => 'siswa',
        ]);

        StudentProfile::create([
            'user_id' => $this->student2->id,
            'nis' => '99002',
            'nisn' => '0099002',
            'kelas_id' => $this->kelas->id,
            'jenis_kelamin' => 'P',
            'is_active' => true,
        ]);
    }

    public function test_admin_can_create_and_update_kokurikuler_fixed_schedule_at_jam_ke_0()
    {
        $response = $this->actingAs($this->admin)->post(route('admin.fixed-schedule.store'), [
            'semester_id' => $this->semester->id,
            'hari' => 'Selasa',
            'jam_ke' => 0,
            'tipe' => 'kokurikuler',
            'keterangan' => 'Literasi & Numerasi',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('fixed_schedules', [
            'semester_id' => $this->semester->id,
            'hari' => 'Selasa',
            'jam_ke' => 0,
            'tipe' => 'kokurikuler',
            'keterangan' => 'Literasi & Numerasi',
        ]);

        $fixed = FixedSchedule::where('semester_id', $this->semester->id)
            ->where('hari', 'Selasa')
            ->where('jam_ke', 0)
            ->firstOrFail();

        $updateResponse = $this->actingAs($this->admin)->put(route('admin.fixed-schedule.update', $fixed->id), [
            'hari' => 'Selasa',
            'jam_ke' => 0,
            'tipe' => 'kokurikuler',
            'keterangan' => 'Tema Selasa: Kewirausahaan Sosial',
        ]);

        $updateResponse->assertSessionHasNoErrors();
        $this->assertDatabaseHas('fixed_schedules', [
            'id' => $fixed->id,
            'keterangan' => 'Tema Selasa: Kewirausahaan Sosial',
            'tipe' => 'kokurikuler',
        ]);
    }

    public function test_non_wali_kelas_cannot_access_penilaian_kokurikuler()
    {
        $response = $this->actingAs($this->regularGuru)->get(route('guru.penilaian-kokurikuler.index'));
        $response->assertStatus(403);
    }

    public function test_new_session_requires_foto_bukti()
    {
        $targetDate = '2026-10-27';

        $response = $this->actingAs($this->waliKelas)
            ->post(route('guru.penilaian-kokurikuler.store'), [
                'tanggal' => $targetDate,
                'tema' => 'Tema Selasa: Literasi Digital',
                'foto_bukti' => '',
                'absensi' => [
                    $this->student1->id => 'hadir',
                ],
                'nilai' => [
                    $this->student1->id => [
                        'predikat' => 'BSH',
                    ],
                ],
            ]);

        $response->assertSessionHasErrors(['foto_bukti']);
    }

    public function test_wali_kelas_can_save_attendance_photo_description_and_rubric()
    {
        FixedSchedule::create([
            'semester_id' => $this->semester->id,
            'hari' => 'Selasa',
            'jam_ke' => 0,
            'tipe' => 'kokurikuler',
            'keterangan' => 'Tema Selasa: Literasi Digital',
        ]);

        // 2026-10-27 is a Tuesday (Selasa)
        $targetDate = '2026-10-27';

        $pageResponse = $this->actingAs($this->waliKelas)
            ->get(route('guru.penilaian-kokurikuler.index', ['tanggal' => $targetDate]));

        $pageResponse->assertStatus(200);
        $pageResponse->assertSee('Tema Selasa: Literasi Digital');
        $pageResponse->assertSee($this->student1->name);
        $pageResponse->assertSee($this->student2->name);

        // Student 1 is 'hadir' with 'SAB', Student 2 is 'sakit' (even if predikat is sent, it must be nulled)
        $storeResponse = $this->actingAs($this->waliKelas)
            ->post(route('guru.penilaian-kokurikuler.store'), [
                'tanggal' => $targetDate,
                'tema' => 'Tema Selasa: Literasi Digital',
                'deskripsi' => 'Siswa membaca buku dan mendiskusikan intisari bacaan.',
                'foto_bukti' => $this->sampleBase64Image,
                'absensi' => [
                    $this->student1->id => 'hadir',
                    $this->student2->id => 'sakit',
                ],
                'nilai' => [
                    $this->student1->id => [
                        'predikat' => 'SAB',
                        'catatan' => 'Sangat aktif berdiskusi',
                    ],
                    $this->student2->id => [
                        'predikat' => 'BSH',
                        'catatan' => 'Surat dokter',
                    ],
                ],
            ]);

        $storeResponse->assertRedirect(route('guru.penilaian-kokurikuler.index', ['tanggal' => $targetDate]));
        $storeResponse->assertSessionHas('success');

        $session = KokurikulerSession::where('kelas_id', $this->kelas->id)
            ->whereDate('tanggal', $targetDate)
            ->first();

        $this->assertNotNull($session);
        $this->assertEquals('Siswa membaca buku dan mendiskusikan intisari bacaan.', $session->deskripsi);
        $this->assertNotNull($session->foto_bukti);
        Storage::disk('public')->assertExists($session->foto_bukti);

        // Student 1 (hadir) has predikat SAB
        $this->assertDatabaseHas('penilaian_kokurikuler', [
            'kokurikuler_session_id' => $session->id,
            'semester_id' => $this->semester->id,
            'kelas_id' => $this->kelas->id,
            'tanggal' => $targetDate,
            'student_user_id' => $this->student1->id,
            'status_absensi' => 'hadir',
            'predikat' => 'SAB',
            'catatan' => 'Sangat aktif berdiskusi',
        ]);

        // Student 2 (sakit) must have predikat = null
        $this->assertDatabaseHas('penilaian_kokurikuler', [
            'kokurikuler_session_id' => $session->id,
            'semester_id' => $this->semester->id,
            'kelas_id' => $this->kelas->id,
            'tanggal' => $targetDate,
            'student_user_id' => $this->student2->id,
            'status_absensi' => 'sakit',
            'predikat' => null,
            'catatan' => 'Surat dokter',
        ]);

        // Updating existing session without uploading a new photo keeps old photo
        $updateResponse = $this->actingAs($this->waliKelas)
            ->post(route('guru.penilaian-kokurikuler.store'), [
                'tanggal' => $targetDate,
                'tema' => 'Tema Selasa: Literasi Digital',
                'deskripsi' => 'Deskripsi diperbarui.',
                'foto_bukti' => '',
                'absensi' => [
                    $this->student1->id => 'hadir',
                    $this->student2->id => 'terlambat',
                ],
                'nilai' => [
                    $this->student1->id => [
                        'predikat' => 'SAB',
                    ],
                    $this->student2->id => [
                        'predikat' => 'SB',
                        'catatan' => 'Menyusul hadir terlambat',
                    ],
                ],
            ]);

        $updateResponse->assertSessionHasNoErrors();

        $this->assertDatabaseHas('penilaian_kokurikuler', [
            'kelas_id' => $this->kelas->id,
            'tanggal' => $targetDate,
            'student_user_id' => $this->student2->id,
            'status_absensi' => 'terlambat',
            'predikat' => 'SB',
            'catatan' => 'Menyusul hadir terlambat',
        ]);
    }

    public function test_wali_kelas_and_admin_can_access_rekap_and_export()
    {
        PenilaianKokurikuler::create([
            'semester_id' => $this->semester->id,
            'kelas_id' => $this->kelas->id,
            'tanggal' => '2026-10-28',
            'hari' => 'Rabu',
            'tema' => 'Tema Rabu: Lingkungan Hidup',
            'student_user_id' => $this->student1->id,
            'penilai_user_id' => $this->waliKelas->id,
            'status_absensi' => 'hadir',
            'predikat' => 'BSH',
            'catatan' => 'Baik',
        ]);

        $rekapResponse = $this->actingAs($this->waliKelas)->get(route('guru.penilaian-kokurikuler.rekap', [
            'bulan' => 10,
            'hari' => 'Rabu',
        ]));
        $rekapResponse->assertStatus(200);
        $rekapResponse->assertSee($this->student1->name);

        $adminRekapResponse = $this->actingAs($this->admin)->get(route('admin.penilaian-kokurikuler.rekap', [
            'kelas_id' => $this->kelas->id,
            'semester_id' => $this->semester->id,
            'bulan' => 10,
            'hari' => 'Rabu',
        ]));
        $adminRekapResponse->assertStatus(200);

        $exportResponse = $this->actingAs($this->waliKelas)->get(route('guru.penilaian-kokurikuler.export', [
            'semester_id' => $this->semester->id,
            'bulan' => 10,
            'hari' => 'Rabu',
        ]));
        $exportResponse->assertStatus(200);

        $export = new \App\Exports\PenilaianKokurikulerExport($this->kelas, $this->semester, '10', 'Rabu');
        $sheets = $export->sheets();
        $rekapHeadings = $sheets[0]->headings();
        $harianHeadings = $sheets[1]->headings();

        $this->assertEquals(['Daftar Kegiatan Kokurikuler:'], $rekapHeadings[4]);
        $this->assertContains('Rabu', end($rekapHeadings));
        $this->assertEquals(['Daftar Kegiatan Kokurikuler:'], $harianHeadings[4]);
        $this->assertContains('Rabu, 28/10/2026', end($harianHeadings));
    }
}
