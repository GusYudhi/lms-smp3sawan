<?php

namespace Database\Seeders;

use App\Models\GuruProfile;
use App\Models\SchoolProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class KepalaSekolahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $email = 'kepalasekolah@gmail.com';
        $nama = 'Nyoman Paksa Adi Gama, S.Pd., M.Pd.';

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $nama,
                'email' => $email,
                'password' => Hash::make('12345678'),
                'role' => 'kepala_sekolah',
            ]
        );

        $guruProfile = GuruProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'nama' => $nama,
                'nip' => '196512311988031001',
                'kode_guru' => 'KS',
                'nomor_telepon' => '085850190190',
                'email' => $email,
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Buleleng',
                'tanggal_lahir' => '1965-12-31',
                'status_kepegawaian' => 'PNS',
                'golongan' => 'IV/b',
                'jabatan_di_sekolah' => 'Kepala Sekolah',
                'password' => Hash::make('12345678'),
                'is_active' => true,
                'can_manage_berita' => true,
                'can_manage_prestasi' => true,
            ]
        );

        // Hubungkan ke SchoolProfile jika profil sekolah sudah ada
        $schoolProfile = SchoolProfile::first();
        if ($schoolProfile) {
            $schoolProfile->update([
                'id_kepala_sekolah' => $guruProfile->id,
                'kepala_sekolah' => $nama,
            ]);
        }

        $this->command?->info("Kepala Sekolah seeder berhasil dijalankan: {$email} / 12345678");
    }
}

