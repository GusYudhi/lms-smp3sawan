<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Prestasi;
use App\Helpers\ImageCompressor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class PrestasiController extends Controller
{
    public function index()
    {
        $prestasis = Prestasi::with('penulis')->latest()->get();
        return view('guru.prestasi.index', compact('prestasis'));
    }

    public function create()
    {
        return view('guru.prestasi.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required',
            'foto' => 'nullable|file|mimes:jpeg,png,jpg,webp,heic,heif|max:10240',
            'foto_tambahan.*' => 'nullable|file|mimes:jpeg,png,jpg,webp,heic,heif|max:10240',
            'tanggal' => 'required|date',
        ]);

        $data = $request->except(['foto_tambahan']);
        $data['penulis_id'] = Auth::id();

        if ($request->hasFile('foto')) {
            $data['foto'] = ImageCompressor::compressAndStore(
                $request->file('foto'),
                'prestasi',
                'prestasi_' . time() . '_' . uniqid(),
                75,
                1280
            );
        }

        $prestasi = Prestasi::create($data);

        if ($request->hasFile('foto_tambahan')) {
            foreach ($request->file('foto_tambahan') as $index => $file) {
                $extraPath = ImageCompressor::compressAndStore(
                    $file,
                    'prestasi',
                    'prestasi_extra_' . time() . '_' . uniqid(),
                    75,
                    1280
                );
                $prestasi->images()->create([
                    'foto' => $extraPath,
                    'urutan' => $index + 1,
                ]);
            }
        }

        return redirect()->route('guru.prestasi.index')->with('success', 'Prestasi berhasil ditambahkan');
    }

    public function edit($id)
    {
        $prestasi = Prestasi::with('images')->findOrFail($id);
        return view('guru.prestasi.edit', compact('prestasi'));
    }

    public function update(Request $request, $id)
    {
        $prestasi = Prestasi::findOrFail($id);

        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required',
            'foto' => 'nullable|file|mimes:jpeg,png,jpg,webp,heic,heif|max:10240',
            'foto_tambahan.*' => 'nullable|file|mimes:jpeg,png,jpg,webp,heic,heif|max:10240',
            'tanggal' => 'required|date',
        ]);

        $data = $request->except(['foto_tambahan']);

        if ($request->hasFile('foto')) {
            if ($prestasi->foto && Storage::disk('public')->exists($prestasi->foto)) {
                Storage::disk('public')->delete($prestasi->foto);
            }
            $data['foto'] = ImageCompressor::compressAndStore(
                $request->file('foto'),
                'prestasi',
                'prestasi_' . time() . '_' . uniqid(),
                75,
                1280
            );
        }

        $prestasi->update($data);

        if ($request->hasFile('foto_tambahan')) {
            $currentMax = $prestasi->images()->max('urutan') ?? 0;
            foreach ($request->file('foto_tambahan') as $index => $file) {
                $extraPath = ImageCompressor::compressAndStore(
                    $file,
                    'prestasi',
                    'prestasi_extra_' . time() . '_' . uniqid(),
                    75,
                    1280
                );
                $prestasi->images()->create([
                    'foto' => $extraPath,
                    'urutan' => $currentMax + $index + 1,
                ]);
            }
        }

        return redirect()->route('guru.prestasi.index')->with('success', 'Prestasi berhasil diperbarui');
    }

    public function destroy($id)
    {
        $prestasi = Prestasi::with('images')->findOrFail($id);
        if ($prestasi->foto && Storage::disk('public')->exists($prestasi->foto)) {
            Storage::disk('public')->delete($prestasi->foto);
        }
        foreach ($prestasi->images as $img) {
            if ($img->foto && Storage::disk('public')->exists($img->foto)) {
                Storage::disk('public')->delete($img->foto);
            }
        }
        $prestasi->delete();

        return redirect()->route('guru.prestasi.index')->with('success', 'Prestasi berhasil dihapus');
    }
}

