<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kelas;
use App\Models\MataPelajaran;

class JadwalSeeder extends Seeder
{
    public function run(): void
    {
        // Seed Kelas
        $kelas = [
            ['nama_kelas' => '7A', 'tingkat' => '7'],
            ['nama_kelas' => '7B', 'tingkat' => '7'],
            ['nama_kelas' => '8A', 'tingkat' => '8'],
            ['nama_kelas' => '8B', 'tingkat' => '8'],
            ['nama_kelas' => '9A', 'tingkat' => '9'],
            ['nama_kelas' => '9B', 'tingkat' => '9'],
        ];

        foreach ($kelas as $k) {
            Kelas::firstOrCreate($k);
        }

        // Seed Mata Pelajaran
        $mapels = [
            ['nama_mapel' => 'Matematika', 'kode_mapel' => 'MTK'],
            ['nama_mapel' => 'Bahasa Indonesia', 'kode_mapel' => 'BIN'],
            ['nama_mapel' => 'Bahasa Inggris', 'kode_mapel' => 'BIG'],
            ['nama_mapel' => 'Ilmu Pengetahuan Alam', 'kode_mapel' => 'IPA'],
            ['nama_mapel' => 'Ilmu Pengetahuan Sosial', 'kode_mapel' => 'IPS'],
            ['nama_mapel' => 'Pendidikan Agama Islam', 'kode_mapel' => 'PAI'],
            ['nama_mapel' => 'PJOK', 'kode_mapel' => 'PJOK'],
            ['nama_mapel' => 'Seni Budaya', 'kode_mapel' => 'SBY'],
        ];

        foreach ($mapels as $m) {
            MataPelajaran::firstOrCreate($m);
        }
    }
}
