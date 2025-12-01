<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kelas;
use App\Models\TahunPelajaran;

class KelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tingkatList = ['7', '8', '9'];
        $kelasNameList = ['A', 'B', 'C', 'D', 'E'];

        // Get active tahun pelajaran
        $tahunPelajaran = TahunPelajaran::where('is_active', true)->first();

        // Default tahun angkatan based on tingkat
        // Tingkat 7 -> angkatan tahun ini
        // Tingkat 8 -> angkatan tahun lalu
        // Tingkat 9 -> angkatan 2 tahun lalu
        $currentYear = now()->year;

        foreach ($tingkatList as $tingkat) {
            // Calculate tahun angkatan
            $tahunAngkatan = $currentYear - ($tingkat - 7);

            foreach ($kelasNameList as $namaKelas) {
                Kelas::firstOrCreate(
                    [
                        'tingkat' => $tingkat,
                        'nama_kelas' => $namaKelas
                    ],
                    [
                        'tahun_angkatan' => $tahunAngkatan,
                        'tahun_pelajaran_id' => $tahunPelajaran?->id,
                    ]
                );
            }
        }

        $this->command->info('Kelas berhasil di-seed! (7A-7E, 8A-8E, 9A-9E)');
        $this->command->info('Dengan tahun angkatan:');
        $this->command->info('- Kelas 7: Angkatan ' . ($currentYear));
        $this->command->info('- Kelas 8: Angkatan ' . ($currentYear - 1));
        $this->command->info('- Kelas 9: Angkatan ' . ($currentYear - 2));
    }
}
