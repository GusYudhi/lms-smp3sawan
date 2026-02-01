<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JadwalPelajaran;
use App\Models\FixedSchedule;
use App\Models\Kelas;
use App\Models\JamPelajaran;
use App\Models\Semester;
use Illuminate\Support\Facades\DB;

class JadwalMengajarController extends Controller
{
    /**
     * Display the teaching schedule page
     */
    public function index()
    {
        // Ensure user is a teacher
        if (!auth()->user()->isGuru()) {
            abort(403, 'Unauthorized access');
        }

        // Get active semester
        $activeSemester = Semester::where('is_active', true)->first();

        $semesterId = $activeSemester ? $activeSemester->id : null;
        $tahunPelajaranId = $activeSemester ? $activeSemester->tahun_pelajaran_id : null;

        // Get all classes with tingkat
        $kelasQuery = Kelas::orderBy('tingkat')->orderBy('nama_kelas');

        // Removed strict filter by tahun_pelajaran_id as some classes might be global
        // if ($tahunPelajaranId) {
        //    $kelasQuery->where('tahun_pelajaran_id', $tahunPelajaranId);
        // }

        $kelas = $kelasQuery->get()->map(function($k) {
            $k->full_name = $k->tingkat . ' ' . $k->nama_kelas;
            return $k;
        });

        // Get jam pelajaran
        $jamQuery = JamPelajaran::orderBy('jam_ke');

        if ($semesterId) {
            $jamQuery->where('semester_id', $semesterId);
        }

        $jamPelajarans = $jamQuery->get();

        return view('guru.jadwal-mengajar.jadwal-mengajar', compact('kelas', 'jamPelajarans'));
    }

    /**
     * Get schedules by class (AJAX)
     */
    public function getByKelas($kelasId)
    {
        try {
            $currentUserId = auth()->id();

            // Get current active semester
            $activeSemester = Semester::where('is_active', true)->first();
            $semesterId = $activeSemester ? $activeSemester->id : null;

            // Get all schedules for the class using Eloquent
            $query = JadwalPelajaran::with(['mataPelajaran', 'guru'])
                ->where('kelas_id', $kelasId);

            if ($semesterId) {
                $query->where('semester_id', $semesterId);
            }

            $schedules = $query->get();

            // Get fixed schedules
            $fixedQuery = FixedSchedule::query();

            if ($semesterId) {
                $fixedQuery->where('semester_id', $semesterId);
            }

            $fixedSchedules = $fixedQuery->get();

            // Organize by day and jam_ke
            $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            $organized = [];

            foreach ($days as $day) {
                $organized[$day] = [];

                // Add lessons
                foreach ($schedules as $schedule) {
                    if ($schedule->hari === $day) {
                        // Check if this is current teacher's schedule
                        $isCurrentTeacher = ($schedule->guru_id == $currentUserId);

                        $organized[$day][$schedule->jam_ke] = [
                            'type' => 'lesson',
                            'id' => $schedule->id,
                            'jam_ke' => $schedule->jam_ke,
                            'mata_pelajaran' => [
                                'id' => $schedule->mata_pelajaran_id,
                                'nama_mapel' => $schedule->mataPelajaran->nama_mapel ?? '-',
                                'kode_mapel' => $schedule->mataPelajaran->kode_mapel ?? '-'
                            ],
                            'guru' => [
                                'id' => $schedule->guru_id,
                                'name' => $schedule->guru->name ?? '-'
                            ],
                            'is_current_teacher' => $isCurrentTeacher
                        ];
                    }
                }

                // Add fixed schedules (Overwrite if exists, typically fixed schedule blocks everything)
                foreach ($fixedSchedules as $fixed) {
                    if ($fixed->hari === $day) {
                        $organized[$day][$fixed->jam_ke] = [
                            'type' => 'fixed',
                            'keterangan' => $fixed->keterangan
                        ];
                    }
                }
            }

            return response()->json($organized);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display today's teaching schedule for current teacher
     */
    public function today()
    {
        // Ensure user is a teacher
        if (!auth()->user()->isGuru()) {
            abort(403, 'Unauthorized access');
        }

        $currentUserId = auth()->id();
        $today = \Carbon\Carbon::now()->locale('id')->dayName;

        $activeSemester = Semester::where('is_active', true)->first();
        $semesterId = $activeSemester ? $activeSemester->id : null;

        // Get today's schedules for current teacher using Eloquent
        $query = JadwalPelajaran::with(['mataPelajaran', 'kelas'])
            ->where('guru_id', $currentUserId)
            ->where('hari', $today);

        if ($semesterId) {
            $query->where('semester_id', $semesterId);
        }

        $schedules = $query->orderBy('jam_ke')->get();

        // Transform for view compatibility if needed, or update view. 
        // View likely accesses object properties directly.
        // But the previous query selected 'kelas_full'. We need to append it.
        $schedules->transform(function($s) {
            if ($s->kelas) {
                $s->kelas_full = $s->kelas->tingkat . ' ' . $s->kelas->nama_kelas;
            }
            return $s;
        });

        // Get jam pelajaran details
        $jamPelajarans = JamPelajaran::orderBy('jam_ke')->get()->keyBy('jam_ke');

        return view('guru.jadwal-mengajar.today', compact('schedules', 'jamPelajarans', 'today'));
    }
}
