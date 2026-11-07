<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Berita;
use App\Models\BeritaImage;
use App\Helpers\ImageCompressor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class BeritaController extends Controller
{
    public function index()
    {
        $beritas = Berita::with('penulis')->latest()->get();
        return view('admin.berita.index', compact('beritas'));
    }

    public function create()
    {
        return view('admin.berita.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required',
            'konten' => 'required',
            'foto' => 'nullable|file|mimes:jpeg,png,jpg,webp,heic,heif|max:10240',
            'foto_tambahan.*' => 'nullable|file|mimes:jpeg,png,jpg,webp,heic,heif|max:10240',
            'tanggal' => 'required|date',
        ]);

        $data = $request->except(['foto_tambahan']);
        $data['penulis_id'] = Auth::id();

        if ($request->hasFile('foto')) {
            $data['foto'] = ImageCompressor::compressAndStore(
                $request->file('foto'),
                'berita',
                'berita_' . time() . '_' . uniqid(),
                75,
                1280
            );
        }

        $berita = Berita::create($data);

        // Upload foto-foto tambahan
        if ($request->hasFile('foto_tambahan')) {
            foreach ($request->file('foto_tambahan') as $index => $file) {
                $extraPath = ImageCompressor::compressAndStore(
                    $file,
                    'berita',
                    'berita_extra_' . time() . '_' . uniqid(),
                    75,
                    1280
                );
                $berita->images()->create([
                    'foto' => $extraPath,
                    'urutan' => $index + 1,
                ]);
            }
        }

        return redirect()->route('admin.berita.index')->with('success', 'Berita berhasil ditambahkan');
    }

    public function edit($id)
    {
        $berita = Berita::with('images')->findOrFail($id);
        return view('admin.berita.edit', compact('berita'));
    }

    public function update(Request $request, $id)
    {
        $berita = Berita::findOrFail($id);

        $request->validate([
            'judul' => 'required',
            'konten' => 'required',
            'foto' => 'nullable|file|mimes:jpeg,png,jpg,webp,heic,heif|max:10240',
            'foto_tambahan.*' => 'nullable|file|mimes:jpeg,png,jpg,webp,heic,heif|max:10240',
            'tanggal' => 'required|date',
        ]);

        $data = $request->except(['foto_tambahan']);

        if ($request->hasFile('foto')) {
            if ($berita->foto && Storage::disk('public')->exists($berita->foto)) {
                Storage::disk('public')->delete($berita->foto);
            }
            $data['foto'] = ImageCompressor::compressAndStore(
                $request->file('foto'),
                'berita',
                'berita_' . time() . '_' . uniqid(),
                75,
                1280
            );
        }

        $berita->update($data);

        // Upload foto-foto tambahan baru
        if ($request->hasFile('foto_tambahan')) {
            $currentMax = $berita->images()->max('urutan') ?? 0;
            foreach ($request->file('foto_tambahan') as $index => $file) {
                $extraPath = ImageCompressor::compressAndStore(
                    $file,
                    'berita',
                    'berita_extra_' . time() . '_' . uniqid(),
                    75,
                    1280
                );
                $berita->images()->create([
                    'foto' => $extraPath,
                    'urutan' => $currentMax + $index + 1,
                ]);
            }
        }

        return redirect()->route('admin.berita.index')->with('success', 'Berita berhasil diperbarui');
    }

    public function destroy($id)
    {
        $berita = Berita::with('images')->findOrFail($id);

        if ($berita->foto && Storage::disk('public')->exists($berita->foto)) {
            Storage::disk('public')->delete($berita->foto);
        }

        foreach ($berita->images as $img) {
            if ($img->foto && Storage::disk('public')->exists($img->foto)) {
                Storage::disk('public')->delete($img->foto);
            }
        }

        $berita->delete();

        return redirect()->route('admin.berita.index')->with('success', 'Berita berhasil dihapus');
    }

    public function destroyImage($id, $imageId)
    {
        $berita = Berita::findOrFail($id);
        $image = $berita->images()->findOrFail($imageId);

        if ($image->foto && Storage::disk('public')->exists($image->foto)) {
            Storage::disk('public')->delete($image->foto);
        }

        $image->delete();

        return response()->json([
            'success' => true,
            'message' => 'Foto tambahan berhasil dihapus',
        ]);
    }

    public function destroyMainImage($id)
    {
        $berita = Berita::with('images')->findOrFail($id);

        if ($berita->foto && Storage::disk('public')->exists($berita->foto)) {
            Storage::disk('public')->delete($berita->foto);
        }

        $firstExtra = $berita->images()->orderBy('urutan')->first();
        if ($firstExtra) {
            $berita->foto = $firstExtra->foto;
            $berita->save();
            $promotedId = $firstExtra->id;
            $promotedUrl = asset('storage/' . $berita->foto);
            $firstExtra->delete();

            return response()->json([
                'success' => true,
                'promoted' => true,
                'promoted_id' => $promotedId,
                'new_main_url' => $promotedUrl,
                'message' => 'Foto sampul dihapus dan foto galeri berikutnya otomatis dipromosikan jadi sampul utama.',
            ]);
        }

        $berita->foto = null;
        $berita->save();

        return response()->json([
            'success' => true,
            'promoted' => false,
            'message' => 'Foto sampul berhasil dihapus.',
        ]);
    }

    public function setMainImage($id, $imageId)
    {
        $berita = Berita::findOrFail($id);
        $targetExtra = $berita->images()->findOrFail($imageId);

        $oldMain = $berita->foto;
        $newMain = $targetExtra->foto;

        $berita->foto = $newMain;
        $berita->save();

        if ($oldMain) {
            $targetExtra->foto = $oldMain;
            $targetExtra->save();
        } else {
            $targetExtra->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Foto berhasil dijadikan Sampul Utama.',
        ]);
    }
}
