<?php

namespace App\Http\Controllers\KepalaSekolah;

use App\Http\Controllers\Controller;
use App\Models\KegiatanKokurikuler;
use App\Helpers\ImageCompressor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KegiatanKokurikulerController extends Controller
{
    public function index()
    {
        $kegiatans = KegiatanKokurikuler::latest()->get();
        return view('kepala-sekolah.kegiatan-kokurikuler.index', compact('kegiatans'));
    }

    public function create()
    {
        return view('kepala-sekolah.kegiatan-kokurikuler.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'deskripsi' => 'required',
            'tipe' => 'required|in:foto,pdf,link',
            'foto' => 'required_if:tipe,foto,pdf|file|mimes:jpeg,png,jpg,webp,heic,heif,pdf|max:10240',
            'link' => 'required_if:tipe,link|url',
            'tanggal' => 'required|date',
        ]);

        $data = $request->all();

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            if ($request->tipe === 'foto' || str_starts_with($file->getMimeType(), 'image/')) {
                $data['foto'] = ImageCompressor::compressAndStore(
                    $file,
                    'kegiatan-kokurikuler',
                    'kokurikuler_' . time() . '_' . uniqid(),
                    75,
                    1280
                );
            } else {
                $data['foto'] = $file->store('kegiatan-kokurikuler', 'public');
            }
        }

        KegiatanKokurikuler::create($data);

        return redirect()->route('kepala-sekolah.kegiatan-kokurikuler.index')->with('success', 'Kegiatan berhasil ditambahkan');
    }

    public function edit($id)
    {
        $kegiatan = KegiatanKokurikuler::findOrFail($id);
        return view('kepala-sekolah.kegiatan-kokurikuler.edit', compact('kegiatan'));
    }

    public function update(Request $request, $id)
    {
        $kegiatan = KegiatanKokurikuler::findOrFail($id);

        $request->validate([
            'nama' => 'required',
            'deskripsi' => 'required',
            'tipe' => 'required|in:foto,pdf,link',
            'foto' => 'nullable|file|mimes:jpeg,png,jpg,webp,heic,heif,pdf|max:10240',
            'link' => 'required_if:tipe,link|url',
            'tanggal' => 'required|date',
        ]);

        $data = $request->all();

        if ($request->hasFile('foto')) {
            if ($kegiatan->foto && Storage::disk('public')->exists($kegiatan->foto)) {
                Storage::disk('public')->delete($kegiatan->foto);
            }
            $file = $request->file('foto');
            if ($request->tipe === 'foto' || str_starts_with($file->getMimeType(), 'image/')) {
                $data['foto'] = ImageCompressor::compressAndStore(
                    $file,
                    'kegiatan-kokurikuler',
                    'kokurikuler_' . time() . '_' . uniqid(),
                    75,
                    1280
                );
            } else {
                $data['foto'] = $file->store('kegiatan-kokurikuler', 'public');
            }
        }

        $kegiatan->update($data);

        return redirect()->route('kepala-sekolah.kegiatan-kokurikuler.index')->with('success', 'Kegiatan berhasil diperbarui');
    }

    public function destroy($id)
    {
        $kegiatan = KegiatanKokurikuler::findOrFail($id);
        if ($kegiatan->foto && Storage::disk('public')->exists($kegiatan->foto)) {
            Storage::disk('public')->delete($kegiatan->foto);
        }
        $kegiatan->delete();

        return redirect()->route('kepala-sekolah.kegiatan-kokurikuler.index')->with('success', 'Kegiatan berhasil dihapus');
    }
}