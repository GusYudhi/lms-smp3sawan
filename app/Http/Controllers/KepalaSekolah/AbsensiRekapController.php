<?php

namespace App\Http\Controllers\KepalaSekolah;

use App\Http\Controllers\Admin\AbsensiRekapController as AdminAbsensiRekapController;
use App\Models\User;
use App\Models\Attendance;
use App\Models\GuruAttendance;
use App\Models\Kelas;
use App\Models\Semester;
use App\Models\TahunPelajaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\StudentsAttendanceExport;
use App\Exports\TeachersAttendanceExport;
use Carbon\Carbon;

class AbsensiRekapController extends AdminAbsensiRekapController
{
    // Override indexSiswa to return kepala-sekolah view
    public function indexSiswa(Request $request)
    {
        $filter = $request->get('filter', 'hari-ini');
        $kelasId = $request->get('kelas_id');
        $statusFilter = $request->get('status_filter'); // Filter status absensi
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $dateRange = $this->getDateRange($filter, $startDate, $endDate);

        // Get rekap data
        $rekap = $this->getRekapSiswa($dateRange['start'], $dateRange['end'], $kelasId, $statusFilter);

        // Get kelas list for filter
        $kelasList = Kelas::orderBy('tingkat', 'asc')
                          ->orderBy('nama_kelas', 'asc')
                          ->get();

        // Get active semester
        $activeSemester = Semester::where('is_active', true)->first();

        return view('kepala-sekolah.absensi.siswa.index', compact(
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

    // Override detailSiswa to return kepala-sekolah view
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

        return view('kepala-sekolah.absensi.siswa.detail', compact(
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
            $firstDay = \Carbon\Carbon::createFromDate($year, $month, 1)->startOfMonth();
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

    // Override indexGuru to return kepala-sekolah view
    public function indexGuru(Request $request)
    {
        $filter = $request->get('filter', 'hari-ini');
        $statusFilter = $request->get('status_filter'); // Filter status absensi
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $dateRange = $this->getDateRange($filter, $startDate, $endDate);

        // Get rekap data
        $rekap = $this->getRekapGuru($dateRange['start'], $dateRange['end'], $statusFilter);

        $activeSemester = Semester::where('is_active', true)->first();

        return view('kepala-sekolah.absensi.guru.index', compact(
            'rekap',
            'filter',
            'statusFilter',
            'startDate',
            'endDate',
            'dateRange',
            'activeSemester'
        ));
    }

    // Override detailGuru to return kepala-sekolah view
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

        return view('kepala-sekolah.absensi.guru.detail', compact(
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
            $firstDay = \Carbon\Carbon::createFromDate($year, $month, 1)->startOfMonth();
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

            // Calculate summary for this month
            $summary = [
                'hadir' => 0,
                'sakit' => 0,
                'izin' => 0,
                'alpha' => 0,
                'terlambat' => 0,
            ];

            $summaryData = GuruAttendance::where('user_id', $userId)
                ->whereBetween('tanggal', [$firstDay, $lastDay])
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
     * Export rekap absensi siswa ke Excel
     */
    public function exportSiswa(Request $request)
    {
        $request->validate([
            'periode' => 'required|string',
            'tingkat_kelas' => 'required|array',
            'tingkat_kelas.*' => 'in:7,8,9',
        ]);

        // Get active semester
        $activeSemester = Semester::where('is_active', true)->first();
        if (!$activeSemester) {
            return back()->with('error', 'Tidak ada semester aktif');
        }

        // Get active tahun pelajaran
        $tahunPelajaran = $activeSemester->tahunPelajaran;
        if (!$tahunPelajaran) {
            return back()->with('error', 'Tidak ada tahun pelajaran aktif');
        }

        // Determine months based on periode
        $periode = $request->periode;
        $tingkatKelas = $request->tingkat_kelas;

        if ($periode == 'semester') {
            // Full semester
            if ($activeSemester->semester_ke == 1) {
                // Semester Ganjil: Juli - Desember (7-12)
                $months = [7, 8, 9, 10, 11, 12];
                $periodeName = 'Semester_Ganjil_' . $tahunPelajaran->nama;
            } else {
                // Semester Genap: Januari - Juni (1-6)
                $months = [1, 2, 3, 4, 5, 6];
                $periodeName = 'Semester_Genap_' . $tahunPelajaran->nama;
            }
        } else {
            // Single month
            $monthNumber = (int) $periode;
            $months = [$monthNumber];
            $monthName = Carbon::create(null, $monthNumber, 1)->locale('id')->translatedFormat('F');
            $year = $this->getYearForMonth($monthNumber);
            $periodeName = $monthName . '_' . $year;
        }

        // Create export
        $export = new StudentsAttendanceExport($tingkatKelas, $months, $tahunPelajaran->id);

        // Generate filename
        $filename = 'Rekap_Absensi_Siswa_' . $periodeName . '.xlsx';

        return Excel::download($export, $filename);
    }

    /**
     * Export rekap absensi guru ke Excel
     */
    public function exportGuru(Request $request)
    {
        $request->validate([
            'periode' => 'required|string',
        ]);

        // Get active semester
        $activeSemester = Semester::where('is_active', true)->first();
        if (!$activeSemester) {
            return back()->with('error', 'Tidak ada semester aktif');
        }

        // Get active tahun pelajaran
        $tahunPelajaran = $activeSemester->tahunPelajaran;
        if (!$tahunPelajaran) {
            return back()->with('error', 'Tidak ada tahun pelajaran aktif');
        }

        // Determine months based on periode
        $periode = $request->periode;

        if ($periode == 'semester') {
            // Full semester
            if ($activeSemester->semester_ke == 1) {
                // Semester Ganjil: Juli - Desember (7-12)
                $months = [7, 8, 9, 10, 11, 12];
                $periodeName = 'Semester_Ganjil_' . $tahunPelajaran->nama;
            } else {
                // Semester Genap: Januari - Juni (1-6)
                $months = [1, 2, 3, 4, 5, 6];
                $periodeName = 'Semester_Genap_' . $tahunPelajaran->nama;
            }
        } else {
            // Single month
            $monthNumber = (int) $periode;
            $months = [$monthNumber];
            $monthName = Carbon::create(null, $monthNumber, 1)->locale('id')->translatedFormat('F');
            $year = $this->getYearForMonth($monthNumber);
            $periodeName = $monthName . '_' . $year;
        }

        // Create export
        $export = new TeachersAttendanceExport($months);

        // Generate filename
        $filename = 'Rekap_Absensi_Guru_' . $periodeName . '.xlsx';

        return Excel::download($export, $filename);
    }

    /**
     * Get year for month (handle semester transition)
     */
    protected function getYearForMonth($month)
    {
        // Bulan 7-12 (Juli-Desember) = tahun sekarang
        // Bulan 1-6 (Januari-Juni) = tahun depan
        $currentYear = Carbon::now()->year;

        if ($month >= 7) {
            return $currentYear;
        } else {
            return $currentYear + 1;
        }
    }
}
