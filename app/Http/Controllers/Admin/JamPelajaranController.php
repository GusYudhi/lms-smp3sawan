<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JamPelajaran;

class JamPelajaranController extends Controller
{
    public function index()
    {
        $jamPelajarans = JamPelajaran::orderBy('jam_ke')->get();
        return view('admin.jam-pelajaran.index', compact('jamPelajarans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'jam_ke' => 'required|integer|unique:jam_pelajarans,jam_ke',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required|after:jam_mulai',
        ]);

        JamPelajaran::create($request->all());

        return redirect()->route('admin.jam-pelajaran.index')->with('success', 'Jam Pelajaran berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'jam_ke' => 'required|integer|unique:jam_pelajarans,jam_ke,' . $id,
            'jam_mulai' => 'required',
            'jam_selesai' => 'required|after:jam_mulai',
        ]);

        $jam = JamPelajaran::findOrFail($id);
        $jam->update($request->all());

        return redirect()->route('admin.jam-pelajaran.index')->with('success', 'Jam Pelajaran berhasil diperbarui');
    }

    public function destroy($id)
    {
        JamPelajaran::findOrFail($id)->delete();
        return redirect()->route('admin.jam-pelajaran.index')->with('success', 'Jam Pelajaran berhasil dihapus');
    }
}
