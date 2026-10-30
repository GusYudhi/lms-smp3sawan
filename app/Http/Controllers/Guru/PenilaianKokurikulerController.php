<?php

namespace App\Http\Controllers\Guru;

use App\Exports\PenilaianKokurikulerExport;
use App\Http\Controllers\Controller;
use App\Models\FixedSchedule;
use App\Models\Kelas;
use App\Models\PenilaianKokurikuler;
use App\Models\Semester;
use App\Models\StudentProfile;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class PenilaianKokurikulerController extends Controller
{
    public const INDONESIAN_DAYS = [
        'Sunday'    => 'Minggu',
        'Monday'    => 'Senin',
        'Tuesday'   => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday'  => 'Kamis',
        'Friday'    => 'Jumat',
        'Saturday'  => 'Sabtu',
    ];

    /**
     * Display daily assessment input page for Wali Kelas
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        if (!$user->isWaliKelas()) {
            abort(403, 'Halaman ini hanya dapat diakses oleh Wali Kelas.');
        }

        $kelas = $user->waliKelasOf();
        $activeSemester = Semester::with('tahunPelajaran')->where('is_active', true)->first();

        if (!$activeSemester) {
            return view('guru.penilaian-kokurikuler.index', [
                'kelas' => $kelas,
                'activeSemester' => null,
                'selectedDate' => Carbon::today()->format('Y-m-d'),
                'selectedDayName' => self::INDONESIAN_DAYS[Carbon::today()->format('l')],
                'kokurikulerSchedule' => null,
                'allKokurikulerSchedules' => collect(),
                'students' => collect(),
                'existingAssessments' => collect(),
                'historySessions' => collect(),
            ]);
        }

        $selectedDate = $request->input('tanggal', Carbon::today()->format('Y-m-d'));
        try {
            $parsedDate = Carbon::parse($selectedDate);
        } catch (\Exception $e) {
            $parsedDate = Carbon::today();
            $selectedDate = $parsedDate->format('Y-m-d');
        }

        $selectedDayName = self::INDONESIAN_DAYS[$parsedDate->format('l')] ?? 'Senin';

        // Get all Kokurikuler schedules in this semester (typically Selasa - Jumat Jam ke-0)
        $allKokurikulerSchedules = FixedSchedule::where('semester_id', $activeSemester->id)
            ->kokurikuler()
            ->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu')")
            ->orderBy('jam_ke')
            ->get();

        // Find Kokurikuler schedule matching the selected day
        $kokurikulerSchedule = $allKokurikulerSchedules->firstWhere('hari', $selectedDayName);

        // Get active students in this class
        $students = StudentProfile::with('user')
            ->where('kelas_id', $kelas->id)
            ->where('is_active', true)
            ->orderBy('nis')
            ->get();

        // Get existing assessments for this class on the selected date
        $existingAssessments = PenilaianKokurikuler::where('semester_id', $activeSemester->id)
            ->where('kelas_id', $kelas->id)
            ->whereDate('tanggal', $selectedDate)
            ->get()
            ->keyBy('student_user_id');

        // Get history of assessed dates for quick navigation
        $historySessions = PenilaianKokurikuler::select(
                'tanggal',
                'hari',
                'tema',
                DB::raw('COUNT(*) as total_dinilai')
            )
            ->where('semester_id', $activeSemester->id)
            ->where('kelas_id', $kelas->id)
            ->groupBy('tanggal', 'hari', 'tema')
            ->orderByDesc('tanggal')
            ->limit(25)
            ->get();

        return view('guru.penilaian-kokurikuler.index', compact(
            'kelas',
            'activeSemester',
            'selectedDate',
            'selectedDayName',
            'kokurikulerSchedule',
            'allKokurikulerSchedules',
            'students',
            'existingAssessments',
            'historySessions'
        ));
    }

    /**
     * Store or update student assessments for a specific meeting date
     */
    public function store(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        if (!$user->isWaliKelas()) {
            abort(403, 'Hanya Wali Kelas yang dapat menyimpan penilaian kokurikuler.');
        }

        $kelas = $user->waliKelasOf();
        $activeSemester = Semester::where('is_active', true)->firstOrFail();

        $request->validate([
            'tanggal' => 'required|date',
            'tema' => 'nullable|string|max:255',
            'nilai' => 'required|array',
            'nilai.*.predikat' => 'nullable|in:B,SB,BSH,SAB',
            'nilai.*.catatan' => 'nullable|string|max:500',
        ]);

        $parsedDate = Carbon::parse($request->input('tanggal'));
        $dateString = $parsedDate->format('Y-m-d');
        $dayName = self::INDONESIAN_DAYS[$parsedDate->format('l')] ?? 'Selasa';

        $kokurikulerSchedule = FixedSchedule::where('semester_id', $activeSemester->id)
            ->kokurikuler()
            ->where('hari', $dayName)
            ->orderBy('jam_ke')
            ->first();

        $tema = $request->input('tema')
            ?: ($kokurikulerSchedule ? $kokurikulerSchedule->keterangan : "Kokurikuler {$dayName}");

        $validStudentIds = StudentProfile::where('kelas_id', $kelas->id)
            ->where('is_active', true)
            ->pluck('user_id')
            ->all();

        $savedCount = 0;

        DB::transaction(function () use (
            $request,
            $activeSemester,
            $kelas,
            $kokurikulerSchedule,
            $dateString,
            $dayName,
            $tema,
            $user,
            $validStudentIds,
            &$savedCount
        ) {
            foreach ($request->input('nilai', []) as $studentUserId => $item) {
                if (!in_array((int) $studentUserId, $validStudentIds, true)) {
                    continue;
                }

                $predikat = $item['predikat'] ?? null;
                if (empty($predikat)) {
                    continue;
                }

                PenilaianKokurikuler::updateOrCreate(
                    [
                        'kelas_id' => $kelas->id,
                        'student_user_id' => (int) $studentUserId,
                        'tanggal' => $dateString,
                    ],
                    [
                        'semester_id' => $activeSemester->id,
                        'fixed_schedule_id' => $kokurikulerSchedule?->id,
                        'hari' => $dayName,
                        'tema' => $tema,
                        'penilai_user_id' => $user->id,
                        'predikat' => $predikat,
                        'catatan' => !empty($item['catatan']) ? trim($item['catatan']) : null,
                    ]
                );

                $savedCount++;
            }
        });

        return redirect()
            ->route('guru.penilaian-kokurikuler.index', ['tanggal' => $dateString])
            ->with('success', "Penilaian kokurikuler ({$dayName}, {$parsedDate->format('d/m/Y')}) untuk {$savedCount} siswa berhasil disimpan.");
    }

    /**
     * Recap page accessible by Wali Kelas (own class) or Admin & Kepala Sekolah (all classes)
     */
    public function rekap(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        if ($user->isGuru() && !$user->isWaliKelas()) {
            abort(403, 'Hanya Wali Kelas yang dapat mengakses rekap penilaian kokurikuler.');
        }

        $semesters = Semester::with('tahunPelajaran')->orderByDesc('is_active')->orderByDesc('id')->get();
        $activeSemester = $request->filled('semester_id')
            ? Semester::with('tahunPelajaran')->findOrFail($request->input('semester_id'))
            : ($semesters->firstWhere('is_active', true) ?? $semesters->first());

        $kelasList = collect();
        if ($user->isAdmin() || $user->isKepalaSekolah()) {
            $kelasList = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();
            $selectedKelasId = $request->input('kelas_id', $kelasList->first()?->id);
            $kelas = $selectedKelasId ? Kelas::with('waliKelas.user')->find($selectedKelasId) : null;
        } else {
            $kelas = $user->waliKelasOf();
            $kelasList = $kelas ? collect([$kelas]) : collect();
        }

        $students = collect();
        $kokurikulerSchedules = collect();
        $penilaians = collect();
        $sessions = collect();

        if ($kelas && $activeSemester) {
            $students = StudentProfile::with('user')
                ->where('kelas_id', $kelas->id)
                ->where('is_active', true)
                ->orderBy('nis')
                ->get();

            $kokurikulerSchedules = FixedSchedule::where('semester_id', $activeSemester->id)
                ->kokurikuler()
                ->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu')")
                ->orderBy('jam_ke')
                ->get();

            $penilaians = PenilaianKokurikuler::where('semester_id', $activeSemester->id)
                ->where('kelas_id', $kelas->id)
                ->orderBy('tanggal')
                ->get();

            $sessions = $penilaians
                ->groupBy(fn ($item) => Carbon::parse($item->tanggal)->format('Y-m-d'))
                ->map(function ($group, $dateStr) {
                    $first = $group->first();
                    return [
                        'tanggal' => $dateStr,
                        'hari' => $first->hari,
                        'tema' => $first->tema,
                        'total_dinilai' => $group->count(),
                    ];
                })
                ->sortKeys()
                ->values();
        }

        return view('guru.penilaian-kokurikuler.rekap', compact(
            'kelas',
            'kelasList',
            'semesters',
            'activeSemester',
            'students',
            'kokurikulerSchedules',
            'penilaians',
            'sessions'
        ));
    }

    /**
     * Export Excel Recap for selected Class & Semester
     */
    public function export(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        if ($user->isGuru() && !$user->isWaliKelas()) {
            abort(403, 'Unauthorized');
        }

        $semester = $request->filled('semester_id')
            ? Semester::with('tahunPelajaran')->findOrFail($request->input('semester_id'))
            : Semester::with('tahunPelajaran')->where('is_active', true)->firstOrFail();

        if ($user->isAdmin() || $user->isKepalaSekolah()) {
            $kelas = Kelas::findOrFail($request->input('kelas_id'));
        } else {
            $kelas = $user->waliKelasOf();
            if (!$kelas) {
                abort(403, 'Kelas perwalian tidak ditemukan.');
            }
        }

        $filename = 'Rekap_Penilaian_Kokurikuler_Kelas_' . $kelas->full_name . '_' . str_replace(['/', ' '], '_', $semester->nama) . '.xlsx';

        return Excel::download(new PenilaianKokurikulerExport($kelas, $semester), $filename);
    }
}

