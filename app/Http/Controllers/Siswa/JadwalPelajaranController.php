<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\FixedSchedule;
use App\Models\JadwalPelajaran;
use App\Models\JamPelajaran;
use App\Models\Semester;
use Carbon\Carbon;

class JadwalPelajaranController extends Controller
{
    /**
     * Display the schedule page for students
     */
    public function index()
    {
        // Ensure user is a student
        if (! auth()->user()->isSiswa()) {
            abort(403, 'Unauthorized access');
        }

        // Get student profile with class
        $studentProfile = auth()->user()->studentProfile;

        if (! $studentProfile || ! $studentProfile->kelas_id) {
            return view('siswa.jadwal-pelajaran.jadwal-mapel', [
                'studentProfile' => null,
                'todaySchedules' => collect(),
                'tomorrowSchedules' => collect(),
                'todayTimeline' => collect(),
                'tomorrowTimeline' => collect(),
                'weeklySchedules' => [],
                'weeklyGrid' => [],
                'slotNumbers' => collect(),
                'jamPelajaranList' => collect(),
                'message' => 'Anda belum terdaftar di kelas manapun. Silakan hubungi admin.',
            ]);
        }

        // Get active semester
        $activeSemester = Semester::getActive();

        if (! $activeSemester) {
            return view('siswa.jadwal-pelajaran.jadwal-mapel', [
                'studentProfile' => $studentProfile,
                'todaySchedules' => collect(),
                'tomorrowSchedules' => collect(),
                'todayTimeline' => collect(),
                'tomorrowTimeline' => collect(),
                'weeklySchedules' => [],
                'weeklyGrid' => [],
                'slotNumbers' => collect(),
                'jamPelajaranList' => collect(),
                'message' => 'Tidak ada semester aktif saat ini.',
            ]);
        }

        // Get today's day name in Indonesian
        $today = Carbon::now();
        $tomorrow = Carbon::tomorrow();

        $daysInIndonesian = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
        ];

        $todayName = $daysInIndonesian[$today->format('l')];
        $tomorrowName = $daysInIndonesian[$tomorrow->format('l')];

        // Get all class schedules for this active semester
        $allSchedules = JadwalPelajaran::where('kelas_id', $studentProfile->kelas_id)
            ->where('semester_id', $activeSemester->id)
            ->with(['mataPelajaran', 'guru'])
            ->orderBy('jam_ke')
            ->get();

        // Get all fixed schedules for this active semester
        $fixedSchedules = FixedSchedule::where('semester_id', $activeSemester->id)
            ->orderBy('jam_ke')
            ->get();

        // Get all jam pelajaran for time reference
        $jamPelajarans = JamPelajaran::where('semester_id', $activeSemester->id)
            ->orderBy('jam_ke')
            ->get();
        $jamPelajaranList = $jamPelajarans->keyBy('jam_ke');

        // Determine all unique slot numbers (including jam_ke = 0 if present)
        $slotNumbers = $jamPelajarans->pluck('jam_ke')
            ->merge($fixedSchedules->pluck('jam_ke'))
            ->merge($allSchedules->pluck('jam_ke'))
            ->map(function ($val) {
                return (int) $val;
            })
            ->unique()
            ->sort()
            ->values();

        if ($slotNumbers->isEmpty()) {
            $slotNumbers = collect(range(1, 7));
        }

        // Today and tomorrow regular schedules (retained for backward compatibility)
        $todaySchedules = $allSchedules->where('hari', $todayName)->sortBy('jam_ke')->values();
        $tomorrowSchedules = $allSchedules->where('hari', $tomorrowName)->sortBy('jam_ke')->values();

