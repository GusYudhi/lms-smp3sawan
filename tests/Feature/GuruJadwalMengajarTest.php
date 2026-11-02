<?php

namespace Tests\Feature;

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
use Tests\TestCase;

class GuruJadwalMengajarTest extends TestCase
{
    use DatabaseTransactions;

    protected $teacher;

    protected $semester;

    protected $kelas;

    protected function setUp(): void
    {
        parent::setUp();

        $tp = TahunPelajaran::firstOrCreate(
            ['nama' => '2025/2026 Test Guru Jadwal'],
            [
                'tahun_mulai' => 2025,
                'tahun_selesai' => 2026,
                'tanggal_mulai' => '2025-07-15',
                'tanggal_selesai' => '2026-06-30',
                'is_active' => true,
            ]
        );

        $this->semester = Semester::where('is_active', true)->first();
        if (! $this->semester) {
            $this->semester = Semester::firstOrCreate(
                ['tahun_pelajaran_id' => $tp->id, 'semester_ke' => 1],
                [
                    'nama' => 'Ganjil',
                    'tanggal_mulai' => '2025-07-15',
                    'tanggal_selesai' => '2025-12-31',
                    'is_active' => true,
                ]
            );
        }

        $this->teacher = User::firstOrCreate(
            ['email' => 'guru_test_jadwal@guru.id'],
            [
                'name' => 'Guru Test Jadwal',
                'password' => bcrypt('password123'),
                'role' => 'guru',
            ]
        );

        GuruProfile::firstOrCreate(
            ['user_id' => $this->teacher->id],
            [
                'nama' => $this->teacher->name,
                'kode_guru' => 'GT1',
                'is_active' => true,
            ]
        );

        $this->kelas = Kelas::firstOrCreate(
            ['tingkat' => '7', 'nama_kelas' => 'Z'],
            [
                'tahun_pelajaran_id' => $tp->id,
            ]
        );
    }

    public function test_teacher_can_access_jadwal_mengajar_page()
    {
        $response = $this->actingAs($this->teacher)->get(route('guru.jadwal-mengajar'));
        $response->assertStatus(200);
        $response->assertSee('Jadwal Mengajar');
    }

    public function test_non_teacher_cannot_access_jadwal_mengajar()
    {
        $student = User::firstOrCreate(
            ['email' => 'siswa_test_jadwal@siswa.id'],
            [
                'name' => 'Siswa Test Jadwal',
                'password' => bcrypt('password123'),
                'role' => 'siswa',
            ]
        );

        $response = $this->actingAs($student)->get(route('guru.jadwal-mengajar'));
        $response->assertStatus(403);
    }

