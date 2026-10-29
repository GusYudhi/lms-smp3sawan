<?php

namespace Tests\Feature;

use App\Models\GuruProfile;
use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Semester;
use App\Models\TahunPelajaran;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AdminJadwalUpdateTest extends TestCase
{
    use DatabaseTransactions;

    protected $admin;
    protected $semester;
    protected $kelasA;
    protected $kelasB;
    protected $teacher;
    protected $mapel1;
    protected $mapel2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::firstOrCreate(
            ['email' => 'admin_test_jadwal_upd@admin.id'],
            [
                'name' => 'Admin Jadwal Update Test',
                'password' => bcrypt('password123'),
                'role' => 'admin',
            ]
        );

        $tp = TahunPelajaran::firstOrCreate(
            ['nama' => '2025/2026 Test Jadwal Update'],
            [
                'tahun_mulai' => 2025,
                'tahun_selesai' => 2026,
                'tanggal_mulai' => now()->toDateString(),
                'tanggal_selesai' => now()->addYear()->toDateString(),
                'is_active' => true,
            ]
        );

        $this->semester = Semester::firstOrCreate(
            ['semester_ke' => 1, 'tahun_pelajaran_id' => $tp->id],
            [
                'nama' => 'Ganjil 2025/2026 Update Test',
                'tanggal_mulai' => now()->toDateString(),
                'tanggal_selesai' => now()->addMonths(6)->toDateString(),
                'is_active' => true,
            ]
        );

        $this->kelasA = Kelas::firstOrCreate(
            ['nama_kelas' => '7A Test Update'],
            ['tingkat' => 7, 'tahun_pelajaran_id' => $tp->id]
        );

        $this->kelasB = Kelas::firstOrCreate(
            ['nama_kelas' => '7B Test Update'],
            ['tingkat' => 7, 'tahun_pelajaran_id' => $tp->id]
        );

        $this->mapel1 = MataPelajaran::firstOrCreate(
            ['kode_mapel' => 'MAPEL_UPD_1'],
            ['nama_mapel' => 'Mapel Update 1', 'is_active' => true]
        );

        $this->mapel2 = MataPelajaran::firstOrCreate(
            ['kode_mapel' => 'MAPEL_UPD_2'],
            ['nama_mapel' => 'Mapel Update 2', 'is_active' => true]
        );

        $this->teacher = User::factory()->create([
            'email' => 'guru_jadwal_upd_' . uniqid() . '@guru.id',
            'role' => 'guru',
            'password' => bcrypt('password123'),
        ]);

        GuruProfile::create([
            'user_id' => $this->teacher->id,
            'nama' => $this->teacher->name,
            'nip' => 'NIP-JADWAL-' . uniqid(),
            'kode_guru' => 'G' . substr(uniqid(), -3),
            'is_active' => true,
            'mata_pelajaran_id' => $this->mapel1->id,
        ]);
    }

    public function test_admin_can_update_schedule_without_changing_anything_and_no_self_conflict()
    {
        // Buat jadwal di kelas A pada jam ke-3
        $jadwal = JadwalPelajaran::create([
            'semester_id' => $this->semester->id,
            'kelas_id' => $this->kelasA->id,
            'mata_pelajaran_id' => $this->mapel1->id,
            'guru_id' => $this->teacher->id,
            'hari' => 'Senin',
            'jam_ke' => 3,
        ]);

        // Kirim permintaan PUT tanpa mengubah apa-apa
        $response = $this->actingAs($this->admin)
            ->putJson(route('admin.jadwal.update', [
                'semester_id' => $this->semester->id,
                'jadwal_mapel' => $jadwal->id,
            ]), [
                'semester_id' => $this->semester->id,
                'kelas_id' => $this->kelasA->id,
                'mata_pelajaran_id' => $this->mapel1->id,
                'guru_id' => $this->teacher->id,
                'hari' => 'Senin',
                'jam_ke' => 3,
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Jadwal berhasil diperbarui',
        ]);

        $this->assertDatabaseHas('jadwal_pelajarans', [
            'id' => $jadwal->id,
            'semester_id' => $this->semester->id,
            'kelas_id' => $this->kelasA->id,
            'guru_id' => $this->teacher->id,
            'hari' => 'Senin',
            'jam_ke' => 3,
        ]);
    }

    public function test_admin_cannot_update_schedule_if_teacher_actually_has_conflict_in_another_class()
    {
        // Jadwal 1: guru mengajar di Kelas B pada jam ke-3
        JadwalPelajaran::create([
            'semester_id' => $this->semester->id,
            'kelas_id' => $this->kelasB->id,
            'mata_pelajaran_id' => $this->mapel1->id,
            'guru_id' => $this->teacher->id,
            'hari' => 'Senin',
            'jam_ke' => 3,
        ]);

        // Jadwal 2: guru mengajar di Kelas A pada jam ke-2
        $jadwalA = JadwalPelajaran::create([
            'semester_id' => $this->semester->id,
            'kelas_id' => $this->kelasA->id,
            'mata_pelajaran_id' => $this->mapel1->id,
            'guru_id' => $this->teacher->id,
            'hari' => 'Senin',
            'jam_ke' => 2,
        ]);

        // Mencoba memindahkan jadwal A dari jam ke-2 ke jam ke-3 (bentrok dengan jadwal guru di Kelas B)
        $response = $this->actingAs($this->admin)
            ->putJson(route('admin.jadwal.update', [
                'semester_id' => $this->semester->id,
                'jadwal_mapel' => $jadwalA->id,
            ]), [
                'semester_id' => $this->semester->id,
                'kelas_id' => $this->kelasA->id,
                'mata_pelajaran_id' => $this->mapel1->id,
                'guru_id' => $this->teacher->id,
                'hari' => 'Senin',
                'jam_ke' => 3,
            ]);

        $response->assertStatus(422);
        $response->assertJson([
            'message' => 'Guru ini sudah mengajar di kelas lain pada jam ke-3',
        ]);
    }

    public function test_admin_cannot_update_schedule_if_class_slot_already_occupied()
    {
        // Guru lain
        $otherTeacher = User::factory()->create([
            'email' => 'guru_other_' . uniqid() . '@guru.id',
            'role' => 'guru',
            'password' => bcrypt('password123'),
        ]);

        // Jadwal 1 di Kelas A jam ke-4
        JadwalPelajaran::create([
            'semester_id' => $this->semester->id,
            'kelas_id' => $this->kelasA->id,
            'mata_pelajaran_id' => $this->mapel2->id,
            'guru_id' => $otherTeacher->id,
            'hari' => 'Senin',
            'jam_ke' => 4,
        ]);

        // Jadwal 2 di Kelas A jam ke-1
        $jadwalTarget = JadwalPelajaran::create([
            'semester_id' => $this->semester->id,
            'kelas_id' => $this->kelasA->id,
            'mata_pelajaran_id' => $this->mapel1->id,
            'guru_id' => $this->teacher->id,
            'hari' => 'Senin',
            'jam_ke' => 1,
        ]);

        // Coba geser jadwalTarget ke jam ke-4 yang sudah terisi di Kelas A
        $response = $this->actingAs($this->admin)
            ->putJson(route('admin.jadwal.update', [
                'semester_id' => $this->semester->id,
                'jadwal_mapel' => $jadwalTarget->id,
            ]), [
                'semester_id' => $this->semester->id,
                'kelas_id' => $this->kelasA->id,
                'mata_pelajaran_id' => $this->mapel1->id,
                'guru_id' => $this->teacher->id,
                'hari' => 'Senin',
                'jam_ke' => 4,
            ]);

        $response->assertStatus(422);
        $response->assertJson([
            'message' => 'Slot jam ke-4 sudah terisi di kelas ini.',
        ]);
    }

    public function test_admin_can_delete_schedule()
    {
        $jadwal = JadwalPelajaran::create([
            'semester_id' => $this->semester->id,
            'kelas_id' => $this->kelasA->id,
            'mata_pelajaran_id' => $this->mapel1->id,
            'guru_id' => $this->teacher->id,
            'hari' => 'Selasa',
            'jam_ke' => 2,
        ]);

        $response = $this->actingAs($this->admin)
            ->deleteJson(route('admin.jadwal.destroy', [
                'semester_id' => $this->semester->id,
                'jadwal_mapel' => $jadwal->id,
            ]));

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Jadwal berhasil dihapus',
        ]);

        $this->assertDatabaseMissing('jadwal_pelajarans', [
            'id' => $jadwal->id,
        ]);
    }
}
