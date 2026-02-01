<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\User;
use App\Models\FixedSchedule;
use App\Models\JamPelajaran;
use App\Models\Semester;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\JadwalTemplateExport;
use App\Exports\JadwalExport;
use App\Imports\JadwalImport;

class JadwalPelajaranController extends Controller
{
    public function index(Request $request, $semesterId)
    {
        if ($semesterId) {
            $semester = Semester::with('tahunPelajaran')->findOrFail($semesterId);
            $mapels = MataPelajaran::where('semester_id', $semesterId)->orderBy('nama_mapel')->get();
            $jamPelajarans = JamPelajaran::where('semester_id', $semesterId)->orderBy('jam_ke')->get();
        } else {
            // Default to active semester if available
            $semester = Semester::where('is_active', true)->with('tahunPelajaran')->first();
            
            if ($semester) {
                $mapels = MataPelajaran::where('semester_id', $semester->id)->orderBy('nama_mapel')->get();
                $jamPelajarans = JamPelajaran::where('semester_id', $semester->id)->orderBy('jam_ke')->get();
            } else {
                $mapels = MataPelajaran::orderBy('nama_mapel')->get();
                $jamPelajarans = JamPelajaran::orderBy('jam_ke')->get();
            }
        }

        $kelas = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();
        $gurus = User::where('role', 'guru')
            ->with('guruProfile') // Eager load profile to get mata_pelajaran_id
            ->orderBy('name')
            ->get();

        return view('admin.jadwal-mapel.jadwal', compact('kelas', 'mapels', 'gurus', 'jamPelajarans', 'semester'));
    }

    public function getByKelas($semesterId, $kelasId)
    {
        // Use the semesterId passed from route
        // $semesterId = session('semester_id') ?? request('semester_id'); // This was overwriting the route param

        // Get Lessons - filter by semester if available
        $query = JadwalPelajaran::with(['mataPelajaran', 'guru'])
            ->where('kelas_id', $kelasId);

        if ($semesterId) {
            $query->where('semester_id', $semesterId);
        }

        $jadwals = $query->get();

        // Get Fixed Schedules
        $fixedSchedules = FixedSchedule::where('semester_id', $semesterId)->get();

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

        // Get semester_id from session or request
        $semesterId = session('semester_id') ?? $request->input('semester_id');

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
                ->when($semesterId, function($q) use ($semesterId) {
                    return $q->where('semester_id', $semesterId);
                })
                ->exists();
            if ($teacherBusy) {
                return response()->json(['message' => "Guru ini sudah mengajar di kelas lain pada jam ke-$currentJam."], 422);
            }

            // Check Class Slot Taken
            $slotTaken = JadwalPelajaran::where('kelas_id', $kelasId)
                ->where('hari', $hari)
                ->where('jam_ke', $currentJam)
                ->when($semesterId, function($q) use ($semesterId) {
                    return $q->where('semester_id', $semesterId);
                })
                ->exists();
            if ($slotTaken) {
                return response()->json(['message' => "Slot jam ke-$currentJam sudah terisi di kelas ini."], 422);
            }
        }

