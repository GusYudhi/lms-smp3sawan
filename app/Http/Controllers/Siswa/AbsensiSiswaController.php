<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\Attendance;

class AbsensiSiswaController extends Controller
{
    /**
     * Show the student absence submission page
     */
    public function index()
    {
        // Ensure user is a student
        if (!auth()->user()->isSiswa()) {
            abort(403, 'Unauthorized access');
        }

        // Check if already submitted today
        $today = Carbon::today();
        $todayAttendance = Attendance::where('user_id', auth()->id())
            ->whereDate('date', $today)
            ->first();

        return view('siswa.absen.index', [
            'todayAttendance' => $todayAttendance
        ]);
    }

    /**
     * Get weekly attendance for current user
     */
    public function weekly(Request $request)
    {
        try {
            Log::info('Weekly attendance request', [
                'user_id' => auth()->id(),
                'date' => $request->input('date')
            ]);

            // Get date from request or use today
            $referenceDate = $request->input('date') ? Carbon::parse($request->input('date')) : Carbon::now();

            // Get start of week (Monday) for the reference date
            $startOfWeek = $referenceDate->copy()->startOfWeek();
            $endOfWeek = $startOfWeek->copy()->addDays(5); // Saturday

            Log::info('Date range', [
                'start' => $startOfWeek->format('Y-m-d'),
                'end' => $endOfWeek->format('Y-m-d')
            ]);

            // Get attendance records for this week
            $attendances = Attendance::where('user_id', auth()->id())
                ->whereBetween('date', [$startOfWeek, $endOfWeek])
                ->orderBy('date')
                ->get();

            Log::info('Found attendances', [
                'count' => $attendances->count(),
                'data' => $attendances->toArray()
            ]);

            // Map to days of week
            $weekData = [];
            $daysOfWeek = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

            for ($i = 0; $i < 6; $i++) {
                $date = $startOfWeek->copy()->addDays($i);
                $dateString = $date->format('Y-m-d');

                // Find attendance for this date
                $attendance = $attendances->first(function($att) use ($dateString) {
                    return $att->date->format('Y-m-d') === $dateString;
                });

                $weekData[] = [
                    'hari' => $daysOfWeek[$i],
                    'tanggal' => $date->format('d M'),
                    'tanggal_full' => $dateString,
                    'status' => $attendance ? $attendance->status : null,
                    'keterangan' => $attendance ? $attendance->notes : null,
                    'has_attendance' => $attendance ? true : false
                ];
            }

            // Calculate statistics
            $statistics = [
                'hadir' => $attendances->where('status', 'hadir')->count(),
                'izin' => $attendances->where('status', 'izin')->count(),
                'sakit' => $attendances->where('status', 'sakit')->count(),
                'alpha' => $attendances->where('status', 'alpha')->count()
            ];

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
            Log::error('Weekly attendance error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store absence submission (izin/sakit)
     */
    public function store(Request $request)
    {
        Log::info('Store absence request', [
            'user_id' => auth()->id(),
            'data' => $request->except(['dokumen'])
        ]);

        // Validate request
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:izin,sakit',
            'keterangan' => 'required|string|min:10',
            'dokumen' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048'
        ]);

        if ($validator->fails()) {
            Log::warning('Validation failed', [
                'errors' => $validator->errors()->toArray()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid: ' . $validator->errors()->first(),
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Check if already submitted today
            $today = Carbon::today();
            $existingAttendance = Attendance::where('user_id', auth()->id())
                ->whereDate('date', $today)
                ->first();

            if ($existingAttendance) {
                Log::warning('Already submitted today', [
                    'user_id' => auth()->id(),
                    'existing_attendance' => $existingAttendance->toArray()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah melakukan laporan absensi hari ini'
                ], 400);
            }

            $type = $request->type;
            $keterangan = $request->keterangan;

            // Store document if provided
            $dokumenPath = null;
            if ($request->hasFile('dokumen')) {
                $dokumenFile = $request->file('dokumen');
                $dokumenName = 'siswa_' . $type . '_' . auth()->id() . '_' . time() . '.' . $dokumenFile->getClientOriginalExtension();
                $dokumenPath = $dokumenFile->storeAs('attendance/siswa/dokumen', $dokumenName, 'public');

                Log::info('Document uploaded', ['path' => $dokumenPath]);
            }

            // Create attendance record
            $currentTime = Carbon::now();
            $attendanceData = [
                'user_id' => auth()->id(),
                'date' => $today,
                'time' => $currentTime,
                'status' => $type,
                'notes' => $keterangan,
                'created_by' => auth()->id()
            ];

            Log::info('Creating attendance record', $attendanceData);

            $attendance = Attendance::create($attendanceData);

            // Store document path in notes if available (since table doesn't have dokumen_path column)
            if ($dokumenPath) {
                $attendance->notes = $keterangan . ' [Dokumen: ' . $dokumenPath . ']';
                $attendance->save();
            }

            Log::info('Attendance created successfully', [
                'id' => $attendance->id,
                'status' => $type
            ]);

            $message = $type === 'izin'
                ? 'Pengajuan izin berhasil dicatat. Semoga urusan Anda lancar.'
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
            Log::error('Store absence error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get monthly attendance calendar for current user
     */
    public function monthly(Request $request)
    {
        try {
            $year = $request->input('year', date('Y'));
            $month = $request->input('month', date('m'));

            Log::info('Monthly attendance request', [
                'user_id' => auth()->id(),
                'year' => $year,
                'month' => $month
            ]);

            // Get first and last day of the month
            $firstDay = Carbon::createFromDate($year, $month, 1)->startOfMonth();
            $lastDay = $firstDay->copy()->endOfMonth();

            Log::info('Date range', [
                'first_day' => $firstDay->format('Y-m-d'),
                'last_day' => $lastDay->format('Y-m-d')
            ]);

            // Get all attendance records for this month
            $attendances = Attendance::where('user_id', auth()->id())
                ->whereBetween('date', [$firstDay, $lastDay])
                ->orderBy('date')
                ->get();

            Log::info('Found attendances', [
                'count' => $attendances->count()
            ]);

            // Map attendance data by date
            $attendanceByDate = [];
            foreach ($attendances as $attendance) {
                $dateKey = $attendance->date->format('Y-m-d');

                $statusLabel = match($attendance->status) {
                    'hadir' => 'Hadir',
                    'izin' => 'Izin',
                    'sakit' => 'Sakit',
                    'alpha' => 'Alpha',
                    default => ucfirst($attendance->status)
                };

                $attendanceByDate[$dateKey] = [
                    'status' => $attendance->status,
                    'status_label' => $statusLabel,
                    'time' => $attendance->time ? $attendance->time->format('H:i') : null,
                    'notes' => $attendance->notes
                ];
            }

            // Calculate statistics for the month
            $statistics = [
                'hadir' => $attendances->where('status', 'hadir')->count(),
                'izin' => $attendances->where('status', 'izin')->count(),
                'sakit' => $attendances->where('status', 'sakit')->count(),
                'alpha' => $attendances->where('status', 'alpha')->count(),
                'total' => $attendances->count()
            ];

            return response()->json([
                'success' => true,
                'data' => $attendanceByDate,
                'statistics' => $statistics,
                'month_info' => [
                    'year' => (int)$year,
                    'month' => (int)$month,
                    'first_day' => $firstDay->format('Y-m-d'),
                    'last_day' => $lastDay->format('Y-m-d'),
                    'days_in_month' => $lastDay->day
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Monthly attendance error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
