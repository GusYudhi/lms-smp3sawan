<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\StudentProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AbsensiController extends Controller
{
    /**
     * Show the student attendance page
     */
    public function absensiSiswa()
    {
        // Ensure user is a teacher
        if (!auth()->user()->isGuru()) {
            abort(403, 'Unauthorized access');
        }

        return view('guru.absensi.absensi-siswa');
    }

    /**
     * Get today's attendance data
     */
    public function getTodayAttendance()
    {
        $today = Carbon::today();

        // Get attendance records for today
        $attendanceRecords = DB::table('attendance')
            ->join('users', 'attendance.user_id', '=', 'users.id')
            ->join('student_profiles', 'users.id', '=', 'student_profiles.user_id')
            ->leftJoin('kelas', 'student_profiles.kelas_id', '=', 'kelas.id')
            ->where('attendance.date', $today)
            ->select(
                'users.name',
                'student_profiles.nisn',
                DB::raw("CONCAT(kelas.tingkat, ' ', kelas.nama_kelas) as class"),
                'attendance.status',
                'attendance.time'
            )
            ->orderBy('attendance.time', 'desc')
            ->get();

        // Count status
        $counts = [
            'hadir' => $attendanceRecords->where('status', 'hadir')->count(),
            'terlambat' => $attendanceRecords->where('status', 'terlambat')->count(),
            'alpha' => $attendanceRecords->where('status', 'alpha')->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $attendanceRecords,
            'counts' => $counts
        ]);
    }

    /**
     * Process attendance by NISN
     */
    public function processAttendance(Request $request)
    {
        $request->validate([
            'nisn' => 'required|string|size:10'
        ]);

        $nisn = $request->nisn;
        $today = Carbon::today();
        $currentTime = Carbon::now();

        // Find student by NISN in student_profiles table
        $student = User::whereHas('studentProfile', function($query) use ($nisn) {
                        $query->where('nisn', $nisn);
                    })
                    ->where('role', 'siswa')
                    ->with('studentProfile.kelas')
                    ->first();

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Siswa dengan NISN ' . $nisn . ' tidak ditemukan'
            ], 404);
        }

        // Check if already attended today
        $existingAttendance = DB::table('attendance')
            ->where('user_id', $student->id)
            ->where('date', $today)
            ->first();

        if ($existingAttendance) {
            return response()->json([
                'success' => false,
                'message' => 'Siswa sudah melakukan absensi hari ini pada ' .
                           Carbon::parse($existingAttendance->time)->format('H:i')
            ], 400);
        }

        // Determine attendance status based on time
        $cutoffTime = Carbon::today()->setTime(7, 30); // 07:30 as cutoff time
        $status = $currentTime->lte($cutoffTime) ? 'hadir' : 'terlambat';

        // Insert attendance record
        try {
            DB::table('attendance')->insert([
                'user_id' => $student->id,
                'date' => $today,
                'time' => $currentTime,
                'status' => $status,
                'created_by' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'name' => $student->name,
                    'nisn' => $student->studentProfile->nisn,
                    'class' => $student->studentProfile->kelas ? $student->studentProfile->kelas->full_name : '-',
                    'status' => $status,
                    'time' => $currentTime->format('H:i:s')
                ],
                'message' => 'Absensi berhasil dicatat'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mencatat absensi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate QR Code for student NISN
     */
    public function generateQRCode($nisn)
    {
        // Validate NISN in student_profiles table
        $student = User::whereHas('studentProfile', function($query) use ($nisn) {
                        $query->where('nisn', $nisn);
                    })
                    ->where('role', 'siswa')
                    ->first();

        if (!$student) {
            abort(404, 'Student not found');
        }

        // You can implement QR code generation here
        // For now, just return the NISN
        return response()->json([
            'nisn' => $nisn,
            'qr_data' => $nisn // This would be the QR code data
        ]);
    }
}
