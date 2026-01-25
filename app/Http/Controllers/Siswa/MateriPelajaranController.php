<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\MateriPelajaran;
use App\Models\MataPelajaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MateriPelajaranController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user->studentProfile) {
            return redirect()->back()->with('error', 'Profil siswa tidak ditemukan');
        }
        $kelasId = $user->studentProfile->kelas_id;

        $activeSemester = \App\Models\Semester::where('is_active', true)->first();

        if ($activeSemester && $kelasId) {
            $mapelIds = \App\Models\JadwalPelajaran::where('semester_id', $activeSemester->id)
                ->where('kelas_id', $kelasId)
                ->pluck('mata_pelajaran_id')
                ->unique();

            $mapels = MataPelajaran::whereIn('id', $mapelIds)->orderBy('nama_mapel')->get();
        } else {
            // Fallback
            $mapels = MataPelajaran::orderBy('nama_mapel')->get();
        }

        return view('siswa.materi.index', compact('mapels'));
    }

    public function show($id)
    {
        $mapel = MataPelajaran::findOrFail($id);
        $user = Auth::user();

        if (!$user->studentProfile) {
            return redirect()->back()->with('error', 'Profil siswa tidak ditemukan');
        }

        $kelasId = $user->studentProfile->kelas_id;
        $kelas = \App\Models\Kelas::find($kelasId);
        $tingkat = $kelas ? $kelas->tingkat : null;

        $materis = MateriPelajaran::where('mata_pelajaran_id', $id)
            ->where(function($query) use ($kelasId, $tingkat) {
                $query->where('kelas_id', $kelasId)
                      ->orWhere(function($q) use ($tingkat) {
                          $q->whereNull('kelas_id')
                            ->where('tingkat', $tingkat);
                      })
                      ->orWhere(function($q) {
                          $q->whereNull('kelas_id')
                            ->whereNull('tingkat');
                      });
            })
            ->with('guru')
            ->latest()
            ->get();

        return view('siswa.materi.show', compact('mapel', 'materis'));
    }
}
