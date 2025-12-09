<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // Create admin user
        User::firstOrCreate([
            'email' => 'admin@gmail.com',
        ], [
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('12345678'),
            'role' => 'admin',
        ]);

        // Menjalankan seeder dengan urutan yang benar
        $this->call([
            TahunPelajaranSeeder::class,    // Harus pertama karena dibutuhkan oleh Kelas
            KelasSeeder::class,             // Kedua, karena dibutuhkan oleh Guru dan Siswa
            GuruSeeder::class,              // Setelah kelas tersedia
            SiswaSeeder::class,             // Setelah kelas tersedia
            MataPelajaranSeeder::class,     // Setelah Semester tersedia
            JamPelajaranSeeder::class,      // Setelah Semester tersedia
            JadwalPelajaranSeeder::class,   // Terakhir, butuh Kelas, Mapel, Guru
        ]);
    }
}
