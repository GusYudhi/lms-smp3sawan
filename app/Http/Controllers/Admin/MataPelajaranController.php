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
            // Default to active semester context if available
            $semester = Semester::where('is_active', true)->with('tahunPelajaran')->first();
            
            if ($semester) {
                // If we found an active semester, show mapels for it (or all? Usually per semester)
                // If the goal is just to show the SIDEBAR, we pass $semester.
                // But logically, if we are in "Semester Context", we should probably show mapels for THAT semester.
                // However, previous logic for 'else' was MataPelajaran::all(). 
                // Let's stick to showing relevant mapels for consistency.
                $mapels = MataPelajaran::where('semester_id', $semester->id)
                    ->orWhereNull('semester_id') // Include global mapels if any
                    ->get();
            } else {
                $mapels = MataPelajaran::all();
            }
        }

        return view('admin.mata-pelajaran.index', compact('mapels', 'semester'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_mapel' => 'required|string|max:10', // Unique check removed globally as per requirement
            'nama_mapel' => 'required|string|max:255',
        ]);

        $activeSemester = \App\Models\Semester::where('is_active', true)->first();

        MataPelajaran::create([
            'kode_mapel' => $request->kode_mapel,
            'nama_mapel' => $request->nama_mapel,
            'semester_id' => $activeSemester ? $activeSemester->id : null,
            'is_universal' => $request->has('is_universal'),
        ]);

        return redirect()->route('admin.mapel.index')->with('success', 'Mata pelajaran berhasil ditambahkan');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'kode_mapel' => 'required|string|max:10',
            'nama_mapel' => 'required|string|max:255',
        ]);

        $mapel = MataPelajaran::findOrFail($id);
        
        $mapel->update([
            'kode_mapel' => $request->kode_mapel,
            'nama_mapel' => $request->nama_mapel,
            'is_universal' => $request->has('is_universal'),
        ]);

        return redirect()->route('admin.mapel.index')->with('success', 'Mata pelajaran berhasil diperbarui');
    }

    public function destroy($id)
    {
        MataPelajaran::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Mata Pelajaran berhasil dihapus');
    }
}
