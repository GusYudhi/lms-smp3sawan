<?php

namespace Tests\Feature;

use App\Models\FixedSchedule;
use App\Models\JadwalPelajaran;
use App\Models\JamPelajaran;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Semester;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SiswaJadwalPelajaranTest extends TestCase
{
    use DatabaseTransactions;

    private User $studentUser;
    private StudentProfile $studentProfile;
    private Kelas $kelas;
    private Semester $semester;

    protected function setUp(): void
    {
        parent::setUp();

        $this->semester = Semester::getActive();
        if (!$this->semester) {
            $this->markTestSkipped('Tidak ada semester aktif untuk pengujian.');
        }

        $this->kelas = Kelas::firstOrCreate(['tingkat' => '8', 'nama_kelas' => 'C']);

        $this->studentUser = User::factory()->create([
            'role' => 'siswa',
            'name' => 'Siswa Test Jadwal',
            'password' => 'password123',
        ]);

        $this->studentProfile = StudentProfile::create([
            'user_id' => $this->studentUser->id,
            'kelas_id' => $this->kelas->id,
            'nis' => '98765432',
            'status' => 'AKTIF',
            'is_active' => true,
        ]);
    }

    public function test_student_can_view_schedule_with_jam_0_and_fixed_schedules(): void
    {
        // 1. Create Jam Ke-0 and Jam Ke-1 in JamPelajaran
        JamPelajaran::create([
            'jam_ke' => 0,
            'jam_mulai' => '06:45:00',
            'jam_selesai' => '07:25:00',
            'semester_id' => $this->semester->id,
        ]);

        // 2. Create Fixed Schedule on Jam 0 (Upacara Bendera) on Senin
        FixedSchedule::create([
            'hari' => 'Senin',
            'jam_ke' => 0,
            'keterangan' => 'Upacara Bendera',
            'tipe' => FixedSchedule::TIPE_UMUM,
            'semester_id' => $this->semester->id,
        ]);

        // 3. Create Fixed Schedule on Jam 0 (Kokurikuler) on Jumat
        FixedSchedule::create([
            'hari' => 'Jumat',
            'jam_ke' => 0,
            'keterangan' => 'Senam & Pembiasaan',
            'tipe' => FixedSchedule::TIPE_KOKURIKULER,
            'semester_id' => $this->semester->id,
        ]);

        // 4. Create regular lesson on Jam 1 on Senin
        $mapel = MataPelajaran::firstOrCreate(
            ['kode_mapel' => 'MAT-TEST'],
            ['nama_mapel' => 'Matematika Test', 'is_active' => true]
        );

        $guru = User::factory()->create([
            'role' => 'guru',
            'name' => 'Guru Matematika',
            'password' => 'password123',
        ]);

        JadwalPelajaran::create([
            'kelas_id' => $this->kelas->id,
            'mata_pelajaran_id' => $mapel->id,
            'guru_id' => $guru->id,
            'hari' => 'Senin',
            'jam_ke' => 1,
            'semester_id' => $this->semester->id,
        ]);

        $response = $this->actingAs($this->studentUser)
            ->get(route('siswa.jadwal-pelajaran.index'));

        $response->assertStatus(200);
        $response->assertViewHas('slotNumbers');
        $slotNumbers = $response->viewData('slotNumbers');

        // Verify Jam Ke-0 is in slot numbers
        $this->assertTrue($slotNumbers->contains(0));
        $this->assertTrue($slotNumbers->contains(1));

        // Verify content contains Upacara, Senam, and Matematika
        $response->assertSee('Upacara Bendera');
        $response->assertSee('Senam & Pembiasaan');
        $response->assertSee('Matematika Test');
        $response->assertSee('Jam 0');
    }

    public function test_student_schedule_handles_consecutive_fixed_schedules(): void
    {
        // Jam 4 & 5 Istirahat
        FixedSchedule::create([
            'hari' => 'Senin',
            'jam_ke' => 4,
            'keterangan' => 'Istirahat Siang',
            'tipe' => FixedSchedule::TIPE_UMUM,
            'semester_id' => $this->semester->id,
        ]);

        FixedSchedule::create([
            'hari' => 'Senin',
            'jam_ke' => 5,
            'keterangan' => 'Istirahat Siang',
            'tipe' => FixedSchedule::TIPE_UMUM,
            'semester_id' => $this->semester->id,
        ]);

        $response = $this->actingAs($this->studentUser)
            ->get(route('siswa.jadwal-pelajaran.index'));

        $response->assertStatus(200);
        $response->assertSee('Istirahat Siang');
    }
}
