<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Kelas;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AbsensiRekapController extends Controller
{
    /**
     * Display student attendance summary for current teacher's classes
     */
    public function indexSiswa(Request $request)
    {
        $filter = $request->get('filter', 'hari-ini');
        $kelasId = $request->get('kelas_id');
        $statusFilter = $request->get('status_filter');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $dateRange = $this->getDateRange($filter, $startDate, $endDate);

        // Get classes taught by current teacher
        $teacherKelas = $this->getTeacherClasses();

        // Get rekap data only for teacher's classes
        $rekap = $this->getRekapSiswa($dateRange['start'], $dateRange['end'], $kelasId, $statusFilter, $teacherKelas);

        // Get kelas list for filter (only teacher's classes)
        $kelasList = Kelas::whereIn('id', $teacherKelas)
                          ->orderBy('tingkat', 'asc')
                          ->orderBy('nama_kelas', 'asc')
                          ->get();

        // Get active semester
        $activeSemester = Semester::where('is_active', true)->first();

        return view('guru.absensi.siswa.index', compact(
            'rekap',
            'filter',
            'kelasId',
            'statusFilter',
            'kelasList',
            'startDate',
            'endDate',
            'dateRange',
            'activeSemester'
        ));
    }

    /**
     * Get classes taught by current teacher
     */
    private function getTeacherClasses()
    {
        $currentUserId = auth()->id();

        // Get active semester
        $semesterId = Semester::where('is_active', true)->value('id');

        // Get unique kelas_id from jadwal_pelajarans where guru is current user
        $query = DB::table('jadwal_pelajarans')
            ->where('guru_id', $currentUserId)
            ->distinct();

        if ($semesterId) {
            $query->where('semester_id', $semesterId);
        }

        return $query->pluck('kelas_id')->toArray();
    }

    /**
     * Get student attendance summary
     */
    private function getRekapSiswa($startDate, $endDate, $kelasId = null, $statusFilter = null, $teacherKelas = [])
    {
        $query = User::with(['studentProfile.kelas'])
            ->where('role', 'siswa')
            ->whereHas('studentProfile', function($q) use ($kelasId, $teacherKelas) {
                if ($kelasId) {
                    $q->where('kelas_id', $kelasId);
                } elseif (!empty($teacherKelas)) {
                    $q->whereIn('kelas_id', $teacherKelas);
                }
            });

        $siswaList = $query->get();

        $rekap = [];

        foreach ($siswaList as $siswa) {
            $absensiQuery = Attendance::where('user_id', $siswa->id)
                ->whereBetween('date', [$startDate, $endDate]);

            if ($statusFilter) {
                $absensiQuery->where('status', $statusFilter);
            }

            $absensiData = $absensiQuery->select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->pluck('total', 'status')
                ->toArray();

            $summary = [
                'hadir' => $absensiData['hadir'] ?? 0,
                'sakit' => $absensiData['sakit'] ?? 0,
                'izin' => $absensiData['izin'] ?? 0,
                'alpha' => $absensiData['alpha'] ?? 0,
                'terlambat' => $absensiData['terlambat'] ?? 0,
            ];

            $totalAbsensi = array_sum($summary);
            $persentaseHadir = $totalAbsensi > 0 ? round(($summary['hadir'] / $totalAbsensi) * 100, 1) : 0;

            $rekap[] = [
                'siswa' => $siswa,
                'summary' => $summary,
                'total' => $totalAbsensi,
                'persentase_hadir' => $persentaseHadir,
            ];
        }

        return collect($rekap);
    }

    /**
     * Get detail attendance for a specific student
     */
    public function detailSiswa($userId, Request $request)
    {
        $siswa = User::with(['studentProfile.kelas'])->findOrFail($userId);

        // Check if student is in teacher's classes
        $teacherKelas = $this->getTeacherClasses();
        if ($siswa->studentProfile && !in_array($siswa->studentProfile->kelas_id, $teacherKelas)) {
            abort(403, 'Unauthorized access');
        }

        $filter = $request->get('filter', 'bulan-ini');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $dateRange = $this->getDateRange($filter, $startDate, $endDate);

        // Get attendance records
        $absensi = Attendance::where('user_id', $userId)
            ->whereBetween('date', [$dateRange['start'], $dateRange['end']])
            ->orderBy('date', 'desc')
            ->paginate(20);

        // Get summary
        $summary = [
            'hadir' => 0,
            'sakit' => 0,
            'izin' => 0,
            'alpha' => 0,
            'terlambat' => 0,
        ];

        $summaryData = Attendance::where('user_id', $userId)
            ->whereBetween('date', [$dateRange['start'], $dateRange['end']])
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        foreach ($summaryData as $status => $total) {
            $summary[$status] = $total;
        }

        // Calculate percentage
        $totalAbsensi = array_sum($summary);
        $persentaseHadir = $totalAbsensi > 0 ? round(($summary['hadir'] / $totalAbsensi) * 100, 1) : 0;

        return view('guru.absensi.siswa.detail', compact(
            'siswa',
            'absensi',
            'summary',
            'persentaseHadir',
            'filter',
            'startDate',
            'endDate',
            'dateRange'
        ));
    }

    /**
     * Get monthly attendance data for student calendar view (Guru)
     */
    public function monthlySiswa($userId, Request $request)
    {
        try {
            $year = $request->input('year', date('Y'));
            $month = $request->input('month', date('m'));

            // Get first and last day of the month
            $firstDay = Carbon::createFromDate($year, $month, 1)->startOfMonth();
            $lastDay = $firstDay->copy()->endOfMonth();

            // Get all attendance records for this month
            $attendances = Attendance::where('user_id', $userId)
                ->whereBetween('date', [$firstDay, $lastDay])
                ->get();

            // Map attendance by date
            $attendanceByDate = [];
            foreach ($attendances as $attendance) {
                $date = $attendance->date->format('Y-m-d');
                $attendanceByDate[$date] = [
                    'status' => $attendance->status,
                    'status_label' => ucfirst($attendance->status),
                    'waktu' => $attendance->time ? $attendance->time->format('H:i') : null,
                    'notes' => $attendance->notes
                ];
            }

            // Calculate summary for this month
            $summary = [
                'hadir' => 0,
                'sakit' => 0,
                'izin' => 0,
                'alpha' => 0,
                'terlambat' => 0,
            ];

            $summaryData = Attendance::where('user_id', $userId)
                ->whereBetween('date', [$firstDay, $lastDay])
                ->select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->pluck('total', 'status')
                ->toArray();

            foreach ($summaryData as $status => $total) {
                $summary[$status] = $total;
            }

            $totalAbsensi = array_sum($summary);
            $persentaseHadir = $totalAbsensi > 0 ? round(($summary['hadir'] / $totalAbsensi) * 100, 1) : 0;

            return response()->json([
                'success' => true,
                'data' => $attendanceByDate,
                'summary' => $summary,
                'persentase_hadir' => $persentaseHadir,
                'month_name' => $firstDay->locale('id')->isoFormat('MMMM Y')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper function to get date range based on filter
     */
    private function getDateRange($filter, $customStart = null, $customEnd = null)
    {
        $now = Carbon::now();

        switch ($filter) {
            case 'hari-ini':
                $start = $now->copy()->startOfDay();
                $end = $now->copy()->endOfDay();
                break;
            case 'minggu-ini':
                $start = $now->copy()->startOfWeek();
                $end = $now->copy()->endOfWeek();
                break;
            case 'bulan-ini':
                $start = $now->copy()->startOfMonth();
                $end = $now->copy()->endOfMonth();
                break;
            case 'custom':
                $start = $customStart ? Carbon::parse($customStart)->startOfDay() : $now->copy()->startOfMonth();
                $end = $customEnd ? Carbon::parse($customEnd)->endOfDay() : $now->copy()->endOfMonth();
                break;
            default:
                $start = $now->copy()->startOfMonth();
                $end = $now->copy()->endOfMonth();
        }

        return [
            'start' => $start->format('Y-m-d'),
            'end' => $end->format('Y-m-d'),
            'start_formatted' => $start->locale('id')->isoFormat('D MMMM YYYY'),
            'end_formatted' => $end->locale('id')->isoFormat('D MMMM YYYY'),
        ];
    }
}
