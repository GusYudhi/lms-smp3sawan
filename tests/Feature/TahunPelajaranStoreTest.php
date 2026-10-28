<?php

namespace Tests\Feature;

use App\Models\FixedSchedule;
use App\Models\JamPelajaran;
use App\Models\Kelas;
use App\Models\Semester;
use App\Models\StudentProfile;
use App\Models\TahunPelajaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TahunPelajaranStoreTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private TahunPelajaran $oldTp;
    private Semester $oldSemester1;
    private Semester $oldSemester2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'password' => 'password',
        ]);

        // Setup old tahun pelajaran 2024/2025
        $this->oldTp = TahunPelajaran::create([
            'nama' => '2024/2025',
            'tahun_mulai' => 2024,
            'tahun_selesai' => 2025,
            'is_active' => true,
        ]);

        $this->oldSemester1 = Semester::create([
            'tahun_pelajaran_id' => $this->oldTp->id,
            'nama' => 'Ganjil',
            'semester_ke' => 1,
            'is_active' => true,
        ]);

        $this->oldSemester2 = Semester::create([
            'tahun_pelajaran_id' => $this->oldTp->id,
            'nama' => 'Genap',
            'semester_ke' => 2,
            'is_active' => false,
        ]);

        // Seed Jam Pelajaran and FixedSchedule in Semester 1 of old year
        JamPelajaran::create([
            'jam_ke' => 1,
            'jam_mulai' => '07:30:00',
            'jam_selesai' => '08:10:00',
            'semester_id' => $this->oldSemester1->id,
        ]);

        JamPelajaran::create([
            'jam_ke' => 2,
            'jam_mulai' => '08:10:00',
            'jam_selesai' => '08:50:00',
            'semester_id' => $this->oldSemester1->id,
        ]);

        FixedSchedule::create([
            'hari' => 'Senin',
            'jam_ke' => 1,
            'keterangan' => 'Upacara Bendera',
            'semester_id' => $this->oldSemester1->id,
        ]);
    }

    public function test_create_tahun_pelajaran_with_all_toggles_enabled()
    {
        // Setup classes
        $kelas7A = Kelas::create(['tingkat' => '7', 'nama_kelas' => 'A']);
        $kelas8A = Kelas::create(['tingkat' => '8', 'nama_kelas' => 'A']);
        $kelas9A = Kelas::create(['tingkat' => '9', 'nama_kelas' => 'A']);

        // Setup students
        $u7 = User::factory()->create(['role' => 'siswa', 'password' => 'password']);
        $student7 = StudentProfile::create([
            'user_id' => $u7->id,
            'nis' => '1001',
            'nisn' => '1001001',
            'kelas_id' => $kelas7A->id,
            'is_active' => true,
            'status' => 'AKTIF',
        ]);

        $u8 = User::factory()->create(['role' => 'siswa', 'password' => 'password']);
        $student8 = StudentProfile::create([
            'user_id' => $u8->id,
            'nis' => '1002',
            'nisn' => '1001002',
            'kelas_id' => $kelas8A->id,
            'is_active' => true,
            'status' => 'AKTIF',
        ]);

        $u9 = User::factory()->create(['role' => 'siswa', 'password' => 'password']);
        $student9 = StudentProfile::create([
            'user_id' => $u9->id,
            'nis' => '1003',
            'nisn' => '1001003',
            'kelas_id' => $kelas9A->id,
            'is_active' => true,
            'status' => 'AKTIF',
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.tahun-pelajaran.store'), [
            'nama' => '2025/2026',
            'tahun_mulai' => 2025,
            'tahun_selesai' => 2026,
            'tanggal_mulai' => '2025-07-15',
            'tanggal_selesai' => '2026-06-30',
            'is_active' => '1',
            'create_semesters' => '1',
            'copy_from_previous' => '1',
            'auto_promote_students' => '1',
        ]);

        $response->assertRedirect(route('admin.tahun-pelajaran.index'));
        $response->assertSessionHas('success');
        $response->assertSessionHas('promotion_info');

        // 1. Check TahunPelajaran activation
        $newTp = TahunPelajaran::where('nama', '2025/2026')->first();
        $this->assertNotNull($newTp);
        $this->assertTrue((bool)$newTp->is_active);

        $this->oldTp->refresh();
        $this->assertFalse((bool)$this->oldTp->is_active);

        // 2. Check Semester activation: There should ONLY be ONE active semester in the entire DB!
        $activeSemesters = Semester::where('is_active', true)->get();
        $this->assertCount(1, $activeSemesters, 'There must be exactly one active semester across all academic years.');

        $activeSemester = $activeSemesters->first();
        $this->assertEquals($newTp->id, $activeSemester->tahun_pelajaran_id);
        $this->assertEquals(1, $activeSemester->semester_ke);

        // 3. Check JamPelajaran and FixedSchedule copied to the new semester
        $copiedJams = JamPelajaran::where('semester_id', $activeSemester->id)->get();
        $this->assertCount(2, $copiedJams, 'JamPelajaran should be copied from previous year even if Genap was empty.');

        $copiedFixed = FixedSchedule::where('semester_id', $activeSemester->id)->get();
        $this->assertCount(1, $copiedFixed, 'FixedSchedule should be copied from previous year.');
        $this->assertEquals('Upacara Bendera', $copiedFixed->first()->keterangan);

        // 4. Check Student Promotion
        $student7->refresh();
        $this->assertEquals($kelas8A->id, $student7->kelas_id, 'Class 7 student should be promoted to 8A.');
        $this->assertTrue((bool)$student7->is_active);
        $this->assertEquals('AKTIF', $student7->status);

        $student8->refresh();
        $this->assertEquals($kelas9A->id, $student8->kelas_id, 'Class 8 student should be promoted to 9A.');
        $this->assertTrue((bool)$student8->is_active);
        $this->assertEquals('AKTIF', $student8->status);

        $student9->refresh();
        $this->assertFalse((bool)$student9->is_active, 'Class 9 student should graduate and be inactive.');
        $this->assertEquals('LULUS', $student9->status);
    }

    public function test_set_active_route_syncs_academic_year_and_semester()
    {
        // Create an inactive tahun pelajaran with 2 semesters
        $inactiveTp = TahunPelajaran::create([
            'nama' => '2026/2027',
            'tahun_mulai' => 2026,
            'tahun_selesai' => 2027,
            'is_active' => false,
        ]);

        $sem1 = Semester::create([
            'tahun_pelajaran_id' => $inactiveTp->id,
            'nama' => 'Ganjil',
            'semester_ke' => 1,
            'is_active' => false,
        ]);

        $sem2 = Semester::create([
            'tahun_pelajaran_id' => $inactiveTp->id,
            'nama' => 'Genap',
            'semester_ke' => 2,
            'is_active' => false,
        ]);

        // Post set-active
        $response = $this->actingAs($this->admin)->post(route('admin.tahun-pelajaran.set-active', $inactiveTp->id));
        $response->assertRedirect();

        $inactiveTp->refresh();
        $this->assertTrue((bool)$inactiveTp->is_active);

        $this->oldTp->refresh();
        $this->assertFalse((bool)$this->oldTp->is_active);

        // Active semester must be sem1 and exactly 1 active semester globally
        $activeSemesters = Semester::where('is_active', true)->get();
        $this->assertCount(1, $activeSemesters);
        $this->assertEquals($sem1->id, $activeSemesters->first()->id);
    }

    public function test_create_inactive_tahun_pelajaran_does_not_disrupt_active_state()
    {
        $response = $this->actingAs($this->admin)->post(route('admin.tahun-pelajaran.store'), [
            'nama' => '2027/2028',
            'tahun_mulai' => 2027,
            'tahun_selesai' => 2028,
            'is_active' => '0',
            'create_semesters' => '1',
        ]);

        $response->assertRedirect(route('admin.tahun-pelajaran.index'));

        $this->oldTp->refresh();
        $this->assertTrue((bool)$this->oldTp->is_active);

        $activeSemester = Semester::getActive();
        $this->assertNotNull($activeSemester);
        $this->assertEquals($this->oldSemester1->id, $activeSemester->id);
    }
}
