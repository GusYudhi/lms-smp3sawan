<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TahunPelajaran;
use App\Models\Semester;

class TahunPelajaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create current academic year (2024/2025)
        $tahunPelajaran = TahunPelajaran::create([
            'nama' => '2024/2025',
            'tahun_mulai' => 2024,
            'tahun_selesai' => 2025,
            'tanggal_mulai' => '2024-07-15',
            'tanggal_selesai' => '2025-06-30',
            'is_active' => true,
            'keterangan' => 'Tahun Pelajaran 2024/2025',
        ]);

        // Create 2 semesters for this academic year
        Semester::create([
            'tahun_pelajaran_id' => $tahunPelajaran->id,
            'nama' => 'Ganjil',
            'semester_ke' => 1,
            'tanggal_mulai' => '2024-07-15',
            'tanggal_selesai' => '2024-12-31',
            'is_active' => true,
            'keterangan' => 'Semester Ganjil 2024/2025',
        ]);

        Semester::create([
            'tahun_pelajaran_id' => $tahunPelajaran->id,
            'nama' => 'Genap',
            'semester_ke' => 2,
            'tanggal_mulai' => '2025-01-01',
            'tanggal_selesai' => '2025-06-30',
            'is_active' => false,
            'keterangan' => 'Semester Genap 2024/2025',
        ]);

        $this->command->info('Tahun Pelajaran 2024/2025 dengan 2 semester berhasil dibuat!');
        $this->command->info('- Semester Ganjil (Aktif)');
        $this->command->info('- Semester Genap');
    }
}

