<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Helpers\ImageCompressor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\GuruAttendance;
use App\Models\SchoolProfile;

class AbsensiGuruController extends Controller
{
    /**
     * Show the teacher self-attendance page
     */
    public function index()
    {
        // Ensure user is a teacher
        if (!auth()->user()->isGuru()) {
            abort(403, 'Unauthorized access');
        }

        // Check if already attended today
        $today = Carbon::today();
        $todayAttendance = GuruAttendance::where('user_id', auth()->id())
            ->whereDate('tanggal', $today)
            ->first();

        return view('guru.absensi-guru.absensi-guru', [
            'todayAttendance' => $todayAttendance
        ]);
    }

    /**
     * Store teacher attendance with photo and GPS location
     */
    public function store(Request $request)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'accuracy' => 'nullable|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Get school coordinates from database
            $schoolProfile = SchoolProfile::first();

            if (!$schoolProfile || !$schoolProfile->maps_latitude || !$schoolProfile->maps_longitude) {
                return response()->json([
                    'success' => false,
                    'message' => 'Koordinat sekolah belum dikonfigurasi. Hubungi administrator.'
                ], 500);
            }

            $schoolLat = $schoolProfile->maps_latitude;
            $schoolLon = $schoolProfile->maps_longitude;
            $maxRadius = config('school.attendance_radius', 300);

            // Calculate distance from school
            $distance = $this->calculateDistance(
                $request->latitude,
                $request->longitude,
                $schoolLat,
                $schoolLon
            );

