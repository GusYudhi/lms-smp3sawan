<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\StudentProfile;
use App\Models\JamPelajaran;
use App\Models\JadwalPelajaran;
use App\Models\Semester;
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
        /** @var \App\Models\User|null $user */
        $user = auth()->user();
        if (!$user || !$user->isGuru()) {
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

        // Determine attendance status based on dynamic morning cutoff time
        $cutoffTime = $this->getTodayCutoffTime($today);

        if ($currentTime->lte($cutoffTime)) {
            $status = 'hadir';
            $notes = null;
        } else {
            $status = 'terlambat';
            $notes = 'Terlambat';
        }

        // Insert attendance record
        try {
            DB::table('attendance')->insert([
                'user_id' => $student->id,
                'date' => $today,
                'time' => $currentTime,
                'status' => $status,
                'notes' => $notes,
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

    /**
     * Get dynamic morning cutoff time based on the earliest scheduled lesson of the day
     */
    public function getTodayCutoffTime(?Carbon $date = null): Carbon
    {
        $targetDate = $date ? $date->copy() : Carbon::today();
        $dayName = $targetDate->locale('id')->dayName;
        $semesterAktif = Semester::getActive();

        // 1. Cek jam pelajaran paling awal dari jadwal pelajaran hari tersebut
        $minJamKeQuery = JadwalPelajaran::where('hari', $dayName);
        if ($semesterAktif) {
            $minJamKeQuery->where('semester_id', $semesterAktif->id);
        }
        $minJamKe = $minJamKeQuery->min('jam_ke');

        $earliestJam = null;
        if ($minJamKe !== null) {
            $jpQuery = JamPelajaran::where('jam_ke', $minJamKe);
            if ($semesterAktif) {
                $jpQuery->where('semester_id', $semesterAktif->id);
            }
            $earliestJam = $jpQuery->first();
        }

        // 2. Fallback: jika tidak ada jadwal di hari tersebut, gunakan jam pelajaran paling awal di semester aktif
        if (!$earliestJam) {
            $jpQuery = JamPelajaran::orderBy('jam_ke', 'asc');
            if ($semesterAktif) {
                $jpQuery->where('semester_id', $semesterAktif->id);
            }
            $earliestJam = $jpQuery->first();
        }

        // 3. Konversi string jam_mulai (HH:mm:ss atau HH:mm) ke Carbon pada tanggal target
        if ($earliestJam && $earliestJam->jam_mulai) {
            $timeParts = explode(':', $earliestJam->jam_mulai);
            $hour = (int) ($timeParts[0] ?? 7);
            $minute = (int) ($timeParts[1] ?? 0);
            $second = (int) ($timeParts[2] ?? 0);

            return $targetDate->copy()->setTime($hour, $minute, $second);
        }

        // Fallback default jika master jam belum tersedia
        return $targetDate->copy()->setTime(7, 0, 0);
    }
}
