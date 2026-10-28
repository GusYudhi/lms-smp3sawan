<?php

namespace Tests\Feature;

use App\Exports\JadwalExport;
use App\Exports\JadwalTemplateExport;
use App\Imports\JadwalImport;
use App\Imports\StudentsImport;
use App\Imports\TeachersImport;
use App\Models\GuruProfile;
use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Semester;
use App\Models\StudentProfile;
use App\Models\TahunPelajaran;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class ExcelImportTest extends TestCase
{
    use DatabaseTransactions;

    protected $admin;
    protected $semester;
    protected $kelas;
    protected $teacher;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::firstOrCreate(
            ['email' => 'admin_test_import@test.com'],
            ['name' => 'Admin Import Tester', 'password' => bcrypt('password'), 'role' => 'admin']
        );

        $tp = TahunPelajaran::firstOrCreate(
            ['nama' => '2024/2025 Test TP'],
            [
                'tahun_mulai' => 2024,
                'tahun_selesai' => 2025,
                'tanggal_mulai' => now()->toDateString(),
                'tanggal_selesai' => now()->addYear()->toDateString(),
                'is_active' => true
            ]
        );

        $this->semester = Semester::firstOrCreate(
            ['semester_ke' => 1, 'tahun_pelajaran_id' => $tp->id],
            ['nama' => 'Ganjil Test', 'tanggal_mulai' => now()->toDateString(), 'tanggal_selesai' => now()->addMonths(6)->toDateString(), 'is_active' => true]
        );

        $this->kelas = Kelas::firstOrCreate(
            ['tingkat' => 7, 'nama_kelas' => 'Z', 'tahun_pelajaran_id' => $tp->id]
        );

        $this->teacher = User::firstOrCreate(
            ['email' => 'guru_import_test@guru.id'],
            ['name' => 'Guru Import Subjek', 'password' => bcrypt('password'), 'role' => 'guru']
        );

        GuruProfile::updateOrCreate(
            ['user_id' => $this->teacher->id],
            [
                'nip' => '198901012020011001',
                'kode_guru' => 'GIS',
                'nama' => 'Guru Import Subjek',
                'is_active' => true,
                'status_kepegawaian' => 'PNS',
            ]
        );
    }

    private function createTemporaryExcelFile(Spreadsheet $spreadsheet): string
    {
        $tempPath = tempnam(sys_get_temp_dir(), 'xlsx_test_') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempPath);
        return $tempPath;
    }

    public function test_jadwal_template_export_and_jadwal_export_can_both_run_without_class_conflict()
    {
        $template = new JadwalTemplateExport();
        $this->assertCount(4, $template->sheets());

        $jadwalExport = new JadwalExport($this->semester->id);
        $this->assertCount(4, $jadwalExport->sheets());
    }

    public function test_jadwal_import_processes_valid_file_successfully()
    {
        $spreadsheet = new Spreadsheet();

        // 1. KODE_MAPEL
        $sheetMapel = $spreadsheet->getActiveSheet();
        $sheetMapel->setTitle('KODE_MAPEL');
        $sheetMapel->setCellValue('A1', 'KODE MAPEL');
        $sheetMapel->setCellValue('B1', 'NAMA MAPEL');
        $sheetMapel->setCellValue('A2', 'MTKTEST');
        $sheetMapel->setCellValue('B2', 'Matematika Test');

        // 2. KODE_GURU
        $sheetGuru = $spreadsheet->createSheet();
        $sheetGuru->setTitle('KODE_GURU');
        $sheetGuru->setCellValue('A1', 'KODE GURU');
        $sheetGuru->setCellValue('B1', 'NAMA GURU');
        $sheetGuru->setCellValue('A2', 'GIS');
        $sheetGuru->setCellValue('B2', 'Guru Import Subjek');

        // 3. INPUT_JADWAL
        $sheetJadwal = $spreadsheet->createSheet();
        $sheetJadwal->setTitle('INPUT_JADWAL');
        $sheetJadwal->setCellValue('A1', 'JADWAL PELAJARAN');
        $sheetJadwal->setCellValue('A2', 'HARI');
        $sheetJadwal->setCellValue('B2', 'JAM KE');
        $sheetJadwal->setCellValue('C2', 'WAKTU');
        $sheetJadwal->setCellValue('D2', '7Z');

        $sheetJadwal->setCellValue('A3', 'Senin');
        $sheetJadwal->setCellValue('B3', 1);
        $sheetJadwal->setCellValue('C3', '07:30 - 08:10');
        $sheetJadwal->setCellValue('D3', 'MTKTEST GIS');

        $filePath = $this->createTemporaryExcelFile($spreadsheet);

        try {
            $import = new JadwalImport($this->semester->id);
            Excel::import($import, $filePath);

            $this->assertEquals(1, $import->successCount);
            $this->assertEquals(0, $import->failureCount);
            $this->assertEmpty($import->errors);

            $mapel = MataPelajaran::where('kode_mapel', 'MTKTEST')->first();
            $this->assertNotNull($mapel);
            $this->assertEquals('Matematika Test', $mapel->nama_mapel);

            $exists = JadwalPelajaran::where('semester_id', $this->semester->id)
                ->where('kelas_id', $this->kelas->id)
                ->where('hari', 'Senin')
                ->where('jam_ke', 1)
                ->where('mata_pelajaran_id', $mapel->id)
                ->where('guru_id', $this->teacher->id)
                ->exists();

            $this->assertTrue($exists);
        } finally {
            @unlink($filePath);
        }
    }

    public function test_jadwal_import_via_http_controller_endpoint()
    {
        $spreadsheet = new Spreadsheet();

        $sheetMapel = $spreadsheet->getActiveSheet();
        $sheetMapel->setTitle('KODE_MAPEL');
        $sheetMapel->setCellValue('A1', 'KODE MAPEL');
        $sheetMapel->setCellValue('B1', 'NAMA MAPEL');
        $sheetMapel->setCellValue('A2', 'IPATEST');
        $sheetMapel->setCellValue('B2', 'IPA Test');

        $sheetGuru = $spreadsheet->createSheet();
        $sheetGuru->setTitle('KODE_GURU');
        $sheetGuru->setCellValue('A1', 'KODE GURU');
        $sheetGuru->setCellValue('B1', 'NAMA GURU');
        $sheetGuru->setCellValue('A2', 'GIS');
        $sheetGuru->setCellValue('B2', 'Guru Import Subjek');

        $sheetJadwal = $spreadsheet->createSheet();
        $sheetJadwal->setTitle('INPUT_JADWAL');
        $sheetJadwal->setCellValue('A1', 'JADWAL PELAJARAN');
        $sheetJadwal->setCellValue('A2', 'HARI');
        $sheetJadwal->setCellValue('B2', 'JAM KE');
        $sheetJadwal->setCellValue('C2', 'WAKTU');
        $sheetJadwal->setCellValue('D2', '7Z');

        $sheetJadwal->setCellValue('A3', 'Selasa');
        $sheetJadwal->setCellValue('B3', 2);
        $sheetJadwal->setCellValue('C3', '08:10 - 08:50');
        $sheetJadwal->setCellValue('D3', 'IPATEST GIS');

        $filePath = $this->createTemporaryExcelFile($spreadsheet);

        try {
            $uploadedFile = new UploadedFile(
                $filePath,
                'jadwal_test.xlsx',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                null,
                true
            );

            $response = $this->actingAs($this->admin)->postJson(route('admin.jadwal.import'), [
                'semester_id' => $this->semester->id,
                'file' => $uploadedFile,
            ]);

            $response->assertStatus(200);
            $response->assertJsonStructure(['message']);
        } finally {
            @unlink($filePath);
        }
    }

    public function test_jadwal_import_handles_missing_sheet_gracefully()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('SheetUnknown');
        $sheet->setCellValue('A1', 'Dummy');

        $filePath = $this->createTemporaryExcelFile($spreadsheet);

        try {
            $import = new JadwalImport($this->semester->id);
            Excel::import($import, $filePath);

            $this->assertNotEmpty($import->errors);
            $this->assertStringContainsString('INPUT_JADWAL', $import->errors[0]);
        } finally {
            @unlink($filePath);
        }
    }

    public function test_teachers_import_with_numeric_nip_and_phone()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'nama_lengkap');
        $sheet->setCellValue('B1', 'nip_nik');
        $sheet->setCellValue('C1', 'jenis_kelamin');
        $sheet->setCellValue('D1', 'tempat_lahir');
        $sheet->setCellValue('E1', 'tanggal_lahir');
        $sheet->setCellValue('F1', 'telepon');
        $sheet->setCellValue('G1', 'status_kepegawaian');
        $sheet->setCellValue('H1', 'golongan');
        $sheet->setCellValue('I1', 'jabatan_di_sekolah');
        $sheet->setCellValue('J1', 'mata_pelajaran');
        $sheet->setCellValue('K1', 'wali_kelas');

        $sheet->setCellValue('A2', 'Guru Baru Import Test');
        $sheet->setCellValue('B2', '198711112015011009');
        $sheet->setCellValue('C2', 'Laki-laki');
        $sheet->setCellValue('D2', 'Buleleng');
        $sheet->setCellValue('E2', '1987-11-11');
        $sheet->setCellValue('F2', '081234567890');
        $sheet->setCellValue('G2', 'PNS');
        $sheet->setCellValue('H2', 'III/b');
        $sheet->setCellValue('I2', 'Guru Mata Pelajaran');
        $sheet->setCellValue('J2', '');
        $sheet->setCellValue('K2', '');

        $filePath = $this->createTemporaryExcelFile($spreadsheet);

        try {
            $import = new TeachersImport();
            Excel::import($import, $filePath);

            $this->assertEquals(1, $import->successCount);
            $this->assertEquals(0, $import->failureCount);
            $this->assertEmpty($import->errors);

            $profile = GuruProfile::where('nip', '198711112015011009')->first();
            $this->assertNotNull($profile);
            $this->assertNotNull($profile->user);
            $this->assertEquals('Guru Baru Import Test', $profile->user->name);
        } finally {
            @unlink($filePath);
        }
    }

    public function test_students_import_processes_valid_data()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'nama');
        $sheet->setCellValue('B1', 'nis');
        $sheet->setCellValue('C1', 'nisn');
        $sheet->setCellValue('D1', 'tempat_lahir');
        $sheet->setCellValue('E1', 'tanggal_lahir');
        $sheet->setCellValue('F1', 'jenis_kelamin');
        $sheet->setCellValue('G1', 'tingkat');
        $sheet->setCellValue('H1', 'kelas');
        $sheet->setCellValue('I1', 'tahun_angkatan');

        $sheet->setCellValue('A2', 'Siswa Test Import Excel');
        $sheet->setCellValue('B2', '888801');
        $sheet->setCellValue('C2', '00888801');
        $sheet->setCellValue('D2', 'Denpasar');
        $sheet->setCellValue('E2', '2011-01-20');
        $sheet->setCellValue('F2', 'P');
        $sheet->setCellValue('G2', '7');
        $sheet->setCellValue('H2', 'A');
        $sheet->setCellValue('I2', '2024');

        $filePath = $this->createTemporaryExcelFile($spreadsheet);

        try {
            $import = new StudentsImport();
            Excel::import($import, $filePath);

            $this->assertEquals(1, $import->successCount);
            $this->assertEquals(0, $import->failureCount);
            $this->assertEmpty($import->errors);

            $profile = StudentProfile::where('nis', '888801')->first();
            $this->assertNotNull($profile);
            $this->assertEquals('888801', $profile->nis);
            $this->assertEquals('P', $profile->jenis_kelamin);
        } finally {
            @unlink($filePath);
        }
    }
}
