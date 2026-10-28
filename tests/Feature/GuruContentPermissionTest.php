<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\GuruProfile;
use App\Models\Berita;
use App\Models\Prestasi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuruContentPermissionTest extends TestCase
{
    protected function createTeacher(bool $canBerita = false, bool $canPrestasi = false): User
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
            'can_manage_berita' => $canBerita,
            'can_manage_prestasi' => $canPrestasi,
        ]);

        return $user;
    }

    public function test_teacher_without_permission_is_forbidden_from_berita_and_prestasi()
    {
        $teacher = $this->createTeacher(false, false);

        $responseBerita = $this->actingAs($teacher)->get('/guru/berita');
        $responseBerita->assertStatus(403);

        $responsePrestasi = $this->actingAs($teacher)->get('/guru/prestasi');
        $responsePrestasi->assertStatus(403);
    }

    public function test_teacher_with_berita_permission_can_manage_berita_only()
    {
        $teacher = $this->createTeacher(true, false);

        // Can access berita index
        $responseBerita = $this->actingAs($teacher)->get('/guru/berita');
        $responseBerita->assertStatus(200);

        // Can create berita
        $postResponse = $this->actingAs($teacher)->post('/guru/berita', [
            'judul' => 'Judul Berita Test Guru',
            'konten' => 'Konten berita yang dibuat oleh guru dengan hak akses.',
            'tanggal' => now()->toDateString(),
        ]);
        $postResponse->assertRedirect(route('guru.berita.index'));

        $this->assertDatabaseHas('berita', [
            'judul' => 'Judul Berita Test Guru',
            'penulis_id' => $teacher->id,
        ]);

        // Cannot access prestasi
        $responsePrestasi = $this->actingAs($teacher)->get('/guru/prestasi');
        $responsePrestasi->assertStatus(403);
    }

    public function test_teacher_with_prestasi_permission_can_manage_prestasi_only()
    {
        $teacher = $this->createTeacher(false, true);

        // Can access prestasi index
        $responsePrestasi = $this->actingAs($teacher)->get('/guru/prestasi');
        $responsePrestasi->assertStatus(200);

        // Can create prestasi
        $postResponse = $this->actingAs($teacher)->post('/guru/prestasi', [
            'judul' => 'Juara 1 Lomba Sains',
            'deskripsi' => 'Prestasi yang diraih di tingkat provinsi.',
            'tanggal' => now()->toDateString(),
        ]);
        $postResponse->assertRedirect(route('guru.prestasi.index'));

        $this->assertDatabaseHas('prestasi', [
            'judul' => 'Juara 1 Lomba Sains',
            'penulis_id' => $teacher->id,
        ]);

        // Cannot access berita
        $responseBerita = $this->actingAs($teacher)->get('/guru/berita');
        $responseBerita->assertStatus(403);
    }

    public function test_admin_can_assign_and_update_content_permissions_for_teacher()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'password' => 'password',
        ]);

        $teacher = $this->createTeacher(false, false);

        // Admin updates teacher to have both permissions
        $updateResponse = $this->actingAs($admin)->put(route('admin.guru.update', $teacher->id), [
            'name' => 'Guru Pengelola',
            'nomor_induk' => $teacher->guruProfile->nip,
            'email' => $teacher->email,
            'jenis_kelamin' => 'L',
            'can_manage_berita' => '1',
            'can_manage_prestasi' => '1',
        ]);

        $updateResponse->assertRedirect(route('admin.guru.index'));

        $this->assertDatabaseHas('guru_profiles', [
            'user_id' => $teacher->id,
            'can_manage_berita' => 1,
            'can_manage_prestasi' => 1,
        ]);

        // Verify teacher can now access both
        $teacher->refresh();
        $this->assertTrue($teacher->canManageBerita());
        $this->assertTrue($teacher->canManagePrestasi());

        $this->actingAs($teacher)->get('/guru/berita')->assertStatus(200);
        $this->actingAs($teacher)->get('/guru/prestasi')->assertStatus(200);
    }
}
