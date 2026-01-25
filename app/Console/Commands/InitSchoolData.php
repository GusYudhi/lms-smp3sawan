<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SchoolProfile;

class InitSchoolData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'school:init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize school profile data with default values';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Initializing school profile data...');

        try {
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
                    'alamat' => 'Desa Suwug, Kecamatan Sawan, Kabupaten Buleleng, Bali 81171',
                    'telepon' => '085850190190',
                    'email' => 'admin@smpn3sawan.sch.id',
                    'website' => 'smpn3sawan.sch.id',
                    'maps_latitude' => -8.1542,
                    'maps_longitude' => 115.0956,
                    'kepala_sekolah' => 'Drs. I Made Sutrisna, M.Pd.',
                    'tahun_berdiri' => 1985,
                    'akreditasi' => 'A',
                    'npsn' => '50100123'
                ]
            );

            $this->info('School profile data initialized successfully!');
            $this->line('You can now visit /data-sekolah to see the school profile.');

        } catch (\Exception $e) {
            $this->error('Failed to initialize school data: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
