<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\GuruAttendance;

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

        return view('guru.absensi-guru.absensi-guru');
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
            // Get school coordinates from config
            $schoolLat = config('school.latitude');
            $schoolLon = config('school.longitude');
            $maxRadius = config('school.attendance_radius');

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

            // Store photo
            $photo = $request->file('photo');
            $photoName = 'guru_' . auth()->id() . '_' . time() . '.' . $photo->getClientOriginalExtension();
            $photoPath = $photo->storeAs('attendance/guru', $photoName, 'public');

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
            // Get current week (Monday to Saturday)
            $startOfWeek = Carbon::now()->startOfWeek(); // Monday
            $endOfWeek = Carbon::now()->startOfWeek()->addDays(5); // Saturday

            // Get attendance records for this week
            $attendances = GuruAttendance::where('user_id', auth()->id())
                ->whereBetween('tanggal', [$startOfWeek, $endOfWeek])
                ->orderBy('tanggal')
                ->get();

            // Map to days of week (0=Monday, 5=Saturday)
            $weekData = [];
            $daysOfWeek = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

            for ($i = 0; $i < 6; $i++) {
                $date = $startOfWeek->copy()->addDays($i);
                $attendance = $attendances->firstWhere('tanggal', $date->format('Y-m-d'));

                $weekData[] = [
                    'hari' => $daysOfWeek[$i],
                    'tanggal' => $date->format('d M'),
                    'status' => $attendance ? $attendance->status : null,
                    'waktu' => $attendance ? $attendance->waktu_absen->format('H:i') : null
                ];
            }

            // Calculate statistics
            $statistics = [
                'hadir' => $attendances->where('status', 'hadir')->count(),
                'terlambat' => $attendances->where('status', 'terlambat')->count(),
                'izin' => $attendances->where('status', 'izin')->count(),
                'alpha' => 0 // To be calculated based on working days vs attendance
            ];

            // Calculate alpha (days without attendance)
            $workingDays = $this->getWorkingDaysCount($startOfWeek, $endOfWeek);
            $totalAttendance = $statistics['hadir'] + $statistics['terlambat'] + $statistics['izin'];
            $statistics['alpha'] = max(0, $workingDays - $totalAttendance);

            return response()->json([
                'success' => true,
                'data' => $weekData,
                'statistics' => $statistics,
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
}
