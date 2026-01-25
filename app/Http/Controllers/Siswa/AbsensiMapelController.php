<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AbsensiMapelController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (!$user->studentProfile) {
            return redirect()->back()->with('error', 'Profil siswa tidak ditemukan');
        }

        $studentProfileId = $user->studentProfile->id;

        $attendanceStats = DB::table('jurnal_attendances')
            ->join('jurnal_mengajar', 'jurnal_attendances.jurnal_mengajar_id', '=', 'jurnal_mengajar.id')
            ->join('mata_pelajarans', 'jurnal_mengajar.mata_pelajaran_id', '=', 'mata_pelajarans.id')
            ->where('jurnal_attendances.student_profile_id', $studentProfileId)
            ->select(
                'mata_pelajarans.nama_mapel',
                DB::raw('count(*) as total_pertemuan'),
                DB::raw('sum(case when jurnal_attendances.status = "hadir" then 1 else 0 end) as hadir'),
                DB::raw('sum(case when jurnal_attendances.status = "sakit" then 1 else 0 end) as sakit'),
                DB::raw('sum(case when jurnal_attendances.status = "izin" then 1 else 0 end) as izin'),
                DB::raw('sum(case when jurnal_attendances.status = "alpa" then 1 else 0 end) as alpa')
            )
            ->groupBy('mata_pelajarans.id', 'mata_pelajarans.nama_mapel')
            ->get();

        return view('siswa.absensi-mapel.index', compact('attendanceStats'));
    }
}
