<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\StudentProfile;
use Illuminate\Support\Facades\Hash;

class SiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 50 student users with profiles
        for ($i = 1; $i <= 50; $i++) {
            // Create user
            $user = User::create([
                'name' => fake()->name(),
                'email' => fake()->unique()->safeEmail(),
                'password' => Hash::make('password'),
                'role' => 'siswa',
                'nomor_induk' => '2024' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'nomor_telepon' => fake()->phoneNumber(),
                'jenis_kelamin' => fake()->randomElement(['L', 'P']),
                'tempat_lahir' => fake()->city(),
                'tanggal_lahir' => fake()->date('Y-m-d', '2010-01-01'),
            ]);

            // Create student profile
            $jenisKelamin = fake()->randomElement(['L', 'P']);

            StudentProfile::create([
                'user_id' => $user->id,
                'nis' => '2024' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'nisn' => '0' . fake()->unique()->numberBetween(100000000, 999999999),
                'tempat_lahir' => $user->tempat_lahir,
                'tanggal_lahir' => $user->tanggal_lahir,
                'kelas' => fake()->randomElement(['7A', '7B', '8A', '8B', '9A', '9B']),
                'nomor_telepon_orangtua' => fake()->phoneNumber(),
                'jenis_kelamin' => $jenisKelamin,
                'is_active' => true,
            ]);
        }
    }
}
