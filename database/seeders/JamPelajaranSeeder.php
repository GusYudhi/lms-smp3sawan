<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JamPelajaran;
use App\Models\Semester;

class JamPelajaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil semester aktif atau semester pertama
        $semester = Semester::where('is_active', true)->first() 
                    ?? Semester::first();

        if (!$semester) {
            $this->command->error('Tidak ada semester. Jalankan seeder untuk Semester terlebih dahulu.');
            return;
        }

        $jamPelajaran = [
            ['jam_ke' => 1, 'jam_mulai' => '07:00:00', 'jam_selesai' => '07:40:00'],
            ['jam_ke' => 2, 'jam_mulai' => '07:40:00', 'jam_selesai' => '08:20:00'],
            ['jam_ke' => 3, 'jam_mulai' => '08:20:00', 'jam_selesai' => '09:00:00'],
            // Istirahat 1: 09:00 - 09:15
            ['jam_ke' => 4, 'jam_mulai' => '09:15:00', 'jam_selesai' => '09:55:00'],
            ['jam_ke' => 5, 'jam_mulai' => '09:55:00', 'jam_selesai' => '10:35:00'],
            ['jam_ke' => 6, 'jam_mulai' => '10:35:00', 'jam_selesai' => '11:15:00'],
            // Istirahat 2: 11:15 - 11:30
            ['jam_ke' => 7, 'jam_mulai' => '11:30:00', 'jam_selesai' => '12:10:00'],
            ['jam_ke' => 8, 'jam_mulai' => '12:10:00', 'jam_selesai' => '12:50:00'],
        ];

        foreach ($jamPelajaran as $jam) {
            JamPelajaran::firstOrCreate(
                [
                    'jam_ke' => $jam['jam_ke'],
                    'semester_id' => $semester->id
                ],
                [
                    'jam_mulai' => $jam['jam_mulai'],
                    'jam_selesai' => $jam['jam_selesai']
                ]
            );
        }

        $this->command->info('Seeder Jam Pelajaran berhasil dijalankan!');
        $this->command->info('Total: ' . count($jamPelajaran) . ' jam pelajaran (07:00 - 12:50)');
    }
}