            // Check if within school radius
            if ($distance > $maxRadius) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda berada di luar area sekolah',
                    'distance' => round($distance, 2)
                ], 403);
            }

            // Check if already checked in today
            $today = Carbon::today();
            $existingAttendance = GuruAttendance::where('user_id', auth()->id())
                ->whereDate('tanggal', $today)
                ->first();

            if ($existingAttendance) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah melakukan absensi hari ini'
                ], 400);
            }

            // Store and compress photo to WebP
            $photo = $request->file('photo');
            $photoPath = ImageCompressor::compressAndStore(
                $photo,
                'attendance/guru',
                'guru_' . auth()->id() . '_' . time()
            );

            // Determine attendance status based on time
            $currentTime = Carbon::now();
            $lateThresholdTime = config('school.late_threshold', '07:30');
            list($hour, $minute) = explode(':', $lateThresholdTime);
            $lateThreshold = Carbon::today()->setTime((int)$hour, (int)$minute, 0);
            $status = $currentTime->gt($lateThreshold) ? 'terlambat' : 'hadir';

            // Create attendance record
            $attendance = GuruAttendance::create([
                'user_id' => auth()->id(),
                'tanggal' => $today,
                'waktu_absen' => $currentTime,
                'status' => $status,
                'photo_path' => $photoPath,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'distance_from_school' => round($distance, 2),
                'accuracy' => $request->accuracy
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Absensi berhasil disimpan',
                'data' => [
                    'status' => $status,
                    'waktu_absen' => $currentTime->format('H:i:s'),
                    'tanggal' => $today->format('d F Y'),
                    'catatan' => $status === 'terlambat' ? 'Anda terlambat masuk' : 'Terima kasih sudah tepat waktu'
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get weekly attendance for current user
     */
    public function weekly(Request $request)
    {
        try {
            // Get date from request or use today
            $referenceDate = $request->input('date') ? Carbon::parse($request->input('date')) : Carbon::now();

            // Get start of week (Monday) for the reference date
            $startOfWeek = $referenceDate->copy()->startOfWeek(); // Monday
            $endOfWeek = $startOfWeek->copy()->addDays(5); // Saturday

            // Get attendance records for this week
            $attendances = GuruAttendance::where('user_id', auth()->id())
                ->whereBetween('tanggal', [$startOfWeek, $endOfWeek])
                ->orderBy('tanggal')
                ->get();

            // Debug: Log the query and results
            logger()->info('Weekly Attendance Query', [
                'user_id' => auth()->id(),
                'start_date' => $startOfWeek->format('Y-m-d'),
                'end_date' => $endOfWeek->format('Y-m-d'),
                'found_records' => $attendances->count(),
                'records' => $attendances->map(function($att) {
                    return [
                        'id' => $att->id,
                        'tanggal' => $att->tanggal,
                        'status' => $att->status,
                        'waktu' => $att->waktu_absen ? $att->waktu_absen->format('Y-m-d H:i:s') : null
                    ];
                })->toArray()
            ]);

            // Map to days of week (0=Monday, 5=Saturday)
            $weekData = [];
            $daysOfWeek = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

            for ($i = 0; $i < 6; $i++) {
                $date = $startOfWeek->copy()->addDays($i);
                $dateString = $date->format('Y-m-d');

                // Find attendance for this date
                $attendance = $attendances->first(function($att) use ($dateString) {
                    return $att->tanggal->format('Y-m-d') === $dateString;
                });

                $weekData[] = [
                    'hari' => $daysOfWeek[$i],
                    'tanggal' => $date->format('d M'),
                    'tanggal_full' => $dateString,
                    'status' => $attendance ? $attendance->status : null,
                    'waktu' => $attendance && $attendance->waktu_absen ? $attendance->waktu_absen->format('H:i') : null,
                    'keterangan' => $attendance ? $attendance->keterangan : null,
                    'dokumen' => $attendance && $attendance->dokumen_path ? true : false,
                    'has_attendance' => $attendance ? true : false // Debug field
                ];
            }

            // Calculate statistics
            $statistics = [
                'hadir' => $attendances->where('status', 'hadir')->count(),
                'terlambat' => $attendances->where('status', 'terlambat')->count(),
                'izin' => $attendances->whereIn('status', ['izin', 'sakit'])->count(),
                'alpha' => 0 // To be calculated based on working days vs attendance
            ];

            // Calculate alpha (days without attendance)
            $workingDays = $this->getWorkingDaysCount($startOfWeek, $endOfWeek);
            $totalAttendance = $statistics['hadir'] + $statistics['terlambat'] + $statistics['izin'];
            $statistics['alpha'] = max(0, $workingDays - $totalAttendance);

            // Week information
            $weekInfo = [
                'week_number' => $startOfWeek->weekOfYear,
                'start_date' => $startOfWeek->format('d M Y'),
                'end_date' => $endOfWeek->format('d M Y')
            ];

            return response()->json([
                'success' => true,
                'data' => $weekData,
                'statistics' => $statistics,
                'week_info' => $weekInfo,
                'week_range' => $startOfWeek->format('d M') . ' - ' . $endOfWeek->format('d M Y')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get monthly attendance for calendar view
     */
    public function monthly(Request $request)
    {
        try {
            $year = $request->input('year', date('Y'));
            $month = $request->input('month', date('m'));

            // Get first and last day of the month
            $firstDay = Carbon::createFromDate($year, $month, 1)->startOfMonth();
            $lastDay = $firstDay->copy()->endOfMonth();

            // Get all attendance records for this month
            $attendances = GuruAttendance::where('user_id', auth()->id())
                ->whereBetween('tanggal', [$firstDay, $lastDay])
                ->get();

            // Map attendance by date
            $attendanceByDate = [];
            foreach ($attendances as $attendance) {
                $date = $attendance->tanggal->format('Y-m-d');
                $attendanceByDate[$date] = [
                    'status' => $attendance->status,
                    'status_label' => ucfirst($attendance->status),
                    'waktu' => $attendance->waktu_absen ? $attendance->waktu_absen->format('H:i') : null,
                    'notes' => $attendance->keterangan,
                    'has_document' => $attendance->dokumen_path ? true : false
                ];
            }

            // Calculate statistics for the month
            $statistics = [
                'hadir' => $attendances->where('status', 'hadir')->count(),
                'terlambat' => $attendances->where('status', 'terlambat')->count(),
                'izin' => $attendances->where('status', 'izin')->count(),
                'sakit' => $attendances->where('status', 'sakit')->count(),
                'alpha' => 0
            ];

            // Calculate alpha (working days without attendance)
            $workingDays = $this->getWorkingDaysCount($firstDay, $lastDay);
            $totalAttendance = $statistics['hadir'] + $statistics['terlambat'] + $statistics['izin'] + $statistics['sakit'];
            $statistics['alpha'] = max(0, $workingDays - $totalAttendance);

            return response()->json([
                'success' => true,
                'data' => $attendanceByDate,
                'statistics' => $statistics,
                'month_info' => [
                    'year' => $year,
                    'month' => $month,
                    'month_name' => $firstDay->format('F Y'),
                    'working_days' => $workingDays
                ]
            ]);

        } catch (\Exception $e) {
            logger()->error('Monthly Attendance Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store non-attendance (izin/sakit) with optional document
     */
    public function storeNonHadir(Request $request)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:izin,sakit',
            'alasan' => 'required_if:type,izin|string|min:10',
            'keterangan' => 'required_if:type,sakit|string|min:10',
            'dokumen' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'surat_dokter' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid: ' . $validator->errors()->first(),
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Check if already checked in today
            $today = Carbon::today();
            $existingAttendance = GuruAttendance::where('user_id', auth()->id())
                ->whereDate('tanggal', $today)
                ->first();

            if ($existingAttendance) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah melakukan absensi hari ini'
                ], 400);
            }

            $type = $request->type;

            // Get keterangan based on type
            $keterangan = $type === 'izin' ? $request->alasan : $request->keterangan;
            $dokumenPath = null;

            // Store document if provided
            $dokumenFile = $request->file('dokumen') ?? $request->file('surat_dokter');
            if ($dokumenFile) {
                $dokumenName = 'guru_' . $type . '_' . auth()->id() . '_' . time() . '.' . $dokumenFile->getClientOriginalExtension();
                $dokumenPath = $dokumenFile->storeAs('attendance/guru/dokumen', $dokumenName, 'public');
            }

            // Create attendance record
            $currentTime = Carbon::now();
            $attendance = GuruAttendance::create([
                'user_id' => auth()->id(),
                'tanggal' => $today,
                'waktu_absen' => $currentTime,
                'status' => $type,
                'keterangan' => $keterangan,
                'dokumen_path' => $dokumenPath,
                'photo_path' => null,
                'latitude' => null,
                'longitude' => null,
                'distance_from_school' => null,
                'accuracy' => null
            ]);

            $message = $type === 'izin'
                ? 'Pengajuan izin berhasil. Semoga urusan Anda lancar.'
                : 'Laporan sakit berhasil dicatat. Semoga cepat sembuh.';

            return response()->json([
                'success' => true,
                'message' => 'Laporan berhasil disimpan',
                'data' => [
                    'status' => $type,
                    'tanggal' => $today->format('d F Y'),
                    'message' => $message
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate distance between two coordinates using Haversine formula
     * Returns distance in meters
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // Earth radius in meters

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Get number of working days (Monday-Saturday) in date range
     */
    private function getWorkingDaysCount($startDate, $endDate)
    {
        $count = 0;
        $current = $startDate->copy();

        while ($current->lte($endDate)) {
            // Count Monday (1) to Saturday (6), skip Sunday (0)
            if ($current->dayOfWeek != 0) {
                $count++;
            }
            $current->addDay();
        }

        return $count;
    }

    /**
     * Get school location configuration
     */
    public function getSchoolLocation()
    {
        $schoolProfile = SchoolProfile::first();

        if (!$schoolProfile || !$schoolProfile->maps_latitude || !$schoolProfile->maps_longitude) {
            return response()->json([
                'success' => false,
                'message' => 'Koordinat sekolah belum dikonfigurasi'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'latitude' => $schoolProfile->maps_latitude,
                'longitude' => $schoolProfile->maps_longitude,
                'school_name' => $schoolProfile->name,
                'radius' => config('school.attendance_radius', 300)
            ]
        ]);
    }
}
