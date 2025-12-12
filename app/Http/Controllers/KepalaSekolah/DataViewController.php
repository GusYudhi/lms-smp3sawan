<?php

namespace App\Http\Controllers\KepalaSekolah;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\GuruProfile;
use App\Models\StudentProfile;
use App\Models\TahunPelajaran;
use App\Models\Semester;
use App\Models\Kelas;
use Illuminate\Http\Request;

class DataViewController extends Controller
{
    // View Data Guru
    public function indexGuru(Request $request)
    {
        $search = $request->get('search');
        $mataPelajaranFilter = $request->get('mata_pelajaran');
        $statusKepegawaianFilter = $request->get('status_kepegawaian');

        $query = User::with(['guruProfile.kelas'])
            ->whereIn('role', ['guru', 'kepala_sekolah']);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('guruProfile', function($query) use ($search) {
                      $query->where('nip', 'like', "%{$search}%");
                  });
            });
        }

        if ($mataPelajaranFilter) {
            $query->whereHas('guruProfile', function($q) use ($mataPelajaranFilter) {
                $q->where('mata_pelajaran', 'like', "%{$mataPelajaranFilter}%");
            });
        }

        if ($statusKepegawaianFilter) {
            $query->whereHas('guruProfile', function($q) use ($statusKepegawaianFilter) {
                $q->where('status_kepegawaian', $statusKepegawaianFilter);
            });
        }

        $users = $query->orderBy('name', 'asc')->paginate(15);

        // Get list mata pelajaran untuk filter
        $mataPelajaranList = \App\Models\MataPelajaran::orderBy('nama_mapel', 'asc')->get();

        return view('kepala-sekolah.data.guru.index', compact('users', 'search', 'mataPelajaranFilter', 'statusKepegawaianFilter', 'mataPelajaranList'));
    }

    public function showGuru($id)
    {
        $user = User::with(['guruProfile.kelas'])->findOrFail($id);

        if (!in_array($user->role, ['guru', 'kepala_sekolah'])) {
            abort(404);
        }

        return view('kepala-sekolah.data.guru.show', compact('user'));
    }    // View Data Siswa
    public function indexSiswa(Request $request)
    {
        $search = $request->get('search');
        $kelasFilter = $request->get('kelas');

        $query = User::with(['studentProfile.kelas'])
            ->where('role', 'siswa');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('studentProfile', function($query) use ($search) {
                      $query->where('nisn', 'like', "%{$search}%")
                            ->orWhere('nis', 'like', "%{$search}%");
                  });
            });
        }

        if ($kelasFilter) {
            $query->whereHas('studentProfile', function($query) use ($kelasFilter) {
                $query->where('kelas_id', $kelasFilter);
            });
        }

        $users = $query->orderBy('name', 'asc')->paginate(15);
        $kelasList = Kelas::orderBy('tingkat', 'asc')
                          ->orderBy('nama_kelas', 'asc')
                          ->get();

        return view('kepala-sekolah.data.siswa.index', compact('users', 'search', 'kelasFilter', 'kelasList'));
    }

    public function showSiswa($id)
    {
        $user = User::with(['studentProfile.kelas'])->findOrFail($id);

        if ($user->role !== 'siswa') {
            abort(404);
        }

        return view('kepala-sekolah.data.siswa.show', compact('user'));
    }

    // View Data Tahun Pelajaran
    public function indexTahunPelajaran()
    {
        $tahunPelajaran = TahunPelajaran::withCount('semester')
            ->orderBy('is_active', 'desc')
            ->orderBy('tahun_mulai', 'desc')
            ->paginate(10);

        return view('kepala-sekolah.data.tahun-pelajaran.index', compact('tahunPelajaran'));
    }

    public function showTahunPelajaran($id)
    {
        $tahunPelajaran = TahunPelajaran::with(['semester'])->findOrFail($id);

        return view('kepala-sekolah.data.tahun-pelajaran.show', compact('tahunPelajaran'));
    }

    // View Data Semester
    public function indexSemester($tahunPelajaranId)
    {
        $tahunPelajaran = TahunPelajaran::findOrFail($tahunPelajaranId);
        $semesters = Semester::where('tahun_pelajaran_id', $tahunPelajaranId)
            ->orderBy('semester_ke', 'asc')
            ->paginate(10);

        return view('kepala-sekolah.data.semester.index', compact('tahunPelajaran', 'semesters'));
    }

    public function showSemester($id)
    {
        $semester = Semester::with(['tahunPelajaran'])->findOrFail($id);

        return view('kepala-sekolah.data.semester.show', compact('semester'));
    }
}
