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
                ->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu')")
                ->orderBy('jam_ke')
                ->get();
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
            'jam_ke' => 'required|integer|min:0',
            'keterangan' => 'required|string|max:255',
            'tipe' => 'nullable|string|in:umum,kokurikuler',
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

        FixedSchedule::create([
            'hari' => $request->hari,
            'jam_ke' => $request->jam_ke,
            'keterangan' => $request->keterangan,
            'tipe' => $request->input('tipe', FixedSchedule::TIPE_UMUM),
            'semester_id' => $request->semester_id,
        ]);

        return redirect()->back()->with('success', 'Jadwal Tetap berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $fixedSchedule = FixedSchedule::findOrFail($id);

        $request->validate([
            'hari' => 'required|string',
            'jam_ke' => 'required|integer|min:0',
            'keterangan' => 'required|string|max:255',
            'tipe' => 'nullable|string|in:umum,kokurikuler',
        ]);

        // Check duplicate on other records in the same semester
        $query = FixedSchedule::where('hari', $request->hari)
            ->where('jam_ke', $request->jam_ke)
            ->where('id', '!=', $fixedSchedule->id);

        if ($fixedSchedule->semester_id) {
            $query->where('semester_id', $fixedSchedule->semester_id);
        }

        if ($query->exists()) {
            return back()->withErrors(['msg' => 'Jadwal tetap untuk hari dan jam tersebut sudah digunakan.']);
        }

        $fixedSchedule->update([
            'hari' => $request->hari,
            'jam_ke' => $request->jam_ke,
            'keterangan' => $request->keterangan,
            'tipe' => $request->input('tipe', FixedSchedule::TIPE_UMUM),
        ]);

        return redirect()->back()->with('success', 'Jadwal Tetap berhasil diperbarui');
    }

    public function destroy($id)
    {
        FixedSchedule::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Jadwal Tetap berhasil dihapus');
    }
}
