<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Berita;
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
            'tanggal' => 'required|date',
        ]);

        $data = $request->all();
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

        Berita::create($data);

        return redirect()->route('admin.berita.index')->with('success', 'Berita berhasil ditambahkan');
    }

    public function edit($id)
    {
        $berita = Berita::findOrFail($id);
        return view('admin.berita.edit', compact('berita'));
    }

    public function update(Request $request, $id)
    {
        $berita = Berita::findOrFail($id);

        $request->validate([
            'judul' => 'required',
            'konten' => 'required',
            'foto' => 'nullable|file|mimes:jpeg,png,jpg,webp,heic,heif|max:10240',
            'tanggal' => 'required|date',
        ]);

        $data = $request->all();

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

        return redirect()->route('admin.berita.index')->with('success', 'Berita berhasil diperbarui');
    }

    public function destroy($id)
    {
        $berita = Berita::findOrFail($id);
        if ($berita->foto && Storage::disk('public')->exists($berita->foto)) {
            Storage::disk('public')->delete($berita->foto);
        }
        $berita->delete();

        return redirect()->route('admin.berita.index')->with('success', 'Berita berhasil dihapus');
    }
}
