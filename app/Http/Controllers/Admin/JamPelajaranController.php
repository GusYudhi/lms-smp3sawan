<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JamPelajaran;
use App\Models\Semester;

class JamPelajaranController extends Controller
{
    public function index(Request $request)
    {
        $semesterId = $request->input('semester_id');

        if ($semesterId) {
            $semester = Semester::with('tahunPelajaran')->findOrFail($semesterId);
            $jamPelajarans = JamPelajaran::where('semester_id', $semesterId)->orderBy('jam_ke')->get();
        } else {
            $semester = null;
            $jamPelajarans = JamPelajaran::orderBy('jam_ke')->get();
        }

        return view('admin.jam-pelajaran.index', compact('jamPelajarans', 'semester'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'jam_ke' => 'required|integer',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required|after:jam_mulai',
            'semester_id' => 'nullable|exists:semester,id',
        ]);

        JamPelajaran::create($request->all());

        return redirect()->back()->with('success', 'Jam Pelajaran berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'jam_ke' => 'required|integer',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required|after:jam_mulai',
            'semester_id' => 'nullable|exists:semester,id',
        ]);

        $jam = JamPelajaran::findOrFail($id);
        $jam->update($request->all());

        return redirect()->back()->with('success', 'Jam Pelajaran berhasil diperbarui');
    }

    public function destroy($id)
    {
        JamPelajaran::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Jam Pelajaran berhasil dihapus');
    }
}
