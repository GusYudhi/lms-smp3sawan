<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\StudentProfile;
use Illuminate\Support\Facades\Hash;

class TestStudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create test students with easy NISN for testing
        $testStudents = [
            [
                'name' => 'Ida Bagus Yudhi Priyatna',
                'email' => 'yudhi@test.com',
                'nisn' => '2215051006',
                'kelas' => '8A'
            ],
            [
                'name' => 'Ahmad Test Siswa',
                'email' => 'ahmad.test@test.com',
                'nisn' => '1234567890',
                'kelas' => '8A'
            ],
            [
                'name' => 'Siti Test Siswa',
                'email' => 'siti.test@test.com',
                'nisn' => '1234567891',
                'kelas' => '8A'
            ],
            [
                'name' => 'Budi Test Siswa',
                'email' => 'budi.test@test.com',
                'nisn' => '1234567892',
                'kelas' => '8B'
            ]
        ];

        foreach ($testStudents as $index => $studentData) {
            // Check if user already exists
            $existingUser = User::where('email', $studentData['email'])->first();
            if ($existingUser) {
                continue; // Skip if already exists
            }

            // Check if NISN already exists
            $existingProfile = StudentProfile::where('nisn', $studentData['nisn'])->first();
            if ($existingProfile) {
                continue; // Skip if already exists
            }

            // Create user
            $user = User::create([
                'name' => $studentData['name'],
                'email' => $studentData['email'],
                'password' => Hash::make('password'),
                'role' => 'siswa',
            ]);

            // Create student profile
            StudentProfile::create([
                'user_id' => $user->id,
                'nis' => 'TEST' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                'nisn' => $studentData['nisn'],
                'tempat_lahir' => 'Jakarta',
                'tanggal_lahir' => '2010-01-01',
                'kelas' => $studentData['kelas'],
                'nomor_telepon_orangtua' => '08123456789' . $index,
                'jenis_kelamin' => $index % 2 == 0 ? 'L' : 'P',
                'is_active' => true,
            ]);
        }
    }
}
