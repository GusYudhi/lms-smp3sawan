<?php

namespace App\Http\Controllers;

use App\Models\SchoolProfile;
use App\Models\Prestasi;
use App\Models\Berita;
use App\Models\User;
use App\Models\Saran;
use App\Models\Galeri;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
                'alamat' => 'Desa Suwug, Kecamatan Sawan, Kabupaten Buleleng, Bali 81171',
                'telepon' => '085850190190',
                'email' => 'admin@smpn3sawan.sch.id',
                'website' => 'www.smpn3sawan.sch.id',
                'maps_latitude' => -8.1234567,
                'maps_longitude' => 115.1234567,
                'kepala_sekolah' => 'Nyoman Paksa Adi Gama, S.Pd., M.Pd.',
                'tahun_berdiri' => 1995,
                'akreditasi' => 'A',
                'npsn' => '50100123'
            ];
        } else {
            $schoolData = $schoolProfile->toArray();
        }

        $prestasis = Prestasi::latest()->take(4)->get();
        $beritas = Berita::latest()->take(4)->get();

        return view('welcome', compact('schoolData', 'prestasis', 'beritas'));
    }

    public function guruStaff()
    {
        $gurus = User::whereIn('role', ['guru', 'kepala_sekolah'])
                     ->with('guruProfile')
                     ->paginate(16);
        return view('guest.guru-staff', compact('gurus'));
    }

    public function berita()
    {
        $beritas = Berita::latest()->paginate(9);
        return view('guest.berita.index', compact('beritas'));
    }

    public function showBerita($id)
    {
        $berita = Berita::findOrFail($id);
        return view('guest.berita.show', compact('berita'));
    }

    public function prestasi()
    {
        $prestasis = Prestasi::latest()->paginate(9);
        return view('guest.prestasi.index', compact('prestasis'));
    }

    public function showPrestasi($id)
    {
        $prestasi = Prestasi::findOrFail($id);
        return view('guest.prestasi.show', compact('prestasi'));
    }

    public function galeri()
    {
        // Manual gallery items
        $galeris = Galeri::select('id', 'judul', 'file_path', 'tipe', 'deskripsi', 'created_at', DB::raw('"galeri" as source'));

        // Prestasi photos
        $prestasi = Prestasi::whereNotNull('foto')
            ->select('id', 'judul', 'foto as file_path', DB::raw('"foto" as tipe'), 'deskripsi', 'created_at', DB::raw('"prestasi" as source'));

        // Berita photos
        $berita = Berita::whereNotNull('foto')
            ->select('id', 'judul', 'foto as file_path', DB::raw('"foto" as tipe'), DB::raw('SUBSTRING(konten, 1, 100) as deskripsi'), 'created_at', DB::raw('"berita" as source'));

        // Union all
        $allGaleri = $galeris->union($prestasi)->union($berita)
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('guest.galeri', ['galeris' => $allGaleri]);
    }

    public function kontak()
    {
        $schoolProfile = SchoolProfile::first();
        $schoolData = $schoolProfile ? $schoolProfile->toArray() : [];
        return view('guest.kontak', compact('schoolData'));
    }

    public function storeSaran(Request $request)
    {
        // Honeypot check: bot biasanya mengisi seluruh input termasuk input tersembunyi
        if ($request->filled('website')) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['saran' => 'Permintaan tidak valid terdeteksi.']);
        }

        $validated = $request->validate([
            'nama_pengirim' => 'required|string|max:100',
            'email_pengirim' => 'nullable|email|max:100',
            'isi_saran' => 'required|string|min:5|max:1000',
        ], [
            'nama_pengirim.required' => 'Nama lengkap wajib diisi.',
            'nama_pengirim.max' => 'Nama lengkap maksimal 100 karakter.',
            'email_pengirim.email' => 'Format email tidak valid.',
            'email_pengirim.max' => 'Email maksimal 100 karakter.',
            'isi_saran.required' => 'Pesan atau saran wajib diisi.',
            'isi_saran.min' => 'Pesan atau saran minimal 5 karakter.',
            'isi_saran.max' => 'Pesan atau saran maksimal 1000 karakter.',
        ]);

        Saran::create([
            'nama_pengirim' => strip_tags(trim($validated['nama_pengirim'])),
            'email_pengirim' => !empty($validated['email_pengirim']) ? trim($validated['email_pengirim']) : null,
            'isi_saran' => strip_tags(trim($validated['isi_saran'])),
            'status' => 'belum_dibaca',
        ]);

        return redirect()->back()->with('success', 'Terima kasih atas saran Anda!');
    }
}
