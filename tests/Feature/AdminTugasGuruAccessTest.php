<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\GuruProfile;
use App\Models\TugasGuru;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AdminTugasGuruAccessTest extends TestCase
{
    use DatabaseTransactions;

    protected function createAdmin(): User
    {
        return User::factory()->create([
            'role' => 'admin',
            'password' => 'password',
        ]);
    }

    protected function createKepalaSekolah(): User
    {
        return User::factory()->create([
            'role' => 'kepala_sekolah',
            'password' => 'password',
        ]);
    }

    protected function createTeacher(): User
    {
        $user = User::factory()->create([
            'role' => 'guru',
            'password' => 'password',
        ]);

        GuruProfile::create([
            'user_id' => $user->id,
            'nama' => $user->name,
            'nip' => 'NIP' . rand(100000, 999999),
            'email' => $user->email,
            'is_active' => true,
        ]);

        return $user;
    }

    public function test_admin_can_access_tugas_guru_index_and_create()
    {
        $admin = $this->createAdmin();

        $responseIndex = $this->actingAs($admin)->get(route('kepala-sekolah.tugas-guru.index'));
        $responseIndex->assertStatus(200);
        $responseIndex->assertSee('Manajemen Tugas Guru');

        $responseCreate = $this->actingAs($admin)->get(route('kepala-sekolah.tugas-guru.create'));
        $responseCreate->assertStatus(200);
    }

    public function test_admin_redirect_from_admin_prefix()
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->get('/admin/tugas-guru');
        $response->assertRedirect('/kepala-sekolah/tugas-guru');
    }

    public function test_admin_can_create_tugas_guru()
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->post(route('kepala-sekolah.tugas-guru.store'), [
            'judul' => 'Tugas Guru Dari Admin',
            'deskripsi' => 'Pengumpulan perangkat ajar semester baru.',
            'deadline' => now()->addDays(7)->format('Y-m-d H:i:s'),
        ]);

        $response->assertRedirect(route('kepala-sekolah.tugas-guru.index'));

        $this->assertDatabaseHas('tugas_guru', [
            'judul' => 'Tugas Guru Dari Admin',
            'created_by' => $admin->id,
        ]);
    }

    public function test_kepala_sekolah_can_still_access_tugas_guru()
    {
        $kepsek = $this->createKepalaSekolah();

        $response = $this->actingAs($kepsek)->get(route('kepala-sekolah.tugas-guru.index'));
        $response->assertStatus(200);
    }

    public function test_regular_teacher_cannot_access_kepala_sekolah_tugas_guru_management()
    {
        $teacher = $this->createTeacher();

        $response = $this->actingAs($teacher)->get(route('kepala-sekolah.tugas-guru.index'));
        $response->assertStatus(403);
    }
}
