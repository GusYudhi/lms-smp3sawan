<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FixedSchedule;
use App\Models\JamPelajaran;
use App\Models\Semester;
use Illuminate\Support\Facades\DB;

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

        $groupedFixedSchedules = FixedSchedule::groupConsecutive($fixedSchedules);

        return view('admin.fixed-schedule.index', compact('fixedSchedules', 'groupedFixedSchedules', 'jamPelajarans', 'semester'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'hari' => 'required|string',
            'jam_ke' => 'required|integer|min:0',
            'jam_selesai' => 'nullable|integer|min:0',
            'keterangan' => 'required|string|max:255',
            'tipe' => 'nullable|string|in:umum,kokurikuler',
            'semester_id' => 'nullable|exists:semester,id',
        ]);

        $startJam = (int) $request->jam_ke;
        $endJam = $request->filled('jam_selesai') ? (int) $request->jam_selesai : $startJam;

        if ($endJam < $startJam) {
            return back()->withInput()->withErrors(['msg' => 'Sampai Jam Ke- tidak boleh lebih kecil dari Jam Ke- mulai.']);
        }

        // Check if any slot in range already exists for this semester
        $conflictingSlots = [];
        for ($j = $startJam; $j <= $endJam; $j++) {
            $query = FixedSchedule::where('hari', $request->hari)
                ->where('jam_ke', $j);

            if ($request->semester_id) {
                $query->where('semester_id', $request->semester_id);
            }

            if ($query->exists()) {
                $conflictingSlots[] = "Jam ke-$j";
            }
        }

        if (!empty($conflictingSlots)) {
            return back()->withInput()->withErrors([
                'msg' => 'Jadwal tetap untuk hari ' . $request->hari . ' pada ' . implode(', ', $conflictingSlots) . ' sudah terisi.',
            ]);
        }

        DB::transaction(function () use ($request, $startJam, $endJam) {
            for ($j = $startJam; $j <= $endJam; $j++) {
                FixedSchedule::create([
                    'hari' => $request->hari,
                    'jam_ke' => $j,
                    'keterangan' => $request->keterangan,
                    'tipe' => $request->input('tipe', FixedSchedule::TIPE_UMUM),
                    'semester_id' => $request->semester_id,
                ]);
            }
        });

        $durasiMsg = ($startJam === $endJam) ? "Jam ke-{$startJam}" : "Jam ke-{$startJam} s/d {$endJam}";
        return redirect()->back()->with('success', "Jadwal Tetap ({$request->hari}, {$durasiMsg}) berhasil ditambahkan");
    }

    public function update(Request $request, $id)
    {
        $fixedSchedule = FixedSchedule::findOrFail($id);

        $request->validate([
            'hari' => 'required|string',
            'jam_ke' => 'required|integer|min:0',
            'jam_selesai' => 'nullable|integer|min:0',
            'keterangan' => 'required|string|max:255',
            'tipe' => 'nullable|string|in:umum,kokurikuler',
            'group_ids' => 'nullable|string',
        ]);

        $startJam = (int) $request->jam_ke;
        $endJam = $request->filled('jam_selesai') ? (int) $request->jam_selesai : $startJam;

        if ($endJam < $startJam) {
            return back()->withInput()->withErrors(['msg' => 'Sampai Jam Ke- tidak boleh lebih kecil dari Jam Ke- mulai.']);
        }

        $groupIds = $request->filled('group_ids')
            ? array_filter(array_map('intval', explode(',', $request->input('group_ids'))))
            : [$fixedSchedule->id];

        // Check conflicts on slots in range outside current group
        $conflictingSlots = [];
        for ($j = $startJam; $j <= $endJam; $j++) {
            $query = FixedSchedule::where('hari', $request->hari)
                ->where('jam_ke', $j)
                ->whereNotIn('id', $groupIds);

            if ($fixedSchedule->semester_id) {
                $query->where('semester_id', $fixedSchedule->semester_id);
            }

            if ($query->exists()) {
                $conflictingSlots[] = "Jam ke-$j";
            }
        }

        if (!empty($conflictingSlots)) {
            return back()->withInput()->withErrors([
                'msg' => 'Jadwal tetap untuk hari ' . $request->hari . ' pada ' . implode(', ', $conflictingSlots) . ' sudah digunakan.',
            ]);
        }

        DB::transaction(function () use ($groupIds, $request, $startJam, $endJam, $fixedSchedule) {
            $neededJams = range($startJam, $endJam);

            // Always update and preserve the primary record
            $fixedSchedule->update([
                'hari' => $request->hari,
                'jam_ke' => $startJam,
                'keterangan' => $request->keterangan,
                'tipe' => $request->input('tipe', FixedSchedule::TIPE_UMUM),
            ]);

            // Other existing IDs in this group
            $otherIds = array_diff($groupIds, [$fixedSchedule->id]);
            $existingOthers = FixedSchedule::whereIn('id', $otherIds)->get();

            // Remaining hours to populate:
            $remainingJams = array_slice($neededJams, 1);
            $usedOtherIds = [];

            foreach ($remainingJams as $j) {
                if ($existingOthers->isNotEmpty() && $other = $existingOthers->shift()) {
                    $other->update([
                        'hari' => $request->hari,
                        'jam_ke' => $j,
                        'keterangan' => $request->keterangan,
                        'tipe' => $request->input('tipe', FixedSchedule::TIPE_UMUM),
                    ]);
                    $usedOtherIds[] = $other->id;
                } else {
                    FixedSchedule::create([
                        'hari' => $request->hari,
                        'jam_ke' => $j,
                        'keterangan' => $request->keterangan,
                        'tipe' => $request->input('tipe', FixedSchedule::TIPE_UMUM),
                        'semester_id' => $fixedSchedule->semester_id,
                    ]);
                }
            }

            // Delete any unneeded excess slots from the old group
            $toDelete = array_diff($otherIds, $usedOtherIds);
            if (!empty($toDelete)) {
                FixedSchedule::whereIn('id', $toDelete)->delete();
            }
        });

        $durasiMsg = ($startJam === $endJam) ? "Jam ke-{$startJam}" : "Jam ke-{$startJam} s/d {$endJam}";
        return redirect()->back()->with('success', "Jadwal Tetap ({$request->hari}, {$durasiMsg}) berhasil diperbarui");
    }

    public function destroy(Request $request, $id)
    {
        $fixedSchedule = FixedSchedule::findOrFail($id);
        $groupIds = $request->input('group_ids');

        if ($groupIds) {
            $ids = is_array($groupIds) ? $groupIds : array_filter(array_map('intval', explode(',', $groupIds)));
            FixedSchedule::whereIn('id', $ids)->delete();
        } else {
            $fixedSchedule->delete();
        }

        return redirect()->back()->with('success', 'Jadwal Tetap berhasil dihapus');
    }
}
