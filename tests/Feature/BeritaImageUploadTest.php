<?php

namespace Tests\Feature;

use App\Models\Berita;
use App\Models\GuruProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BeritaImageUploadTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;
    protected User $guruWithAccess;
    protected User $guruWithoutAccess;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->admin = User::firstOrCreate(
            ['email' => 'admin_berita_test@smp3sawan.sch.id'],
            [
                'name' => 'Admin Berita Test',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        $this->guruWithAccess = User::firstOrCreate(
            ['email' => 'guru_berita_test@smp3sawan.sch.id'],
            [
                'name' => 'Guru Berita Test',
                'password' => bcrypt('password'),
                'role' => 'guru',
                'is_active' => true,
            ]
        );
        GuruProfile::updateOrCreate(
            ['user_id' => $this->guruWithAccess->id],
            [
                'nama' => 'Guru Berita Test',
                'can_manage_berita' => true,
            ]
        );

        $this->guruWithoutAccess = User::firstOrCreate(
            ['email' => 'guru_no_berita_test@smp3sawan.sch.id'],
            [
                'name' => 'Guru No Berita Test',
                'password' => bcrypt('password'),
                'role' => 'guru',
                'is_active' => true,
            ]
        );
        GuruProfile::updateOrCreate(
            ['user_id' => $this->guruWithoutAccess->id],
            [
                'nama' => 'Guru No Berita Test',
                'can_manage_berita' => false,
            ]
        );
    }

    public function test_admin_can_view_create_page_with_unified_uploader(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.berita.create'));

        $response->assertStatus(200);
        $response->assertSee('unified_file_input');
        $response->assertSee('dropzoneBox');
        $response->assertSee('previewGallery');
    }

    public function test_guru_with_access_can_view_create_page_with_unified_uploader(): void
    {
        $response = $this->actingAs($this->guruWithAccess)->get(route('guru.berita.create'));

        $response->assertStatus(200);
        $response->assertSee('unified_file_input');
        $response->assertSee('dropzoneBox');
        $response->assertSee('previewGallery');
    }

    public function test_guru_without_access_is_forbidden(): void
    {
        $response = $this->actingAs($this->guruWithoutAccess)->get(route('guru.berita.create'));

        $response->assertStatus(403);
    }

    public function test_admin_can_store_berita_with_main_and_extra_images(): void
    {
        $mainPhoto = UploadedFile::fake()->image('main.jpg', 600, 400);
        $extraPhoto1 = UploadedFile::fake()->image('extra1.jpg', 600, 400);
        $extraPhoto2 = UploadedFile::fake()->image('extra2.jpg', 600, 400);

        $response = $this->actingAs($this->admin)->post(route('admin.berita.store'), [
            'judul' => 'Berita Kegiatan Sekolah',
            'tanggal' => now()->toDateString(),
            'konten' => 'Isi berita lengkap kegiatan sekolah.',
            'foto' => $mainPhoto,
            'foto_tambahan' => [$extraPhoto1, $extraPhoto2],
        ]);

        $response->assertRedirect(route('admin.berita.index'));

        $this->assertDatabaseHas('berita', [
            'judul' => 'Berita Kegiatan Sekolah',
            'penulis_id' => $this->admin->id,
        ]);

        $berita = Berita::where('judul', 'Berita Kegiatan Sekolah')->first();
        $this->assertNotNull($berita);
        $this->assertNotNull($berita->foto);
        $this->assertCount(2, $berita->images);
    }

    public function test_admin_can_set_main_image_from_extra_images(): void
    {
        $berita = Berita::create([
            'judul' => 'Berita Test Main Image',
            'tanggal' => now()->toDateString(),
            'konten' => 'Isi konten...',
            'penulis_id' => $this->admin->id,
            'foto' => 'berita/original_main.webp',
        ]);

        $extra1 = $berita->images()->create([
            'foto' => 'berita/extra1.webp',
            'urutan' => 1,
        ]);

        $response = $this->actingAs($this->admin)->postJson(route('admin.berita.set-main-image', [
            'id' => $berita->id,
            'imageId' => $extra1->id,
        ]));

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $berita->refresh();
        $this->assertEquals('berita/extra1.webp', $berita->foto);

        $extra1->refresh();
        $this->assertEquals('berita/original_main.webp', $extra1->foto);
    }

    public function test_admin_can_destroy_extra_image(): void
    {
        $berita = Berita::create([
            'judul' => 'Berita Test Delete Extra',
            'tanggal' => now()->toDateString(),
            'konten' => 'Isi konten...',
            'penulis_id' => $this->admin->id,
            'foto' => 'berita/main.webp',
        ]);

        $extra = $berita->images()->create([
            'foto' => 'berita/to_delete.webp',
            'urutan' => 1,
        ]);

        $response = $this->actingAs($this->admin)->deleteJson(route('admin.berita.delete-image', [
            'id' => $berita->id,
            'imageId' => $extra->id,
        ]));

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertDatabaseMissing('berita_images', ['id' => $extra->id]);
    }

    public function test_destroy_main_image_promotes_next_extra_image(): void
    {
        $berita = Berita::create([
            'judul' => 'Berita Test Promote Extra',
            'tanggal' => now()->toDateString(),
            'konten' => 'Isi konten...',
            'penulis_id' => $this->admin->id,
            'foto' => 'berita/old_main.webp',
        ]);

        $extra1 = $berita->images()->create([
            'foto' => 'berita/extra_first.webp',
            'urutan' => 1,
        ]);
        $extra2 = $berita->images()->create([
            'foto' => 'berita/extra_second.webp',
            'urutan' => 2,
        ]);

        $response = $this->actingAs($this->admin)->deleteJson(route('admin.berita.delete-main-image', $berita->id));

        $response->assertStatus(200);
        $response->assertJson(['success' => true, 'promoted' => true]);

        $berita->refresh();
        $this->assertEquals('berita/extra_first.webp', $berita->foto);
        $this->assertDatabaseMissing('berita_images', ['id' => $extra1->id]);
        $this->assertDatabaseHas('berita_images', ['id' => $extra2->id]);
    }

    public function test_destroy_main_image_clears_foto_when_no_extra_images(): void
    {
        $berita = Berita::create([
            'judul' => 'Berita Test Single Image',
            'tanggal' => now()->toDateString(),
            'konten' => 'Isi konten...',
            'penulis_id' => $this->admin->id,
            'foto' => 'berita/lonely_main.webp',
        ]);

        $response = $this->actingAs($this->admin)->deleteJson(route('admin.berita.delete-main-image', $berita->id));

        $response->assertStatus(200);
        $response->assertJson(['success' => true, 'promoted' => false]);

        $berita->refresh();
        $this->assertNull($berita->foto);
    }

    public function test_guru_with_access_can_delete_extra_image_and_main_image(): void
    {
        $berita = Berita::create([
            'judul' => 'Berita Guru Test',
            'tanggal' => now()->toDateString(),
            'konten' => 'Isi konten...',
            'penulis_id' => $this->guruWithAccess->id,
            'foto' => 'berita/main_guru.webp',
        ]);

        $extra = $berita->images()->create([
            'foto' => 'berita/extra_guru.webp',
            'urutan' => 1,
        ]);

        $respDelExtra = $this->actingAs($this->guruWithAccess)->deleteJson(route('guru.berita.delete-image', [
            'id' => $berita->id,
            'imageId' => $extra->id,
        ]));
        $respDelExtra->assertStatus(200);

        $respDelMain = $this->actingAs($this->guruWithAccess)->deleteJson(route('guru.berita.delete-main-image', $berita->id));
        $respDelMain->assertStatus(200);
        $berita->refresh();
        $this->assertNull($berita->foto);
    }
}
