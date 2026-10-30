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
            KepalaSekolahSeeder::class,     // Akun Kepala Sekolah
            MataPelajaranSeeder::class,     // Master data mapel (independen)
            TahunPelajaranSeeder::class,    // Dibutuhkan oleh Kelas & Semester
            KelasSeeder::class,             // Dibutuhkan oleh Guru dan Siswa
            JamPelajaranSeeder::class,      // Butuh Semester
            GuruSeeder::class,              // Butuh Kelas & Mapel
            SiswaSeeder::class,             // Setelah kelas tersedia
            JadwalPelajaranSeeder::class,   // Terakhir, butuh Kelas, Mapel, Guru
            SchoolProfileSeeder::class,     // Master profil sekolah & relasi kepala sekolah
        ]);
    }
}
