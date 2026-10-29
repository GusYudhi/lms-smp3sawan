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
        // 1. Ambil semester aktif
        $semester = Semester::where('is_active', true)->first()
                    ?? Semester::first();

        if (!$semester) {
            $this->command->error('Tidak ada semester aktif. Jalankan seeder Semester terlebih dahulu.');
            return;
        }

        // 2. Ambil master kelas
        $kelasList = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();
        if ($kelasList->isEmpty()) {
            $this->command->error('Tidak ada kelas. Jalankan KelasSeeder terlebih dahulu.');
            return;
        }

        // 3. Ambil master mata pelajaran & guru terhubung
        $mataPelajaranMap = MataPelajaran::all()->keyBy('kode_mapel');
        
        // Ambil guru aktif dengan relasi profile
        $guruList = User::where('role', 'guru')
            ->whereHas('guruProfile', function($q) {
                $q->where('is_active', true);
            })
            ->with(['guruProfile.mataPelajaran'])
            ->get();

        if ($guruList->isEmpty()) {
            $this->command->error('Tidak ada guru dengan profil aktif. Jalankan GuruSeeder terlebih dahulu.');
            return;
        }

        // Group guru by kode_mapel
        $guruByMapel = [];
        foreach ($guruList as $guru) {
            $mapel = $guru->guruProfile?->mataPelajaran;
            if ($mapel) {
                $guruByMapel[$mapel->kode_mapel][] = $guru;
            }
        }

        // Helper untuk mendapatkan guru berdasarkan mapel & indeks alternatif
        $getGuru = function (string $kodeMapel, int $variant = 0) use ($guruByMapel) {
            if (!isset($guruByMapel[$kodeMapel]) || empty($guruByMapel[$kodeMapel])) {
                return null;
            }
            $list = $guruByMapel[$kodeMapel];
            return $list[$variant % count($list)];
        };

        // Bersihkan jadwal lama untuk semester ini agar data konsisten & bebas duplikasi
        JadwalPelajaran::where('semester_id', $semester->id)->delete();

        // Tracker anti-bentrok internal seeder
        $teacherBusy = []; // [$hari][$jamKe][$guruId] = $kelasName
        $classBusy = [];   // [$hari][$jamKe][$kelasId] = true

        $insertSlot = function ($kelas, $hari, $jamKe, $kodeMapel, $variant = 0) use (
            $semester,
            $mataPelajaranMap,
            $getGuru,
            &$teacherBusy,
            &$classBusy,
            &$totalJadwal
        ) {
            $mapel = $mataPelajaranMap->get($kodeMapel);
            if (!$mapel) {
                return;
            }

            $guru = $getGuru($kodeMapel, $variant);
            if (!$guru) {
                return;
            }

            // Validasi kelas tidak bertabrakan
            if (isset($classBusy[$hari][$jamKe][$kelas->id])) {
                throw new \RuntimeException("Konflik Kelas: Kelas {$kelas->tingkat}{$kelas->nama_kelas} sudah terisi pada $hari jam ke-$jamKe");
            }

            // Validasi guru tidak bertabrakan di kelas lain
            if (isset($teacherBusy[$hari][$jamKe][$guru->id])) {
                $prevClass = $teacherBusy[$hari][$jamKe][$guru->id];
                throw new \RuntimeException("Konflik Guru: Guru {$guru->name} bentrok mengajar di $prevClass dan {$kelas->tingkat}{$kelas->nama_kelas} pada $hari jam ke-$jamKe");
            }

            // Simpan jadwal
            JadwalPelajaran::create([
                'semester_id' => $semester->id,
                'kelas_id' => $kelas->id,
                'mata_pelajaran_id' => $mapel->id,
                'guru_id' => $guru->id,
                'hari' => $hari,
                'jam_ke' => $jamKe,
            ]);

            // Tandai occupancy
            $classBusy[$hari][$jamKe][$kelas->id] = true;
            $teacherBusy[$hari][$jamKe][$guru->id] = "{$kelas->tingkat}{$kelas->nama_kelas}";
            $totalJadwal++;
        };

        $insertBlock = function ($kelas, $hari, array $jamList, $kodeMapel, $variant = 0) use ($insertSlot) {
            foreach ($jamList as $jam) {
                $insertSlot($kelas, $hari, $jam, $kodeMapel, $variant);
            }
        };

        $totalJadwal = 0;

        // =========================================================================
        // JADWAL KELAS 7 (7A, 7B, 7C, 7D, 7E) - Conflict Free Schedule Grid
        // =========================================================================
        $k7A = $kelasList->where('tingkat', 7)->where('nama_kelas', 'A')->first();
        $k7B = $kelasList->where('tingkat', 7)->where('nama_kelas', 'B')->first();
        $k7C = $kelasList->where('tingkat', 7)->where('nama_kelas', 'C')->first();
        $k7D = $kelasList->where('tingkat', 7)->where('nama_kelas', 'D')->first();
        $k7E = $kelasList->where('tingkat', 7)->where('nama_kelas', 'E')->first();

        if ($k7A && $k7B && $k7C && $k7D && $k7E) {
            // --- SENIN ---
            // Jam 1-2: 5 mapel berbeda & 5 guru berbeda
            $insertBlock($k7A, 'Senin', [1, 2], 'BIND', 0); // Dra. Siti Nurhaliza
            $insertBlock($k7B, 'Senin', [1, 2], 'MTK', 0);  // Dr. Ahmad Hidayat
            $insertBlock($k7C, 'Senin', [1, 2], 'IPA', 0);  // I Ketut Sukadana
            $insertBlock($k7D, 'Senin', [1, 2], 'BING', 0); // Kadek Budi Artana
            $insertBlock($k7E, 'Senin', [1, 2], 'IPS', 0);  // Ni Made Dewi Astuti

            // Jam 3-4
            $insertBlock($k7A, 'Senin', [3, 4], 'MTK', 0);  // Dr. Ahmad Hidayat
            $insertBlock($k7B, 'Senin', [3, 4], 'BIND', 0); // Dra. Siti Nurhaliza
            $insertBlock($k7C, 'Senin', [3, 4], 'BING', 0); // Kadek Budi Artana
            $insertBlock($k7D, 'Senin', [3, 4], 'IPA', 0);  // I Ketut Sukadana
            $insertBlock($k7E, 'Senin', [3, 4], 'PPKN', 0); // Nyoman Ariani

            // Jam 5-6
            $insertBlock($k7A, 'Senin', [5, 6], 'IPA', 0);  // I Ketut Sukadana
            $insertBlock($k7B, 'Senin', [5, 6], 'BING', 0); // Kadek Budi Artana
            $insertBlock($k7C, 'Senin', [5, 6], 'MTK', 0);  // Dr. Ahmad Hidayat
            $insertBlock($k7D, 'Senin', [5, 6], 'PPKN', 0); // Nyoman Ariani
            $insertBlock($k7E, 'Senin', [5, 6], 'BIND', 0); // Dra. Siti Nurhaliza

            // Jam 7-8
            $insertBlock($k7A, 'Senin', [7, 8], 'BING', 0); // Kadek Budi Artana
            $insertBlock($k7B, 'Senin', [7, 8], 'IPS', 0);  // Ni Made Dewi Astuti
            $insertBlock($k7C, 'Senin', [7, 8], 'PPKN', 0); // Nyoman Ariani
            $insertBlock($k7D, 'Senin', [7, 8], 'MTK', 0);  // Dr. Ahmad Hidayat
            $insertBlock($k7E, 'Senin', [7, 8], 'IPA', 0);  // I Ketut Sukadana

            // --- SELASA ---
            // Jam 1-2
            $insertBlock($k7A, 'Selasa', [1, 2], 'PAI', 0);  // H. Abdul Rahman
            $insertBlock($k7B, 'Selasa', [1, 2], 'PJOK', 0); // Gede Agus Pratama
            $insertBlock($k7C, 'Selasa', [1, 2], 'SB', 0);   // Putu Indah Lestari
            $insertBlock($k7D, 'Selasa', [1, 2], 'INF', 0);  // Komang Suastika
            $insertBlock($k7E, 'Selasa', [1, 2], 'PKK', 0);  // Luh Putu Dian

            // Jam 3-4
            $insertBlock($k7A, 'Selasa', [3, 4], 'PJOK', 0); // Gede Agus Pratama
            $insertBlock($k7B, 'Selasa', [3, 4], 'PAI', 0);  // H. Abdul Rahman
            $insertBlock($k7C, 'Selasa', [3, 4], 'INF', 0);  // Komang Suastika
            $insertBlock($k7D, 'Selasa', [3, 4], 'SB', 0);   // Putu Indah Lestari
            $insertBlock($k7E, 'Selasa', [3, 4], 'BDAE', 0); // I Made Rai Semara

            // Jam 5-6
            $insertBlock($k7A, 'Selasa', [5, 6], 'SB', 0);   // Putu Indah Lestari
            $insertBlock($k7B, 'Selasa', [5, 6], 'BDAE', 0); // I Made Rai Semara
            $insertBlock($k7C, 'Selasa', [5, 6], 'PAI', 0);  // H. Abdul Rahman
            $insertBlock($k7D, 'Selasa', [5, 6], 'PJOK', 0); // Gede Agus Pratama
            $insertBlock($k7E, 'Selasa', [5, 6], 'INF', 0);  // Komang Suastika

            // Jam 7-8
            $insertBlock($k7A, 'Selasa', [7, 8], 'BDAE', 0); // I Made Rai Semara
            $insertBlock($k7B, 'Selasa', [7, 8], 'SB', 0);   // Putu Indah Lestari
            $insertBlock($k7C, 'Selasa', [7, 8], 'PJOK', 0); // Gede Agus Pratama
            $insertBlock($k7D, 'Selasa', [7, 8], 'PAI', 0);  // H. Abdul Rahman
            $insertBlock($k7E, 'Selasa', [7, 8], 'PKK', 0);  // Luh Putu Dian

            // --- RABU ---
            // Jam 1-2
            $insertBlock($k7A, 'Rabu', [1, 2], 'PKK', 0);  // Luh Putu Dian
            $insertBlock($k7B, 'Rabu', [1, 2], 'BDAE', 0); // I Made Rai Semara
            $insertBlock($k7C, 'Rabu', [1, 2], 'PPKN', 0); // Nyoman Ariani
            $insertBlock($k7D, 'Rabu', [1, 2], 'IPS', 0);  // Ni Made Dewi Astuti
            $insertBlock($k7E, 'Rabu', [1, 2], 'SB', 0);   // Putu Indah Lestari

            // Jam 3-4
            $insertBlock($k7A, 'Rabu', [3, 4], 'PPKN', 0); // Nyoman Ariani
            $insertBlock($k7B, 'Rabu', [3, 4], 'PKK', 0);  // Luh Putu Dian
            $insertBlock($k7C, 'Rabu', [3, 4], 'IPS', 0);  // Ni Made Dewi Astuti
            $insertBlock($k7D, 'Rabu', [3, 4], 'BDAE', 0); // I Made Rai Semara
            $insertBlock($k7E, 'Rabu', [3, 4], 'PJOK', 0); // Gede Agus Pratama

            // Jam 5-6
            $insertBlock($k7A, 'Rabu', [5, 6], 'IPS', 0);  // Ni Made Dewi Astuti
            $insertBlock($k7B, 'Rabu', [5, 6], 'PPKN', 0); // Nyoman Ariani
            $insertBlock($k7C, 'Rabu', [5, 6], 'PKK', 0);  // Luh Putu Dian
            $insertBlock($k7D, 'Rabu', [5, 6], 'SB', 0);   // Putu Indah Lestari
            $insertBlock($k7E, 'Rabu', [5, 6], 'PAI', 0);  // H. Abdul Rahman

            // Jam 7-8
            $insertBlock($k7A, 'Rabu', [7, 8], 'INF', 0);  // Komang Suastika
            $insertBlock($k7B, 'Rabu', [7, 8], 'IPS', 0);  // Ni Made Dewi Astuti
            $insertBlock($k7C, 'Rabu', [7, 8], 'BDAE', 0); // I Made Rai Semara
            $insertBlock($k7D, 'Rabu', [7, 8], 'PPKN', 0); // Nyoman Ariani
            $insertBlock($k7E, 'Rabu', [7, 8], 'PKK', 0);  // Luh Putu Dian

            // --- KAMIS (Sesuai hari tampilan Dashboard Guru) ---
            // Jam 1-2: Dr. Ahmad Hidayat HANYA di Kelas 7A! Kelas lain diajar guru masing-masing
            $insertBlock($k7A, 'Kamis', [1, 2], 'MTK', 0);  // Dr. Ahmad Hidayat (Kelas 7A)
            $insertBlock($k7B, 'Kamis', [1, 2], 'BIND', 0); // Dra. Siti Nurhaliza
            $insertBlock($k7C, 'Kamis', [1, 2], 'IPA', 0);  // I Ketut Sukadana
            $insertBlock($k7D, 'Kamis', [1, 2], 'BING', 0); // Kadek Budi Artana
            $insertBlock($k7E, 'Kamis', [1, 2], 'IPS', 0);  // Ni Made Dewi Astuti

            // Jam 3-4: Dr. Ahmad Hidayat mengajar di Kelas 7B
            $insertBlock($k7A, 'Kamis', [3, 4], 'BIND', 0); // Dra. Siti Nurhaliza
            $insertBlock($k7B, 'Kamis', [3, 4], 'MTK', 0);  // Dr. Ahmad Hidayat (Kelas 7B)
            $insertBlock($k7C, 'Kamis', [3, 4], 'BING', 0); // Kadek Budi Artana
            $insertBlock($k7D, 'Kamis', [3, 4], 'IPA', 0);  // I Ketut Sukadana
            $insertBlock($k7E, 'Kamis', [3, 4], 'INF', 0);  // Komang Suastika

            // Jam 5-6: Dr. Ahmad Hidayat mengajar di Kelas 7C
            $insertBlock($k7A, 'Kamis', [5, 6], 'IPA', 0);  // I Ketut Sukadana
            $insertBlock($k7B, 'Kamis', [5, 6], 'INF', 0);  // Komang Suastika
            $insertBlock($k7C, 'Kamis', [5, 6], 'MTK', 0);  // Dr. Ahmad Hidayat (Kelas 7C)
            $insertBlock($k7D, 'Kamis', [5, 6], 'PKK', 0);  // Luh Putu Dian
            $insertBlock($k7E, 'Kamis', [5, 6], 'BDAE', 0); // I Made Rai Semara

            // Jam 7-8: Dr. Ahmad Hidayat mengajar di Kelas 7D
            $insertBlock($k7A, 'Kamis', [7, 8], 'INF', 0);  // Komang Suastika
            $insertBlock($k7B, 'Kamis', [7, 8], 'PKK', 0);  // Luh Putu Dian
            $insertBlock($k7C, 'Kamis', [7, 8], 'BDAE', 0); // I Made Rai Semara
            $insertBlock($k7D, 'Kamis', [7, 8], 'MTK', 0);  // Dr. Ahmad Hidayat (Kelas 7D)
            $insertBlock($k7E, 'Kamis', [7, 8], 'PJOK', 0); // Gede Agus Pratama

            // --- JUMAT ---
            // Jam 1-2
            $insertBlock($k7A, 'Jumat', [1, 2], 'BING', 0); // Kadek Budi Artana
            $insertBlock($k7B, 'Jumat', [1, 2], 'IPA', 0);  // I Ketut Sukadana
            $insertBlock($k7C, 'Jumat', [1, 2], 'BIND', 0); // Dra. Siti Nurhaliza
            $insertBlock($k7D, 'Jumat', [1, 2], 'MTK', 0);  // Dr. Ahmad Hidayat
            $insertBlock($k7E, 'Jumat', [1, 2], 'PPKN', 0); // Nyoman Ariani

            // Jam 3-4
            $insertBlock($k7A, 'Jumat', [3, 4], 'IPA', 0);  // I Ketut Sukadana
            $insertBlock($k7B, 'Jumat', [3, 4], 'BING', 0); // Kadek Budi Artana
            $insertBlock($k7C, 'Jumat', [3, 4], 'MTK', 0);  // Dr. Ahmad Hidayat
            $insertBlock($k7D, 'Jumat', [3, 4], 'BIND', 0); // Dra. Siti Nurhaliza
            $insertBlock($k7E, 'Jumat', [3, 4], 'IPS', 0);  // Ni Made Dewi Astuti

            // Jam 5-6
            $insertBlock($k7A, 'Jumat', [5, 6], 'SB', 0);   // Putu Indah Lestari
            $insertBlock($k7B, 'Jumat', [5, 6], 'BIND', 0); // Dra. Siti Nurhaliza
            $insertBlock($k7C, 'Jumat', [5, 6], 'BING', 0); // Kadek Budi Artana
            $insertBlock($k7D, 'Jumat', [5, 6], 'IPA', 0);  // I Ketut Sukadana
            $insertBlock($k7E, 'Jumat', [5, 6], 'MTK', 0);  // Dr. Ahmad Hidayat
        }

        // =========================================================================
        // JADWAL KELAS 8 (8A-8E) & 9 (9A-9E) - Menggunakan guru pendamping (variant 1)
        // =========================================================================
        // Guru pendamping:
        // MTK variant 1: Kadek Suardika
        // BIND variant 1: I Wayan Eka Putra
        // IPA variant 1: Ni Komang Sri Wahyuni
        // BING variant 1: Luh Ayu Kartika
        // IPS variant 1: Made Agus Wirawan
        // PPKN variant 1: Ketut Suastini
        $k8A = $kelasList->where('tingkat', 8)->where('nama_kelas', 'A')->first();
        $k8B = $kelasList->where('tingkat', 8)->where('nama_kelas', 'B')->first();
        $k8C = $kelasList->where('tingkat', 8)->where('nama_kelas', 'C')->first();
        $k8D = $kelasList->where('tingkat', 8)->where('nama_kelas', 'D')->first();
        $k8E = $kelasList->where('tingkat', 8)->where('nama_kelas', 'E')->first();

        if ($k8A && $k8B && $k8C && $k8D && $k8E) {
            // Hari Senin untuk Kelas 8
            $insertBlock($k8A, 'Senin', [1, 2], 'MTK', 1);  // Kadek Suardika
            $insertBlock($k8B, 'Senin', [1, 2], 'BIND', 1); // I Wayan Eka Putra
            $insertBlock($k8C, 'Senin', [1, 2], 'IPA', 1);  // Ni Komang Sri Wahyuni
            $insertBlock($k8D, 'Senin', [1, 2], 'BING', 1); // Luh Ayu Kartika
            $insertBlock($k8E, 'Senin', [1, 2], 'IPS', 1);  // Made Agus Wirawan

            $insertBlock($k8A, 'Senin', [3, 4], 'BIND', 1); // I Wayan Eka Putra
            $insertBlock($k8B, 'Senin', [3, 4], 'MTK', 1);  // Kadek Suardika
            $insertBlock($k8C, 'Senin', [3, 4], 'BING', 1); // Luh Ayu Kartika
            $insertBlock($k8D, 'Senin', [3, 4], 'IPA', 1);  // Ni Komang Sri Wahyuni
            $insertBlock($k8E, 'Senin', [3, 4], 'PPKN', 1); // Ketut Suastini

            $insertBlock($k8A, 'Senin', [5, 6], 'IPA', 1);  // Ni Komang Sri Wahyuni
            $insertBlock($k8B, 'Senin', [5, 6], 'BING', 1); // Luh Ayu Kartika
            $insertBlock($k8C, 'Senin', [5, 6], 'MTK', 1);  // Kadek Suardika
            $insertBlock($k8D, 'Senin', [5, 6], 'PPKN', 1); // Ketut Suastini
            $insertBlock($k8E, 'Senin', [5, 6], 'BIND', 1); // I Wayan Eka Putra

            $insertBlock($k8A, 'Senin', [7, 8], 'BING', 1); // Luh Ayu Kartika
            $insertBlock($k8B, 'Senin', [7, 8], 'IPS', 1);  // Made Agus Wirawan
            $insertBlock($k8C, 'Senin', [7, 8], 'PPKN', 1); // Ketut Suastini
            $insertBlock($k8D, 'Senin', [7, 8], 'MTK', 1);  // Kadek Suardika
            $insertBlock($k8E, 'Senin', [7, 8], 'IPA', 1);  // Ni Komang Sri Wahyuni

            // Hari Kamis untuk Kelas 8
            $insertBlock($k8A, 'Kamis', [1, 2], 'BIND', 1); // I Wayan Eka Putra
            $insertBlock($k8B, 'Kamis', [1, 2], 'MTK', 1);  // Kadek Suardika
            $insertBlock($k8C, 'Kamis', [1, 2], 'IPA', 1);  // Ni Komang Sri Wahyuni
            $insertBlock($k8D, 'Kamis', [1, 2], 'BING', 1); // Luh Ayu Kartika
            $insertBlock($k8E, 'Kamis', [1, 2], 'PPKN', 1); // Ketut Suastini

            $insertBlock($k8A, 'Kamis', [3, 4], 'MTK', 1);  // Kadek Suardika
            $insertBlock($k8B, 'Kamis', [3, 4], 'BIND', 1); // I Wayan Eka Putra
            $insertBlock($k8C, 'Kamis', [3, 4], 'PPKN', 1); // Ketut Suastini
            $insertBlock($k8D, 'Kamis', [3, 4], 'IPS', 1);  // Made Agus Wirawan
            $insertBlock($k8E, 'Kamis', [3, 4], 'IPA', 1);  // Ni Komang Sri Wahyuni
        }

        // Kelas 9 (9A-9E)
        $k9A = $kelasList->where('tingkat', 9)->where('nama_kelas', 'A')->first();
        $k9B = $kelasList->where('tingkat', 9)->where('nama_kelas', 'B')->first();
        $k9C = $kelasList->where('tingkat', 9)->where('nama_kelas', 'C')->first();
        $k9D = $kelasList->where('tingkat', 9)->where('nama_kelas', 'D')->first();
        $k9E = $kelasList->where('tingkat', 9)->where('nama_kelas', 'E')->first();

        if ($k9A && $k9B && $k9C && $k9D && $k9E) {
            // Hari Kamis jam 5-8 menggunakan guru yang bebas di jam tersebut
            $insertBlock($k9A, 'Kamis', [5, 6], 'BIND', 1); // I Wayan Eka Putra
            $insertBlock($k9B, 'Kamis', [5, 6], 'IPA', 1);  // Ni Komang Sri Wahyuni
            $insertBlock($k9C, 'Kamis', [5, 6], 'BING', 1); // Luh Ayu Kartika
            $insertBlock($k9D, 'Kamis', [5, 6], 'IPS', 1);  // Made Agus Wirawan
            $insertBlock($k9E, 'Kamis', [5, 6], 'PPKN', 1); // Ketut Suastini

            $insertBlock($k9A, 'Kamis', [7, 8], 'IPA', 1);  // Ni Komang Sri Wahyuni
            $insertBlock($k9B, 'Kamis', [7, 8], 'BING', 1); // Luh Ayu Kartika
            $insertBlock($k9C, 'Kamis', [7, 8], 'IPS', 1);  // Made Agus Wirawan
            $insertBlock($k9D, 'Kamis', [7, 8], 'PPKN', 1); // Ketut Suastini
            $insertBlock($k9E, 'Kamis', [7, 8], 'MTK', 1);  // Kadek Suardika
        }

        $this->command->info('Seeder Jadwal Pelajaran berhasil dijalankan!');
        $this->command->info("Total jadwal tersimpan: $totalJadwal slot (100% Bebas Konflik Guru & Kelas)");
    }
}
