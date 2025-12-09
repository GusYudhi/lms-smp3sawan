<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\MataPelajaran;
use App\Models\Semester;

class MataPelajaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil semester aktif atau semester pertama
        $semester = Semester::where('is_active', true)->first()
                    ?? Semester::first();

        if (!$semester) {
            $this->command->error('Tidak ada semester. Jalankan seeder untuk Semester terlebih dahulu.');
            return;
        }

        $mataPelajaran = [
            // Kelompok A (Umum)
            ['nama_mapel' => 'Pendidikan Agama Islam', 'kode_mapel' => 'PAI'],
            ['nama_mapel' => 'Pendidikan Pancasila dan Kewarganegaraan', 'kode_mapel' => 'PPKN'],
            ['nama_mapel' => 'Bahasa Indonesia', 'kode_mapel' => 'BIND'],
            ['nama_mapel' => 'Matematika', 'kode_mapel' => 'MTK'],
            ['nama_mapel' => 'Ilmu Pengetahuan Alam', 'kode_mapel' => 'IPA'],
            ['nama_mapel' => 'Ilmu Pengetahuan Sosial', 'kode_mapel' => 'IPS'],
            ['nama_mapel' => 'Bahasa Inggris', 'kode_mapel' => 'BING'],

            // Kelompok B (Umum)
            ['nama_mapel' => 'Seni Budaya', 'kode_mapel' => 'SB'],
            ['nama_mapel' => 'Pendidikan Jasmani, Olahraga, dan Kesehatan', 'kode_mapel' => 'PJOK'],
            ['nama_mapel' => 'Prakarya', 'kode_mapel' => 'PKK'],

            // Muatan Lokal
            ['nama_mapel' => 'Bahasa Daerah (Bali)', 'kode_mapel' => 'BDAE'],
            ['nama_mapel' => 'Informatika', 'kode_mapel' => 'INF'],
        ];

        foreach ($mataPelajaran as $mapel) {
            MataPelajaran::firstOrCreate(
                [
                    'kode_mapel' => $mapel['kode_mapel'],
                    'semester_id' => $semester->id
                ],
                [
                    'nama_mapel' => $mapel['nama_mapel']
                ]
            );
        }

        $this->command->info('Seeder Mata Pelajaran berhasil dijalankan!');
        $this->command->info('Total: ' . count($mataPelajaran) . ' mata pelajaran');
    }
}
