<?php

namespace App\Http\Controllers\KepalaSekolah;

use App\Http\Controllers\Controller;
use App\Models\Prestasi;
use App\Helpers\ImageCompressor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PrestasiController extends Controller
{
    public function index()
    {
        $prestasis = Prestasi::latest()->get();
        return view('kepala-sekolah.prestasi.index', compact('prestasis'));
    }

    public function create()
    {
        return view('kepala-sekolah.prestasi.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required',
            'deskripsi' => 'required',
            'foto' => 'nullable|file|mimes:jpeg,png,jpg,webp,heic,heif|max:10240',
            'tanggal' => 'required|date',
        ]);

        $data = $request->all();

        if ($request->hasFile('foto')) {
            $data['foto'] = ImageCompressor::compressAndStore(
                $request->file('foto'),
                'prestasi',
                'prestasi_' . time() . '_' . uniqid(),
                75,
                1280
            );
        }

        Prestasi::create($data);

        return redirect()->route('kepala-sekolah.prestasi.index')->with('success', 'Prestasi berhasil ditambahkan');
    }

    public function edit($id)
    {
        $prestasi = Prestasi::findOrFail($id);
        return view('kepala-sekolah.prestasi.edit', compact('prestasi'));
    }

    public function update(Request $request, $id)
    {
        $prestasi = Prestasi::findOrFail($id);

        $request->validate([
            'judul' => 'required',
            'deskripsi' => 'required',
            'foto' => 'nullable|file|mimes:jpeg,png,jpg,webp,heic,heif|max:10240',
            'tanggal' => 'required|date',
        ]);

        $data = $request->all();

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

        return redirect()->route('kepala-sekolah.prestasi.index')->with('success', 'Prestasi berhasil diperbarui');
    }

    public function destroy($id)
    {
        $prestasi = Prestasi::findOrFail($id);
        if ($prestasi->foto && Storage::disk('public')->exists($prestasi->foto)) {
            Storage::disk('public')->delete($prestasi->foto);
        }
        $prestasi->delete();

        return redirect()->route('kepala-sekolah.prestasi.index')->with('success', 'Prestasi berhasil dihapus');
    }
}
