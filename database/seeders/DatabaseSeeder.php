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

        // Menjalankan seeder dengan urutan yang benar
        $this->call([
            AdminSeeder::class,
            TahunPelajaranSeeder::class,    // Harus pertama karena dibutuhkan oleh Kelas & Semester
            KelasSeeder::class,             // Kedua, karena dibutuhkan oleh Guru dan Siswa
            MataPelajaranSeeder::class,     // Butuh Semester
            JamPelajaranSeeder::class,      // Butuh Semester
            GuruSeeder::class,              // Butuh Kelas & Mapel
            SiswaSeeder::class,             // Setelah kelas tersedia
            JadwalPelajaranSeeder::class,   // Terakhir, butuh Kelas, Mapel, Guru
        ]);
    }
}
