<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MataPelajaran;

class MataPelajaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mataPelajaran = [
            // Kelompok A (Umum)
            ['nama_mapel' => 'Pendidikan Agama Islam', 'kode_mapel' => 'PAI', 'is_universal' => false],
            ['nama_mapel' => 'Pendidikan Pancasila dan Kewarganegaraan', 'kode_mapel' => 'PPKN', 'is_universal' => false],
            ['nama_mapel' => 'Bahasa Indonesia', 'kode_mapel' => 'BIND', 'is_universal' => false],
            ['nama_mapel' => 'Matematika', 'kode_mapel' => 'MTK', 'is_universal' => false],
            ['nama_mapel' => 'Ilmu Pengetahuan Alam', 'kode_mapel' => 'IPA', 'is_universal' => false],
            ['nama_mapel' => 'Ilmu Pengetahuan Sosial', 'kode_mapel' => 'IPS', 'is_universal' => false],
            ['nama_mapel' => 'Bahasa Inggris', 'kode_mapel' => 'BING', 'is_universal' => false],

            // Kelompok B (Umum)
            ['nama_mapel' => 'Seni Budaya', 'kode_mapel' => 'SB', 'is_universal' => false],
            ['nama_mapel' => 'Pendidikan Jasmani, Olahraga, dan Kesehatan', 'kode_mapel' => 'PJOK', 'is_universal' => false],
            ['nama_mapel' => 'Prakarya', 'kode_mapel' => 'PKK', 'is_universal' => false],

            // Muatan Lokal
            ['nama_mapel' => 'Bahasa Daerah (Bali)', 'kode_mapel' => 'BDAE', 'is_universal' => false],
            ['nama_mapel' => 'Informatika', 'kode_mapel' => 'INF', 'is_universal' => false],
        ];

        foreach ($mataPelajaran as $mapel) {
            MataPelajaran::firstOrCreate(
                ['kode_mapel' => $mapel['kode_mapel']],
                [
                    'nama_mapel' => $mapel['nama_mapel'],
                    'is_universal' => $mapel['is_universal'],
                    'is_active' => true,
                ]
            );
        }

        $this->command->info('Seeder Mata Pelajaran (Master) berhasil dijalankan!');
        $this->command->info('Total: ' . count($mataPelajaran) . ' mata pelajaran');
    }
}
