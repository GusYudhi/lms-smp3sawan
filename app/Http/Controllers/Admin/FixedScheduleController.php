<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FixedSchedule;
use App\Models\JamPelajaran;
use App\Models\Semester;

class FixedScheduleController extends Controller
{
    public function index(Request $request)
    {
        $semesterId = $request->input('semester_id');

        if ($semesterId) {
            $semester = Semester::with('tahunPelajaran')->findOrFail($semesterId);
            $fixedSchedules = FixedSchedule::where('semester_id', $semesterId)
                ->orderBy('hari')->orderBy('jam_ke')->get();
            $jamPelajarans = JamPelajaran::where('semester_id', $semesterId)->orderBy('jam_ke')->get();
        } else {
            $semester = null;
            $fixedSchedules = FixedSchedule::orderBy('hari')->orderBy('jam_ke')->get();
            $jamPelajarans = JamPelajaran::orderBy('jam_ke')->get();
        }

        return view('admin.fixed-schedule.index', compact('fixedSchedules', 'jamPelajarans', 'semester'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'hari' => 'required|string',
            'jam_ke' => 'required|integer',
            'keterangan' => 'required|string',
            'semester_id' => 'nullable|exists:semester,id',
        ]);

        // Check if exists for this semester
        $query = FixedSchedule::where('hari', $request->hari)
            ->where('jam_ke', $request->jam_ke);

        if ($request->semester_id) {
            $query->where('semester_id', $request->semester_id);
        }

        if ($query->exists()) {
            return back()->withErrors(['msg' => 'Jadwal tetap untuk hari dan jam tersebut sudah ada.']);
        }

        FixedSchedule::create($request->all());

        return redirect()->back()->with('success', 'Jadwal Tetap berhasil ditambahkan');
    }

    public function destroy($id)
    {
        FixedSchedule::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Jadwal Tetap berhasil dihapus');
    }
}
