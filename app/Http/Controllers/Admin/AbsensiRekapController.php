<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\GuruAttendance;
use App\Models\User;
use App\Models\Kelas;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AbsensiRekapController extends Controller
{
    // Rekap Absensi Siswa
    public function indexSiswa(Request $request)
    {
        $filter = $request->get('filter', 'hari-ini'); // hari-ini, minggu-ini, bulan-ini, semester-ini, custom
        $kelasId = $request->get('kelas_id');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // Set date range based on filter
        $dateRange = $this->getDateRange($filter, $startDate, $endDate);

        // Get rekap data
        $rekap = $this->getRekapSiswa($dateRange['start'], $dateRange['end'], $kelasId);

        // Get kelas list for filter
        $kelasList = Kelas::orderBy('tingkat', 'asc')
                          ->orderBy('nama_kelas', 'asc')
                          ->get();

        // Get active semester
        $activeSemester = Semester::where('is_active', true)->first();

        return view('admin.absensi.siswa.index', compact(
            'rekap',
            'filter',
            'kelasId',
            'kelasList',
            'startDate',
            'endDate',
            'dateRange',
            'activeSemester'
        ));
    }

    public function detailSiswa($userId, Request $request)
    {
        $siswa = User::with(['studentProfile.kelas'])->findOrFail($userId);

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

        return view('admin.absensi.siswa.detail', compact(
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
     * Get monthly attendance data for siswa calendar view
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

            return response()->json([
                'success' => true,
                'data' => $attendanceByDate
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    // Rekap Absensi Guru
    public function indexGuru(Request $request)
    {
        $filter = $request->get('filter', 'hari-ini');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $dateRange = $this->getDateRange($filter, $startDate, $endDate);

        // Get rekap data
        $rekap = $this->getRekapGuru($dateRange['start'], $dateRange['end']);

        $activeSemester = Semester::where('is_active', true)->first();

        return view('admin.absensi.guru.index', compact(
            'rekap',
            'filter',
            'startDate',
            'endDate',
            'dateRange',
            'activeSemester'
        ));
    }

    public function detailGuru($userId, Request $request)
    {
        $guru = User::with(['guruProfile'])->findOrFail($userId);

        $filter = $request->get('filter', 'bulan-ini');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $dateRange = $this->getDateRange($filter, $startDate, $endDate);

        // Get attendance records
        $absensi = GuruAttendance::where('user_id', $userId)
            ->whereBetween('tanggal', [$dateRange['start'], $dateRange['end']])
            ->orderBy('tanggal', 'desc')
            ->paginate(20);

        // Get summary
        $summary = [
            'hadir' => 0,
            'sakit' => 0,
            'izin' => 0,
            'alpha' => 0,
            'terlambat' => 0,
        ];

        $summaryData = GuruAttendance::where('user_id', $userId)
            ->whereBetween('tanggal', [$dateRange['start'], $dateRange['end']])
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

        return view('admin.absensi.guru.detail', compact(
            'guru',
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
     * Get monthly attendance data for guru calendar view
     */
    public function monthlyGuru($userId, Request $request)
    {
        try {
            $year = $request->input('year', date('Y'));
            $month = $request->input('month', date('m'));

            // Get first and last day of the month
            $firstDay = Carbon::createFromDate($year, $month, 1)->startOfMonth();
            $lastDay = $firstDay->copy()->endOfMonth();

            // Get all attendance records for this month
            $attendances = GuruAttendance::where('user_id', $userId)
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
                    'notes' => $attendance->keterangan
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $attendanceByDate
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    // Helper Methods
    protected function getDateRange($filter, $startDate = null, $endDate = null)
    {
        $now = Carbon::now();

        switch ($filter) {
            case 'hari-ini':
                return [
                    'start' => $now->copy()->startOfDay(),
                    'end' => $now->copy()->endOfDay(),
                    'label' => 'Hari Ini - ' . $now->format('d F Y')
                ];

            case 'minggu-ini':
                return [
                    'start' => $now->copy()->startOfWeek(),
                    'end' => $now->copy()->endOfWeek(),
                    'label' => 'Minggu Ini - ' . $now->copy()->startOfWeek()->format('d M') . ' s/d ' . $now->copy()->endOfWeek()->format('d M Y')
                ];

            case 'bulan-ini':
                return [
                    'start' => $now->copy()->startOfMonth(),
                    'end' => $now->copy()->endOfMonth(),
                    'label' => 'Bulan Ini - ' . $now->format('F Y')
                ];

            case 'semester-ini':
                $semester = Semester::where('is_active', true)->first();
                if ($semester) {
                    return [
                        'start' => Carbon::parse($semester->tanggal_mulai),
                        'end' => Carbon::parse($semester->tanggal_selesai),
                        'label' => 'Semester Ini - ' . $semester->nama
                    ];
                }
                // Fallback to this month if no active semester
                return [
                    'start' => $now->copy()->startOfMonth(),
                    'end' => $now->copy()->endOfMonth(),
                    'label' => 'Bulan Ini - ' . $now->format('F Y')
                ];

            case 'custom':
                if ($startDate && $endDate) {
                    $start = Carbon::parse($startDate);
                    $end = Carbon::parse($endDate);
                    return [
                        'start' => $start,
                        'end' => $end,
                        'label' => 'Custom - ' . $start->format('d M Y') . ' s/d ' . $end->format('d M Y')
                    ];
                }
                // Fallback
                return [
                    'start' => $now->copy()->startOfMonth(),
                    'end' => $now->copy()->endOfMonth(),
                    'label' => 'Bulan Ini - ' . $now->format('F Y')
                ];

            default:
                return [
                    'start' => $now->copy()->startOfDay(),
                    'end' => $now->copy()->endOfDay(),
                    'label' => 'Hari Ini - ' . $now->format('d F Y')
                ];
        }
    }

    protected function getRekapSiswa($startDate, $endDate, $kelasId = null)
    {
        $query = User::where('role', 'siswa')
            ->with(['studentProfile.kelas'])
            ->select('users.*')
            ->selectRaw('
                (SELECT COUNT(*) FROM attendance
                 WHERE attendance.user_id = users.id
                 AND attendance.date BETWEEN ? AND ?
                 AND attendance.status = "hadir") as total_hadir,
                (SELECT COUNT(*) FROM attendance
                 WHERE attendance.user_id = users.id
                 AND attendance.date BETWEEN ? AND ?
                 AND attendance.status = "sakit") as total_sakit,
                (SELECT COUNT(*) FROM attendance
                 WHERE attendance.user_id = users.id
                 AND attendance.date BETWEEN ? AND ?
                 AND attendance.status = "izin") as total_izin,
                (SELECT COUNT(*) FROM attendance
                 WHERE attendance.user_id = users.id
                 AND attendance.date BETWEEN ? AND ?
                 AND attendance.status = "alpha") as total_alpha,
                (SELECT COUNT(*) FROM attendance
                 WHERE attendance.user_id = users.id
                 AND attendance.date BETWEEN ? AND ?
                 AND attendance.status = "terlambat") as total_terlambat
            ', [
                $startDate, $endDate,
                $startDate, $endDate,
                $startDate, $endDate,
                $startDate, $endDate,
                $startDate, $endDate
            ]);

        if ($kelasId) {
            $query->whereHas('studentProfile', function($q) use ($kelasId) {
                $q->where('kelas_id', $kelasId);
            });
        }

        return $query->orderBy('name', 'asc')->paginate(20);
    }

    protected function getRekapGuru($startDate, $endDate)
    {
        $query = User::whereIn('role', ['guru', 'kepala_sekolah'])
            ->with(['guruProfile'])
            ->select('users.*')
            ->selectRaw('
                (SELECT COUNT(*) FROM guru_attendances
                 WHERE guru_attendances.user_id = users.id
                 AND guru_attendances.tanggal BETWEEN ? AND ?
                 AND guru_attendances.status = "hadir") as total_hadir,
                (SELECT COUNT(*) FROM guru_attendances
                 WHERE guru_attendances.user_id = users.id
                 AND guru_attendances.tanggal BETWEEN ? AND ?
                 AND guru_attendances.status = "sakit") as total_sakit,
                (SELECT COUNT(*) FROM guru_attendances
                 WHERE guru_attendances.user_id = users.id
                 AND guru_attendances.tanggal BETWEEN ? AND ?
                 AND guru_attendances.status = "izin") as total_izin,
                (SELECT COUNT(*) FROM guru_attendances
                 WHERE guru_attendances.user_id = users.id
                 AND guru_attendances.tanggal BETWEEN ? AND ?
                 AND guru_attendances.status = "alpha") as total_alpha,
                (SELECT COUNT(*) FROM guru_attendances
                 WHERE guru_attendances.user_id = users.id
                 AND guru_attendances.tanggal BETWEEN ? AND ?
                 AND guru_attendances.status = "terlambat") as total_terlambat
            ', [
                $startDate, $endDate,
                $startDate, $endDate,
                $startDate, $endDate,
                $startDate, $endDate,
                $startDate, $endDate
            ]);

        return $query->orderBy('name', 'asc')->paginate(20);
    }
}
