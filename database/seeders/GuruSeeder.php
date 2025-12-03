<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\GuruProfile;
use App\Models\Kelas;
use Illuminate\Support\Facades\Hash;

class GuruSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teachers = [
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
                    'nomor_telepon' => '081234567890',
                    'email' => 'ahmad.hidayat@guru.id',
                    'jenis_kelamin' => 'L',
                    'tempat_lahir' => 'Singaraja',
                    'tanggal_lahir' => '1968-01-01',
                    'status_kepegawaian' => 'PNS',
                    'golongan' => 'IV/a',
                    'mata_pelajaran' => ['Matematika'],
                    'wali_kelas' => ['9', 'A'],
                    'password' => Hash::make('12345678'),
                    'is_active' => true
                ]
            ],
        ];

        foreach ($teachers as $teacherData) {
            // Create user
            $user = User::firstOrCreate(
                ['email' => $teacherData['user']['email']],
                $teacherData['user']
            );

            // Get kelas_id
            $kelasId = null;
            if (isset($teacherData['profile']['wali_kelas'])) {
                $kelas = Kelas::where('tingkat', $teacherData['profile']['wali_kelas'][0])
                              ->where('nama_kelas', $teacherData['profile']['wali_kelas'][1])
                              ->first();
                $kelasId = $kelas?->id;
            }

            // Create guru profile
            $profileData = $teacherData['profile'];
            $profileData['user_id'] = $user->id;
            $profileData['kelas_id'] = $kelasId;
            unset($profileData['wali_kelas']);

            GuruProfile::firstOrCreate(
                ['user_id' => $user->id],
                $profileData
            );
        }

        $this->command->info('Guru seeder berhasil dijalankan!');
    }
}
