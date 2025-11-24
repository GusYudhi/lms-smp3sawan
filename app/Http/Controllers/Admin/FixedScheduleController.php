<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FixedSchedule;
use App\Models\JamPelajaran;

class FixedScheduleController extends Controller
{
    public function index()
    {
        $fixedSchedules = FixedSchedule::orderBy('hari')->orderBy('jam_ke')->get();
        $jamPelajarans = JamPelajaran::orderBy('jam_ke')->get();
        return view('admin.fixed-schedule.index', compact('fixedSchedules', 'jamPelajarans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'hari' => 'required|string',
            'jam_ke' => 'required|integer|exists:jam_pelajarans,jam_ke',
            'keterangan' => 'required|string',
        ]);

        // Check if exists
        $exists = FixedSchedule::where('hari', $request->hari)
            ->where('jam_ke', $request->jam_ke)
            ->exists();

        if ($exists) {
            return back()->withErrors(['msg' => 'Jadwal tetap untuk hari dan jam tersebut sudah ada.']);
        }

        FixedSchedule::create($request->all());

        return redirect()->route('admin.fixed-schedule.index')->with('success', 'Jadwal Tetap berhasil ditambahkan');
    }

    public function destroy($id)
    {
        FixedSchedule::findOrFail($id)->delete();
        return redirect()->route('admin.fixed-schedule.index')->with('success', 'Jadwal Tetap berhasil dihapus');
    }
}
