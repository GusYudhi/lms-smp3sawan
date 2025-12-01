<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\User;
use App\Models\FixedSchedule;
use App\Models\JamPelajaran;
use App\Models\Semester;

class JadwalPelajaranController extends Controller
{
    public function index(Request $request)
    {
        $semesterId = $request->input('semester_id');

        if ($semesterId) {
            $semester = Semester::with('tahunPelajaran')->findOrFail($semesterId);
            $mapels = MataPelajaran::where('semester_id', $semesterId)->get();
            $jamPelajarans = JamPelajaran::where('semester_id', $semesterId)->orderBy('jam_ke')->get();
        } else {
            $semester = null;
            $mapels = MataPelajaran::all();
            $jamPelajarans = JamPelajaran::orderBy('jam_ke')->get();
        }

        $kelas = Kelas::all();
        $gurus = User::where('role', 'guru')->get();

        return view('admin.jadwal-mapel.jadwal', compact('kelas', 'mapels', 'gurus', 'jamPelajarans', 'semester'));
    }

    public function getByKelas($kelasId)
    {
        // Get Lessons
        $jadwals = JadwalPelajaran::with(['mataPelajaran', 'guru'])
            ->where('kelas_id', $kelasId)
            ->get();

        // Get Fixed Schedules
        $fixedSchedules = FixedSchedule::all();

        // Structure data: [hari][jam_ke] = item
        $data = [];
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        foreach ($days as $hari) {
            $data[$hari] = [];
            // Initialize with empty or fixed
            for ($i = 1; $i <= 7; $i++) { // Assuming 7 slots
                $fixed = $fixedSchedules->where('hari', $hari)->where('jam_ke', $i)->first();
                if ($fixed) {
                    $data[$hari][$i] = [
                        'type' => 'fixed',
                        'keterangan' => $fixed->keterangan
                    ];
                } else {
                    $lesson = $jadwals->where('hari', $hari)->where('jam_ke', $i)->first();
                    if ($lesson) {
                        $data[$hari][$i] = [
                            'type' => 'lesson',
                            'id' => $lesson->id,
                            'mata_pelajaran' => $lesson->mataPelajaran,
                            'guru' => $lesson->guru,
                            'jam_ke' => $lesson->jam_ke
                        ];
                    } else {
                        $data[$hari][$i] = null; // Empty slot
                    }
                }
            }
        }

        return response()->json($data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'mata_pelajaran_id' => 'required|exists:mata_pelajarans,id',
            'guru_id' => 'required|exists:users,id',
            'hari' => 'required|string',
            'jam_ke' => 'required|integer',
            'jumlah_jam' => 'nullable|integer|min:1|max:7',
        ]);

        $jumlahJam = $request->input('jumlah_jam', 1);
        $startJam = $request->jam_ke;
        $kelasId = $request->kelas_id;
        $hari = $request->hari;
        $guruId = $request->guru_id;
        $mapelId = $request->mata_pelajaran_id;

        // 1. Pre-check ALL slots for conflicts
        for ($i = 0; $i < $jumlahJam; $i++) {
            $currentJam = $startJam + $i;

            // Check if JamPelajaran exists
            $jamExists = JamPelajaran::where('jam_ke', $currentJam)->exists();
            if (!$jamExists) {
                 return response()->json(['message' => "Jam ke-$currentJam tidak valid atau tidak tersedia."], 422);
            }

            // Check Fixed Schedule
            $isFixed = FixedSchedule::where('hari', $hari)
                ->where('jam_ke', $currentJam)
                ->exists();
            if ($isFixed) {
                return response()->json(['message' => "Slot jam ke-$currentJam adalah jadwal tetap (misal: Upacara)."], 422);
            }

            // Check Teacher Conflict
            $teacherBusy = JadwalPelajaran::where('guru_id', $guruId)
                ->where('hari', $hari)
                ->where('jam_ke', $currentJam)
                ->exists();
            if ($teacherBusy) {
                return response()->json(['message' => "Guru ini sudah mengajar di kelas lain pada jam ke-$currentJam."], 422);
            }

            // Check Class Slot Taken
            $slotTaken = JadwalPelajaran::where('kelas_id', $kelasId)
                ->where('hari', $hari)
                ->where('jam_ke', $currentJam)
                ->exists();
            if ($slotTaken) {
                return response()->json(['message' => "Slot jam ke-$currentJam sudah terisi di kelas ini."], 422);
            }
        }

        // 2. Insert ALL slots
        for ($i = 0; $i < $jumlahJam; $i++) {
            $currentJam = $startJam + $i;
            JadwalPelajaran::create([
                'kelas_id' => $kelasId,
                'mata_pelajaran_id' => $mapelId,
                'guru_id' => $guruId,
                'hari' => $hari,
                'jam_ke' => $currentJam,
            ]);
        }

        return response()->json(['message' => 'Jadwal berhasil ditambahkan (' . $jumlahJam . ' jam pelajaran).']);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'mata_pelajaran_id' => 'required|exists:mata_pelajarans,id',
            'guru_id' => 'required|exists:users,id',
            'hari' => 'required|string',
            'jam_ke' => 'required|integer',
        ]);

        // Check if slot is fixed
        $isFixed = FixedSchedule::where('hari', $request->hari)
            ->where('jam_ke', $request->jam_ke)
            ->exists();

        if ($isFixed) {
            return response()->json(['message' => 'Slot ini adalah jadwal tetap.'], 422);
        }

        // Check for conflicts (Teacher busy)
        $conflict = JadwalPelajaran::where('guru_id', $request->guru_id)
            ->where('hari', $request->hari)
            ->where('jam_ke', $request->jam_ke)
            ->where('id', '!=', $id)
            ->exists();

        if ($conflict) {
            return response()->json(['message' => 'Guru ini sudah mengajar di kelas lain pada jam ke-' . $request->jam_ke], 422);
        }

        $jadwal = JadwalPelajaran::findOrFail($id);
        $jadwal->update($request->all());

        return response()->json(['message' => 'Jadwal berhasil diperbarui']);
    }

    public function destroy($id)
    {
        JadwalPelajaran::findOrFail($id)->delete();
        return response()->json(['message' => 'Jadwal berhasil dihapus']);
    }
}
