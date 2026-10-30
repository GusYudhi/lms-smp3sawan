<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JamPelajaran;
use App\Models\Semester;
use Illuminate\Validation\Rule;

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
            'jam_ke' => [
                'required',
                'integer',
                'min:0',
                Rule::unique('jam_pelajarans', 'jam_ke')->where(function ($query) use ($request) {
                    return $query->where('semester_id', $request->semester_id);
                }),
            ],
            'jam_mulai' => 'required',
            'jam_selesai' => 'required|after:jam_mulai',
            'semester_id' => 'nullable|exists:semester,id',
        ], [
            'jam_ke.min' => 'Jam ke minimal bernilai 0.',
            'jam_ke.unique' => 'Jam ke tersebut sudah terdaftar pada semester ini.',
            'jam_selesai.after' => 'Jam selesai harus setelah jam mulai.',
        ]);

        JamPelajaran::create($request->all());

        return redirect()->back()->with('success', 'Jam Pelajaran berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $jam = JamPelajaran::findOrFail($id);
        $semesterId = $request->input('semester_id', $jam->semester_id);

        $request->validate([
            'jam_ke' => [
                'required',
                'integer',
                'min:0',
                Rule::unique('jam_pelajarans', 'jam_ke')->where(function ($query) use ($semesterId) {
                    return $query->where('semester_id', $semesterId);
                })->ignore($jam->id),
            ],
            'jam_mulai' => 'required',
            'jam_selesai' => 'required|after:jam_mulai',
            'semester_id' => 'nullable|exists:semester,id',
        ], [
            'jam_ke.min' => 'Jam ke minimal bernilai 0.',
            'jam_ke.unique' => 'Jam ke tersebut sudah terdaftar pada semester ini.',
            'jam_selesai.after' => 'Jam selesai harus setelah jam mulai.',
        ]);

        $jam->update($request->all());

        return redirect()->back()->with('success', 'Jam Pelajaran berhasil diperbarui');
    }

    public function destroy($id)
    {
        JamPelajaran::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Jam Pelajaran berhasil dihapus');
    }
}
