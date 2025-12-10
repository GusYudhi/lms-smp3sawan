<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JadwalPelajaran;
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
        if (!auth()->user()->isSiswa()) {
            abort(403, 'Unauthorized access');
        }

        // Get student profile with class
        $studentProfile = auth()->user()->studentProfile;

        if (!$studentProfile || !$studentProfile->kelas_id) {
            return view('siswa.jadwal-pelajaran.jadwal-mapel', [
                'studentProfile' => null,
                'todaySchedules' => collect(),
                'tomorrowSchedules' => collect(),
                'weeklySchedules' => [],
                'message' => 'Anda belum terdaftar di kelas manapun. Silakan hubungi admin.'
            ]);
        }

        // Get active semester
        $activeSemester = Semester::where('is_active', true)->first();

        if (!$activeSemester) {
            return view('siswa.jadwal-pelajaran.jadwal-mapel', [
                'studentProfile' => $studentProfile,
                'todaySchedules' => collect(),
                'tomorrowSchedules' => collect(),
                'weeklySchedules' => [],
                'message' => 'Tidak ada semester aktif saat ini.'
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
            'Saturday' => 'Sabtu'
        ];

        $todayName = $daysInIndonesian[$today->format('l')];
        $tomorrowName = $daysInIndonesian[$tomorrow->format('l')];

        // Get today's schedule
        $todaySchedules = JadwalPelajaran::where('kelas_id', $studentProfile->kelas_id)
            ->where('semester_id', $activeSemester->id)
            ->where('hari', $todayName)
            ->with(['mataPelajaran', 'guru'])
            ->orderBy('jam_ke')
            ->get();

        // Get tomorrow's schedule
        $tomorrowSchedules = JadwalPelajaran::where('kelas_id', $studentProfile->kelas_id)
            ->where('semester_id', $activeSemester->id)
            ->where('hari', $tomorrowName)
            ->with(['mataPelajaran', 'guru'])
            ->orderBy('jam_ke')
            ->get();

        // Get full weekly schedule
        $allSchedules = JadwalPelajaran::where('kelas_id', $studentProfile->kelas_id)
            ->where('semester_id', $activeSemester->id)
            ->with(['mataPelajaran', 'guru'])
            ->orderBy('jam_ke')
            ->get();

        // Get all jam pelajaran for time reference
        $jamPelajaranList = \App\Models\JamPelajaran::orderBy('jam_ke')->get()->keyBy('jam_ke');

        // Organize by day
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $weeklySchedules = [];

        foreach ($days as $day) {
            $weeklySchedules[$day] = $allSchedules->where('hari', $day)->sortBy('jam_ke')->values();
        }

        return view('siswa.jadwal-pelajaran.jadwal-mapel', [
            'studentProfile' => $studentProfile,
            'todaySchedules' => $todaySchedules,
            'tomorrowSchedules' => $tomorrowSchedules,
            'weeklySchedules' => $weeklySchedules,
            'todayName' => $todayName,
            'tomorrowName' => $tomorrowName,
            'activeSemester' => $activeSemester,
            'jamPelajaranList' => $jamPelajaranList
        ]);
    }
}
