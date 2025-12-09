<?php

namespace App\Http\Controllers\KepalaSekolah;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Kelas;
use App\Models\Attendance;
use App\Models\GuruAttendance;
use App\Models\TugasGuru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Hitung statistik guru (termasuk kepala sekolah)
        $totalGuru = User::whereIn('role', ['guru', 'kepala_sekolah'])->count();

        // Hitung total siswa
        $totalSiswa = User::where('role', 'siswa')->count();

        // Hitung kehadiran siswa hari ini
        $today = Carbon::today();
        $siswaHadirHariIni = Attendance::where('date', $today)
            ->where('status', 'hadir')
            ->distinct('user_id')
            ->count('user_id');

        // Hitung siswa tidak hadir hari ini (terlambat, alpha)
        $siswaTidakHadirHariIni = Attendance::where('date', $today)
            ->whereIn('status', ['terlambat', 'alpha'])
            ->distinct('user_id')
            ->count('user_id');

        // Hitung total kelas
        $totalKelas = Kelas::count();

        // Hitung persentase kehadiran hari ini
        $persentaseKehadiran = $totalSiswa > 0
            ? round(($siswaHadirHariIni / $totalSiswa) * 100, 1)
            : 0;

        // Hitung kehadiran 7 hari terakhir
        $attendanceWeekly = [];
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $dayName = $days[$date->dayOfWeek - 1] ?? 'Minggu';

            // Skip hari Minggu
            if ($dayName === 'Minggu') continue;

            $hadirCount = Attendance::where('date', $date)
                ->where('status', 'hadir')
                ->distinct('user_id')
                ->count('user_id');

            $percentage = $totalSiswa > 0
                ? round(($hadirCount / $totalSiswa) * 100, 1)
                : 0;

            $attendanceWeekly[] = [
                'day' => $dayName,
                'percentage' => $percentage
            ];
        }

        // Hitung siswa sering absen (lebih dari 3 kali tidak hadir dalam 30 hari terakhir)
        $siswaSeringAbsen = DB::table('attendance')
            ->select('user_id')
            ->where('date', '>=', Carbon::today()->subDays(30))
            ->where('status', 'alpha')
            ->groupBy('user_id')
            ->havingRaw('COUNT(*) > 3')
            ->count();

        // Hitung rata-rata kehadiran bulan ini
        $startOfMonth = Carbon::now()->startOfMonth();
        $totalHadirBulanIni = Attendance::where('date', '>=', $startOfMonth)
            ->where('status', 'hadir')
            ->count();

        $totalRecordBulanIni = Attendance::where('date', '>=', $startOfMonth)
            ->count();

        $rataRataKehadiranBulan = $totalRecordBulanIni > 0
            ? round(($totalHadirBulanIni / $totalRecordBulanIni) * 100, 1)
            : 0;

        // Cari kelas dengan kehadiran terbaik bulan ini
        $kelasTerbaik = DB::table('attendance')
            ->join('users', 'attendance.user_id', '=', 'users.id')
            ->join('student_profiles', 'users.id', '=', 'student_profiles.user_id')
            ->join('kelas', 'student_profiles.kelas_id', '=', 'kelas.id')
            ->select('kelas.id', 'kelas.tingkat', 'kelas.nama_kelas')
            ->selectRaw('COUNT(CASE WHEN attendance.status = "hadir" THEN 1 END) * 100.0 / COUNT(*) as attendance_rate')
            ->where('attendance.date', '>=', $startOfMonth)
            ->where('users.role', 'siswa')
            ->groupBy('kelas.id', 'kelas.tingkat', 'kelas.nama_kelas')
            ->orderByDesc('attendance_rate')
            ->first();

        // Data Absensi Guru Hari Ini
        $totalGuruAbsenHariIni = GuruAttendance::whereDate('tanggal', $today)
            ->where('status', 'hadir')
            ->count();

        $totalGuruTidakHadirHariIni = GuruAttendance::whereDate('tanggal', $today)
            ->whereIn('status', ['sakit', 'izin', 'alpha'])
            ->count();

        // Data Tugas Guru
        $totalTugasAktif = TugasGuru::where('status', 'aktif')->count();

        $totalSubmissions = DB::table('tugas_guru_submissions')
            ->where('status_pengumpulan', 'dikumpulkan')
            ->count();

        return view('kepala-sekolah.dashboard', compact(
            'totalGuru',
            'totalSiswa',
            'siswaHadirHariIni',
            'siswaTidakHadirHariIni',
            'totalKelas',
            'persentaseKehadiran',
            'attendanceWeekly',
            'siswaSeringAbsen',
            'rataRataKehadiranBulan',
            'kelasTerbaik',
            'totalGuruAbsenHariIni',
            'totalGuruTidakHadirHariIni',
            'totalTugasAktif',
            'totalSubmissions'
        ));
    }
}
