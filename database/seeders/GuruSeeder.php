<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class GuruSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $teachers = [
            [
                'name' => 'Dr. Ahmad Hidayat, S.Pd., M.Pd.',
                'email' => 'ahmad.hidayat@smp3sawan.sch.id',
                'nomor_induk' => '196801011990031001',
                'nomor_telepon' => '081234567890',
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Singaraja',
                'tanggal_lahir' => '1968-01-01',
                'status_kepegawaian' => 'PNS',
                'golongan' => 'IV/a',
                'mata_pelajaran' => 'Matematika',
                'wali_kelas' => '9A',
                'role' => 'guru',
                'password' => Hash::make('12345678')
            ],
            [
                'name' => 'Dra. Siti Fatimah, M.Pd.',
                'email' => 'siti.fatimah@smp3sawan.sch.id',
                'nomor_induk' => '197205101995032001',
                'nomor_telepon' => '081234567891',
                'jenis_kelamin' => 'P',
                'tempat_lahir' => 'Buleleng',
                'tanggal_lahir' => '1972-05-10',
                'status_kepegawaian' => 'PNS',
                'golongan' => 'IV/b',
                'mata_pelajaran' => 'Bahasa Indonesia',
                'wali_kelas' => '8B',
                'role' => 'guru',
                'password' => Hash::make('12345678')
            ],
            [
                'name' => 'Muhammad Rizki, S.Pd.',
                'email' => 'muhammad.rizki@smp3sawan.sch.id',
                'nomor_induk' => '198503121010121002',
                'nomor_telepon' => '081234567892',
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Denpasar',
                'tanggal_lahir' => '1985-03-12',
                'status_kepegawaian' => 'PPPK',
                'golongan' => 'III/c',
                'mata_pelajaran' => 'IPA Fisika',
                'wali_kelas' => '7A',
                'role' => 'guru',
                'password' => Hash::make('12345678')
            ],
            [
                'name' => 'Dewi Kartika, S.Pd.',
                'email' => 'dewi.kartika@smp3sawan.sch.id',
                'nomor_induk' => '199012151018032001',
                'nomor_telepon' => '081234567893',
                'jenis_kelamin' => 'P',
                'tempat_lahir' => 'Tabanan',
                'tanggal_lahir' => '1990-12-15',
                'status_kepegawaian' => 'PPPK',
                'golongan' => 'III/b',
                'mata_pelajaran' => 'Bahasa Inggris',
                'wali_kelas' => null,
                'role' => 'guru',
                'password' => Hash::make('12345678')
            ],
            [
                'name' => 'Andi Setiawan, S.Pd.',
                'email' => 'andi.setiawan@smp3sawan.sch.id',
                'nomor_induk' => '199208201120031001',
                'nomor_telepon' => '081234567894',
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Gianyar',
                'tanggal_lahir' => '1992-08-20',
                'status_kepegawaian' => 'Honorer',
                'golongan' => null,
                'mata_pelajaran' => 'Pendidikan Jasmani',
                'wali_kelas' => '8A',
                'role' => 'guru',
                'password' => Hash::make('12345678')
            ],
            [
                'name' => 'Rina Marlina, S.Pd.',
                'email' => 'rina.marlina@smp3sawan.sch.id',
                'nomor_induk' => '199404251125032001',
                'nomor_telepon' => '081234567895',
                'jenis_kelamin' => 'P',
                'tempat_lahir' => 'Bangli',
                'tanggal_lahir' => '1994-04-25',
                'status_kepegawaian' => 'Honorer',
                'golongan' => null,
                'mata_pelajaran' => 'IPS Sejarah',
                'wali_kelas' => null,
                'role' => 'guru',
                'password' => Hash::make('12345678')
            ],
            [
                'name' => 'Dr. Budi Santoso, M.Pd.',
                'email' => 'budi.santoso@smp3sawan.sch.id',
                'nomor_induk' => '196512101988031001',
                'nomor_telepon' => '081234567896',
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Karangasem',
                'tanggal_lahir' => '1965-12-10',
                'status_kepegawaian' => 'PNS',
                'golongan' => 'IV/c',
                'mata_pelajaran' => 'IPA Biologi',
                'wali_kelas' => '9B',
                'role' => 'guru',
                'password' => Hash::make('12345678')
            ],
            [
                'name' => 'Lestari Wulandari, S.Pd.',
                'email' => 'lestari.wulandari@smp3sawan.sch.id',
                'nomor_induk' => '198709151012032001',
                'nomor_telepon' => '081234567897',
                'jenis_kelamin' => 'P',
                'tempat_lahir' => 'Klungkung',
                'tanggal_lahir' => '1987-09-15',
                'status_kepegawaian' => 'PNS',
                'golongan' => 'III/d',
                'mata_pelajaran' => 'PKN',
                'wali_kelas' => '7B',
                'role' => 'guru',
                'password' => Hash::make('12345678')
            ],
            [
                'name' => 'Made Sutrisno, S.Pd.',
                'email' => 'made.sutrisno@smp3sawan.sch.id',
                'nomor_induk' => '198812201015031002',
                'nomor_telepon' => '081234567898',
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Jembrana',
                'tanggal_lahir' => '1988-12-20',
                'status_kepegawaian' => 'PNS',
                'golongan' => 'III/c',
                'mata_pelajaran' => 'IPA Kimia',
                'wali_kelas' => '8C',
                'role' => 'guru',
                'password' => Hash::make('12345678')
            ],
            [
                'name' => 'Ni Kadek Sari, S.Pd.',
                'email' => 'kadek.sari@smp3sawan.sch.id',
                'nomor_induk' => '199101051016032002',
                'nomor_telepon' => '081234567899',
                'jenis_kelamin' => 'P',
                'tempat_lahir' => 'Negara',
                'tanggal_lahir' => '1991-01-05',
                'status_kepegawaian' => 'PPPK',
                'golongan' => 'III/a',
                'mata_pelajaran' => 'Seni Budaya',
                'wali_kelas' => '7C',
                'role' => 'guru',
                'password' => Hash::make('12345678')
            ]
        ];

        foreach ($teachers as $teacher) {
            User::firstOrCreate(
                ['email' => $teacher['email']], // Kondisi pencarian
                $teacher // Data yang akan dibuat jika tidak ditemukan
            );
        }
    }
}
