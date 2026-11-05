<?php

namespace Tests\Feature;

use App\Models\GuruProfile;
use App\Models\JadwalPelajaran;
use App\Models\JamPelajaran;
use App\Models\JurnalMengajar;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Semester;
use App\Models\StudentProfile;
use App\Models\TahunPelajaran;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class GuruRekapHakAksesTest extends TestCase
{
    use DatabaseTransactions;

    protected TahunPelajaran $tp;

    protected Semester $semester;

    protected Kelas $kelas7A;

    protected Kelas $kelas7B;

    protected Kelas $kelas7C;

    protected Kelas $kelas8B;

    protected Kelas $kelas9A;

    protected MataPelajaran $mapelMTK;

    protected MataPelajaran $mapelIPA;

    protected User $siswa7A;

    protected User $siswa7B;

    protected User $siswa8B;

    protected User $siswa9A;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tp = TahunPelajaran::firstOrCreate(
            ['nama' => '2026/2027 Test Hak Akses'],
            [
                'tahun_mulai' => 2026,
                'tahun_selesai' => 2027,
                'tanggal_mulai' => '2026-07-15',
                'tanggal_selesai' => '2027-06-30',
                'is_active' => true,
            ]
        );

        $this->semester = Semester::firstOrCreate(
            ['tahun_pelajaran_id' => $this->tp->id, 'semester_ke' => 1],
            [
                'nama' => 'Ganjil Test Hak Akses',
                'tanggal_mulai' => '2026-07-15',
                'tanggal_selesai' => '2026-12-31',
                'is_active' => true,
            ]
        );

        $this->kelas7A = Kelas::firstOrCreate(['tingkat' => '7', 'nama_kelas' => 'A Test'], ['tahun_pelajaran_id' => $this->tp->id]);
        $this->kelas7B = Kelas::firstOrCreate(['tingkat' => '7', 'nama_kelas' => 'B Test'], ['tahun_pelajaran_id' => $this->tp->id]);
        $this->kelas7C = Kelas::firstOrCreate(['tingkat' => '7', 'nama_kelas' => 'C Test'], ['tahun_pelajaran_id' => $this->tp->id]);
        $this->kelas8B = Kelas::firstOrCreate(['tingkat' => '8', 'nama_kelas' => 'B Test'], ['tahun_pelajaran_id' => $this->tp->id]);
        $this->kelas9A = Kelas::firstOrCreate(['tingkat' => '9', 'nama_kelas' => 'A Test'], ['tahun_pelajaran_id' => $this->tp->id]);

        $this->mapelMTK = MataPelajaran::firstOrCreate(['kode_mapel' => 'T_MTK'], ['nama_mapel' => 'Matematika Test', 'is_active' => true]);
        $this->mapelIPA = MataPelajaran::firstOrCreate(['kode_mapel' => 'T_IPA'], ['nama_mapel' => 'IPA Test', 'is_active' => true]);

        // Setup Siswa
        $this->siswa7A = $this->createStudent($this->kelas7A, 'Siswa 7A Hak Akses', '8001');
        $this->siswa7B = $this->createStudent($this->kelas7B, 'Siswa 7B Hak Akses', '8002');
        $this->siswa8B = $this->createStudent($this->kelas8B, 'Siswa 8B Hak Akses', '8003');
        $this->siswa9A = $this->createStudent($this->kelas9A, 'Siswa 9A Hak Akses', '8004');
    }

    private function createStudent(Kelas $kelas, string $name, string $nis): User
    {
        $user = User::create([
            'name' => $name,
            'email' => 'siswa_'.$nis.'_'.uniqid().'@sekolah.id',
            'role' => 'siswa',
            'password' => bcrypt('password123'),
        ]);

        StudentProfile::create([
            'user_id' => $user->id,
            'kelas_id' => $kelas->id,
            'nis' => $nis,
            'nisn' => 'NISN'.$nis,
            'status' => 'AKTIF',
            'is_active' => true,
        ]);

        return $user;
    }

    private function createTeacher(string $name, string $kodeGuru, ?Kelas $waliKelas = null): User
    {
        $user = User::create([
            'name' => $name,
            'email' => 'guru_'.strtolower($kodeGuru).'_'.uniqid().'@sekolah.id',
            'role' => 'guru',
            'password' => bcrypt('password123'),
        ]);

        GuruProfile::create([
            'user_id' => $user->id,
            'nama' => $name,
            'kode_guru' => $kodeGuru,
            'kelas_id' => $waliKelas?->id,
            'mata_pelajaran_id' => $this->mapelMTK->id,
            'is_active' => true,
        ]);

        return $user;
    }

    /**
     * Test Guru Biasa hanya dapat mengakses rekap absensi untuk kelas-kelas yang diajar (7A, 7C, 8B).
     */
    public function test_guru_biasa_hanya_dapat_mengakses_kelas_yang_diajar(): void
    {
        $guruBiasa = $this->createTeacher('Guru Biasa Test', 'GBT1', null);

        // Guru mengajar MTK di 7A & 8B, dan IPA di 7C
        JadwalPelajaran::create([
            'semester_id' => $this->semester->id,
            'kelas_id' => $this->kelas7A->id,
            'mata_pelajaran_id' => $this->mapelMTK->id,
            'guru_id' => $guruBiasa->id,
            'hari' => 'Senin',
            'jam_ke' => 1,
        ]);
        JadwalPelajaran::create([
            'semester_id' => $this->semester->id,
            'kelas_id' => $this->kelas8B->id,
            'mata_pelajaran_id' => $this->mapelMTK->id,
            'guru_id' => $guruBiasa->id,
            'hari' => 'Selasa',
            'jam_ke' => 1,
        ]);
        JadwalPelajaran::create([
            'semester_id' => $this->semester->id,
            'kelas_id' => $this->kelas7C->id,
            'mata_pelajaran_id' => $this->mapelIPA->id,
            'guru_id' => $guruBiasa->id,
            'hari' => 'Rabu',
            'jam_ke' => 1,
        ]);

        $response = $this->actingAs($guruBiasa)->get(route('guru.rekap-absensi.siswa'));
        $response->assertStatus(200);

        $kelasList = $response->viewData('kelasList');
        $kelasIds = $kelasList->pluck('id')->toArray();

        // 7A, 7C, dan 8B harus ada dalam daftar kelas yang diajar
        $this->assertContains($this->kelas7A->id, $kelasIds);
        $this->assertContains($this->kelas7C->id, $kelasIds);
        $this->assertContains($this->kelas8B->id, $kelasIds);

        // 7B dan 9A tidak boleh ada
        $this->assertNotContains($this->kelas7B->id, $kelasIds);
        $this->assertNotContains($this->kelas9A->id, $kelasIds);

        // Siswa 7A dan 8B tampil, Siswa 7B tidak tampil
        $response->assertSee('Siswa 7A Hak Akses');
        $response->assertSee('Siswa 8B Hak Akses');
        $response->assertDontSee('Siswa 7B Hak Akses');
    }

    /**
     * Test Guru Biasa ditolak saat mengakses detail siswa di kelas yang tidak diajar.
     */
    public function test_guru_biasa_ditolak_akses_detail_siswa_kelas_lain(): void
    {
        $guruBiasa = $this->createTeacher('Guru Biasa Test 2', 'GBT2', null);

        JadwalPelajaran::create([
            'semester_id' => $this->semester->id,
            'kelas_id' => $this->kelas7A->id,
            'mata_pelajaran_id' => $this->mapelMTK->id,
            'guru_id' => $guruBiasa->id,
            'hari' => 'Senin',
            'jam_ke' => 1,
        ]);

        // Buka detail siswa 7A (kelas yang diajar) -> 200 OK
        $res7A = $this->actingAs($guruBiasa)->get(route('guru.rekap-absensi.siswa.detail', $this->siswa7A->id));
        $res7A->assertStatus(200);

        // Buka detail siswa 7B (kelas yang TIDAK diajar) -> 403 Forbidden
        $res7B = $this->actingAs($guruBiasa)->get(route('guru.rekap-absensi.siswa.detail', $this->siswa7B->id));
        $res7B->assertStatus(403);

        // Buka monthly kalender siswa 7B -> 403 Forbidden
        $resMonthly = $this->actingAs($guruBiasa)->get(route('guru.rekap-absensi.siswa.monthly', $this->siswa7B->id));
        $resMonthly->assertStatus(403);
    }

    /**
     * Test Wali Kelas otomatis dapat mengakses seluruh kelas pada angkatannya + kelas yang diajar di tingkat lain.
     */
    public function test_wali_kelas_dapat_mengakses_seluruh_kelas_satu_angkatan(): void
    {
        // Guru adalah Wali Kelas 7A (Tingkat 7)
        $waliKelas7A = $this->createTeacher('Wali Kelas 7A', 'WLI7', $this->kelas7A);

        // Wali Kelas 7A juga mengajar di 8B
        JadwalPelajaran::create([
            'semester_id' => $this->semester->id,
            'kelas_id' => $this->kelas8B->id,
            'mata_pelajaran_id' => $this->mapelMTK->id,
            'guru_id' => $waliKelas7A->id,
            'hari' => 'Kamis',
            'jam_ke' => 1,
        ]);

        $response = $this->actingAs($waliKelas7A)->get(route('guru.rekap-absensi.siswa'));
        $response->assertStatus(200);

        $kelasList = $response->viewData('kelasList');
        $kelasIds = $kelasList->pluck('id')->toArray();

        // Seluruh rombel tingkat 7 (7A, 7B, 7C) harus ada
        $this->assertContains($this->kelas7A->id, $kelasIds);
        $this->assertContains($this->kelas7B->id, $kelasIds);
        $this->assertContains($this->kelas7C->id, $kelasIds);

        // Kelas 8B yang diajar juga harus ada
        $this->assertContains($this->kelas8B->id, $kelasIds);

        // Tingkat 9 tidak boleh ada
        $this->assertNotContains($this->kelas9A->id, $kelasIds);

        // Dapat melihat detail siswa 7B (teman 1 angkatan meski bukan wali kelas 7B langsung)
        $res7B = $this->actingAs($waliKelas7A)->get(route('guru.rekap-absensi.siswa.detail', $this->siswa7B->id));
        $res7B->assertStatus(200);

        // Ditolak saat mengakses siswa 9A (bukan angkatannya & tidak diajar)
        $res9A = $this->actingAs($waliKelas7A)->get(route('guru.rekap-absensi.siswa.detail', $this->siswa9A->id));
        $res9A->assertStatus(403);
    }

    /**
     * Test Wali Kelas dapat memantau jurnal mengajar seluruh kelas di angkatannya.
     */
    public function test_wali_kelas_dapat_melihat_jurnal_seluruh_angkatan(): void
    {
        $waliKelas7A = $this->createTeacher('Wali Kelas 7A Jurnal', 'WLJ7', $this->kelas7A);
        $guruLain = $this->createTeacher('Guru Lain 7B', 'GL7B', null);

        // Jam pelajaran mock
        JamPelajaran::firstOrCreate(['jam_ke' => 1], ['mulai' => '07:30', 'selesai' => '08:10']);
        JamPelajaran::firstOrCreate(['jam_ke' => 2], ['mulai' => '08:10', 'selesai' => '08:50']);

        // Guru lain membuat jurnal di 7B
        JurnalMengajar::create([
            'guru_id' => $guruLain->id,
            'kelas_id' => $this->kelas7B->id,
            'mata_pelajaran_id' => $this->mapelIPA->id,
            'hari' => 'Kamis',
            'tanggal' => now()->toDateString(),
            'jam_ke_mulai' => 1,
            'jam_ke_selesai' => 2,
            'materi_pembelajaran' => 'Materi Fotosintesis 7B',
            'keterangan' => 'Praktikum Lapangan',
        ]);

        // Jurnal di kelas 9A oleh guru lain
        JurnalMengajar::create([
            'guru_id' => $guruLain->id,
            'kelas_id' => $this->kelas9A->id,
            'mata_pelajaran_id' => $this->mapelIPA->id,
            'hari' => 'Kamis',
            'tanggal' => now()->toDateString(),
            'jam_ke_mulai' => 1,
            'jam_ke_selesai' => 2,
            'materi_pembelajaran' => 'Materi Genetika 9A',
            'keterangan' => 'Teori',
        ]);

        // Wali Kelas 7A membuka jurnal mengajar
        $response = $this->actingAs($waliKelas7A)->get(route('guru.jurnal-mengajar.index'));
        $response->assertStatus(200);

        // Wali Kelas 7A harus bisa melihat jurnal 7B (1 angkatan), tapi TIDAK bisa melihat jurnal 9A
        $response->assertSee('Materi Fotosintesis 7B');
        $response->assertDontSee('Materi Genetika 9A');
    }
}
