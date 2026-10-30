<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\GuruProfile;
use App\Models\Berita;
use App\Models\Prestasi;
use App\Models\Galeri;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImageCompressionUploadTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    protected function createAdmin(): User
    {
        return User::factory()->create([
            'role' => 'admin',
            'password' => 'password',
        ]);
    }

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

    public function test_admin_can_upload_berita_compressed_to_webp()
    {
        $admin = $this->createAdmin();
        $file = UploadedFile::fake()->image('berita_hero.jpg', 1600, 900);

        $response = $this->actingAs($admin)->post(route('admin.berita.store'), [
            'judul' => 'Berita Uji WebP Kompresi',
            'konten' => 'Isi berita dengan gambar resolusi tinggi yang dikompresi ke WebP.',
            'tanggal' => now()->toDateString(),
            'foto' => $file,
        ]);

        $response->assertRedirect(route('admin.berita.index'));

        $berita = Berita::where('judul', 'Berita Uji WebP Kompresi')->first();
        $this->assertNotNull($berita);
        $this->assertStringEndsWith('.webp', $berita->foto);
        $this->assertStringStartsWith('berita/', $berita->foto);

        Storage::disk('public')->assertExists($berita->foto);
    }

    public function test_admin_updating_berita_deletes_old_photo_and_stores_new_webp()
    {
        $admin = $this->createAdmin();
        $initialFile = UploadedFile::fake()->image('old_photo.jpg', 800, 600);

        $this->actingAs($admin)->post(route('admin.berita.store'), [
            'judul' => 'Berita Awal',
            'konten' => 'Konten awal',
            'tanggal' => now()->toDateString(),
            'foto' => $initialFile,
        ]);

        $berita = Berita::where('judul', 'Berita Awal')->first();
        $oldPath = $berita->foto;
        Storage::disk('public')->assertExists($oldPath);

        $newFile = UploadedFile::fake()->image('new_photo.png', 1000, 800);
        $response = $this->actingAs($admin)->put(route('admin.berita.update', $berita->id), [
            'judul' => 'Berita Terupdate',
            'konten' => 'Konten terupdate',
            'tanggal' => now()->toDateString(),
            'foto' => $newFile,
        ]);

        $response->assertRedirect(route('admin.berita.index'));

        $berita->refresh();
        $this->assertNotEquals($oldPath, $berita->foto);
        $this->assertStringEndsWith('.webp', $berita->foto);

        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($berita->foto);
    }

    public function test_admin_can_access_edit_berita_page()
    {
        $admin = $this->createAdmin();
        $berita = Berita::create([
            'judul' => 'Berita untuk diedit',
            'konten' => 'Isi berita contoh.',
            'tanggal' => now(),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.berita.edit', $berita->id));
        $response->assertStatus(200);
        $response->assertSee('Berita untuk diedit');
    }

    public function test_admin_can_upload_prestasi_compressed_to_webp()
    {
        $admin = $this->createAdmin();
        $file = UploadedFile::fake()->image('prestasi_juara.png', 1200, 800);

        $response = $this->actingAs($admin)->post(route('admin.prestasi.store'), [
            'judul' => 'Juara Olimpiade Sains',
            'deskripsi' => 'Prestasi membanggakan siswa di kancah nasional.',
            'tanggal' => now()->toDateString(),
            'foto' => $file,
        ]);

        $response->assertRedirect(route('admin.prestasi.index'));

        $prestasi = Prestasi::where('judul', 'Juara Olimpiade Sains')->first();
        $this->assertNotNull($prestasi);
        $this->assertStringEndsWith('.webp', $prestasi->foto);
        $this->assertStringStartsWith('prestasi/', $prestasi->foto);

        Storage::disk('public')->assertExists($prestasi->foto);
    }

    public function test_guru_with_permission_can_upload_berita_and_prestasi_as_webp()
    {
        $teacher = $this->createTeacher(true, true);

        // Upload Berita by Guru
        $beritaFile = UploadedFile::fake()->image('guru_berita.jpg', 1400, 900);
        $responseBerita = $this->actingAs($teacher)->post(route('guru.berita.store'), [
            'judul' => 'Berita Dari Guru',
            'konten' => 'Laporan kegiatan literasi.',
            'tanggal' => now()->toDateString(),
            'foto' => $beritaFile,
        ]);
        $responseBerita->assertRedirect(route('guru.berita.index'));

        $berita = Berita::where('judul', 'Berita Dari Guru')->first();
        $this->assertNotNull($berita);
        $this->assertStringEndsWith('.webp', $berita->foto);
        Storage::disk('public')->assertExists($berita->foto);

        // Upload Prestasi by Guru
        $prestasiFile = UploadedFile::fake()->image('guru_prestasi.jpg', 1200, 800);
        $responsePrestasi = $this->actingAs($teacher)->post(route('guru.prestasi.store'), [
            'judul' => 'Prestasi Dari Guru',
            'deskripsi' => 'Siswa meraih medali perunggu.',
            'tanggal' => now()->toDateString(),
            'foto' => $prestasiFile,
        ]);
        $responsePrestasi->assertRedirect(route('guru.prestasi.index'));

        $prestasi = Prestasi::where('judul', 'Prestasi Dari Guru')->first();
        $this->assertNotNull($prestasi);
        $this->assertStringEndsWith('.webp', $prestasi->foto);
        Storage::disk('public')->assertExists($prestasi->foto);
    }

    public function test_galeri_photo_is_compressed_to_webp()
    {
        $admin = $this->createAdmin();
        $file = UploadedFile::fake()->image('kegiatan_sekolah.jpg', 1920, 1080);

        $response = $this->actingAs($admin)->post(route('admin.galeri.store'), [
            'judul' => 'Pentas Seni Tahunan',
            'tipe' => 'foto',
            'deskripsi' => 'Dokumentasi pentas seni siswa',
            'file' => $file,
        ]);

        $response->assertRedirect(route('admin.galeri.index'));

        $galeri = Galeri::where('judul', 'Pentas Seni Tahunan')->first();
        $this->assertNotNull($galeri);
        $this->assertStringEndsWith('.webp', $galeri->file_path);
        $this->assertStringStartsWith('galeri/', $galeri->file_path);

        Storage::disk('public')->assertExists($galeri->file_path);
    }

    public function test_admin_can_upload_teacher_profile_photo_compressed_to_webp()
    {
        $admin = $this->createAdmin();
        $photo = UploadedFile::fake()->image('teacher_avatar.jpg', 600, 600);

        $response = $this->actingAs($admin)->post(route('admin.guru.store'), [
            'name' => 'Guru Baru WebP',
            'email' => 'gurubaru@example.com',
            'nomor_induk' => '99887766',
            'kode_guru' => 'GBW',
            'nomor_telepon' => '081234567890',
            'jenis_kelamin' => 'L',
            'jabatan_di_sekolah' => 'Guru Mapel',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'profile_photo' => $photo,
        ]);

        $response->assertRedirect(route('admin.guru.index'));

        $teacherUser = User::where('email', 'gurubaru@example.com')->first();
        $this->assertNotNull($teacherUser);
        $this->assertNotNull($teacherUser->guruProfile);

        $fotoProfil = $teacherUser->guruProfile->foto_profil;
        $this->assertNotNull($fotoProfil);
        $this->assertStringEndsWith('.webp', $fotoProfil);
        $this->assertFalse(str_starts_with($fotoProfil, 'profile_photos/'));

        Storage::disk('public')->assertExists('profile_photos/' . $fotoProfil);
    }

    public function test_siswa_absence_photo_document_is_compressed_to_webp()
    {
        $studentUser = User::factory()->create([
            'role' => 'siswa',
            'password' => 'password',
        ]);
        \App\Models\StudentProfile::create([
            'user_id' => $studentUser->id,
            'nis' => 'NIS999',
            'nisn' => 'NISN999',
            'is_active' => true,
        ]);

        $photo = UploadedFile::fake()->image('surat_dokter.jpg', 1200, 800);

        $response = $this->actingAs($studentUser)->post(route('siswa.absensi.store'), [
            'type' => 'sakit',
            'keterangan' => 'Sakit demam berdarah dan harus dirawat di rumah sakit',
            'dokumen' => $photo,
        ]);

        $response->assertJson(['success' => true]);

        $attendance = \App\Models\Attendance::where('user_id', $studentUser->id)->first();
        $this->assertNotNull($attendance);
        $this->assertStringContainsString('.webp', $attendance->notes);
    }
}
