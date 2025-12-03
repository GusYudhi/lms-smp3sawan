<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\StudentProfile;
use App\Models\Kelas;
use Illuminate\Support\Facades\Hash;

class SiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all kelas
        $kelasList = Kelas::all();

        if ($kelasList->isEmpty()) {
            $this->command->error('Tidak ada kelas di database! Jalankan KelasSeeder terlebih dahulu.');
            return;
        }

        // Create 50 student users with profiles
        for ($i = 1; $i <= 50; $i++) {
            // Create user
            $user = User::create([
                'name' => fake()->name(),
                'email' => fake()->unique()->safeEmail(),
                'password' => Hash::make('password'),
                'role' => 'siswa',
            ]);

            // Create student profile
            $jenisKelamin = fake()->randomElement(['L', 'P']);
            $randomKelas = $kelasList->random();

            StudentProfile::create([
                'user_id' => $user->id,
                'nis' => '2024' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'nisn' => '0' . fake()->unique()->numberBetween(100000000, 999999999),
                'tempat_lahir' => fake()->city(),
                'tanggal_lahir' => fake()->date('Y-m-d', '2010-01-01'),
                'kelas_id' => $randomKelas->id,
                'nomor_telepon_orangtua' => fake()->phoneNumber(),
                'jenis_kelamin' => $jenisKelamin,
                'is_active' => true,
            ]);
        }

        $this->command->info('50 siswa berhasil dibuat dan didistribusikan ke kelas-kelas yang ada!');
    }
}