    public function test_get_by_kelas_returns_complete_schedule_slots_with_is_current_teacher_flag()
    {
        $jam1 = JamPelajaran::firstOrCreate(
            ['semester_id' => $this->semester->id, 'jam_ke' => 1],
            [
                'jam_mulai' => '07:30',
                'jam_selesai' => '08:15',
            ]
        );

        $jam2 = JamPelajaran::firstOrCreate(
            ['semester_id' => $this->semester->id, 'jam_ke' => 2],
            [
                'jam_mulai' => '08:15',
                'jam_selesai' => '09:00',
            ]
        );

        $mapel = MataPelajaran::firstOrCreate(
            ['kode_mapel' => 'TST'],
            [
                'nama_mapel' => 'Mapel Test',
                'is_active' => true,
            ]
        );

        $teacher2 = User::firstOrCreate(
            ['email' => 'guru2_test_jadwal@guru.id'],
            [
                'name' => 'Guru Dua Test',
                'password' => bcrypt('password123'),
                'role' => 'guru',
            ]
        );

        // Teacher 1 schedule at Jam 1
        JadwalPelajaran::updateOrCreate(
            [
                'semester_id' => $this->semester->id,
                'kelas_id' => $this->kelas->id,
                'hari' => 'Senin',
                'jam_ke' => 1,
            ],
            [
                'mata_pelajaran_id' => $mapel->id,
                'guru_id' => $this->teacher->id,
            ]
        );

        // Teacher 2 schedule at Jam 2
        JadwalPelajaran::updateOrCreate(
            [
                'semester_id' => $this->semester->id,
                'kelas_id' => $this->kelas->id,
                'hari' => 'Senin',
                'jam_ke' => 2,
            ],
            [
                'mata_pelajaran_id' => $mapel->id,
                'guru_id' => $teacher2->id,
            ]
        );

        // Fixed schedule on Selasa jam 1
        FixedSchedule::updateOrCreate(
            [
                'semester_id' => $this->semester->id,
                'hari' => 'Selasa',
                'jam_ke' => 1,
            ],
            [
                'keterangan' => 'Upacara',
            ]
        );

        // Acting as Teacher 1
        $response = $this->actingAs($this->teacher)->get(route('guru.jadwal-mengajar.get-by-kelas', $this->kelas->id));
        $response->assertStatus(200);

        $json = $response->json();

        // Check Senin Jam 1 is Teacher 1 (is_current_teacher = true)
        $this->assertEquals('lesson', $json['Senin']['1']['type']);
        $this->assertTrue($json['Senin']['1']['is_current_teacher']);
        $this->assertEquals($this->teacher->id, $json['Senin']['1']['guru']['id']);

        // Check Senin Jam 2 is Teacher 2 (is_current_teacher = false)
        $this->assertEquals('lesson', $json['Senin']['2']['type']);
        $this->assertFalse($json['Senin']['2']['is_current_teacher']);
        $this->assertEquals($teacher2->id, $json['Senin']['2']['guru']['id']);

        // Check Selasa Jam 1 is fixed schedule
        $this->assertEquals('fixed', $json['Selasa']['1']['type']);
        $this->assertEquals('Upacara', $json['Selasa']['1']['keterangan']);

        // Check Selasa Jam 2 is empty slot (null)
        $this->assertNull($json['Selasa']['2']);

        // Check all days exist in keys
        foreach (['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $day) {
            $this->assertArrayHasKey($day, $json);
            $this->assertArrayHasKey('1', $json[$day]);
            $this->assertArrayHasKey('2', $json[$day]);
        }
    }

    public function test_guru_schedule_prefers_active_academic_year_even_if_old_year_has_active_semester()
    {
        // Deactivate existing years first in transaction
        TahunPelajaran::query()->update(['is_active' => false]);
        Semester::query()->update(['is_active' => false]);

        // Simulate previous academic year which mistakenly has active semester
        $oldTp = TahunPelajaran::create([
            'nama' => '2023/2024 Old Year',
            'tahun_mulai' => 2023,
            'tahun_selesai' => 2024,
            'is_active' => false,
        ]);

        $oldSem = Semester::create([
            'tahun_pelajaran_id' => $oldTp->id,
            'nama' => 'Genap',
            'semester_ke' => 2,
            'is_active' => true, // Mistakenly left active
        ]);

        // Current academic year is active
        $currentTp = TahunPelajaran::create([
            'nama' => '2026/2027 New Year',
            'tahun_mulai' => 2026,
            'tahun_selesai' => 2027,
            'is_active' => true,
        ]);

        $currentSem = Semester::create([
            'tahun_pelajaran_id' => $currentTp->id,
            'nama' => 'Ganjil',
            'semester_ke' => 1,
            'is_active' => true,
        ]);

        // Semester::getActive() must resolve to current academic year semester
        $resolved = Semester::getActive();
        $this->assertEquals($currentSem->id, $resolved->id);
        $this->assertEquals($currentTp->id, $resolved->tahun_pelajaran_id);

        // When activating a new Tahun Pelajaran via setActive, all other semesters are wiped of is_active
        $oldTp->setActive(true);
        $this->assertTrue((bool) $oldTp->fresh()->is_active);
        $this->assertFalse((bool) $currentTp->fresh()->is_active);
        $this->assertFalse((bool) $currentSem->fresh()->is_active);
        $this->assertTrue((bool) $oldSem->fresh()->is_active);
    }
}
