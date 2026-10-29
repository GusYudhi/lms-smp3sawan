<?php

namespace Tests\Feature;

use App\Imports\JadwalImport;
use App\Models\GuruProfile;
use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\Semester;
use App\Models\TahunPelajaran;
use App\Models\User;
use Database\Seeders\GuruSeeder;
use Database\Seeders\JadwalPelajaranSeeder;
use Database\Seeders\KelasSeeder;
use Database\Seeders\MataPelajaranSeeder;
use Database\Seeders\TahunPelajaranSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class JadwalConflictTest extends TestCase
{
    use DatabaseTransactions;

    public function test_seeder_produces_completely_conflict_free_schedules()
    {
        // Jalankan seeder berurutan
        $this->seed(MataPelajaranSeeder::class);
        $this->seed(TahunPelajaranSeeder::class);
        $this->seed(KelasSeeder::class);
        $this->seed(GuruSeeder::class);
        $this->seed(JadwalPelajaranSeeder::class);

        $activeSemester = Semester::where('is_active', true)->first();
        $this->assertNotNull($activeSemester);

        $allSchedules = JadwalPelajaran::where('semester_id', $activeSemester->id)
            ->with(['guru.guruProfile', 'mataPelajaran', 'kelas'])
            ->get();

        $this->assertGreaterThan(0, $allSchedules->count());

        $teacherSlots = [];
        $classSlots = [];

        foreach ($allSchedules as $schedule) {
            $hari = $schedule->hari;
            $jam = $schedule->jam_ke;
            $guruId = $schedule->guru_id;
            $kelasId = $schedule->kelas_id;

            // 1. Pastikan guru tidak mengajar di 2 kelas berbeda pada hari dan jam yang sama
            $teacherSlotKey = "{$hari}_{$jam}_{$guruId}";
            $this->assertArrayNotHasKey(
                $teacherSlotKey,
                $teacherSlots,
                "Bentrok Terdeteksi! Guru {$schedule->guru->name} mengajar ganda pada $hari jam ke-$jam"
            );
            $teacherSlots[$teacherSlotKey] = $schedule->id;

            // 2. Pastikan kelas tidak memiliki 2 mapel berbeda pada hari dan jam yang sama
            $classSlotKey = "{$hari}_{$jam}_{$kelasId}";
            $this->assertArrayNotHasKey(
                $classSlotKey,
                $classSlots,
                "Bentrok Terdeteksi! Kelas {$schedule->kelas->tingkat}{$schedule->kelas->nama_kelas} memiliki slot ganda pada $hari jam ke-$jam"
            );
            $classSlots[$classSlotKey] = $schedule->id;

            // 3. Pastikan mapel yang diajar sesuai dengan profil mapel guru
            $guruMapelId = $schedule->guru->guruProfile?->mata_pelajaran_id;
            $this->assertEquals(
                $guruMapelId,
                $schedule->mata_pelajaran_id,
                "Guru {$schedule->guru->name} mengajar mapel ID {$schedule->mata_pelajaran_id} padahal mapel profilnya {$guruMapelId}"
            );
        }

        // Verifikasi khusus Dr. Ahmad Hidayat pada hari Kamis
        $hidayat = User::where('email', 'ahmad.hidayat@guru.id')->first();
        $this->assertNotNull($hidayat);

        $kamisHidayat = $allSchedules->where('guru_id', $hidayat->id)->where('hari', 'Kamis');
        $jamKamis = $kamisHidayat->pluck('jam_ke')->toArray();
        $uniqueJamKamis = array_unique($jamKamis);

        // Tidak boleh ada jam yang duplikat pada hari Kamis
        $this->assertCount(count($jamKamis), $uniqueJamKamis);
    }

    public function test_jadwal_import_detects_teacher_clash_in_same_timeslot_across_classes()
    {
        $tp = TahunPelajaran::firstOrCreate(
            ['nama' => '2024/2025 Test TP Jadwal'],
            [
                'tahun_mulai' => 2024,
                'tahun_selesai' => 2025,
                'tanggal_mulai' => now()->toDateString(),
                'tanggal_selesai' => now()->addYear()->toDateString(),
                'is_active' => true,
            ]
        );

        $semester = Semester::firstOrCreate(
            ['semester_ke' => 1, 'tahun_pelajaran_id' => $tp->id],
            ['nama' => 'Ganjil Jadwal Test', 'tanggal_mulai' => now()->toDateString(), 'tanggal_selesai' => now()->addMonths(6)->toDateString(), 'is_active' => true]
        );

        $kelas7A = Kelas::firstOrCreate(['tingkat' => 7, 'nama_kelas' => 'A', 'tahun_pelajaran_id' => $tp->id]);
        $kelas7B = Kelas::firstOrCreate(['tingkat' => 7, 'nama_kelas' => 'B', 'tahun_pelajaran_id' => $tp->id]);

        $teacher = User::firstOrCreate(
            ['email' => 'guru_clash_test@guru.id'],
            ['name' => 'Guru Clash Test', 'password' => bcrypt('password'), 'role' => 'guru']
        );

        GuruProfile::updateOrCreate(
            ['user_id' => $teacher->id],
            ['nip' => '199901012022011009', 'kode_guru' => 'GCT', 'nama' => 'Guru Clash Test', 'is_active' => true]
        );

        $spreadsheet = new Spreadsheet;

        // 1. Sheet KODE_MAPEL
        $sheetMapel = $spreadsheet->getActiveSheet();
        $sheetMapel->setTitle('KODE_MAPEL');
        $sheetMapel->setCellValue('A1', 'KODE MAPEL');
        $sheetMapel->setCellValue('B1', 'NAMA MAPEL');
        $sheetMapel->setCellValue('A2', 'MATCLASH');
        $sheetMapel->setCellValue('B2', 'Matematika Clash');

        // 2. Sheet KODE_GURU
        $sheetGuru = $spreadsheet->createSheet();
        $sheetGuru->setTitle('KODE_GURU');
        $sheetGuru->setCellValue('A1', 'KODE GURU');
        $sheetGuru->setCellValue('B1', 'NAMA GURU');
        $sheetGuru->setCellValue('A2', 'GCT');
        $sheetGuru->setCellValue('B2', 'Guru Clash Test');

        // 3. Sheet INPUT_JADWAL: Memberi guru GCT pada jam yang sama di 7A dan 7B
        $sheetJadwal = $spreadsheet->createSheet();
        $sheetJadwal->setTitle('INPUT_JADWAL');
        $sheetJadwal->setCellValue('A1', 'JADWAL PELAJARAN');
        $sheetJadwal->setCellValue('A2', 'HARI');
        $sheetJadwal->setCellValue('B2', 'JAM KE');
        $sheetJadwal->setCellValue('C2', 'WAKTU');
        $sheetJadwal->setCellValue('D2', '7A');
        $sheetJadwal->setCellValue('E2', '7B');

        $sheetJadwal->setCellValue('A3', 'Senin');
        $sheetJadwal->setCellValue('B3', 1);
        $sheetJadwal->setCellValue('C3', '07:00 - 07:40');
        $sheetJadwal->setCellValue('D3', 'MATCLASH GCT');
        $sheetJadwal->setCellValue('E3', 'MATCLASH GCT'); // Konflik di baris yang sama!

        $tempPath = tempnam(sys_get_temp_dir(), 'xlsx_clash_').'.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempPath);

        try {
            $import = new JadwalImport($semester->id);
            Excel::import($import, $tempPath);

            $this->assertEquals(1, $import->successCount); // Slot pertama sukses
            $this->assertEquals(1, $import->failureCount); // Slot kedua bentrok & ditolak!
            $this->assertNotEmpty($import->errors);
            $this->assertStringContainsString('bentrok di file Excel', $import->errors[0]);
        } finally {
            @unlink($tempPath);
        }
    }
}
