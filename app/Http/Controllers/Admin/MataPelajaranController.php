<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MataPelajaran;
use App\Models\Semester;

class MataPelajaranController extends Controller
{
    public function index(Request $request)
    {
        $semesterId = $request->input('semester_id');

        if ($semesterId) {
            $semester = Semester::with('tahunPelajaran')->findOrFail($semesterId);
            $mapels = MataPelajaran::where('semester_id', $semesterId)->get();
        } else {
            $semester = null;
            $mapels = MataPelajaran::all();
        }

        return view('admin.mata-pelajaran.index', compact('mapels', 'semester'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_mapel' => 'required|string|max:255',
            'kode_mapel' => 'required|string|max:10|unique:mata_pelajarans,kode_mapel',
            'semester_id' => 'nullable|exists:semester,id',
        ]);

        MataPelajaran::create($request->all());

        return redirect()->back()->with('success', 'Mata Pelajaran berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_mapel' => 'required|string|max:255',
            'kode_mapel' => 'required|string|max:10|unique:mata_pelajarans,kode_mapel,' . $id,
            'semester_id' => 'nullable|exists:semester,id',
        ]);

        $mapel = MataPelajaran::findOrFail($id);
        $mapel->update($request->all());

        return redirect()->back()->with('success', 'Mata Pelajaran berhasil diperbarui');
    }

    public function destroy($id)
    {
        MataPelajaran::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Mata Pelajaran berhasil dihapus');
    }
}
