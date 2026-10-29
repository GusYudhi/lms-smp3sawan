<?php

namespace Tests\Feature;

use App\Models\GuruProfile;
use App\Models\MataPelajaran;
use App\Models\Saran;
use App\Models\TugasGuru;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test pengiriman saran publik valid dan tersimpan dengan status belum_dibaca
     */
    public function test_saran_valid_tersimpan_dengan_status_belum_dibaca()
    {
        $response = $this->from(route('guest.kontak'))->post(route('guest.saran.store'), [
            'nama_pengirim' => 'Budi Santoso',
            'email_pengirim' => 'budi@example.com',
            'isi_saran' => 'Mohon ditingkatkan fasilitas perpustakaan.',
            'status' => 'sudah_dibaca', // Mencoba manipulasi mass assignment
        ]);

        $response->assertRedirect(route('guest.kontak'));
        $response->assertSessionHas('success', 'Terima kasih atas saran Anda!');

        $saran = Saran::where('email_pengirim', 'budi@example.com')->first();
        $this->assertNotNull($saran);
        $this->assertEquals('Budi Santoso', $saran->nama_pengirim);
        $this->assertEquals('belum_dibaca', $saran->status); // Status tidak boleh berubah jadi 'sudah_dibaca'
    }

    /**
     * Test bot tertolak jika honeypot terisi
     */
    public function test_saran_ditolak_jika_honeypot_terisi()
    {
        $response = $this->from(route('guest.kontak'))->post(route('guest.saran.store'), [
            'website' => 'http://spam-link.com',
            'nama_pengirim' => 'Spam Bot',
            'isi_saran' => 'Ini isi spam bot otomatis.',
        ]);

        $response->assertRedirect(route('guest.kontak'));
        $response->assertSessionHasErrors(['saran']);

        $saran = Saran::where('nama_pengirim', 'Spam Bot')->first();
        $this->assertNull($saran);
    }

    /**
     * Test upload materi menolak ekstensi yang tidak diizinkan (misal executable / script)
     */
    public function test_materi_pelajaran_menolak_file_tidak_diizinkan()
    {
        Storage::fake('public');

        $mapel = MataPelajaran::first() ?? MataPelajaran::create([
            'nama_mapel' => 'Bahasa Bali',
            'kode_mapel' => 'BAL',
            'is_active' => true,
        ]);

        $guru = User::factory()->create([
            'role' => 'guru',
            'password' => Hash::make('password123'),
        ]);

        $disallowedFile = UploadedFile::fake()->create('malicious_script.php', 100, 'text/x-php');

        $response = $this->actingAs($guru)->post(route('guru.materi.store'), [
            'mata_pelajaran_id' => $mapel->id,
            'judul' => 'Materi Tes Keamanan',
            'tipe' => 'file',
            'file_path' => $disallowedFile,
            'target_kelas' => 'all_global',
        ]);

        $response->assertSessionHasErrors(['file_path']);
    }

    /**
     * Test ganti password mewajibkan verifikasi current_password
     */
    public function test_ganti_password_memerlukan_current_password_yang_benar()
    {
        $user = User::factory()->create([
            'role' => 'guru',
            'password' => Hash::make('password_lama_123'),
        ]);

        // Uji dengan current_password salah
        $responseWrong = $this->actingAs($user)->postJson(route('profile.update-password'), [
            'current_password' => 'password_salah',
            'password' => 'password_baru_123',
            'password_confirmation' => 'password_baru_123',
        ]);

        $responseWrong->assertStatus(422);
        $responseWrong->assertJsonValidationErrors(['current_password']);

        // Uji dengan current_password benar
        $responseRight = $this->actingAs($user)->postJson(route('profile.update-password'), [
            'current_password' => 'password_lama_123',
            'password' => 'password_baru_123',
            'password_confirmation' => 'password_baru_123',
        ]);

        $responseRight->assertStatus(200);
        $responseRight->assertJson(['success' => true]);

        $user->refresh();
        $this->assertTrue(Hash::check('password_baru_123', $user->password));
    }

    /**
     * Test guru tidak dapat mengubah NIP melalui request update profile
     */
    public function test_guru_tidak_dapat_mengubah_nip_lewat_update_profile()
    {
        $user = User::factory()->create([
            'role' => 'guru',
            'password' => Hash::make('password123'),
        ]);

        GuruProfile::create([
            'user_id' => $user->id,
            'nama' => $user->name,
            'nip' => '198501012010011001',
            'kode_guru' => 'TES',
        ]);

        $response = $this->actingAs($user)->postJson(route('profile.update'), [
            'name' => $user->name,
            'email' => $user->email,
            'nip' => '199999999999999999', // Mencoba ubah NIP yang seharusnya readonly
        ]);

        $response->assertStatus(200);

        $profile = GuruProfile::where('user_id', $user->id)->first();
        $this->assertEquals('198501012010011001', $profile->nip);
    }

    /**
     * Test upload batch kartu identitas menolak session_id dengan path traversal
     */
    public function test_batch_id_card_menolak_path_traversal()
    {
        $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->postJson(route('admin.siswa.upload_batch_idcard'), [
            'session_id' => '../traversal_test',
            'batch_number' => 1,
            'total_batches' => 1,
            'cards' => [],
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['session_id']);
    }

    /**
     * Test route GET /logout tidak lagi tersedia
     */
    public function test_route_get_logout_tidak_tersedia()
    {
        $response = $this->get('/logout');
        $response->assertStatus(405); // Method Not Allowed (hanya POST yang diizinkan)
    }
}