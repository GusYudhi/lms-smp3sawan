<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kelas;

class KelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tingkatList = ['7', '8', '9'];
        $kelasNameList = ['A', 'B', 'C', 'D', 'E'];

        foreach ($tingkatList as $tingkat) {
            foreach ($kelasNameList as $namaKelas) {
                Kelas::firstOrCreate(
                    [
                        'tingkat' => $tingkat,
                        'nama_kelas' => $namaKelas
                    ]
                );
            }
        }

        $this->command->info('Kelas berhasil di-seed! (7A-7E, 8A-8E, 9A-9E)');
    }
}
