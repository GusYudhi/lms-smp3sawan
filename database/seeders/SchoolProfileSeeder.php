<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SchoolProfile;
use App\Models\GuruProfile;

class SchoolProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SchoolProfile::updateOrCreate(
            ['id' => 1],
            [
                'name' => 'SMPN 3 SAWAN',
                'visi' => 'Menjadi sekolah unggulan yang menghasilkan lulusan berkualitas, berkarakter, dan berdaya saing global dengan mengutamakan nilai-nilai Pancasila dan budaya lokal.',
                'misi' => [
                    'Menyelenggarakan pendidikan yang berkualitas dan berstandar nasional dengan kurikulum yang seimbang antara akademik dan karakter',
                    'Mengembangkan potensi peserta didik secara optimal melalui pembelajaran yang inovatif dan bermakna',
                    'Membangun karakter yang berakhlak mulia dan berbudi pekerti luhur berdasarkan nilai-nilai agama dan budaya',
                    'Menciptakan lingkungan belajar yang kondusif, aman, dan menyenangkan untuk seluruh warga sekolah',
                    'Meningkatkan profesionalisme tenaga pendidik dan kependidikan melalui pengembangan berkelanjutan',
                    'Menjalin kerjasama yang harmonis dengan orang tua dan masyarakat dalam mendukung pendidikan karakter'
                ],
                'alamat' => 'Jl. Raya Sawan No. 123, Desa Sawan, Kecamatan Sawan, Kabupaten Buleleng, Bali 81171',
                'telepon' => '(0362) 123456',
                'email' => 'info@smpn3sawan.sch.id',
                'website' => 'smpn3sawan.sch.id',
                'maps_latitude' => -8.1542,
                'maps_longitude' => 115.0956,
                'kepala_sekolah' => 'Dr. Budi Santoso, M.Pd.',
                'tahun_berdiri' => 1985,
                'akreditasi' => 'A',
                'npsn' => '50100123'
            ]
        );

        // Set kepala sekolah after seeding is done
        $schoolProfile = SchoolProfile::first();
        if ($schoolProfile) {
            // Find kepala sekolah guru profile
            $kepalaSekolahProfile = GuruProfile::whereHas('user', function($query) {
                $query->where('role', 'kepala_sekolah');
            })->first();

            if ($kepalaSekolahProfile) {
                $schoolProfile->update([
                    'id_kepala_sekolah' => $kepalaSekolahProfile->id
                ]);
            }
        }
    }
}
