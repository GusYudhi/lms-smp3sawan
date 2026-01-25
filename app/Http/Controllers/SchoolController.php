<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\SchoolProfile;

class SchoolController extends Controller
{
    public function index()
    {
        // Get school data from database, or use default if none exists
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
                'alamat' => 'Desa Suwug, Kecamatan Sawan, Kabupaten Buleleng, Bali 81171',
                'telepon' => '085850190190',
                'email' => 'admin@smpn3sawan.sch.id',
                'website' => 'smpn3sawan.sch.id',
                'maps_latitude' => '-8.1234567',
                'maps_longitude' => '115.1234567',
                'kepala_sekolah' => 'Nyoman Paksa Adi Gama, S.Pd., M.Pd.',
                'tahun_berdiri' => '1995',
                'akreditasi' => 'A',
                'npsn' => '50100123'
            ];
        } else {
            $schoolData = $schoolProfile->toArray();
        }

        return view('admin.data-sekolah.index', compact('schoolData'));
    }

    public function edit()
    {
        // Get school data for editing
        $schoolProfile = SchoolProfile::first();

        if (!$schoolProfile) {
            // Default data for form
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
                'alamat' => 'Desa Suwug, Kecamatan Sawan, Kabupaten Buleleng, Bali 81171',
                'telepon' => '085850190190',
                'email' => 'admin@smpn3sawan.sch.id',
                'website' => 'smpn3sawan.sch.id',
                'maps_latitude' => '-8.1234567',
                'maps_longitude' => '115.1234567',
                'kepala_sekolah' => 'Nyoman Paksa Adi Gama, S.Pd., M.Pd.',
                'tahun_berdiri' => '1995',
                'akreditasi' => 'A',
                'npsn' => '50100123'
            ];
        } else {
            $schoolData = $schoolProfile->toArray();
        }

        return view('admin.data-sekolah.edit', compact('schoolData'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'visi' => 'required|string',
            'misi.*' => 'required|string',
            'alamat' => 'required|string',
            'telepon' => 'required|string|max:20',
            'email' => 'required|email',
            'website' => 'nullable|string',
            'maps_latitude' => 'required|numeric',
            'maps_longitude' => 'required|numeric',
            'kepala_sekolah' => 'required|string|max:255',
            'tahun_berdiri' => 'required|numeric|min:1900|max:' . date('Y'),
            'akreditasi' => 'required|string|max:2',
            'npsn' => 'required|string|max:20'
        ]);

        // Prepare data for saving
        $schoolData = [
            'name' => $request->name,
            'visi' => $request->visi,
            'misi' => array_filter($request->misi), // Remove empty values
            'alamat' => $request->alamat,
            'telepon' => $request->telepon,
            'email' => $request->email,
            'website' => $request->website,
            'maps_latitude' => $request->maps_latitude,
            'maps_longitude' => $request->maps_longitude,
            'kepala_sekolah' => $request->kepala_sekolah,
            'tahun_berdiri' => $request->tahun_berdiri,
            'akreditasi' => $request->akreditasi,
            'npsn' => $request->npsn
        ];

        // Update or create school profile
        SchoolProfile::updateOrCreate(
            ['id' => 1], // Always use ID 1 for single school profile
            $schoolData
        );

        return redirect()->route('school.profile')->with('success', 'Data sekolah berhasil diperbarui!');
    }
}
