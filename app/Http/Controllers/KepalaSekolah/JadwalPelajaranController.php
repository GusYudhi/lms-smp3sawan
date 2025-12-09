<?php

namespace App\Http\Controllers\KepalaSekolah;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\User;
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

        return view('kepala-sekolah.jadwal-pelajaran.index', compact('kelas', 'mapels', 'gurus', 'jamPelajarans', 'semester'));
    }

    public function getByKelas($kelasId)
    {
        try {
            // Get current active semester from session or request
            $semesterId = session('semester_id') ?? request('semester_id');

            // Get Lessons - filter by semester if available
            $query = JadwalPelajaran::with(['mataPelajaran', 'guru'])
                ->where('kelas_id', $kelasId);

            if ($semesterId) {
                $query->where('semester_id', $semesterId);
            }

            $jadwals = $query->get();

            // Get Fixed Schedule (Upacara, Istirahat) - optional, global for all classes
            try {
                $fixedQuery = \App\Models\FixedSchedule::query();

                if ($semesterId) {
                    $fixedQuery->where('semester_id', $semesterId);
                }

                $fixedSchedules = $fixedQuery->get();
            } catch (\Exception $e) {
                // If fixed_schedules table doesn't exist or has issues, just skip it
                $fixedSchedules = collect([]);
            }

            // Group by day and jam
            $schedule = [];
            $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

            foreach ($days as $day) {
                $schedule[$day] = [];
            }

            // Add regular lessons
            foreach ($jadwals as $jadwal) {
                if (!isset($schedule[$jadwal->hari][$jadwal->jam_ke])) {
                    $schedule[$jadwal->hari][$jadwal->jam_ke] = [];
                }
                $schedule[$jadwal->hari][$jadwal->jam_ke] = [
                    'id' => $jadwal->id,
                    'mapel' => $jadwal->mataPelajaran->nama_mapel ?? 'N/A',
                    'guru' => $jadwal->guru->name ?? 'N/A',
                    'type' => 'regular'
                ];
            }

            // Add fixed schedules (Upacara, Istirahat) if available
            foreach ($fixedSchedules as $fixed) {
                if (!isset($schedule[$fixed->hari][$fixed->jam_ke])) {
                    $schedule[$fixed->hari][$fixed->jam_ke] = [];
                }
                $schedule[$fixed->hari][$fixed->jam_ke] = [
                    'id' => $fixed->id,
                    'label' => $fixed->keterangan ?? $fixed->label ?? 'Fixed Schedule',
                    'type' => 'fixed'
                ];
            }

            return response()->json([
                'schedule' => $schedule
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Gagal memuat jadwal',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
