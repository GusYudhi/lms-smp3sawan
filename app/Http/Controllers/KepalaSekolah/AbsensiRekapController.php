<?php

namespace App\Http\Controllers\KepalaSekolah;

use App\Http\Controllers\Admin\AbsensiRekapController as AdminAbsensiRekapController;
use App\Models\User;
use App\Models\Attendance;
use App\Models\GuruAttendance;
use App\Models\Kelas;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AbsensiRekapController extends AdminAbsensiRekapController
{
    // Override indexSiswa to return kepala-sekolah view
    public function indexSiswa(Request $request)
    {
        $filter = $request->get('filter', 'hari-ini');
        $kelasId = $request->get('kelas_id');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $dateRange = $this->getDateRange($filter, $startDate, $endDate);

        // Get rekap data
        $rekap = $this->getRekapSiswa($dateRange['start'], $dateRange['end'], $kelasId);

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

    // Override indexGuru to return kepala-sekolah view
    public function indexGuru(Request $request)
    {
        $filter = $request->get('filter', 'hari-ini');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $dateRange = $this->getDateRange($filter, $startDate, $endDate);

        // Get rekap data
        $rekap = $this->getRekapGuru($dateRange['start'], $dateRange['end']);

        $activeSemester = Semester::where('is_active', true)->first();

        return view('kepala-sekolah.absensi.guru.index', compact(
            'rekap',
            'filter',
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
}
