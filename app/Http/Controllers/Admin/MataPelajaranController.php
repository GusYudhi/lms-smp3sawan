<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MataPelajaran;

class MataPelajaranController extends Controller
{
    /**
     * Tampilkan daftar master mata pelajaran.
     */
    public function index()
    {
        $mapels = MataPelajaran::orderBy('nama_mapel')->get();

        return view('admin.mata-pelajaran.index', compact('mapels'));
    }

    /**
     * Simpan data master mata pelajaran baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode_mapel' => 'required|string|max:10|unique:mata_pelajarans,kode_mapel',
            'nama_mapel' => 'required|string|max:255',
        ], [
            'kode_mapel.unique' => 'Kode mata pelajaran sudah digunakan.',
        ]);

        MataPelajaran::create([
            'kode_mapel' => strtoupper(trim($request->kode_mapel)),
            'nama_mapel' => trim($request->nama_mapel),
            'is_universal' => $request->has('is_universal'),
            'is_active' => true,
        ]);

        return redirect()->route('admin.mapel.index')->with('success', 'Mata pelajaran berhasil ditambahkan.');
    }

    /**
     * Perbarui data master mata pelajaran.
     */
    public function update(Request $request, string $id)
    {
        $mapel = MataPelajaran::findOrFail($id);

        $request->validate([
            'kode_mapel' => 'required|string|max:10|unique:mata_pelajarans,kode_mapel,' . $id,
            'nama_mapel' => 'required|string|max:255',
        ], [
            'kode_mapel.unique' => 'Kode mata pelajaran sudah digunakan.',
        ]);

        $mapel->update([
            'kode_mapel' => strtoupper(trim($request->kode_mapel)),
            'nama_mapel' => trim($request->nama_mapel),
            'is_universal' => $request->has('is_universal'),
        ]);

        return redirect()->route('admin.mapel.index')->with('success', 'Mata pelajaran berhasil diperbarui.');
    }

    /**
     * Hapus mata pelajaran jika belum pernah memiliki riwayat akademik.
     */
    public function destroy(string $id)
    {
        $mapel = MataPelajaran::findOrFail($id);

        if ($mapel->hasAcademicRecords()) {
            return redirect()->back()->with('error', 'Mata pelajaran tidak dapat dihapus karena memiliki riwayat akademik (jadwal/jurnal/guru/materi). Silakan nonaktifkan statusnya.');
        }

        $mapel->delete();

        return redirect()->route('admin.mapel.index')->with('success', 'Mata pelajaran berhasil dihapus.');
    }

    /**
     * Ubah status aktif/nonaktif mata pelajaran.
     */
    public function toggleStatus(string $id)
    {
        $mapel = MataPelajaran::findOrFail($id);
        $mapel->is_active = !$mapel->is_active;
        $mapel->save();

        $statusStr = $mapel->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->back()->with('success', "Mata pelajaran {$mapel->nama_mapel} berhasil {$statusStr}.");
    }
}
