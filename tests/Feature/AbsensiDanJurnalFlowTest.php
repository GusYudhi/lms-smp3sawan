<?php

namespace Tests\Feature;

use App\Http\Controllers\Guru\AbsensiController;
use App\Models\Attendance;
use App\Models\GuruProfile;
use App\Models\JadwalPelajaran;
use App\Models\JamPelajaran;
use App\Models\JurnalAttendance;
use App\Models\JurnalMengajar;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Semester;
use App\Models\StudentProfile;
use App\Models\TahunPelajaran;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AbsensiDanJurnalFlowTest extends TestCase
{
    use DatabaseTransactions;

    protected $guru;
    protected $siswa;
    protected $studentProfile;
    protected $semester;
    protected $kelas;
    protected $mapel;
    protected $jam1;
    protected $jam2;

    protected function setUp(): void
    {
        parent::setUp();

        $activeSemester = Semester::where('is_active', true)->first();
        if ($activeSemester) {
            $this->semester = $activeSemester;
        } else {
            $tp = TahunPelajaran::firstOrCreate(
                ['nama' => '2024/2025 Test TP Absensi'],
                [
                    'tahun_mulai' => 2024,
                    'tahun_selesai' => 2025,
                    'tanggal_mulai' => now()->toDateString(),
                    'tanggal_selesai' => now()->addYear()->toDateString(),
                    'is_active' => true,
                ]
            );

            $this->semester = Semester::firstOrCreate(
                ['semester_ke' => 1, 'tahun_pelajaran_id' => $tp->id],
                [
                    'nama' => 'Ganjil Absensi Test',
                    'tanggal_mulai' => now()->toDateString(),
                    'tanggal_selesai' => now()->addMonths(6)->toDateString(),
                    'is_active' => true,
                ]
            );
        }

        $this->guru = User::firstOrCreate(
            ['email' => 'guru.absensiflow@test.com'],
            [
                'name' => 'Guru Flow Test',
                'password' => bcrypt('password'),
                'role' => 'guru',
            ]
        );

        $this->kelas = Kelas::firstOrCreate(
            ['tingkat' => '7', 'nama_kelas' => 'Z'],
            [
                'status' => 'aktif',
            ]
        );

        $this->mapel = MataPelajaran::firstOrCreate(
            ['kode_mapel' => 'TEST_MAPEL'],
            [
                'nama_mapel' => 'Mapel Flow Test',
                'is_active' => true,
            ]
        );

        GuruProfile::firstOrCreate(
            ['user_id' => $this->guru->id],
            [
                'nama' => 'Guru Flow Test',
                'nip' => '9998887771',
                'mata_pelajaran_id' => $this->mapel->id,
            ]
        );

        $this->siswa = User::firstOrCreate(
            ['email' => 'siswa.absensiflow@test.com'],
            [
                'name' => 'Siswa Flow Test',
                'password' => bcrypt('password'),
                'role' => 'siswa',
            ]
        );

        $this->studentProfile = StudentProfile::firstOrCreate(
            ['user_id' => $this->siswa->id],
            [
                'nis' => '888001',
                'nisn' => '1234567890',
                'kelas_id' => $this->kelas->id,
                'is_active' => true,
            ]
        );

        $this->jam1 = JamPelajaran::firstOrCreate(
            ['semester_id' => $this->semester->id, 'jam_ke' => 1],
            [
                'jam_mulai' => '07:00:00',
                'jam_selesai' => '07:40:00',
            ]
        );

        $this->jam2 = JamPelajaran::firstOrCreate(
            ['semester_id' => $this->semester->id, 'jam_ke' => 2],
            [
                'jam_mulai' => '07:40:00',
                'jam_selesai' => '08:20:00',
            ]
        );
    }

    public function test_dynamic_morning_cutoff_time_uses_earliest_scheduled_lesson()
    {
        $controller = new AbsensiController();
        $targetDate = Carbon::create(2026, 11, 9); // Senin
        $dayName = $targetDate->locale('id')->dayName;

        // Buat jadwal jam ke-1 di hari Senin
        JadwalPelajaran::firstOrCreate(
            [
                'semester_id' => $this->semester->id,
                'kelas_id' => $this->kelas->id,
                'mata_pelajaran_id' => $this->mapel->id,
                'guru_id' => $this->guru->id,
                'hari' => $dayName,
                'jam_ke' => 1,
            ]
        );

        $cutoff = $controller->getTodayCutoffTime($targetDate);
        $this->assertEquals('07:00:00', $cutoff->format('H:i:s'));

        // Jika ada jam ke-0 yang mulai jam 06:30
        JamPelajaran::firstOrCreate(
            ['semester_id' => $this->semester->id, 'jam_ke' => 0],
            [
                'jam_mulai' => '06:30:00',
                'jam_selesai' => '07:00:00',
            ]
        );
        JadwalPelajaran::firstOrCreate(
            [
                'semester_id' => $this->semester->id,
                'kelas_id' => $this->kelas->id,
                'mata_pelajaran_id' => $this->mapel->id,
                'guru_id' => $this->guru->id,
                'hari' => $dayName,
                'jam_ke' => 0,
            ]
        );

        $cutoffJam0 = $controller->getTodayCutoffTime($targetDate);
        $this->assertEquals('06:30:00', $cutoffJam0->format('H:i:s'));
    }

    public function test_scan_attendance_on_time_and_late_status()
    {
        $targetDate = Carbon::today();
        $dayName = $targetDate->locale('id')->dayName;

        JadwalPelajaran::firstOrCreate(
            [
                'semester_id' => $this->semester->id,
                'kelas_id' => $this->kelas->id,
                'mata_pelajaran_id' => $this->mapel->id,
                'guru_id' => $this->guru->id,
                'hari' => $dayName,
                'jam_ke' => 1,
            ]
        );

        // Simulasi tepat waktu: 06:50:00 (sebelum 07:00:00)
        Carbon::setTestNow($targetDate->copy()->setTime(6, 50, 0));

        $responseOnTime = $this->actingAs($this->guru)->postJson(route('guru.absensi.process'), [
            'nisn' => $this->studentProfile->nisn,
        ]);

        $responseOnTime->assertStatus(200);
        $responseOnTime->assertJsonPath('data.status', 'hadir');
        $this->assertDatabaseHas('attendance', [
            'user_id' => $this->siswa->id,
            'date' => $targetDate->toDateString(),
            'status' => 'hadir',
        ]);

        // Hapus untuk tes terlambat
        Attendance::where('user_id', $this->siswa->id)->whereDate('date', $targetDate)->delete();

        // Simulasi terlambat: 07:15:00 (setelah 07:00:00)
        Carbon::setTestNow($targetDate->copy()->setTime(7, 15, 0));

        $responseLate = $this->actingAs($this->guru)->postJson(route('guru.absensi.process'), [
            'nisn' => $this->studentProfile->nisn,
        ]);

        $responseLate->assertStatus(200);
        $responseLate->assertJsonPath('data.status', 'terlambat');
        $this->assertDatabaseHas('attendance', [
            'user_id' => $this->siswa->id,
            'date' => $targetDate->toDateString(),
            'status' => 'terlambat',
        ]);

        Carbon::setTestNow(); // Reset time
    }

    public function test_jurnal_wizard_defaults_unscanned_student_to_alpa()
    {
        $targetDate = Carbon::today()->toDateString();
        // Pastikan tidak ada record di attendance harian
        Attendance::where('user_id', $this->siswa->id)->whereDate('date', $targetDate)->delete();

        $response = $this->actingAs($this->guru)->get(route('guru.jurnal-mengajar.wizard', [
            'tanggal' => $targetDate,
            'kelas_id' => $this->kelas->id,
            'mata_pelajaran_id' => $this->mapel->id,
            'jam_ke_mulai' => 1,
            'jam_ke_selesai' => 2,
        ]));

        $response->assertStatus(200);
        $response->assertViewHas('defaultAbsensiSiswa', function ($defaultAbsensi) {
            return isset($defaultAbsensi[$this->studentProfile->id])
                && $defaultAbsensi[$this->studentProfile->id]['default_status'] === 'alpa'
                && $defaultAbsensi[$this->studentProfile->id]['status_acuan'] === 'belum_absen';
        });
    }

    public function test_jurnal_store_with_hadir_auto_syncs_terlambat_to_daily_attendance()
    {
        $targetDate = Carbon::today()->toDateString();
        Attendance::where('user_id', $this->siswa->id)->whereDate('date', $targetDate)->delete();

        // Guru menyimpan jurnal dengan absensi 'hadir' untuk siswa yang belum scan
        $response = $this->actingAs($this->guru)->post(route('guru.jurnal-mengajar.store'), [
            'tanggal' => $targetDate,
            'kelas_id' => $this->kelas->id,
            'jam_ke_mulai' => 1,
            'jam_ke_selesai' => 2,
            'mata_pelajaran_id' => $this->mapel->id,
            'materi_pembelajaran' => 'Materi Uji Flow Presensi',
            'foto_bukti' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
            'absensi' => [
                $this->studentProfile->id => 'hadir',
            ],
        ]);

        $response->assertRedirect();

        // Jurnal attendance harus 'hadir'
        $this->assertDatabaseHas('jurnal_attendances', [
            'student_profile_id' => $this->studentProfile->id,
            'status' => 'hadir',
        ]);

        // Daily attendance otomatis dibuat dengan status 'terlambat'
        $this->assertDatabaseHas('attendance', [
            'user_id' => $this->siswa->id,
            'date' => $targetDate,
            'status' => 'terlambat',
        ]);
    }

    public function test_jurnal_store_with_sakit_auto_syncs_to_daily_attendance()
    {
        $targetDate = Carbon::today()->toDateString();
        Attendance::where('user_id', $this->siswa->id)->whereDate('date', $targetDate)->delete();

        $response = $this->actingAs($this->guru)->post(route('guru.jurnal-mengajar.store'), [
            'tanggal' => $targetDate,
            'kelas_id' => $this->kelas->id,
            'jam_ke_mulai' => 1,
            'jam_ke_selesai' => 2,
            'mata_pelajaran_id' => $this->mapel->id,
            'materi_pembelajaran' => 'Materi Uji Sakit',
            'foto_bukti' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
            'absensi' => [
                $this->studentProfile->id => 'sakit',
            ],
        ]);

        $response->assertRedirect();

        // Daily attendance tercatat sebagai 'sakit'
        $this->assertDatabaseHas('attendance', [
            'user_id' => $this->siswa->id,
            'date' => $targetDate,
            'status' => 'sakit',
        ]);
    }

    public function test_jurnal_store_does_not_overwrite_existing_morning_hadir_with_sakit()
    {
        $targetDate = Carbon::today()->toDateString();

        // Siswa sudah scan 'hadir' pagi di gerbang
        Attendance::updateOrCreate(
            ['user_id' => $this->siswa->id, 'date' => $targetDate],
            [
                'status' => 'hadir',
                'time' => now()->setTime(6, 45, 0),
                'created_by' => $this->guru->id,
            ]
        );

        // Di jam ke-3 siswa merasa sakit dan guru menandai 'sakit' di jurnal
        $response = $this->actingAs($this->guru)->post(route('guru.jurnal-mengajar.store'), [
            'tanggal' => $targetDate,
            'kelas_id' => $this->kelas->id,
            'jam_ke_mulai' => 3,
            'jam_ke_selesai' => 4,
            'mata_pelajaran_id' => $this->mapel->id,
            'materi_pembelajaran' => 'Materi Jam 3-4',
            'foto_bukti' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
            'absensi' => [
                $this->studentProfile->id => 'sakit',
            ],
        ]);

        $response->assertRedirect();

        // Daily attendance tetap 'hadir' (tidak ditimpa menjadi 'sakit')
        $this->assertDatabaseHas('attendance', [
            'user_id' => $this->siswa->id,
            'date' => $targetDate,
            'status' => 'hadir',
        ]);
    }

    public function test_consecutive_jurnal_converts_previous_terlambat_to_hadir()
    {
        $targetDate = Carbon::today()->toDateString();

        // Jurnal jam 1-2 siswa berstatus 'terlambat'
        $jurnal1 = JurnalMengajar::create([
            'guru_id' => $this->guru->id,
            'kelas_id' => $this->kelas->id,
            'mata_pelajaran_id' => $this->mapel->id,
            'hari' => Carbon::parse($targetDate)->locale('id')->dayName,
            'tanggal' => $targetDate,
            'jam_ke_mulai' => 1,
            'jam_ke_selesai' => 2,
            'materi_pembelajaran' => 'Jam 1-2 Telat',
            'foto_bukti' => 'dummy.jpg',
        ]);

        JurnalAttendance::create([
            'jurnal_mengajar_id' => $jurnal1->id,
            'student_profile_id' => $this->studentProfile->id,
            'status' => 'terlambat',
            'status_awal' => 'terlambat',
        ]);

        // Buka wizard jurnal jam 3-4 di kelas yang sama
        $response = $this->actingAs($this->guru)->get(route('guru.jurnal-mengajar.wizard', [
            'tanggal' => $targetDate,
            'kelas_id' => $this->kelas->id,
            'mata_pelajaran_id' => $this->mapel->id,
            'jam_ke_mulai' => 3,
            'jam_ke_selesai' => 4,
        ]));

        $response->assertStatus(200);
        $response->assertViewHas('defaultAbsensiSiswa', function ($defaultAbsensi) {
            return isset($defaultAbsensi[$this->studentProfile->id])
                && $defaultAbsensi[$this->studentProfile->id]['default_status'] === 'hadir'
                && $defaultAbsensi[$this->studentProfile->id]['status_acuan'] === 'terlambat';
        });
    }
}
