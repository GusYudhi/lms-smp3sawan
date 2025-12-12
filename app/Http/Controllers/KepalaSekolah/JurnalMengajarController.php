<?php

namespace App\Http\Controllers\KepalaSekolah;

use App\Http\Controllers\Controller;
use App\Models\JurnalMengajar;
use App\Models\User;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use Illuminate\Http\Request;
use Carbon\Carbon;

class JurnalMengajarController extends Controller
{
    public function index(Request $request)
    {
        $guruFilter = $request->get('guru_id');
        $kelasFilter = $request->get('kelas_id');
        $mapelFilter = $request->get('mata_pelajaran_id');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $filterPeriode = $request->get('filter', 'bulan-ini');

        // Set default date range based on filter
        if ($filterPeriode == 'hari-ini') {
            $startDate = Carbon::today()->toDateString();
            $endDate = Carbon::today()->toDateString();
        } elseif ($filterPeriode == 'minggu-ini') {
            $startDate = Carbon::now()->startOfWeek()->toDateString();
            $endDate = Carbon::now()->endOfWeek()->toDateString();
        } elseif ($filterPeriode == 'bulan-ini') {
            $startDate = Carbon::now()->startOfMonth()->toDateString();
            $endDate = Carbon::now()->endOfMonth()->toDateString();
        } elseif ($filterPeriode == 'custom') {
            if (!$startDate) $startDate = Carbon::now()->startOfMonth()->toDateString();
            if (!$endDate) $endDate = Carbon::now()->endOfMonth()->toDateString();
        } else {
            // Default to current month
            $startDate = Carbon::now()->startOfMonth()->toDateString();
            $endDate = Carbon::now()->endOfMonth()->toDateString();
        }

        $query = JurnalMengajar::with(['guru.guruProfile', 'kelas', 'mataPelajaran', 'jurnalAttendances'])
            ->whereBetween('tanggal', [$startDate, $endDate]);

        if ($guruFilter) {
            $query->where('guru_id', $guruFilter);
        }

        if ($kelasFilter) {
            $query->where('kelas_id', $kelasFilter);
        }

        if ($mapelFilter) {
            $query->where('mata_pelajaran_id', $mapelFilter);
        }

        $jurnalList = $query->orderBy('tanggal', 'desc')
            ->orderBy('jam_ke_mulai', 'asc')
            ->paginate(20)
            ->appends($request->all());

        // Get filter options
        $guruList = User::where('role', 'guru')
            ->orderBy('name', 'asc')
            ->get();
        $kelasList = Kelas::orderBy('tingkat', 'asc')
            ->orderBy('nama_kelas', 'asc')
            ->get();
        $mapelList = MataPelajaran::orderBy('nama_mapel', 'asc')
            ->get();

        return view('kepala-sekolah.jurnal-mengajar.index', compact(
            'jurnalList',
            'guruList',
            'kelasList',
            'mapelList',
            'guruFilter',
            'kelasFilter',
            'mapelFilter',
            'startDate',
            'endDate',
            'filterPeriode'
        ));
    }

    public function show($id)
    {
        $jurnal = JurnalMengajar::with([
            'guru.guruProfile',
            'kelas',
            'mataPelajaran',
            'jurnalAttendances.studentProfile'
        ])->findOrFail($id);

        return view('kepala-sekolah.jurnal-mengajar.show', compact('jurnal'));
    }
}
