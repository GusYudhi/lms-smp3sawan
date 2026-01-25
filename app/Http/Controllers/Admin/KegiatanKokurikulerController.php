<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KegiatanKokurikuler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KegiatanKokurikulerController extends Controller
{
    public function index()
    {
        $kegiatans = KegiatanKokurikuler::latest()->get();
        return view('admin.kegiatan-kokurikuler.index', compact('kegiatans'));
    }

    public function create()
    {
        return view('admin.kegiatan-kokurikuler.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'deskripsi' => 'required',
            'foto' => 'image|mimes:jpeg,png,jpg|max:2048',
            'tanggal' => 'required|date',
        ]);

        $data = $request->all();

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('kegiatan-kokurikuler', 'public');
        }

        KegiatanKokurikuler::create($data);

        return redirect()->route('admin.kegiatan-kokurikuler.index')->with('success', 'Kegiatan berhasil ditambahkan');
    }

    public function edit($id)
    {
        $kegiatan = KegiatanKokurikuler::findOrFail($id);
        return view('admin.kegiatan-kokurikuler.edit', compact('kegiatan'));
    }

    public function update(Request $request, $id)
    {
        $kegiatan = KegiatanKokurikuler::findOrFail($id);

        $request->validate([
            'nama' => 'required',
            'deskripsi' => 'required',
            'foto' => 'image|mimes:jpeg,png,jpg|max:2048',
            'tanggal' => 'required|date',
        ]);

        $data = $request->all();

        if ($request->hasFile('foto')) {
            if ($kegiatan->foto) {
                Storage::disk('public')->delete($kegiatan->foto);
            }
            $data['foto'] = $request->file('foto')->store('kegiatan-kokurikuler', 'public');
        }

        $kegiatan->update($data);

        return redirect()->route('admin.kegiatan-kokurikuler.index')->with('success', 'Kegiatan berhasil diperbarui');
    }

    public function destroy($id)
    {
        $kegiatan = KegiatanKokurikuler::findOrFail($id);
        if ($kegiatan->foto) {
            Storage::disk('public')->delete($kegiatan->foto);
        }
        $kegiatan->delete();

        return redirect()->route('admin.kegiatan-kokurikuler.index')->with('success', 'Kegiatan berhasil dihapus');
    }
}
