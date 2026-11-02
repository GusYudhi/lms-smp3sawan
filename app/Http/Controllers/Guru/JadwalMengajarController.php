<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\FixedSchedule;
use App\Models\JadwalPelajaran;
use App\Models\JamPelajaran;
use App\Models\Kelas;
use App\Models\Semester;

class JadwalMengajarController extends Controller
{
    /**
     * Display the teaching schedule page
     */
    public function index()
    {
        // Ensure user is a teacher
        /** @var \App\Models\User|null $user */
        $user = auth()->user();
        if (! $user || ! $user->isGuru()) {
            abort(403, 'Unauthorized access');
        }

        // Get active semester (tied to active academic year)
        $activeSemester = Semester::getActive();

        $semesterId = $activeSemester ? $activeSemester->id : null;
        $tahunPelajaranId = $activeSemester ? $activeSemester->tahun_pelajaran_id : null;

        // Get all classes with tingkat
        $kelasQuery = Kelas::orderBy('tingkat')->orderBy('nama_kelas');

        // Removed strict filter by tahun_pelajaran_id as some classes might be global
        // if ($tahunPelajaranId) {
        //    $kelasQuery->where('tahun_pelajaran_id', $tahunPelajaranId);
        // }

        $kelas = $kelasQuery->get()->map(function ($k) {
            $k->full_name = $k->tingkat.' '.$k->nama_kelas;

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

            // Get current active semester (tied to active academic year)
            $activeSemester = Semester::getActive();
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

            // Get all available time slots based on active semester or default range
            $jamQuery = JamPelajaran::orderBy('jam_ke');
            if ($semesterId) {
                $jamQuery->where('semester_id', $semesterId);
            }
            $jamPelajarans = $jamQuery->get();
            $slotNumbers = $jamPelajarans->isNotEmpty()
                ? $jamPelajarans->pluck('jam_ke')->unique()->sort()->values()
                : collect(range(1, 7));

            // Organize by day and jam_ke
            $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            $organized = [];

            foreach ($days as $day) {
                $organized[$day] = [];

                foreach ($slotNumbers as $i) {
                    $fixed = $fixedSchedules->where('hari', $day)->where('jam_ke', $i)->first();
                    if ($fixed) {
                        $organized[$day][$i] = [
                            'type' => 'fixed',
                            'keterangan' => $fixed->keterangan,
                        ];
                    } else {
                        $lesson = $schedules->where('hari', $day)->where('jam_ke', $i)->first();
                        if ($lesson) {
                            $isCurrentTeacher = ($lesson->guru_id == $currentUserId);
                            $organized[$day][$i] = [
                                'type' => 'lesson',
                                'id' => $lesson->id,
                                'jam_ke' => $lesson->jam_ke,
                                'mata_pelajaran' => [
                                    'id' => $lesson->mata_pelajaran_id,
                                    'nama_mapel' => $lesson->mataPelajaran->nama_mapel ?? '-',
                                    'kode_mapel' => $lesson->mataPelajaran->kode_mapel ?? '-',
                                ],
                                'guru' => [
                                    'id' => $lesson->guru_id,
                                    'name' => $lesson->guru->name ?? '-',
                                ],
                                'is_current_teacher' => $isCurrentTeacher,
                            ];
                        } else {
                            $organized[$day][$i] = null;
                        }
                    }
                }
            }

            return response()->json($organized);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Terjadi kesalahan: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display today's teaching schedule for current teacher
     */
    public function today()
    {
        // Ensure user is a teacher
        /** @var \App\Models\User|null $user */
        $user = auth()->user();
        if (! $user || ! $user->isGuru()) {
            abort(403, 'Unauthorized access');
        }

        $currentUserId = $user->id;
        $today = \Carbon\Carbon::now()->locale('id')->dayName;

        $activeSemester = Semester::getActive();
        $semesterId = $activeSemester ? $activeSemester->id : null;

        // Get today's schedules for current teacher using Eloquent
        $query = JadwalPelajaran::with(['mataPelajaran', 'kelas'])
            ->where('guru_id', $currentUserId)
            ->where('hari', $today);

        if ($semesterId) {
            $query->where('semester_id', $semesterId);
        }

        $rawSchedules = $query->orderBy('jam_ke')->get();

        // Ambil jurnal yang sudah diisi hari ini oleh guru
        $todayJurnals = \App\Models\JurnalMengajar::where('guru_id', $currentUserId)
            ->whereDate('tanggal', \Carbon\Carbon::today())
            ->get();

        // Gabungkan jadwal jam berurutan untuk kelas dan mapel yang sama
        $grouped = JadwalPelajaran::groupConsecutiveSchedules($rawSchedules, $todayJurnals);

        $schedules = $grouped->map(function ($item) {
            $obj = (object) $item;
            $obj->kelas_full = $item['kelas']
                ? ($item['kelas']->tingkat.' '.$item['kelas']->nama_kelas)
                : '-';

            return $obj;
        });

        // Get jam pelajaran details
        $jamQuery = JamPelajaran::orderBy('jam_ke');
        if ($semesterId) {
            $jamQuery->where('semester_id', $semesterId);
        }
        $jamPelajarans = $jamQuery->get()->keyBy('jam_ke');

        $totalJp = $schedules->sum('jumlah_jp');
        $totalSesi = $schedules->count();
        $sesiTerisi = $schedules->where('has_jurnal', true)->count();
        $sesiBelumTerisi = $schedules->where('has_jurnal', false)->count();

        return view('guru.jadwal-mengajar.today', compact(
            'schedules',
            'jamPelajarans',
            'today',
            'totalJp',
            'totalSesi',
            'sesiTerisi',
            'sesiBelumTerisi'
        ));
    }
}
