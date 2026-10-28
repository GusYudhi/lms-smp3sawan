<?php

namespace App\Http\Controllers\Admin;

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
        $viewPrefix = auth()->user()->isGuru() ? 'guru' : (auth()->user()->isKepalaSekolah() ? 'kepala-sekolah' : 'admin');
        return view($viewPrefix . '.kegiatan-kokurikuler.index', compact('kegiatans'));
    }

    public function create()
    {
        $viewPrefix = auth()->user()->isGuru() ? 'guru' : (auth()->user()->isKepalaSekolah() ? 'kepala-sekolah' : 'admin');
        return view($viewPrefix . '.kegiatan-kokurikuler.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'deskripsi' => 'required',
            'tipe' => 'required|in:foto,pdf,link',
            'foto' => 'nullable|required_if:tipe,foto,pdf|file|mimes:jpeg,png,jpg,webp,heic,heif,pdf|max:10240',
            'link' => 'nullable|required_if:tipe,link|url',
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
        } else {
            // Jika tipe link, pastikan foto null
            if ($request->tipe == 'link') {
                $data['foto'] = null;
            }
        }

        KegiatanKokurikuler::create($data);

        $routePrefix = auth()->user()->isGuru() ? 'guru' : (auth()->user()->isKepalaSekolah() ? 'kepala-sekolah' : 'admin');
        return redirect()->route($routePrefix . '.kegiatan-kokurikuler.index')->with('success', 'Kegiatan berhasil ditambahkan');
    }

    public function edit($id)
    {
        $kegiatan = KegiatanKokurikuler::findOrFail($id);
        $viewPrefix = auth()->user()->isGuru() ? 'guru' : (auth()->user()->isKepalaSekolah() ? 'kepala-sekolah' : 'admin');
        return view($viewPrefix . '.kegiatan-kokurikuler.edit', compact('kegiatan'));
    }

    public function update(Request $request, $id)
    {
        $kegiatan = KegiatanKokurikuler::findOrFail($id);

        $request->validate([
            'nama' => 'required',
            'deskripsi' => 'required',
            'tipe' => 'required|in:foto,pdf,link',
            'foto' => 'nullable|file|mimes:jpeg,png,jpg,webp,heic,heif,pdf|max:10240',
            'link' => 'nullable|required_if:tipe,link|url',
            'tanggal' => 'required|date',
        ]);

        $data = $request->all();

        if ($request->tipe == 'link') {
            // Jika ganti ke link, hapus foto lama jika ada dan set null
            if ($kegiatan->foto && Storage::disk('public')->exists($kegiatan->foto)) {
                Storage::disk('public')->delete($kegiatan->foto);
            }
            $data['foto'] = null;
        } else {
            // Jika tipe foto/pdf
            if ($request->hasFile('foto')) {
                // Ada upload baru
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
            } else {
                // Tidak ada upload baru, pertahankan foto lama
                // Hapus key 'foto' dari data agar tidak terupdate null
                unset($data['foto']);
            }
            
            // Link harus null jika tipe bukan link
            $data['link'] = null;
        }

        $kegiatan->update($data);

        $routePrefix = auth()->user()->isGuru() ? 'guru' : (auth()->user()->isKepalaSekolah() ? 'kepala-sekolah' : 'admin');
        return redirect()->route($routePrefix . '.kegiatan-kokurikuler.index')->with('success', 'Kegiatan berhasil diperbarui');
    }

    public function destroy($id)
    {
        $kegiatan = KegiatanKokurikuler::findOrFail($id);
        if ($kegiatan->foto && Storage::disk('public')->exists($kegiatan->foto)) {
            Storage::disk('public')->delete($kegiatan->foto);
        }
        $kegiatan->delete();

        $routePrefix = auth()->user()->isGuru() ? 'guru' : (auth()->user()->isKepalaSekolah() ? 'kepala-sekolah' : 'admin');
        return redirect()->route($routePrefix . '.kegiatan-kokurikuler.index')->with('success', 'Kegiatan berhasil dihapus');
    }
}