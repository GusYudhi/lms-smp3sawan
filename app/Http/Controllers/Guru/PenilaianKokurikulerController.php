<?php

namespace App\Http\Controllers\Guru;

use App\Exports\PenilaianKokurikulerExport;
use App\Helpers\ImageCompressor;
use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\FixedSchedule;
use App\Models\Kelas;
use App\Models\KokurikulerSession;
use App\Models\PenilaianKokurikuler;
use App\Models\Semester;
use App\Models\StudentProfile;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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

    public const INDONESIAN_MONTHS = [
        1  => 'Januari',
        2  => 'Februari',
        3  => 'Maret',
        4  => 'April',
        5  => 'Mei',
        6  => 'Juni',
        7  => 'Juli',
        8  => 'Agustus',
        9  => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember',
    ];

    /**
     * Display 3-Step Wizard for daily Kokurikuler Attendance, Documentation, and Rubric Assessment
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
                'absensiPagi' => collect(),
                'existingSession' => null,
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

        // Get all Kokurikuler schedules in this semester
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

        // Get morning QR/daily attendance for default status in Step 1
        $absensiPagi = Attendance::whereIn('user_id', $students->pluck('user_id'))
            ->whereDate('date', $selectedDate)
            ->get()
            ->keyBy('user_id');

        // Get existing session header (photo & description) if already filled
        $existingSession = KokurikulerSession::where('kelas_id', $kelas->id)
            ->whereDate('tanggal', $selectedDate)
            ->first();

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
                DB::raw('COUNT(*) as total_siswa'),
                DB::raw("SUM(CASE WHEN predikat IS NOT NULL AND predikat != '' THEN 1 ELSE 0 END) as total_dinilai")
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
            'absensiPagi',
            'existingSession',
            'existingAssessments',
            'historySessions'
        ));
    }

    /**
     * Store or update 3-Step Wizard: Attendance, Documentation Photo & Description, and Student Rubric
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

        $parsedDate = Carbon::parse($request->input('tanggal', Carbon::today()->format('Y-m-d')));
        $dateString = $parsedDate->format('Y-m-d');

        $existingSession = KokurikulerSession::where('kelas_id', $kelas->id)
            ->whereDate('tanggal', $dateString)
            ->first();

        // Foto bukti wajib jika belum ada sesi dengan foto sebelumnya
        $fotoRule = ($existingSession && $existingSession->foto_bukti) ? 'nullable|string' : 'required|string';

        $request->validate([
            'tanggal' => 'required|date',
            'tema' => 'nullable|string|max:255',
            'deskripsi' => 'nullable|string|max:2000',
            'foto_bukti' => $fotoRule,
            'absensi' => 'nullable|array',
            'absensi.*' => 'nullable|in:hadir,terlambat,sakit,izin,alpa',
            'nilai' => 'required|array',
            'nilai.*.predikat' => 'nullable|in:B,SB,BSH,SAB',
            'nilai.*.catatan' => 'nullable|string|max:500',
        ], [
            'foto_bukti.required' => 'Foto bukti kegiatan kokurikuler wajib diambil atau diunggah.',
        ]);

        $dayName = self::INDONESIAN_DAYS[$parsedDate->format('l')] ?? 'Selasa';

        $kokurikulerSchedule = FixedSchedule::where('semester_id', $activeSemester->id)
            ->kokurikuler()
            ->where('hari', $dayName)
            ->orderBy('jam_ke')
            ->first();

        $tema = $request->input('tema')
            ?: ($kokurikulerSchedule ? $kokurikulerSchedule->keterangan : "Kokurikuler {$dayName}");

        // Handle Foto Bukti (Base64 image compression to WebP)
        $fotoPath = $existingSession?->foto_bukti;
        if ($request->filled('foto_bukti')) {
            try {
                $compressedImage = ImageCompressor::compressBase64Image(
                    $request->input('foto_bukti'),
                    60,
                    1200
                );

                $filename = 'koku_' . $kelas->id . '_' . $user->id . '_' . time() . '_' . uniqid() . '.webp';
                $newPath = 'kokurikuler_bukti/' . $filename;

                Storage::disk('public')->put($newPath, $compressedImage);

                // Delete old photo if replacing
                if ($fotoPath && Storage::disk('public')->exists($fotoPath)) {
                    Storage::disk('public')->delete($fotoPath);
                }

                $fotoPath = $newPath;
            } catch (\Exception $e) {
                return back()->withErrors(['foto_bukti' => 'Gagal memproses foto bukti: ' . $e->getMessage()])->withInput();
            }
        }

        $validStudents = StudentProfile::where('kelas_id', $kelas->id)
            ->where('is_active', true)
            ->get();

        $savedCount = 0;

        DB::transaction(function () use (
            $request,
            $activeSemester,
            $kelas,
            $kokurikulerSchedule,
            $dateString,
            $dayName,
            $tema,
            $fotoPath,
            $user,
            $validStudents,
            &$savedCount
        ) {
            $session = KokurikulerSession::updateOrCreate(
                [
                    'kelas_id' => $kelas->id,
                    'tanggal' => $dateString,
                ],
                [
                    'semester_id' => $activeSemester->id,
                    'fixed_schedule_id' => $kokurikulerSchedule?->id,
                    'penilai_user_id' => $user->id,
                    'hari' => $dayName,
                    'tema' => $tema,
                    'deskripsi' => $request->filled('deskripsi') ? trim($request->input('deskripsi')) : null,
                    'foto_bukti' => $fotoPath,
                ]
            );

            $absensiInput = $request->input('absensi', []);
            $nilaiInput = $request->input('nilai', []);

            foreach ($validStudents as $student) {
                $studentUserId = (int) $student->user_id;
                $statusAbsensi = $absensiInput[$studentUserId] ?? 'hadir';
                $isPresent = in_array($statusAbsensi, ['hadir', 'terlambat'], true);

                $itemNilai = $nilaiInput[$studentUserId] ?? [];
                $predikat = $isPresent ? ($itemNilai['predikat'] ?? null) : null;
                $catatan = !empty($itemNilai['catatan']) ? trim($itemNilai['catatan']) : null;

                // Skip if neither attendance was explicitly submitted nor rubric was selected
                if (!isset($absensiInput[$studentUserId]) && empty($predikat) && empty($catatan)) {
                    continue;
                }

                PenilaianKokurikuler::updateOrCreate(
                    [
                        'kelas_id' => $kelas->id,
                        'student_user_id' => $studentUserId,
                        'tanggal' => $dateString,
                    ],
                    [
                        'kokurikuler_session_id' => $session->id,
                        'semester_id' => $activeSemester->id,
                        'fixed_schedule_id' => $kokurikulerSchedule?->id,
                        'hari' => $dayName,
                        'tema' => $tema,
                        'penilai_user_id' => $user->id,
                        'status_absensi' => $statusAbsensi,
                        'predikat' => $predikat,
                        'catatan' => $catatan,
                    ]
                );

                $savedCount++;
            }
        });

        return redirect()
            ->route('guru.penilaian-kokurikuler.index', ['tanggal' => $dateString])
            ->with('success', "Absensi, dokumentasi, dan penilaian kokurikuler ({$dayName}, {$parsedDate->format('d/m/Y')}) untuk {$savedCount} siswa berhasil disimpan.");
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
        $allKokurikulerSchedules = collect();
        $penilaians = collect();
        $sessions = collect();

        $selectedBulan = $request->input('bulan', 'all');
        $selectedHari = $request->input('hari', 'all');
        $monthsList = self::INDONESIAN_MONTHS;

        if ($kelas && $activeSemester) {
            $students = StudentProfile::with('user')
                ->where('kelas_id', $kelas->id)
                ->where('is_active', true)
                ->orderBy('nis')
                ->get();

            $allKokurikulerSchedules = FixedSchedule::where('semester_id', $activeSemester->id)
                ->kokurikuler()
                ->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu')")
                ->orderBy('jam_ke')
                ->get();

            $kokurikulerSchedules = ($selectedHari !== 'all')
                ? $allKokurikulerSchedules->where('hari', $selectedHari)->values()
                : $allKokurikulerSchedules;

            $penilaianQuery = PenilaianKokurikuler::where('semester_id', $activeSemester->id)
                ->where('kelas_id', $kelas->id);

            $sessionQuery = KokurikulerSession::where('semester_id', $activeSemester->id)
                ->where('kelas_id', $kelas->id);

            if ($selectedBulan !== 'all' && is_numeric($selectedBulan)) {
                $penilaianQuery->whereMonth('tanggal', (int) $selectedBulan);
                $sessionQuery->whereMonth('tanggal', (int) $selectedBulan);
            }

            if ($selectedHari !== 'all') {
                $penilaianQuery->where('hari', $selectedHari);
                $sessionQuery->where('hari', $selectedHari);
            }

            $penilaians = $penilaianQuery->orderBy('tanggal')->get();

            $sessionModels = $sessionQuery->get()
                ->keyBy(fn ($s) => Carbon::parse($s->tanggal)->format('Y-m-d'));

            $sessions = $penilaians
                ->groupBy(fn ($item) => Carbon::parse($item->tanggal)->format('Y-m-d'))
                ->map(function ($group, $dateStr) use ($sessionModels) {
                    $first = $group->first();
                    $sessModel = $sessionModels->get($dateStr);
                    return [
                        'tanggal' => $dateStr,
                        'hari' => $first->hari,
                        'tema' => $sessModel?->tema ?? $first->tema,
                        'deskripsi' => $sessModel?->deskripsi,
                        'foto_bukti' => $sessModel?->foto_bukti,
                        'total_dinilai' => $group->whereNotNull('predikat')->count(),
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
            'allKokurikulerSchedules',
            'penilaians',
            'sessions',
            'selectedBulan',
            'selectedHari',
            'monthsList'
        ));
    }

    /**
     * Export Excel Recap for selected Class & Semester (with optional month & day filters)
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

        $bulan = $request->input('bulan', 'all');
        $hari = $request->input('hari', 'all');

        $suffix = '';
        if ($bulan !== 'all' && isset(self::INDONESIAN_MONTHS[(int) $bulan])) {
            $suffix .= '_' . self::INDONESIAN_MONTHS[(int) $bulan];
        }
        if ($hari !== 'all') {
            $suffix .= '_' . $hari;
        }

        $filename = 'Rekap_Penilaian_Kokurikuler_Kelas_' . $kelas->full_name . $suffix . '_' . str_replace(['/', ' '], '_', $semester->nama) . '.xlsx';

        return Excel::download(new PenilaianKokurikulerExport($kelas, $semester, $bulan, $hari), $filename);
    }
}
