<?php

namespace App\Http\Controllers\KepalaSekolah;

use App\Http\Controllers\Controller;
use App\Models\AgendaGuru;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AgendaGuruController extends Controller
{
    public function index(Request $request)
    {
        $guruFilter = $request->get('guru_id');
        $statusFilter = $request->get('status');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $filterPeriode = $request->get('filter', 'bulan-ini');
        $sortBy = $request->get('sort_by', 'tanggal_desc'); // Default: tanggal terbaru

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
            $startDate = Carbon::now()->startOfMonth()->toDateString();
            $endDate = Carbon::now()->endOfMonth()->toDateString();
        }

        $query = AgendaGuru::with(['user.guruProfile', 'jamMulai', 'jamSelesai'])
            ->whereBetween('tanggal', [$startDate, $endDate]);

        if ($guruFilter) {
            $query->where('user_id', $guruFilter);
        }

        if ($statusFilter) {
            $query->where('status_jurnal', $statusFilter);
        }

        // Apply sorting
        switch ($sortBy) {
            case 'tanggal_asc':
                $query->orderBy('tanggal', 'asc');
                break;
            case 'tanggal_desc':
                $query->orderBy('tanggal', 'desc');
                break;
            case 'guru_asc':
                $query->join('users', 'agenda_guru.user_id', '=', 'users.id')
                    ->orderBy('users.name', 'asc')
                    ->select('agenda_guru.*');
                break;
            case 'guru_desc':
                $query->join('users', 'agenda_guru.user_id', '=', 'users.id')
                    ->orderBy('users.name', 'desc')
                    ->select('agenda_guru.*');
                break;
            default:
                $query->orderBy('tanggal', 'desc');
        }

        $agendaList = $query
            ->paginate(20)
            ->appends($request->all());

        // Get guru list for filter
        $guruList = User::where('role', 'guru')
            ->orderBy('name', 'asc')
            ->get();

        return view('kepala-sekolah.agenda-guru.index', compact(
            'agendaList',
            'guruList',
            'guruFilter',
            'statusFilter',
            'startDate',
            'endDate',
            'filterPeriode'
        ));
    }

    public function show($id)
    {
        $agenda = AgendaGuru::with([
            'user.guruProfile',
            'jamMulai',
            'jamSelesai'
        ])->findOrFail($id);

        return view('kepala-sekolah.agenda-guru.show', compact('agenda'));
    }
}
