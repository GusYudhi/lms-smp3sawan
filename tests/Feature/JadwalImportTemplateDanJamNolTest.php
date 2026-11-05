<?php

namespace Tests\Feature;

use App\Exports\JadwalTemplateExport;
use App\Models\FixedSchedule;
use App\Models\GuruProfile;
use App\Models\JadwalPelajaran;
use App\Models\JamPelajaran;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Semester;
use App\Models\TahunPelajaran;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class JadwalImportTemplateDanJamNolTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;

    protected Semester $semester;

    protected Kelas $kelas7A;

    protected MataPelajaran $mapelMAT;

    protected MataPelajaran $mapelIPA;

    protected User $guruAHM;

    protected User $guruBUD;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::firstOrCreate(
            ['email' => 'admin_test_jadwal@test.com'],
            ['name' => 'Admin Test Jadwal', 'password' => bcrypt('password'), 'role' => 'admin']
        );

        $tp = TahunPelajaran::firstOrCreate(
            ['nama' => '2026/2027 Test TP Template'],
            [
                'tahun_mulai' => 2026,
                'tahun_selesai' => 2027,
                'tanggal_mulai' => '2026-07-15',
                'tanggal_selesai' => '2027-06-30',
                'is_active' => true,
            ]
        );

        $this->semester = Semester::firstOrCreate(
            ['semester_ke' => 1, 'tahun_pelajaran_id' => $tp->id],
            [
                'nama' => 'Semester Ganjil Test',
                'tanggal_mulai' => '2026-07-15',
                'tanggal_selesai' => '2026-12-20',
                'is_active' => true,
            ]
        );

        $this->kelas7A = Kelas::firstOrCreate(
            ['tingkat' => 7, 'nama_kelas' => 'A', 'tahun_pelajaran_id' => $tp->id]
        );

        $this->mapelMAT = MataPelajaran::firstOrCreate(
            ['kode_mapel' => 'MAT'],
            ['nama_mapel' => 'Matematika', 'is_active' => true]
        );

        $this->mapelIPA = MataPelajaran::firstOrCreate(
            ['kode_mapel' => 'IPA'],
            ['nama_mapel' => 'Ilmu Pengetahuan Alam', 'is_active' => true]
        );

        $this->guruAHM = User::firstOrCreate(
            ['email' => 'guru.tga@smp3sawan.sch.id'],
            ['name' => 'Ahmad Guru', 'password' => bcrypt('password'), 'role' => 'guru']
        );
        GuruProfile::updateOrCreate(
            ['user_id' => $this->guruAHM->id],
            ['nama' => 'Ahmad Guru', 'kode_guru' => 'TGA', 'is_active' => true]
        );

        $this->guruBUD = User::firstOrCreate(
            ['email' => 'guru.tgb@smp3sawan.sch.id'],
            ['name' => 'Budi Guru', 'password' => bcrypt('password'), 'role' => 'guru']
        );
        GuruProfile::updateOrCreate(
            ['user_id' => $this->guruBUD->id],
            ['nama' => 'Budi Guru', 'kode_guru' => 'TGB', 'is_active' => true]
        );
    }

    private function createTemporaryExcelFile(Spreadsheet $spreadsheet): string
    {
        $tempPath = tempnam(sys_get_temp_dir(), 'jadwal_test_').'.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempPath);

        return $tempPath;
    }

    public function test_download_template_jadwal_menyertakan_jam_ke_0_dan_jadwal_tetap_serta_prefilled(): void
    {
        // 1. Buat JamPelajaran jam ke-0 dan jam ke-1
        JamPelajaran::updateOrCreate(
            ['semester_id' => $this->semester->id, 'jam_ke' => 0],
            ['jam_mulai' => '07:00:00', 'jam_selesai' => '07:30:00']
        );
        JamPelajaran::updateOrCreate(
            ['semester_id' => $this->semester->id, 'jam_ke' => 1],
            ['jam_mulai' => '07:30:00', 'jam_selesai' => '08:15:00']
        );

        // 2. Buat FixedSchedule pada Senin jam ke-0 (UPACARA)
        FixedSchedule::create([
            'semester_id' => $this->semester->id,
            'hari' => 'Senin',
            'jam_ke' => 0,
            'keterangan' => 'UPACARA',
            'tipe' => 'umum',
        ]);

        // 3. Buat JadwalPelajaran yang sudah ada pada Senin jam ke-1 (MAT AHM)
        JadwalPelajaran::create([
            'semester_id' => $this->semester->id,
            'kelas_id' => $this->kelas7A->id,
            'mata_pelajaran_id' => $this->mapelMAT->id,
            'guru_id' => $this->guruAHM->id,
            'hari' => 'Senin',
            'jam_ke' => 1,
        ]);

        // Test export template instance
        $export = new JadwalTemplateExport($this->semester->id);
        $sheets = $export->sheets();
        $this->assertCount(4, $sheets);

        $inputSheet = $sheets[0];
        $collection = $inputSheet->collection();

        // Cari baris Senin jam 0
        $seninJam0 = $collection->first(function ($row) {
            return ($row[0] === 'Senin' || $row[0] === '') && $row[1] === 0;
        });
        $this->assertNotNull($seninJam0, 'Baris Jam ke-0 harus ada di template.');
        $this->assertSame('UPACARA', $seninJam0[3], 'Slot Upacara harus ditandai teks UPACARA.');

        // Cari baris Senin jam 1
        $seninJam1 = $collection->first(function ($row) {
            return $row[1] === 1;
        });
        $this->assertNotNull($seninJam1);
        $this->assertSame('MAT TGA', $seninJam1[3], 'Slot yang sudah ada harus pre-filled dengan format MAT TGA.');

        // Test HTTP route download template
        $response = $this->actingAs($this->admin)->get(route('admin.jadwal.template', ['semester_id' => $this->semester->id]));
        $response->assertStatus(200);
        $response->assertHeader('content-disposition');
    }

    public function test_dry_run_import_mendeteksi_perubahan_jadwal_dan_jam_ke_0(): void
    {
        // Setup jadwal lama: Senin jam 1 = MAT TGA
        JadwalPelajaran::create([
            'semester_id' => $this->semester->id,
            'kelas_id' => $this->kelas7A->id,
            'mata_pelajaran_id' => $this->mapelMAT->id,
            'guru_id' => $this->guruAHM->id,
            'hari' => 'Senin',
            'jam_ke' => 1,
        ]);

        // Buat file excel uji
        $spreadsheet = new Spreadsheet;

        // Sheet 1: KODE_MAPEL
        $sheetMapel = $spreadsheet->getActiveSheet();
        $sheetMapel->setTitle('KODE_MAPEL');
        $sheetMapel->setCellValue('A1', 'KODE MAPEL');
        $sheetMapel->setCellValue('B1', 'NAMA MAPEL');
        $sheetMapel->setCellValue('A2', 'MAT');
        $sheetMapel->setCellValue('B2', 'Matematika');
        $sheetMapel->setCellValue('A3', 'IPA');
        $sheetMapel->setCellValue('B3', 'Ilmu Pengetahuan Alam');

        // Sheet 2: KODE_GURU
        $sheetGuru = $spreadsheet->createSheet();
        $sheetGuru->setTitle('KODE_GURU');
        $sheetGuru->setCellValue('A1', 'KODE GURU');
        $sheetGuru->setCellValue('B1', 'NAMA GURU');
        $sheetGuru->setCellValue('A2', 'TGA');
        $sheetGuru->setCellValue('B2', 'Ahmad Guru');
        $sheetGuru->setCellValue('A3', 'TGB');
        $sheetGuru->setCellValue('B3', 'Budi Guru');

        // Sheet 3: INPUT_JADWAL
        $sheetJadwal = $spreadsheet->createSheet();
        $sheetJadwal->setTitle('INPUT_JADWAL');
        $sheetJadwal->setCellValue('A1', 'JADWAL PELAJARAN');
        $sheetJadwal->setCellValue('A2', 'HARI');
        $sheetJadwal->setCellValue('B2', 'JAM KE');
        $sheetJadwal->setCellValue('C2', 'WAKTU');
        $sheetJadwal->setCellValue('D2', '7A');

        // Row 3: Jam ke-0 (Baru) -> IPA TGB
        $sheetJadwal->setCellValue('A3', 'Senin');
        $sheetJadwal->setCellValue('B3', 0);
        $sheetJadwal->setCellValue('C3', '07:00 - 07:30');
        $sheetJadwal->setCellValue('D3', 'IPA TGB');

        // Row 4: Jam ke-1 (Update dari MAT TGA ke IPA TGB)
        $sheetJadwal->setCellValue('A4', 'Senin');
        $sheetJadwal->setCellValue('B4', 1);
        $sheetJadwal->setCellValue('C4', '07:30 - 08:15');
        $sheetJadwal->setCellValue('D4', 'IPA TGB');

        // Row 5: Jam ke-2 (Fixed Schedule -> UPACARA, harus di-skip)
        $sheetJadwal->setCellValue('A5', 'Senin');
        $sheetJadwal->setCellValue('B5', 2);
        $sheetJadwal->setCellValue('C5', '08:15 - 09:00');
        $sheetJadwal->setCellValue('D5', 'UPACARA');

        $filePath = $this->createTemporaryExcelFile($spreadsheet);
        $uploadedFile = new UploadedFile(
            $filePath,
            'jadwal_test.xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null,
            true
        );

        // Dry Run = 1
        $response = $this->actingAs($this->admin)->post(route('admin.jadwal.import'), [
            'file' => $uploadedFile,
            'semester_id' => $this->semester->id,
            'dry_run' => '1',
        ]);

        $response->assertStatus(200);
        $json = $response->json();

        $this->assertSame('preview', $json['status']);
        $this->assertEquals(2, $json['success_count']); // Jam 0 dan Jam 1 valid, Upacara diskip
        $this->assertEquals(0, $json['failure_count']);

        // Verifikasi changes
        $changes = collect($json['changes']);
        $newChange = $changes->firstWhere('type', 'jadwal_new');
        $this->assertNotNull($newChange, 'Perubahan jadwal baru (jam 0) harus tercatat.');
        $this->assertSame(0, $newChange['jam_ke']);
        $this->assertSame('(Kosong)', $newChange['old']);
        $this->assertSame('IPA TGB', $newChange['new']);

        $updateChange = $changes->firstWhere('type', 'jadwal_update');
        $this->assertNotNull($updateChange, 'Perubahan jadwal lama (jam 1) harus tercatat.');
        $this->assertSame(1, $updateChange['jam_ke']);
        $this->assertSame('MAT TGA', $updateChange['old']);
        $this->assertSame('IPA TGB', $updateChange['new']);

        // Verifikasi database TIDAK berubah saat dry run
        $this->assertDatabaseHas('jadwal_pelajarans', [
            'semester_id' => $this->semester->id,
            'kelas_id' => $this->kelas7A->id,
            'hari' => 'Senin',
            'jam_ke' => 1,
            'mata_pelajaran_id' => $this->mapelMAT->id,
        ]);
        $this->assertDatabaseMissing('jadwal_pelajarans', [
            'semester_id' => $this->semester->id,
            'kelas_id' => $this->kelas7A->id,
            'hari' => 'Senin',
            'jam_ke' => 0,
        ]);

        // Real Execution (dry_run = 0)
        $realFile = new UploadedFile(
            $filePath,
            'jadwal_test.xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null,
            true
        );
        $realResponse = $this->actingAs($this->admin)->post(route('admin.jadwal.import'), [
            'file' => $realFile,
            'semester_id' => $this->semester->id,
            'dry_run' => '0',
        ]);

        $realResponse->assertStatus(200);

        // Verifikasi di database: Jam 0 tersimpan
        $this->assertDatabaseHas('jadwal_pelajarans', [
            'semester_id' => $this->semester->id,
            'kelas_id' => $this->kelas7A->id,
            'hari' => 'Senin',
            'jam_ke' => 0,
            'mata_pelajaran_id' => $this->mapelIPA->id,
            'guru_id' => $this->guruBUD->id,
        ]);

        // Verifikasi di database: Jam 1 ter-update ke IPA TGB
        $this->assertDatabaseHas('jadwal_pelajarans', [
            'semester_id' => $this->semester->id,
            'kelas_id' => $this->kelas7A->id,
            'hari' => 'Senin',
            'jam_ke' => 1,
            'mata_pelajaran_id' => $this->mapelIPA->id,
            'guru_id' => $this->guruBUD->id,
        ]);

        // Verifikasi UPACARA tidak menjadi jadwal pelajaran
        $this->assertDatabaseMissing('jadwal_pelajarans', [
            'semester_id' => $this->semester->id,
            'kelas_id' => $this->kelas7A->id,
            'hari' => 'Senin',
            'jam_ke' => 2,
        ]);

        @unlink($filePath);
    }

    public function test_import_dengan_toggle_skipped_slots_tidak_menimpa_jadwal_yang_dinonaktifkan(): void
    {
        // Setup jadwal lama: Senin jam 1 = MAT TGA
        JadwalPelajaran::create([
            'semester_id' => $this->semester->id,
            'kelas_id' => $this->kelas7A->id,
            'mata_pelajaran_id' => $this->mapelMAT->id,
            'guru_id' => $this->guruAHM->id,
            'hari' => 'Senin',
            'jam_ke' => 1,
        ]);

        // Buat file excel uji
        $spreadsheet = new Spreadsheet();

        $sheetMapel = $spreadsheet->getActiveSheet();
        $sheetMapel->setTitle('KODE_MAPEL');
        $sheetMapel->setCellValue('A1', 'KODE MAPEL');
        $sheetMapel->setCellValue('B1', 'NAMA MAPEL');
        $sheetMapel->setCellValue('A2', 'IPA');
        $sheetMapel->setCellValue('B2', 'Ilmu Pengetahuan Alam');

        $sheetGuru = $spreadsheet->createSheet();
        $sheetGuru->setTitle('KODE_GURU');
        $sheetGuru->setCellValue('A1', 'KODE GURU');
        $sheetGuru->setCellValue('B1', 'NAMA GURU');
        $sheetGuru->setCellValue('A2', 'TGB');
        $sheetGuru->setCellValue('B2', 'Budi Guru');

        $sheetJadwal = $spreadsheet->createSheet();
        $sheetJadwal->setTitle('INPUT_JADWAL');
        $sheetJadwal->setCellValue('A1', 'JADWAL PELAJARAN');
        $sheetJadwal->setCellValue('A2', 'HARI');
        $sheetJadwal->setCellValue('B2', 'JAM KE');
        $sheetJadwal->setCellValue('C2', 'WAKTU');
        $sheetJadwal->setCellValue('D2', '7A');

        // Row 3: Jam ke-1 di Excel ingin diubah ke IPA TGB
        $sheetJadwal->setCellValue('A3', 'Senin');
        $sheetJadwal->setCellValue('B3', 1);
        $sheetJadwal->setCellValue('C3', '07:30 - 08:15');
        $sheetJadwal->setCellValue('D3', 'IPA TGB');

        $filePath = $this->createTemporaryExcelFile($spreadsheet);
        $file = new UploadedFile($filePath, 'jadwal_test.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);

        // Kirim permintaan dengan skipped_slots menonaktifkan perubahan slot Senin_1_{kelasId}
        $slotKey = "Senin_1_{$this->kelas7A->id}";

        $response = $this->actingAs($this->admin)->post(route('admin.jadwal.import'), [
            'file' => $file,
            'semester_id' => $this->semester->id,
            'dry_run' => '0',
            'skipped_slots' => json_encode([$slotKey]),
        ]);

        $response->assertStatus(200);

        // Verifikasi jadwal lama MAT TGA TIDAK tertimpa (tetap MAT TGA, bukan IPA TGB)
        $this->assertDatabaseHas('jadwal_pelajarans', [
            'semester_id' => $this->semester->id,
            'kelas_id' => $this->kelas7A->id,
            'hari' => 'Senin',
            'jam_ke' => 1,
            'mata_pelajaran_id' => $this->mapelMAT->id,
            'guru_id' => $this->guruAHM->id,
        ]);

        @unlink($filePath);
    }

    public function test_guru_sheet_import_mengenali_kepala_sekolah_dan_nama_profile(): void
    {
        // Buat user kepala sekolah yang memiliki GuruProfile (gunakan kode unik agar tidak collision)
        $kodeTest = 'TGK';
        $kepsekUser = User::create([
            'name' => 'Kepala Sekolah Test ' . uniqid(),
            'email' => 'paksa_test_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'role' => 'kepala_sekolah',
        ]);

        GuruProfile::create([
            'user_id' => $kepsekUser->id,
            'nama' => $kepsekUser->name,
            'nip' => '19651231198803' . rand(1000, 9999),
            'kode_guru' => $kodeTest,
            'is_active' => true,
        ]);

        $spreadsheet = new Spreadsheet();
        $spreadsheet->removeSheetByIndex(0);

        $sheetMapel = $spreadsheet->createSheet();
        $sheetMapel->setTitle('KODE_MAPEL');
        $sheetMapel->setCellValue('A1', 'KODE MAPEL');
        $sheetMapel->setCellValue('B1', 'NAMA MAPEL');

        $sheetGuru = $spreadsheet->createSheet();
        $sheetGuru->setTitle('KODE_GURU');
        $sheetGuru->setCellValue('A1', 'KODE GURU');
        $sheetGuru->setCellValue('B1', 'NAMA GURU');
        $sheetGuru->setCellValue('A2', $kodeTest);
        $sheetGuru->setCellValue('B2', $kepsekUser->name);

        $sheetJadwal = $spreadsheet->createSheet();
        $sheetJadwal->setTitle('INPUT_JADWAL');
        $sheetJadwal->setCellValue('A1', 'JADWAL PELAJARAN');
        $sheetJadwal->setCellValue('A2', 'HARI');
        $sheetJadwal->setCellValue('B2', 'JAM KE');
        $sheetJadwal->setCellValue('C2', 'WAKTU');
        $sheetJadwal->setCellValue('D2', '7A');

        $filePath = $this->createTemporaryExcelFile($spreadsheet);
        $file = new UploadedFile($filePath, 'jadwal_test_ks.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);

        $response = $this->actingAs($this->admin)->post(route('admin.jadwal.import'), [
            'file' => $file,
            'semester_id' => $this->semester->id,
            'dry_run' => '1',
        ]);

        $response->assertStatus(200);
        $data = $response->json();

        // Pastikan tidak ada failure_count dari kepala sekolah
        $this->assertEquals(0, $data['failure_count']);
        $this->assertEmpty($data['errors']);

        @unlink($filePath);
    }

    public function test_import_eksekusi_ditolak_jika_terdapat_bentrok_atau_error_validasi(): void
    {
        // 1. Buat 2 kelas: 7A dan 7B
        $kelas7B = Kelas::firstOrCreate(
            ['tingkat' => '7', 'nama_kelas' => 'B', 'tahun_pelajaran_id' => $this->semester->tahun_pelajaran_id]
        );

        // Buat file Excel yang memiliki bentrok: Guru TGA mengajar di 7A dan 7B pada Senin jam 1
        $spreadsheet = new Spreadsheet();
        $spreadsheet->removeSheetByIndex(0);

        $sheetMapel = $spreadsheet->createSheet();
        $sheetMapel->setTitle('KODE_MAPEL');
        $sheetMapel->setCellValue('A1', 'KODE MAPEL');
        $sheetMapel->setCellValue('B1', 'NAMA MAPEL');
        $sheetMapel->setCellValue('A2', 'MAT');
        $sheetMapel->setCellValue('B2', 'Matematika');

        $sheetGuru = $spreadsheet->createSheet();
        $sheetGuru->setTitle('KODE_GURU');
        $sheetGuru->setCellValue('A1', 'KODE GURU');
        $sheetGuru->setCellValue('B1', 'NAMA GURU');
        $sheetGuru->setCellValue('A2', 'TGA');
        $sheetGuru->setCellValue('B2', 'Ahmad Guru');

        $sheetJadwal = $spreadsheet->createSheet();
        $sheetJadwal->setTitle('INPUT_JADWAL');
        $sheetJadwal->setCellValue('A1', 'JADWAL PELAJARAN');
        $sheetJadwal->setCellValue('A2', 'HARI');
        $sheetJadwal->setCellValue('B2', 'JAM KE');
        $sheetJadwal->setCellValue('C2', 'WAKTU');
        $sheetJadwal->setCellValue('D2', '7A');
        $sheetJadwal->setCellValue('E2', '7B');

        // Baris 3: Senin Jam 1 diisi MAT TGA untuk KEDUA KELAS (BENTROK!)
        $sheetJadwal->setCellValue('A3', 'Senin');
        $sheetJadwal->setCellValue('B3', 1);
        $sheetJadwal->setCellValue('C3', '07:30 - 08:15');
        $sheetJadwal->setCellValue('D3', 'MAT TGA');
        $sheetJadwal->setCellValue('E3', 'MAT TGA');

        $filePath = $this->createTemporaryExcelFile($spreadsheet);
        $file = new UploadedFile($filePath, 'jadwal_bentrok.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);

        // A. Coba kirim request tahap Preview (dry_run=1)
        $previewResponse = $this->actingAs($this->admin)->post(route('admin.jadwal.import'), [
            'file' => $file,
            'semester_id' => $this->semester->id,
            'dry_run' => '1',
        ]);

        $previewResponse->assertStatus(200);
        $previewData = $previewResponse->json();

        // Verifikasi failure_count terdeteksi dan slot pertama ditandai bentrok
        $this->assertGreaterThan(0, $previewData['failure_count']);
        $this->assertNotEmpty($previewData['errors']);
        $this->assertTrue(collect($previewData['changes'])->contains('is_conflict', true));

        // B. Coba kirim request Eksekusi (dry_run=0) dengan file yang ada bentrok
        $executeFile = new UploadedFile($filePath, 'jadwal_bentrok.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);
        $executeResponse = $this->actingAs($this->admin)->post(route('admin.jadwal.import'), [
            'file' => $executeFile,
            'semester_id' => $this->semester->id,
            'dry_run' => '0',
        ]);

        // Verifikasi backend menolak eksekusi dengan HTTP 422
        $executeResponse->assertStatus(422);
        $executeResponse->assertJsonFragment([
            'message' => "Import dibatalkan! Ditemukan {$previewData['failure_count']} kesalahan pada file Excel. Harap perbaiki seluruh kesalahan sebelum mengimpor.",
        ]);

        @unlink($filePath);
    }
}
