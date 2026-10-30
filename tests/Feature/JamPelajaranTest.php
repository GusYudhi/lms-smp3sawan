<?php

namespace Tests\Feature;

use App\Models\JamPelajaran;
use App\Models\Semester;
use App\Models\TahunPelajaran;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class JamPelajaranTest extends TestCase
{
    use DatabaseTransactions;

    protected $admin;
    protected $semester1;
    protected $semester2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::firstOrCreate(
            ['email' => 'admin_test_jam_ke0@admin.id'],
            [
                'name' => 'Admin Test Jam Ke 0',
                'password' => bcrypt('password123'),
                'role' => 'admin',
            ]
        );

        $tp = TahunPelajaran::firstOrCreate(
            ['nama' => '2025/2026 Test Jam Ke 0'],
            [
                'tahun_mulai' => 2025,
                'tahun_selesai' => 2026,
                'tanggal_mulai' => '2025-07-01',
                'tanggal_selesai' => '2026-06-30',
                'is_active' => true,
            ]
        );

        $this->semester1 = Semester::firstOrCreate(
            ['semester_ke' => 1, 'tahun_pelajaran_id' => $tp->id],
            [
                'nama' => 'Ganjil 2025/2026 Test Jam Ke 0',
                'tanggal_mulai' => '2025-07-01',
                'tanggal_selesai' => '2025-12-31',
                'is_active' => true,
            ]
        );

        $this->semester2 = Semester::firstOrCreate(
            ['semester_ke' => 2, 'tahun_pelajaran_id' => $tp->id],
            [
                'nama' => 'Genap 2025/2026 Test Jam Ke 0',
                'tanggal_mulai' => '2026-01-01',
                'tanggal_selesai' => '2026-06-30',
                'is_active' => false,
            ]
        );
    }

    public function test_admin_can_add_jam_ke_0()
    {
        $response = $this->actingAs($this->admin)->post(route('admin.jam-pelajaran.store'), [
            'semester_id' => $this->semester1->id,
            'jam_ke' => 0,
            'jam_mulai' => '06:30',
            'jam_selesai' => '07:10',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('jam_pelajarans', [
            'semester_id' => $this->semester1->id,
            'jam_ke' => 0,
            'jam_mulai' => '06:30:00',
            'jam_selesai' => '07:10:00',
        ]);
    }

    public function test_admin_cannot_add_negative_jam_ke()
    {
        $response = $this->actingAs($this->admin)->post(route('admin.jam-pelajaran.store'), [
            'semester_id' => $this->semester1->id,
            'jam_ke' => -1,
            'jam_mulai' => '06:30',
            'jam_selesai' => '07:10',
        ]);

        $response->assertSessionHasErrors(['jam_ke']);
        $this->assertDatabaseMissing('jam_pelajarans', [
            'semester_id' => $this->semester1->id,
            'jam_ke' => -1,
        ]);
    }

    public function test_admin_cannot_add_duplicate_jam_ke_in_same_semester()
    {
        JamPelajaran::create([
            'semester_id' => $this->semester1->id,
            'jam_ke' => 0,
            'jam_mulai' => '06:30:00',
            'jam_selesai' => '07:10:00',
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.jam-pelajaran.store'), [
            'semester_id' => $this->semester1->id,
            'jam_ke' => 0,
            'jam_mulai' => '06:30',
            'jam_selesai' => '07:10',
        ]);

        $response->assertSessionHasErrors(['jam_ke']);
    }

    public function test_admin_can_add_same_jam_ke_in_different_semester()
    {
        JamPelajaran::create([
            'semester_id' => $this->semester1->id,
            'jam_ke' => 0,
            'jam_mulai' => '06:30:00',
            'jam_selesai' => '07:10:00',
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.jam-pelajaran.store'), [
            'semester_id' => $this->semester2->id,
            'jam_ke' => 0,
            'jam_mulai' => '06:30',
            'jam_selesai' => '07:10',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('jam_pelajarans', [
            'semester_id' => $this->semester2->id,
            'jam_ke' => 0,
        ]);
    }

    public function test_admin_can_update_jam_pelajaran_to_jam_ke_0()
    {
        $jam = JamPelajaran::create([
            'semester_id' => $this->semester1->id,
            'jam_ke' => 9,
            'jam_mulai' => '13:00:00',
            'jam_selesai' => '13:40:00',
        ]);

        $response = $this->actingAs($this->admin)->put(route('admin.jam-pelajaran.update', $jam->id), [
            'semester_id' => $this->semester1->id,
            'jam_ke' => 0,
            'jam_mulai' => '06:30',
            'jam_selesai' => '07:10',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('jam_pelajarans', [
            'id' => $jam->id,
            'jam_ke' => 0,
        ]);
    }
}
