<?php

namespace App\Http\Controllers\KepalaSekolah;

use App\Http\Controllers\Controller;
use App\Models\Galeri;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GaleriController extends Controller
{
    public function index()
    {
        $galeris = Galeri::latest()->get();
        return view('kepala-sekolah.galeri.index', compact('galeris'));
    }

    public function create()
    {
        return view('kepala-sekolah.galeri.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'nullable|string',
            'file' => 'required|file|mimes:jpg,jpeg,png,mp4,mov,avi|max:20480', // 20MB max for video
            'tipe' => 'required|in:foto,video',
            'deskripsi' => 'nullable|string',
        ]);

        $data = $request->only(['judul', 'tipe', 'deskripsi']);

        if ($request->hasFile('file')) {
            $data['file_path'] = $request->file('file')->store('galeri', 'public');
        }

        Galeri::create($data);

        return redirect()->route('kepala-sekolah.galeri.index')->with('success', 'Galeri berhasil ditambahkan');
    }

    public function edit($id)
    {
        $galeri = Galeri::findOrFail($id);
        return view('kepala-sekolah.galeri.edit', compact('galeri'));
    }

    public function update(Request $request, $id)
    {
        $galeri = Galeri::findOrFail($id);

        $request->validate([
            'judul' => 'nullable|string',
            'file' => 'nullable|file|mimes:jpg,jpeg,png,mp4,mov,avi|max:20480',
            'tipe' => 'required|in:foto,video',
            'deskripsi' => 'nullable|string',
        ]);

        $data = $request->only(['judul', 'tipe', 'deskripsi']);

        if ($request->hasFile('file')) {
            if ($galeri->file_path) {
                Storage::disk('public')->delete($galeri->file_path);
            }
            $data['file_path'] = $request->file('file')->store('galeri', 'public');
        }

        $galeri->update($data);

        return redirect()->route('kepala-sekolah.galeri.index')->with('success', 'Galeri berhasil diperbarui');
    }

    public function destroy($id)
    {
        $galeri = Galeri::findOrFail($id);
        if ($galeri->file_path) {
            Storage::disk('public')->delete($galeri->file_path);
        }
        $galeri->delete();

        return redirect()->route('kepala-sekolah.galeri.index')->with('success', 'Galeri berhasil dihapus');
    }
}
