<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\GuruProfile;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use Illuminate\Support\Facades\Hash;

class GuruSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teachers = [
            // 1. Matematika (MTK) - Utama
            [
                'user' => [
                    'name' => 'Dr. Ahmad Hidayat, M.Pd',
                    'email' => 'ahmad.hidayat@guru.id',
                    'role' => 'guru',
                    'password' => Hash::make('12345678')
                ],
                'profile' => [
                    'nama' => 'Dr. Ahmad Hidayat, M.Pd',
                    'nip' => '196801011990031001',
                    'kode_guru' => 'AHM',
                    'nomor_telepon' => '081234567890',
                    'email' => 'ahmad.hidayat@guru.id',
                    'jenis_kelamin' => 'L',
                    'tempat_lahir' => 'Singaraja',
                    'tanggal_lahir' => '1968-01-01',
                    'status_kepegawaian' => 'PNS',
                    'golongan' => 'IV/a',
                    'wali_kelas' => ['9', 'A'],
                    'password' => Hash::make('12345678'),
                    'is_active' => true
                ],
                'mapel' => 'MTK'
            ],
            // 2. Bahasa Indonesia (BIND) - Utama
            [
                'user' => [
                    'name' => 'Dra. Siti Nurhaliza, M.Pd',
                    'email' => 'siti.nurhaliza@guru.id',
                    'role' => 'guru',
                    'password' => Hash::make('12345678')
                ],
                'profile' => [
                    'nama' => 'Dra. Siti Nurhaliza, M.Pd',
                    'nip' => '197003151995122001',
                    'kode_guru' => 'STN',
                    'nomor_telepon' => '081234567891',
                    'email' => 'siti.nurhaliza@guru.id',
                    'jenis_kelamin' => 'P',
                    'tempat_lahir' => 'Buleleng',
                    'tanggal_lahir' => '1970-03-15',
                    'status_kepegawaian' => 'PNS',
                    'golongan' => 'IV/a',
                    'wali_kelas' => ['7', 'A'],
                    'password' => Hash::make('12345678'),
                    'is_active' => true
                ],
                'mapel' => 'BIND'
            ],
            // 3. Ilmu Pengetahuan Alam (IPA) - Utama
            [
                'user' => [
                    'name' => 'I Ketut Sukadana, S.Pd',
                    'email' => 'ketut.sukadana@guru.id',
                    'role' => 'guru',
                    'password' => Hash::make('12345678')
                ],
                'profile' => [
                    'nama' => 'I Ketut Sukadana, S.Pd',
                    'nip' => '197505202000031002',
                    'kode_guru' => 'SUK',
                    'nomor_telepon' => '081234567892',
                    'email' => 'ketut.sukadana@guru.id',
                    'jenis_kelamin' => 'L',
                    'tempat_lahir' => 'Sawan',
                    'tanggal_lahir' => '1975-05-20',
                    'status_kepegawaian' => 'PNS',
                    'golongan' => 'III/d',
                    'wali_kelas' => ['7', 'B'],
                    'password' => Hash::make('12345678'),
                    'is_active' => true
                ],
                'mapel' => 'IPA'
            ],
            // 4. Bahasa Inggris (BING) - Utama
            [
                'user' => [
                    'name' => 'Kadek Budi Artana, S.Pd',
                    'email' => 'budi.artana@guru.id',
                    'role' => 'guru',
                    'password' => Hash::make('12345678')
                ],
                'profile' => [
                    'nama' => 'Kadek Budi Artana, S.Pd',
                    'nip' => '198008102005011003',
                    'kode_guru' => 'BUD',
                    'nomor_telepon' => '081234567893',
                    'email' => 'budi.artana@guru.id',
                    'jenis_kelamin' => 'L',
                    'tempat_lahir' => 'Singaraja',
                    'tanggal_lahir' => '1980-08-10',
                    'status_kepegawaian' => 'PNS',
                    'golongan' => 'III/c',
                    'wali_kelas' => ['7', 'C'],
                    'password' => Hash::make('12345678'),
                    'is_active' => true
                ],
                'mapel' => 'BING'
            ],
            // 5. Ilmu Pengetahuan Sosial (IPS) - Utama
            [
                'user' => [
                    'name' => 'Ni Made Dewi Astuti, S.Pd',
                    'email' => 'dewi.astuti@guru.id',
                    'role' => 'guru',
                    'password' => Hash::make('12345678')
                ],
                'profile' => [
                    'nama' => 'Ni Made Dewi Astuti, S.Pd',
                    'nip' => '198202142006042004',
                    'kode_guru' => 'DEW',
                    'nomor_telepon' => '081234567894',
                    'email' => 'dewi.astuti@guru.id',
                    'jenis_kelamin' => 'P',
                    'tempat_lahir' => 'Denpasar',
                    'tanggal_lahir' => '1982-02-14',
                    'status_kepegawaian' => 'PNS',
                    'golongan' => 'III/c',
                    'wali_kelas' => ['7', 'D'],
                    'password' => Hash::make('12345678'),
                    'is_active' => true
                ],
                'mapel' => 'IPS'
            ],
            // 6. PPKN - Utama
            [
                'user' => [
                    'name' => 'Nyoman Ariani, S.Pd',
                    'email' => 'nyoman.ariani@guru.id',
                    'role' => 'guru',
                    'password' => Hash::make('12345678')
                ],
                'profile' => [
                    'nama' => 'Nyoman Ariani, S.Pd',
                    'nip' => '198507222009022005',
                    'kode_guru' => 'ARI',
                    'nomor_telepon' => '081234567895',
                    'email' => 'nyoman.ariani@guru.id',
                    'jenis_kelamin' => 'P',
                    'tempat_lahir' => 'Singaraja',
                    'tanggal_lahir' => '1985-07-22',
                    'status_kepegawaian' => 'PNS',
                    'golongan' => 'III/b',
                    'wali_kelas' => ['7', 'E'],
                    'password' => Hash::make('12345678'),
                    'is_active' => true
                ],
                'mapel' => 'PPKN'
            ],
            // 7. Pendidikan Agama Islam (PAI)
            [
                'user' => [
                    'name' => 'H. Abdul Rahman, S.Ag',
                    'email' => 'abdul.rahman@guru.id',
                    'role' => 'guru',
                    'password' => Hash::make('12345678')
                ],
                'profile' => [
                    'nama' => 'H. Abdul Rahman, S.Ag',
                    'nip' => '197211111998031006',
                    'kode_guru' => 'ABD',
                    'nomor_telepon' => '081234567896',
                    'email' => 'abdul.rahman@guru.id',
                    'jenis_kelamin' => 'L',
                    'tempat_lahir' => 'Banyuwangi',
                    'tanggal_lahir' => '1972-11-11',
                    'status_kepegawaian' => 'PNS',
                    'golongan' => 'IV/a',
                    'wali_kelas' => ['8', 'A'],
                    'password' => Hash::make('12345678'),
                    'is_active' => true
                ],
                'mapel' => 'PAI'
            ],
            // 8. PJOK
            [
                'user' => [
                    'name' => 'Gede Agus Pratama, S.Pd',
                    'email' => 'agus.pratama@guru.id',
                    'role' => 'guru',
                    'password' => Hash::make('12345678')
                ],
                'profile' => [
                    'nama' => 'Gede Agus Pratama, S.Pd',
                    'nip' => '198812052011011007',
                    'kode_guru' => 'AGU',
                    'nomor_telepon' => '081234567897',
                    'email' => 'agus.pratama@guru.id',
                    'jenis_kelamin' => 'L',
                    'tempat_lahir' => 'Sawan',
                    'tanggal_lahir' => '1988-12-05',
                    'status_kepegawaian' => 'PPPK',
                    'golongan' => 'IX',
                    'wali_kelas' => ['8', 'B'],
                    'password' => Hash::make('12345678'),
                    'is_active' => true
                ],
                'mapel' => 'PJOK'
            ],
            // 9. Seni Budaya (SB)
            [
                'user' => [
                    'name' => 'Putu Indah Lestari, S.Sn',
                    'email' => 'indah.lestari@guru.id',
                    'role' => 'guru',
                    'password' => Hash::make('12345678')
                ],
                'profile' => [
                    'nama' => 'Putu Indah Lestari, S.Sn',
                    'nip' => '199004182014022008',
                    'kode_guru' => 'IND',
                    'nomor_telepon' => '081234567898',
                    'email' => 'indah.lestari@guru.id',
                    'jenis_kelamin' => 'P',
                    'tempat_lahir' => 'Singaraja',
                    'tanggal_lahir' => '1990-04-18',
                    'status_kepegawaian' => 'PNS',
                    'golongan' => 'III/b',
                    'wali_kelas' => ['8', 'C'],
                    'password' => Hash::make('12345678'),
                    'is_active' => true
                ],
                'mapel' => 'SB'
            ],
            // 10. Informatika (INF)
            [
                'user' => [
                    'name' => 'Komang Suastika, S.Kom',
                    'email' => 'komang.suastika@guru.id',
                    'role' => 'guru',
                    'password' => Hash::make('12345678')
                ],
                'profile' => [
                    'nama' => 'Komang Suastika, S.Kom',
                    'nip' => '199209302016011009',
                    'kode_guru' => 'SST',
                    'nomor_telepon' => '081234567899',
                    'email' => 'komang.suastika@guru.id',
                    'jenis_kelamin' => 'L',
                    'tempat_lahir' => 'Sawan',
                    'tanggal_lahir' => '1992-09-30',
                    'status_kepegawaian' => 'PPPK',
                    'golongan' => 'IX',
                    'wali_kelas' => ['8', 'D'],
                    'password' => Hash::make('12345678'),
                    'is_active' => true
                ],
                'mapel' => 'INF'
            ],
            // 11. Prakarya (PKK)
            [
                'user' => [
                    'name' => 'Luh Putu Dian Cahyani, S.Pd',
                    'email' => 'dian.cahyani@guru.id',
                    'role' => 'guru',
                    'password' => Hash::make('12345678')
                ],
                'profile' => [
                    'nama' => 'Luh Putu Dian Cahyani, S.Pd',
                    'nip' => '199306122018032010',
                    'kode_guru' => 'DIA',
                    'nomor_telepon' => '081234567810',
                    'email' => 'dian.cahyani@guru.id',
                    'jenis_kelamin' => 'P',
                    'tempat_lahir' => 'Singaraja',
                    'tanggal_lahir' => '1993-06-12',
                    'status_kepegawaian' => 'HONORER',
                    'golongan' => 'III/a',
                    'wali_kelas' => ['8', 'E'],
                    'password' => Hash::make('12345678'),
                    'is_active' => true
                ],
                'mapel' => 'PKK'
            ],
            // 12. Bahasa Daerah Bali (BDAE)
            [
                'user' => [
                    'name' => 'I Made Rai Semara, S.Pd',
                    'email' => 'rai.semara@guru.id',
                    'role' => 'guru',
                    'password' => Hash::make('12345678')
                ],
                'profile' => [
                    'nama' => 'I Made Rai Semara, S.Pd',
                    'nip' => '198603252010011011',
                    'kode_guru' => 'RAI',
                    'nomor_telepon' => '081234567811',
                    'email' => 'rai.semara@guru.id',
                    'jenis_kelamin' => 'L',
                    'tempat_lahir' => 'Sawan',
                    'tanggal_lahir' => '1986-03-25',
                    'status_kepegawaian' => 'PNS',
                    'golongan' => 'III/c',
                    'wali_kelas' => ['9', 'B'],
                    'password' => Hash::make('12345678'),
                    'is_active' => true
                ],
                'mapel' => 'BDAE'
            ],
            // 13. Matematika (MTK) - Pendamping
            [
                'user' => [
                    'name' => 'Kadek Suardika, S.Pd',
                    'email' => 'kadek.suardika@guru.id',
                    'role' => 'guru',
                    'password' => Hash::make('12345678')
                ],
                'profile' => [
                    'nama' => 'Kadek Suardika, S.Pd',
                    'nip' => '197904122003121002',
                    'kode_guru' => 'SDA',
                    'nomor_telepon' => '081234567812',
                    'email' => 'kadek.suardika@guru.id',
                    'jenis_kelamin' => 'L',
                    'tempat_lahir' => 'Singaraja',
                    'tanggal_lahir' => '1979-04-12',
                    'status_kepegawaian' => 'PNS',
                    'golongan' => 'III/d',
                    'wali_kelas' => ['9', 'C'],
                    'password' => Hash::make('12345678'),
                    'is_active' => true
                ],
                'mapel' => 'MTK'
            ],
            // 14. Bahasa Indonesia (BIND) - Pendamping
            [
                'user' => [
                    'name' => 'I Wayan Eka Putra, S.Pd',
                    'email' => 'wayan.ekaputra@guru.id',
                    'role' => 'guru',
                    'password' => Hash::make('12345678')
                ],
                'profile' => [
                    'nama' => 'I Wayan Eka Putra, S.Pd',
                    'nip' => '198305102008011004',
                    'kode_guru' => 'EKA',
                    'nomor_telepon' => '081234567813',
                    'email' => 'wayan.ekaputra@guru.id',
                    'jenis_kelamin' => 'L',
                    'tempat_lahir' => 'Denpasar',
                    'tanggal_lahir' => '1983-05-10',
                    'status_kepegawaian' => 'PNS',
                    'golongan' => 'III/c',
                    'wali_kelas' => ['9', 'D'],
                    'password' => Hash::make('12345678'),
                    'is_active' => true
                ],
                'mapel' => 'BIND'
            ],
            // 15. Ilmu Pengetahuan Alam (IPA) - Pendamping
            [
                'user' => [
                    'name' => 'Ni Komang Sri Wahyuni, S.Pd',
                    'email' => 'sri.wahyuni@guru.id',
                    'role' => 'guru',
                    'password' => Hash::make('12345678')
                ],
                'profile' => [
                    'nama' => 'Ni Komang Sri Wahyuni, S.Pd',
                    'nip' => '198411082009032006',
                    'kode_guru' => 'SRI',
                    'nomor_telepon' => '081234567814',
                    'email' => 'sri.wahyuni@guru.id',
                    'jenis_kelamin' => 'P',
                    'tempat_lahir' => 'Singaraja',
                    'tanggal_lahir' => '1984-11-08',
                    'status_kepegawaian' => 'PNS',
                    'golongan' => 'III/c',
                    'wali_kelas' => ['9', 'E'],
                    'password' => Hash::make('12345678'),
                    'is_active' => true
                ],
                'mapel' => 'IPA'
            ],
            // 16. Bahasa Inggris (BING) - Pendamping
            [
                'user' => [
                    'name' => 'Luh Ayu Kartika, S.Pd',
                    'email' => 'ayu.kartika@guru.id',
                    'role' => 'guru',
                    'password' => Hash::make('12345678')
                ],
                'profile' => [
                    'nama' => 'Luh Ayu Kartika, S.Pd',
                    'nip' => '198709142010012012',
                    'kode_guru' => 'AYU',
                    'nomor_telepon' => '081234567815',
                    'email' => 'ayu.kartika@guru.id',
                    'jenis_kelamin' => 'P',
                    'tempat_lahir' => 'Tabanan',
                    'tanggal_lahir' => '1987-09-14',
                    'status_kepegawaian' => 'PNS',
                    'golongan' => 'III/b',
                    'password' => Hash::make('12345678'),
                    'is_active' => true
                ],
                'mapel' => 'BING'
            ],
            // 17. Ilmu Pengetahuan Sosial (IPS) - Pendamping
            [
                'user' => [
                    'name' => 'Made Agus Wirawan, S.Pd',
                    'email' => 'agus.wirawan@guru.id',
                    'role' => 'guru',
                    'password' => Hash::make('12345678')
                ],
                'profile' => [
                    'nama' => 'Made Agus Wirawan, S.Pd',
                    'nip' => '198506182009041005',
                    'kode_guru' => 'WIR',
                    'nomor_telepon' => '081234567816',
                    'email' => 'agus.wirawan@guru.id',
                    'jenis_kelamin' => 'L',
                    'tempat_lahir' => 'Singaraja',
                    'tanggal_lahir' => '1985-06-18',
                    'status_kepegawaian' => 'PNS',
                    'golongan' => 'III/b',
                    'password' => Hash::make('12345678'),
                    'is_active' => true
                ],
                'mapel' => 'IPS'
            ],
            // 18. PPKN - Pendamping
            [
                'user' => [
                    'name' => 'Ketut Suastini, S.Pd',
                    'email' => 'ketut.suastini@guru.id',
                    'role' => 'guru',
                    'password' => Hash::make('12345678')
                ],
                'profile' => [
                    'nama' => 'Ketut Suastini, S.Pd',
                    'nip' => '198801202011012009',
                    'kode_guru' => 'KST',
                    'nomor_telepon' => '081234567817',
                    'email' => 'ketut.suastini@guru.id',
                    'jenis_kelamin' => 'P',
                    'tempat_lahir' => 'Buleleng',
                    'tanggal_lahir' => '1988-01-20',
                    'status_kepegawaian' => 'PPPK',
                    'golongan' => 'IX',
                    'password' => Hash::make('12345678'),
                    'is_active' => true
                ],
                'mapel' => 'PPKN'
            ],
        ];

        foreach ($teachers as $teacherData) {
            // Create or update user
            $user = User::updateOrCreate(
                ['email' => $teacherData['user']['email']],
                $teacherData['user']
            );

            // Get kelas_id for wali kelas
            $kelasId = null;
            if (isset($teacherData['profile']['wali_kelas'])) {
                $kelas = Kelas::where('tingkat', $teacherData['profile']['wali_kelas'][0])
                              ->where('nama_kelas', $teacherData['profile']['wali_kelas'][1])
                              ->first();
                $kelasId = $kelas?->id;
            }

            // Get mata_pelajaran_id by kode or name
            $mapelId = null;
            if (isset($teacherData['mapel'])) {
                $mapel = MataPelajaran::where('kode_mapel', $teacherData['mapel'])
                            ->orWhere('nama_mapel', 'like', '%' . $teacherData['mapel'] . '%')
                            ->first();
                $mapelId = $mapel?->id;
            }

            // Create or update guru profile
            $profileData = $teacherData['profile'];
            $profileData['user_id'] = $user->id;
            $profileData['kelas_id'] = $kelasId;
            $profileData['mata_pelajaran_id'] = $mapelId;
            unset($profileData['wali_kelas']);

            GuruProfile::updateOrCreate(
                ['user_id' => $user->id],
                $profileData
            );
        }

        $this->command->info('Guru seeder berhasil dijalankan! Total: ' . count($teachers) . ' guru.');
    }
}
