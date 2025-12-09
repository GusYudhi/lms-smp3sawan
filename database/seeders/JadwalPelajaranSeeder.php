<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\User;
use App\Models\Semester;

class JadwalPelajaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil semester aktif
        $semester = Semester::where('is_active', true)->first()
                    ?? Semester::first();

        if (!$semester) {
            $this->command->error('Tidak ada semester. Jalankan seeder untuk Semester terlebih dahulu.');
            return;
        }

        // Ambil semua kelas
        $kelasList = Kelas::all();

        if ($kelasList->isEmpty()) {
            $this->command->error('Tidak ada kelas. Buat kelas terlebih dahulu.');
            return;
        }

        // Ambil mata pelajaran
        $mataPelajaran = MataPelajaran::where('semester_id', $semester->id)->get();

        if ($mataPelajaran->isEmpty()) {
            $this->command->error('Tidak ada mata pelajaran. Jalankan MataPelajaranSeeder terlebih dahulu.');
            return;
        }

        // Ambil guru (role = guru)
        $guruList = User::where('role', 'guru')->get();

        if ($guruList->isEmpty()) {
            $this->command->error('Tidak ada guru. Buat user guru terlebih dahulu.');
            return;
        }

        // Hari dalam seminggu
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];

        $totalJadwal = 0;

        // Loop untuk setiap kelas
        foreach ($kelasList as $kelas) {
            // Contoh jadwal untuk kelas 7
            if ($kelas->tingkat == 7) {
                $jadwalKelas7 = [
                    // Senin
                    ['hari' => 'Senin', 'jam_ke' => 1, 'mapel' => 'PPKN'],
                    ['hari' => 'Senin', 'jam_ke' => 2, 'mapel' => 'PPKN'],
                    ['hari' => 'Senin', 'jam_ke' => 3, 'mapel' => 'BIND'],
                    ['hari' => 'Senin', 'jam_ke' => 4, 'mapel' => 'BIND'],
                    ['hari' => 'Senin', 'jam_ke' => 5, 'mapel' => 'MTK'],
                    ['hari' => 'Senin', 'jam_ke' => 6, 'mapel' => 'MTK'],
                    ['hari' => 'Senin', 'jam_ke' => 7, 'mapel' => 'IPA'],
                    ['hari' => 'Senin', 'jam_ke' => 8, 'mapel' => 'IPA'],

                    // Selasa
                    ['hari' => 'Selasa', 'jam_ke' => 1, 'mapel' => 'PAI'],
                    ['hari' => 'Selasa', 'jam_ke' => 2, 'mapel' => 'PAI'],
                    ['hari' => 'Selasa', 'jam_ke' => 3, 'mapel' => 'BING'],
                    ['hari' => 'Selasa', 'jam_ke' => 4, 'mapel' => 'BING'],
                    ['hari' => 'Selasa', 'jam_ke' => 5, 'mapel' => 'IPS'],
                    ['hari' => 'Selasa', 'jam_ke' => 6, 'mapel' => 'IPS'],
                    ['hari' => 'Selasa', 'jam_ke' => 7, 'mapel' => 'MTK'],
                    ['hari' => 'Selasa', 'jam_ke' => 8, 'mapel' => 'MTK'],

                    // Rabu
                    ['hari' => 'Rabu', 'jam_ke' => 1, 'mapel' => 'SB'],
                    ['hari' => 'Rabu', 'jam_ke' => 2, 'mapel' => 'SB'],
                    ['hari' => 'Rabu', 'jam_ke' => 3, 'mapel' => 'PJOK'],
                    ['hari' => 'Rabu', 'jam_ke' => 4, 'mapel' => 'PJOK'],
                    ['hari' => 'Rabu', 'jam_ke' => 5, 'mapel' => 'PJOK'],
                    ['hari' => 'Rabu', 'jam_ke' => 6, 'mapel' => 'BDAE'],
                    ['hari' => 'Rabu', 'jam_ke' => 7, 'mapel' => 'BDAE'],
                    ['hari' => 'Rabu', 'jam_ke' => 8, 'mapel' => 'INF'],

                    // Kamis
                    ['hari' => 'Kamis', 'jam_ke' => 1, 'mapel' => 'BIND'],
                    ['hari' => 'Kamis', 'jam_ke' => 2, 'mapel' => 'BIND'],
                    ['hari' => 'Kamis', 'jam_ke' => 3, 'mapel' => 'MTK'],
                    ['hari' => 'Kamis', 'jam_ke' => 4, 'mapel' => 'MTK'],
                    ['hari' => 'Kamis', 'jam_ke' => 5, 'mapel' => 'IPA'],
                    ['hari' => 'Kamis', 'jam_ke' => 6, 'mapel' => 'IPA'],
                    ['hari' => 'Kamis', 'jam_ke' => 7, 'mapel' => 'PKK'],
                    ['hari' => 'Kamis', 'jam_ke' => 8, 'mapel' => 'PKK'],

                    // Jumat
                    ['hari' => 'Jumat', 'jam_ke' => 1, 'mapel' => 'IPS'],
                    ['hari' => 'Jumat', 'jam_ke' => 2, 'mapel' => 'IPS'],
                    ['hari' => 'Jumat', 'jam_ke' => 3, 'mapel' => 'BING'],
                    ['hari' => 'Jumat', 'jam_ke' => 4, 'mapel' => 'BING'],
                    ['hari' => 'Jumat', 'jam_ke' => 5, 'mapel' => 'INF'],
                    ['hari' => 'Jumat', 'jam_ke' => 6, 'mapel' => 'INF'],
                ];

                foreach ($jadwalKelas7 as $jadwal) {
                    $mapel = $mataPelajaran->where('kode_mapel', $jadwal['mapel'])->first();

                    if (!$mapel) {
                        continue;
                    }

                    // Pilih guru secara acak
                    $guru = $guruList->random();

                    JadwalPelajaran::firstOrCreate(
                        [
                            'kelas_id' => $kelas->id,
                            'mata_pelajaran_id' => $mapel->id,
                            'hari' => $jadwal['hari'],
                            'jam_ke' => $jadwal['jam_ke'],
                            'semester_id' => $semester->id
                        ],
                        [
                            'guru_id' => $guru->id
                        ]
                    );

                    $totalJadwal++;
                }
            }

            // Untuk kelas 8 dan 9, buat jadwal sederhana (opsional)
            if ($kelas->tingkat == 8 || $kelas->tingkat == 9) {
                // Buat jadwal sederhana untuk hari Senin saja sebagai contoh
                $jamList = [1, 2, 3, 4, 5, 6, 7, 8];
                foreach ($jamList as $jam) {
                    $mapel = $mataPelajaran->random();
                    $guru = $guruList->random();

                    JadwalPelajaran::firstOrCreate(
                        [
                            'kelas_id' => $kelas->id,
                            'mata_pelajaran_id' => $mapel->id,
                            'hari' => 'Senin',
                            'jam_ke' => $jam,
                            'semester_id' => $semester->id
                        ],
                        [
                            'guru_id' => $guru->id
                        ]
                    );

                    $totalJadwal++;
                }
            }
        }

        $this->command->info('Seeder Jadwal Pelajaran berhasil dijalankan!');
        $this->command->info('Total: ' . $totalJadwal . ' jadwal pelajaran');
    }
}
