<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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

        // Get all classes with tingkat
        $kelas = DB::table('kelas')
            ->select('kelas.*', DB::raw('CONCAT(tingkat, " ", nama_kelas) as full_name'))
            ->orderBy('tingkat')
            ->orderBy('nama_kelas')
            ->get();

        // Get jam pelajaran
        $jamPelajarans = DB::table('jam_pelajarans')
            ->orderBy('jam_ke')
            ->get();

        return view('guru.jadwal-mengajar.jadwal-mengajar', compact('kelas', 'jamPelajarans'));
    }

    /**
     * Get schedules by class (AJAX)
     */
    public function getByKelas($kelasId)
    {
        try {
            $currentUserId = auth()->id();

            // Get all schedules for the class
            $schedules = DB::table('jadwal_pelajarans as jp')
                ->join('mata_pelajarans as mp', 'jp.mata_pelajaran_id', '=', 'mp.id')
                ->join('users as u', 'jp.guru_id', '=', 'u.id')
                ->where('jp.kelas_id', $kelasId)
                ->select(
                    'jp.*',
                    'mp.nama_mapel',
                    'mp.kode_mapel',
                    'u.name as guru_name',
                    'u.id as guru_id'
                )
                ->get();

            // Get fixed schedules (istirahat, upacara, etc)
            $fixedSchedules = DB::table('fixed_schedules')
                ->where('kelas_id', $kelasId)
                ->orWhereNull('kelas_id')
                ->get();

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
                                'nama_mapel' => $schedule->nama_mapel,
                                'kode_mapel' => $schedule->kode_mapel
                            ],
                            'guru' => [
                                'id' => $schedule->guru_id,
                                'name' => $schedule->guru_name
                            ],
                            'is_current_teacher' => $isCurrentTeacher
                        ];
                    }
                }

                // Add fixed schedules
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
}
