<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LandingHero;
use App\Models\SchoolProfile;
use App\Helpers\ImageCompressor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LandingHeroController extends Controller
{
    public function index()
    {
        $heroes = LandingHero::orderBy('urutan', 'asc')->orderBy('id', 'desc')->get();
        $schoolProfile = SchoolProfile::first();
        $heroMode = $schoolProfile ? ($schoolProfile->hero_mode ?? 'slideshow') : 'slideshow';

        return view('admin.landing-hero.index', compact('heroes', 'heroMode'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'gambar' => 'required|file|mimes:jpeg,png,jpg,webp,heic,heif|max:12288',
            'judul' => 'nullable|string|max:255',
            'subjudul' => 'nullable|string|max:255',
            'urutan' => 'nullable|integer',
        ]);

        $path = ImageCompressor::compressAndStore(
            $request->file('gambar'),
            'landing_heroes',
            'hero_' . time() . '_' . uniqid(),
            80,
            1920
        );

        LandingHero::create([
            'judul' => $request->input('judul'),
            'subjudul' => $request->input('subjudul'),
            'gambar' => $path,
            'urutan' => (int) $request->input('urutan', 0),
            'is_active' => true,
        ]);

        return redirect()->route('admin.landing-hero.index')->with('success', 'Wallpaper Hero baru berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $hero = LandingHero::findOrFail($id);

        $request->validate([
            'gambar' => 'nullable|file|mimes:jpeg,png,jpg,webp,heic,heif|max:12288',
            'judul' => 'nullable|string|max:255',
            'subjudul' => 'nullable|string|max:255',
            'urutan' => 'nullable|integer',
        ]);

        $data = [
            'judul' => $request->input('judul'),
            'subjudul' => $request->input('subjudul'),
            'urutan' => (int) $request->input('urutan', 0),
        ];

        if ($request->hasFile('gambar')) {
            if ($hero->gambar && Storage::disk('public')->exists($hero->gambar)) {
                Storage::disk('public')->delete($hero->gambar);
            }

            $data['gambar'] = ImageCompressor::compressAndStore(
                $request->file('gambar'),
                'landing_heroes',
                'hero_' . time() . '_' . uniqid(),
                80,
                1920
            );
        }

        $hero->update($data);

        return redirect()->route('admin.landing-hero.index')->with('success', 'Wallpaper Hero berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $hero = LandingHero::findOrFail($id);

        if ($hero->gambar && Storage::disk('public')->exists($hero->gambar)) {
            Storage::disk('public')->delete($hero->gambar);
        }

        $hero->delete();

        return redirect()->route('admin.landing-hero.index')->with('success', 'Wallpaper Hero berhasil dihapus.');
    }

    public function toggleMode(Request $request)
    {
        $mode = $request->input('mode') === 'static' ? 'static' : 'slideshow';
        $schoolProfile = SchoolProfile::first();

        if ($schoolProfile) {
            $schoolProfile->hero_mode = $mode;
            $schoolProfile->save();
        }

        return response()->json([
            'success' => true,
            'mode' => $mode,
            'message' => 'Mode tampilan hero berhasil diubah menjadi ' . ($mode === 'static' ? 'Tetap (Statis)' : 'Slide Show (Rotasi)'),
        ]);
    }

    public function toggleStatus($id)
    {
        $hero = LandingHero::findOrFail($id);
        $hero->is_active = !$hero->is_active;
        $hero->save();

        return response()->json([
            'success' => true,
            'is_active' => $hero->is_active,
            'message' => 'Status wallpaper berhasil diperbarui.',
        ]);
    }
}

