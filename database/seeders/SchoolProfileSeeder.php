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
                'name' => 'SMP NEGERI 3 SAWAN',
                'visi' => 'Terwujud Lulusan yang Unggul dalam Prestasi, Berkarakter, dan Berwawasan lingkungan',
                'misi' => [
                    'Mewujudkan lulusan yang unggul dalam bidang akademik dan non akademik.',
                    'Terwujud sikap mental dan spiritual yang menjunjung tinggi nilai-nilai  Pendidikan Karakter dengan konsep Nangun Sat Kerthi Loka Bali dengan semboyan : Loka Jnana Shanti, Loka berarti alam/dunia, Shanti berarti damai, Jnana berarti Pengetahuan,  jadi SMP Negeri 3 Sawan merupakan alam/tempat/dunia pengetahuan yang damai.',
                    'Mewujudkan  pendidikan yang berkarakter mengedepankan pembentukan Delapan dimensi profil lulusan, yang memiliki delapan dimensi utama yaitu : Keimanan dan Ketakwaan kepada Tuhan Yang Maha Esa, Kewargaan, Penalaran Kritis, Kreativitas, Kolaborasi, Kemandirian, Kesehatan, dan Komunikasi.',
                    'Mewujudkan pendidikan yang mengembangkan keterampilan abad 21'
                ],
                'alamat' => 'Desa Suwug, Kecamatan Sawan, Kabupaten Buleleng, Bali 81171',
                'telepon' => '085850190190',
                'email' => 'info@smpn3sawan.sch.id',
                'website' => 'smpn3sawan.sch.id',
                'maps_latitude' => -8.13332,
                'maps_longitude' => 115.15519,
                'kepala_sekolah' => 'Nyoman Paksa Adi Gama, S.Pd., M.Pd.',
                'tahun_berdiri' => 1995,
                'akreditasi' => 'A',
                'npsn' => '50100301'
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
