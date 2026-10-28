<?php

namespace Tests\Feature;

use App\Models\Kelas;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AdminSiswaKenaikanKelasTest extends TestCase
{
    use DatabaseTransactions;

    private User $admin;
    private Kelas $kelas7A;
    private Kelas $kelas7B;
    private Kelas $kelas8A;
    private Kelas $kelas8B;
    private Kelas $kelas9A;
    private Kelas $kelas9B;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'password' => 'password',
        ]);

        $this->kelas7A = Kelas::firstOrCreate(['tingkat' => '7', 'nama_kelas' => 'A']);
        $this->kelas7B = Kelas::firstOrCreate(['tingkat' => '7', 'nama_kelas' => 'B']);
        $this->kelas8A = Kelas::firstOrCreate(['tingkat' => '8', 'nama_kelas' => 'A']);
        $this->kelas8B = Kelas::firstOrCreate(['tingkat' => '8', 'nama_kelas' => 'B']);
        $this->kelas9A = Kelas::firstOrCreate(['tingkat' => '9', 'nama_kelas' => 'A']);
        $this->kelas9B = Kelas::firstOrCreate(['tingkat' => '9', 'nama_kelas' => 'B']);
    }

    private function createStudent(Kelas $kelas, string $name, string $nis): StudentProfile
    {
        $user = User::factory()->create([
            'name' => $name,
            'role' => 'siswa',
            'password' => 'password',
        ]);

        return StudentProfile::create([
            'user_id' => $user->id,
            'kelas_id' => $kelas->id,
            'nis' => $nis,
            'status' => 'AKTIF',
            'is_active' => true,
        ]);
    }

    public function test_kenaikan_summary_returns_all_grades_summary_and_students(): void
    {
        $this->createStudent($this->kelas7A, 'Siswa 7A Khusus', '9991');
        $this->createStudent($this->kelas8A, 'Siswa 8A Khusus', '9992');
        $this->createStudent($this->kelas9A, 'Siswa 9A Khusus', '9993');

        $response = $this->actingAs($this->admin)
            ->getJson(route('admin.siswa.kenaikan_summary'));

        $response->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        $data = $response->json();
        $this->assertGreaterThanOrEqual(3, $data['total_siswa']);
        $this->assertNotEmpty($data['rombel_summary']['7']);
        $this->assertNotEmpty($data['rombel_summary']['8']);
        $this->assertNotEmpty($data['rombel_summary']['9']);
    }

    public function test_simultaneous_promotion_safely_processes_in_reverse_order_without_cascading(): void
    {
        $s7 = $this->createStudent($this->kelas7A, 'Siswa 7A Awal', '8881');
        $s8 = $this->createStudent($this->kelas8A, 'Siswa 8A Awal', '8882');
        $s9 = $this->createStudent($this->kelas9A, 'Siswa 9A Awal', '8883');

        $response = $this->actingAs($this->admin)
            ->postJson(route('admin.siswa.proses_kenaikan_kelas'), []);

        $response->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        $s9->refresh();
        $s8->refresh();
        $s7->refresh();

        // 9th grader graduated
        $this->assertEquals('LULUS', $s9->status);
        $this->assertFalse((bool) $s9->is_active);

        // 8th grader promoted to 9A, still ACTIVE (not graduated!)
        $this->assertEquals($this->kelas9A->id, $s8->kelas_id);
        $this->assertEquals('AKTIF', $s8->status);
        $this->assertTrue((bool) $s8->is_active);

        // 7th grader promoted to 8A, still ACTIVE (not promoted to 9A!)
        $this->assertEquals($this->kelas8A->id, $s7->kelas_id);
        $this->assertEquals('AKTIF', $s7->status);
        $this->assertTrue((bool) $s7->is_active);
    }

    public function test_simultaneous_promotion_with_retained_students_in_various_grades(): void
    {
        $s7_promoted = $this->createStudent($this->kelas7A, 'Siswa 7 Naik', '7771');
        $s7_retained = $this->createStudent($this->kelas7A, 'Siswa 7 Tinggal', '7772');

        $s8_promoted = $this->createStudent($this->kelas8A, 'Siswa 8 Naik', '7773');
        $s8_retained = $this->createStudent($this->kelas8A, 'Siswa 8 Tinggal', '7774');

        $s9_graduated = $this->createStudent($this->kelas9A, 'Siswa 9 Lulus', '7775');
        $s9_retained  = $this->createStudent($this->kelas9A, 'Siswa 9 Tidak Lulus', '7776');

        $response = $this->actingAs($this->admin)
            ->postJson(route('admin.siswa.proses_kenaikan_kelas'), [
                'siswa_tinggal_ids' => [
                    $s7_retained->id,
                    $s8_retained->id,
                    $s9_retained->id,
                ],
            ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        $s7_promoted->refresh();
        $s7_retained->refresh();
        $s8_promoted->refresh();
        $s8_retained->refresh();
        $s9_graduated->refresh();
        $s9_retained->refresh();

        // Check promoted
        $this->assertEquals($this->kelas8A->id, $s7_promoted->kelas_id);
        $this->assertEquals($this->kelas9A->id, $s8_promoted->kelas_id);
        $this->assertEquals('LULUS', $s9_graduated->status);
        $this->assertFalse((bool) $s9_graduated->is_active);

        // Check retained
        $this->assertEquals($this->kelas7A->id, $s7_retained->kelas_id);
        $this->assertEquals('AKTIF', $s7_retained->status);

        $this->assertEquals($this->kelas8A->id, $s8_retained->kelas_id);
        $this->assertEquals('AKTIF', $s8_retained->status);

        $this->assertEquals($this->kelas9A->id, $s9_retained->kelas_id);
        $this->assertEquals('AKTIF', $s9_retained->status);
        $this->assertTrue((bool) $s9_retained->is_active);
    }
}