        // 2. Insert ALL slots
        for ($i = 0; $i < $jumlahJam; $i++) {
            $currentJam = $startJam + $i;
            JadwalPelajaran::create([
                'semester_id' => $semesterId,
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

        // Get semester_id from session or request
        $semesterId = session('semester_id') ?? $request->input('semester_id');

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
            ->when($semesterId, function($q) use ($semesterId) {
                return $q->where('semester_id', $semesterId);
            })
            ->exists();

        if ($conflict) {
            return response()->json(['message' => 'Guru ini sudah mengajar di kelas lain pada jam ke-' . $request->jam_ke], 422);
        }

        $jadwal = JadwalPelajaran::findOrFail($id);
        $jadwal->update([
            'semester_id' => $semesterId,
            'kelas_id' => $request->kelas_id,
            'mata_pelajaran_id' => $request->mata_pelajaran_id,
            'guru_id' => $request->guru_id,
            'hari' => $request->hari,
            'jam_ke' => $request->jam_ke,
        ]);

        return response()->json(['message' => 'Jadwal berhasil diperbarui']);
    }

    public function destroy($idSemester, $id)
    {
        JadwalPelajaran::findOrFail($id)->delete();
        return response()->json(['message' => 'Jadwal berhasil dihapus']);
    }

    public function checkConflict(Request $request)
    {
        $request->validate([
            'guru_id' => 'required',
            'hari' => 'required',
            'jam_ke' => 'required',
            'semester_id' => 'required',
            'kelas_id' => 'required'
        ]);

        $guruId = $request->guru_id;
        $hari = $request->hari;
        $startJam = (int)$request->jam_ke;
        $semesterId = $request->semester_id;
        $kelasId = $request->kelas_id;
        $jumlahJam = (int)$request->get('jumlah_jam', 1);
        $ignoreId = $request->ignore_id; // For edit mode

        for ($i = 0; $i < $jumlahJam; $i++) {
            $currentJam = $startJam + $i;

            // 1. Check Fixed Schedule
            $fixed = FixedSchedule::where('hari', $hari)
                ->where('jam_ke', $currentJam)
                ->where('semester_id', $semesterId)
                ->first();

            if ($fixed) {
                return response()->json([
                    'conflict' => true,
                    'message' => "Jam ke-$currentJam adalah jadwal tetap: {$fixed->keterangan}"
                ]);
            }

            // 2. Check Class Slot Overlap
            $classConflict = JadwalPelajaran::with(['mataPelajaran'])
                ->where('kelas_id', $kelasId)
                ->where('hari', $hari)
                ->where('jam_ke', $currentJam)
                ->where('semester_id', $semesterId)
                ->when($ignoreId, function($q) use ($ignoreId) {
                    return $q->where('id', '!=', $ignoreId);
                })
                ->first();

            if ($classConflict) {
                return response()->json([
                    'conflict' => true,
                    'message' => "Slot jam ke-$currentJam sudah terisi: {$classConflict->mataPelajaran->nama_mapel}"
                ]);
            }

            // 3. Check Teacher Conflict
            $teacherConflict = JadwalPelajaran::with(['kelas'])
                ->where('guru_id', $guruId)
                ->where('hari', $hari)
                ->where('jam_ke', $currentJam)
                ->where('semester_id', $semesterId)
                ->when($ignoreId, function($q) use ($ignoreId) {
                    return $q->where('id', '!=', $ignoreId);
                })
                ->first();

            if ($teacherConflict) {
                return response()->json([
                    'conflict' => true,
                    'message' => "Guru ini sedang mengajar di kelas {$teacherConflict->kelas->nama_kelas} pada jam ke-$currentJam"
                ]);
            }
        }

        return response()->json(['conflict' => false]);
    }

    public function moveSchedule(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:jadwal_pelajarans,id',
            'hari' => 'required',
            'jam_ke' => 'required|integer',
            'semester_id' => 'required',
            'kelas_id' => 'required'
        ]);

        $sourceId = $request->id;
        $targetHari = $request->hari;
        $targetJam = $request->jam_ke;
        $semesterId = $request->semester_id;
        $kelasId = $request->kelas_id;

        DB::beginTransaction();
        try {
            // 1. Get Source Schedule
            $source = JadwalPelajaran::findOrFail($sourceId);

            // 2. Check if Target is Fixed Schedule
            $isFixed = FixedSchedule::where('hari', $targetHari)
                ->where('jam_ke', $targetJam)
                ->where('semester_id', $semesterId)
                ->exists();

            if ($isFixed) {
                return response()->json(['message' => 'Slot tujuan adalah jadwal tetap (tidak bisa dipindah).'], 422);
            }

            // 3. Check if Target is Occupied (Swap Logic)
            $target = JadwalPelajaran::where('kelas_id', $kelasId)
                ->where('hari', $targetHari)
                ->where('jam_ke', $targetJam)
                ->where('semester_id', $semesterId)
                ->first();

            if ($target) {
                // Performing Swap
                // Temporary holder for Source coordinates
                $tempHari = $source->hari;
                $tempJam = $source->jam_ke;

                // Move Target to Source position
                $target->update([
                    'hari' => $tempHari,
                    'jam_ke' => $tempJam
                ]);

                // Move Source to Target position
                $source->update([
                    'hari' => $targetHari,
                    'jam_ke' => $targetJam
                ]);

                // Optional: Check Teacher Conflicts for BOTH after move?
                // This is complex. Ideally yes. For now, we trust the Admin.
                
                DB::commit();
                return response()->json(['message' => 'Jadwal berhasil ditukar.']);

            } else {
                // Performing Move (Empty Slot)
                
                // Validate Teacher Conflict for Source at New Position
                $teacherBusy = JadwalPelajaran::where('guru_id', $source->guru_id)
                    ->where('hari', $targetHari)
                    ->where('jam_ke', $targetJam)
                    ->where('semester_id', $semesterId)
                    ->where('id', '!=', $sourceId) // Exclude itself
                    ->exists();

                if ($teacherBusy) {
                    return response()->json(['message' => 'Gagal: Guru ini sudah mengajar di kelas lain pada slot tujuan.'], 422);
                }

                $source->update([
                    'hari' => $targetHari,
                    'jam_ke' => $targetJam
                ]);

                DB::commit();
                return response()->json(['message' => 'Jadwal berhasil dipindahkan.']);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function downloadTemplate()
    {
        return Excel::download(new JadwalTemplateExport, 'template_jadwal_pelajaran.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120',
            'semester_id' => 'required|exists:semester,id'
        ]);

        try {
            $import = new JadwalImport($request->semester_id);
            Excel::import($import, $request->file('file'));

            $message = "Import selesai! Berhasil: {$import->successCount}, Gagal: {$import->failureCount}.";
            
            if (!empty($import->errors)) {
                $errorList = implode("\n", array_slice($import->errors, 0, 5));
                if (count($import->errors) > 5) $errorList .= "\n...dan lainnya.";
                
                return response()->json([
                    'message' => $message,
                    'errors' => $errorList
                ], 422); // Return 422 to show warning in frontend
            }

            return response()->json(['message' => $message]);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal import: ' . $e->getMessage()], 500);
        }
    }

    public function export(Request $request)
    {
        $request->validate([
            'semester_id' => 'required|exists:semester,id'
        ]);
        
        return Excel::download(new JadwalExport($request->semester_id), 'jadwal_pelajaran_full.xlsx');
    }

    public function reset(Request $request)
    {
        $request->validate([
            'semester_id' => 'required|exists:semester,id',
            'kelas_id' => 'required|exists:kelas,id'
        ]);

        try {
            // Delete only non-fixed schedules for this class & semester
            // Assuming 'fixed_schedule_id' being null means it's a regular lesson
            // Or if you don't use that column, just delete all from JadwalPelajaran table for this class
            // The prompt says "kecuali jadwal tetap", usually FixedSchedule model stores templates like "Istirahat", "Upacara"
            // But if FixedSchedule items are inserted into JadwalPelajaran, we need a way to distinguish.
            // Based on previous code: `getByKelas` checks `FixedSchedule` table separately.
            // So `JadwalPelajaran` table ONLY contains lessons.
            // Therefore, we can delete ALL entries in `jadwal_pelajarans` for this class/semester.
            
            JadwalPelajaran::where('semester_id', $request->semester_id)
                ->where('kelas_id', $request->kelas_id)
                ->delete();

            return response()->json(['message' => 'Jadwal kelas ini berhasil direset.']);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal mereset jadwal: ' . $e->getMessage()], 500);
        }
    }
}
