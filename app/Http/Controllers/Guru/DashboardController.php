<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\TugasGuru;
use App\Models\GuruAttendance;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Tugas Guru Statistics
        $totalTugasAktif = TugasGuru::where('status', 'aktif')
            ->where('deadline', '>=', now())
            ->count();

        $tugasSaya = TugasGuru::whereHas('submissions', function($query) use ($user) {
            $query->where('guru_id', $user->id);
        })->count();

        $tugasBelumDikumpulkan = TugasGuru::where('status', 'aktif')
            ->where('deadline', '>=', now())
            ->whereDoesntHave('submissions', function($query) use ($user) {
                $query->where('guru_id', $user->id);
            })->count();

        // Attendance Statistics (bulan ini)
        $absensiCount = GuruAttendance::where('user_id', $user->id)
            ->whereMonth('tanggal', Carbon::now()->month)
            ->whereYear('tanggal', Carbon::now()->year)
            ->count();

        $hadirCount = GuruAttendance::where('user_id', $user->id)
            ->whereMonth('tanggal', Carbon::now()->month)
            ->whereYear('tanggal', Carbon::now()->year)
            ->where('status', 'hadir')
            ->count();

        // Tugas Menunggu (belum dikumpulkan & deadline terdekat)
        $tugasMenunggu = TugasGuru::where('status', 'aktif')
            ->where('deadline', '>=', now())
            ->whereDoesntHave('submissions', function($query) use ($user) {
                $query->where('guru_id', $user->id);
            })
            ->orderBy('deadline', 'asc')
            ->take(5)
            ->get();

        // Recent Submissions
        $submissionTerbaru = TugasGuru::whereHas('submissions', function($query) use ($user) {
            $query->where('guru_id', $user->id);
        })
        ->with(['submissions' => function($query) use ($user) {
            $query->where('guru_id', $user->id)->latest();
        }])
        ->orderBy('created_at', 'desc')
        ->take(5)
        ->get();

        return view('guru.dashboard', compact(
            'totalTugasAktif',
            'tugasSaya',
            'tugasBelumDikumpulkan',
            'absensiCount',
            'hadirCount',
            'tugasMenunggu',
            'submissionTerbaru'
        ));
    }
}