        // Helper to build timeline combining lessons and grouped fixed schedules
        $buildDailyTimeline = function ($dayName) use ($allSchedules, $fixedSchedules, $jamPelajaranList) {
            $timeline = collect();

            // 1. Regular lessons on this day
            $dayLessons = $allSchedules->where('hari', $dayName);
            foreach ($dayLessons as $lesson) {
                $jamKe = (int) $lesson->jam_ke;
                $jamObj = $jamPelajaranList[$jamKe] ?? null;
                $startTime = $jamObj ? Carbon::parse($jamObj->jam_mulai)->format('H:i') : null;
                $endTime = $jamObj ? Carbon::parse($jamObj->jam_selesai)->format('H:i') : null;

                $timeline->push((object) [
                    'type' => 'lesson',
                    'jam_ke' => $jamKe,
                    'jam_selesai' => $jamKe,
                    'jam_label' => "Jam ke-{$jamKe}",
                    'nama_mapel' => $lesson->mataPelajaran->nama_mapel ?? 'Mata Pelajaran tidak tersedia',
                    'kode_mapel' => $lesson->mataPelajaran->kode_mapel ?? '-',
                    'guru_name' => $lesson->guru->name ?? 'Belum ditentukan',
                    'waktu_mulai' => $startTime,
                    'waktu_selesai' => $endTime,
                    'waktu_text' => ($startTime && $endTime) ? "{$startTime} - {$endTime}" : null,
                    'schedule' => $lesson,
                ]);
            }

            // 2. Fixed schedules on this day (grouped consecutive)
            $dayFixed = $fixedSchedules->where('hari', $dayName);
            $groupedFixed = FixedSchedule::groupConsecutive($dayFixed);

            foreach ($groupedFixed as $fixed) {
                $startJam = (int) $fixed->jam_ke;
                $endJam = (int) $fixed->jam_selesai;

                $startJamObj = $jamPelajaranList[$startJam] ?? null;
                $endJamObj = $jamPelajaranList[$endJam] ?? null;

                $startTime = $startJamObj ? Carbon::parse($startJamObj->jam_mulai)->format('H:i') : null;
                $endTime = $endJamObj ? Carbon::parse($endJamObj->jam_selesai)->format('H:i') : null;

                $timeline->push((object) [
                    'type' => 'fixed',
                    'tipe' => $fixed->tipe,
                    'jam_ke' => $startJam,
                    'jam_selesai' => $endJam,
                    'jam_label' => $fixed->jam_label,
                    'keterangan' => $fixed->keterangan,
                    'waktu_mulai' => $startTime,
                    'waktu_selesai' => $endTime,
                    'waktu_text' => ($startTime && $endTime) ? "{$startTime} - {$endTime}" : null,
                ]);
            }

            return $timeline->sortBy('jam_ke')->values();
        };

        $todayTimeline = $buildDailyTimeline($todayName);
        $tomorrowTimeline = $buildDailyTimeline($tomorrowName);

        // Organize weekly matrix by day and slot
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $weeklySchedules = [];
        $weeklyGrid = [];

        foreach ($days as $day) {
            $weeklySchedules[$day] = $allSchedules->where('hari', $day)->sortBy('jam_ke')->values();
            $weeklyGrid[$day] = [];

            foreach ($slotNumbers as $slot) {
                // Fixed schedule has priority / reservation on this slot
                $fixed = $fixedSchedules->where('hari', $day)->where('jam_ke', $slot)->first();
                if ($fixed) {
                    $weeklyGrid[$day][$slot] = (object) [
                        'type' => 'fixed',
                        'keterangan' => $fixed->keterangan,
                        'tipe' => $fixed->tipe,
                        'jam_ke' => $slot,
                    ];
                } else {
                    $lesson = $allSchedules->where('hari', $day)->where('jam_ke', $slot)->first();
                    if ($lesson) {
                        $weeklyGrid[$day][$slot] = (object) [
                            'type' => 'lesson',
                            'jam_ke' => $slot,
                            'nama_mapel' => $lesson->mataPelajaran->nama_mapel ?? '-',
                            'kode_mapel' => $lesson->mataPelajaran->kode_mapel ?? '-',
                            'guru_name' => $lesson->guru->name ?? '-',
                            'schedule' => $lesson,
                        ];
                    } else {
                        $weeklyGrid[$day][$slot] = null;
                    }
                }
            }
        }

        return view('siswa.jadwal-pelajaran.jadwal-mapel', [
            'studentProfile' => $studentProfile,
            'todaySchedules' => $todaySchedules,
            'tomorrowSchedules' => $tomorrowSchedules,
            'todayTimeline' => $todayTimeline,
            'tomorrowTimeline' => $tomorrowTimeline,
            'weeklySchedules' => $weeklySchedules,
            'weeklyGrid' => $weeklyGrid,
            'slotNumbers' => $slotNumbers,
            'todayName' => $todayName,
            'tomorrowName' => $tomorrowName,
            'activeSemester' => $activeSemester,
            'jamPelajaranList' => $jamPelajaranList,
        ]);
    }
}
