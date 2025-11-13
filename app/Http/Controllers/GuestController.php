<?php

namespace App\Http\Controllers;

use App\Models\SchoolProfile;
use Illuminate\Http\Request;

class GuestController extends Controller
{
    public function welcome()
    {
        // Get school data from database for landing page
        $schoolProfile = SchoolProfile::first();

        if (!$schoolProfile) {
            // Default data if no record exists
            $schoolData = [
                'name' => 'SMPN 3 SAWAN',
                'visi' => 'Menjadi sekolah unggulan yang menghasilkan lulusan berkualitas, berkarakter, dan berdaya saing global.',
                'misi' => [
                    'Menyelenggarakan pendidikan yang berkualitas dan berstandar nasional',
                    'Mengembangkan potensi peserta didik secara optimal',
                    'Membangun karakter yang berakhlak mulia dan berbudi pekerti luhur',
                    'Menciptakan lingkungan belajar yang kondusif dan menyenangkan',
                    'Meningkatkan profesionalisme tenaga pendidik dan kependidikan'
                ],
                'alamat' => 'Jl. Raya Sawan No. 123, Sawan, Buleleng, Bali',
                'telepon' => '(0362) 123456',
                'email' => 'info@smpn3sawan.sch.id',
                'website' => 'www.smpn3sawan.sch.id',
                'maps_latitude' => -8.1234567,
                'maps_longitude' => 115.1234567,
                'kepala_sekolah' => 'Drs. I Made Sutrisna, M.Pd.',
                'tahun_berdiri' => 1985,
                'akreditasi' => 'A',
                'npsn' => '50100123'
            ];
        } else {
            $schoolData = $schoolProfile->toArray();
        }

        return view('welcome', compact('schoolData'));
    }
}
