<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TahunPelajaran;
use App\Models\Semester;
use App\Models\MataPelajaran;
use App\Models\JamPelajaran;
use App\Models\FixedSchedule;
use App\Models\StudentProfile;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TahunPelajaranController extends Controller
{
    /**
     * Display a listing of all tahun pelajaran
     */
    public function index()
    {
        $tahunPelajaranList = TahunPelajaran::with('semester')
            ->orderBy('tahun_mulai', 'desc')
            ->paginate(10);

        $activeTahunPelajaran = TahunPelajaran::getActive();

        return view('admin.tahun-pelajaran.index', compact('tahunPelajaranList', 'activeTahunPelajaran'));
    }

    /**
     * Show the form for creating a new tahun pelajaran
     */
    public function create()
    {
        return view('admin.tahun-pelajaran.create');
    }

    /**
     * Store a newly created tahun pelajaran in storage
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:tahun_pelajaran,nama',
            'tahun_mulai' => 'required|integer|min:2000|max:2100',
            'tahun_selesai' => 'required|integer|min:2000|max:2100|gt:tahun_mulai',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after:tanggal_mulai',
            'is_active' => 'nullable|boolean',
            'keterangan' => 'nullable|string',
            'create_semesters' => 'nullable|boolean',
            'copy_from_previous' => 'nullable|boolean',
            'auto_promote_students' => 'nullable|boolean',
        ]);

        DB::transaction(function () use ($request) {
            // Create tahun pelajaran
            $tahunPelajaran = TahunPelajaran::create([
                'nama' => $request->nama,
                'tahun_mulai' => $request->tahun_mulai,
                'tahun_selesai' => $request->tahun_selesai,
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai,
                'is_active' => $request->boolean('is_active', false),
                'keterangan' => $request->keterangan,
            ]);

            // If is_active, deactivate others
            if ($request->boolean('is_active')) {
                $tahunPelajaran->setActive();
            }

            // Auto promote students if requested
            if ($request->boolean('auto_promote_students')) {
                $promotionResult = $this->promoteStudents($request->tahun_mulai);
                session()->flash('promotion_info', $promotionResult);
            }

            // Auto-create 2 semesters if requested
            if ($request->boolean('create_semesters', true)) {
                // Semester Ganjil
                $semesterGanjil = Semester::create([
                    'tahun_pelajaran_id' => $tahunPelajaran->id,
                    'nama' => 'Ganjil',
                    'semester_ke' => 1,
                    'tanggal_mulai' => $request->tanggal_mulai,
                    'tanggal_selesai' => null,
                    'is_active' => $request->boolean('is_active', false),
                    'keterangan' => 'Semester Ganjil ' . $request->nama,
                ]);

                // Semester Genap
                Semester::create([
                    'tahun_pelajaran_id' => $tahunPelajaran->id,
                    'nama' => 'Genap',
                    'semester_ke' => 2,
                    'tanggal_mulai' => null,
                    'tanggal_selesai' => $request->tanggal_selesai,
                    'is_active' => false,
                    'keterangan' => 'Semester Genap ' . $request->nama,
                ]);

                // Copy data from previous year if requested
                if ($request->boolean('copy_from_previous') && $semesterGanjil) {
                    $this->copyDataFromPreviousSemester($semesterGanjil);
                }
            }
        });

        return redirect()->route('admin.tahun-pelajaran.index')
            ->with('success', 'Tahun Pelajaran berhasil ditambahkan!');
    }

    /**
     * Copy data from the last semester of previous year to new semester
     */
    private function copyDataFromPreviousSemester($targetSemester)
    {
        // Find the last active semester from previous tahun pelajaran
        $previousSemester = Semester::whereHas('tahunPelajaran', function ($query) use ($targetSemester) {
            $query->where('tahun_mulai', '<', $targetSemester->tahunPelajaran->tahun_mulai);
        })
        ->orderBy('id', 'desc')
        ->first();

        if (!$previousSemester) {
            return;
        }

        // Copy Mata Pelajaran
        $mataPelajarans = MataPelajaran::where('semester_id', $previousSemester->id)->get();
        foreach ($mataPelajarans as $mapel) {
            MataPelajaran::create([
                'nama_mapel' => $mapel->nama_mapel,
                'kode_mapel' => $mapel->kode_mapel,
                'semester_id' => $targetSemester->id,
            ]);
        }

        // Copy Jam Pelajaran
        $jamPelajarans = JamPelajaran::where('semester_id', $previousSemester->id)->get();
        foreach ($jamPelajarans as $jam) {
            JamPelajaran::create([
                'jam_ke' => $jam->jam_ke,
                'jam_mulai' => $jam->jam_mulai,
                'jam_selesai' => $jam->jam_selesai,
                'semester_id' => $targetSemester->id,
            ]);
        }

        // Copy Fixed Schedules
        $fixedSchedules = FixedSchedule::where('semester_id', $previousSemester->id)->get();
        foreach ($fixedSchedules as $schedule) {
            FixedSchedule::create([
                'hari' => $schedule->hari,
                'jam_ke' => $schedule->jam_ke,
                'keterangan' => $schedule->keterangan,
                'semester_id' => $targetSemester->id,
            ]);
        }
    }

    /**
     * Display the dashboard for specific tahun pelajaran
     */
    public function dashboard($id)
    {
        $tahunPelajaran = TahunPelajaran::with('semester')->findOrFail($id);

        $statistics = [
            'total_semester' => $tahunPelajaran->semester()->count(),
            'total_kelas' => $tahunPelajaran->kelas()->count(),
        ];

        return view('admin.tahun-pelajaran.dashboard', compact('tahunPelajaran', 'statistics'));
    }

    /**
     * Show the form for editing the specified tahun pelajaran
     */
    public function edit($id)
    {
        $tahunPelajaran = TahunPelajaran::findOrFail($id);
        return view('admin.tahun-pelajaran.edit', compact('tahunPelajaran'));
    }

    /**
     * Update the specified tahun pelajaran in storage
     */
    public function update(Request $request, $id)
    {
        $tahunPelajaran = TahunPelajaran::findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:255|unique:tahun_pelajaran,nama,' . $id,
            'tahun_mulai' => 'required|integer|min:2000|max:2100',
            'tahun_selesai' => 'required|integer|min:2000|max:2100|gt:tahun_mulai',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after:tanggal_mulai',
            'keterangan' => 'nullable|string',
        ]);

        $tahunPelajaran->update($request->only([
            'nama', 'tahun_mulai', 'tahun_selesai', 'tanggal_mulai', 'tanggal_selesai', 'keterangan'
        ]));

        return redirect()->route('admin.tahun-pelajaran.index')
            ->with('success', 'Tahun Pelajaran berhasil diperbarui!');
    }

    /**
     * Set tahun pelajaran as active
     */
    public function setActive($id)
    {
        $tahunPelajaran = TahunPelajaran::findOrFail($id);
        $tahunPelajaran->setActive();

        return redirect()->back()
            ->with('success', 'Tahun Pelajaran "' . $tahunPelajaran->nama . '" berhasil diaktifkan!');
    }

    /**
     * Remove the specified tahun pelajaran from storage
     */
    public function destroy($id)
    {
        $tahunPelajaran = TahunPelajaran::findOrFail($id);

        if ($tahunPelajaran->is_active) {
            return redirect()->back()
                ->with('error', 'Tidak dapat menghapus Tahun Pelajaran yang sedang aktif!');
        }

        $nama = $tahunPelajaran->nama;
        $tahunPelajaran->delete();

        return redirect()->route('admin.tahun-pelajaran.index')
            ->with('success', 'Tahun Pelajaran "' . $nama . '" berhasil dihapus!');
    }

    /**
     * Promote students to next grade based on their enrollment year
     */
    private function promoteStudents($tahunAjaranBaru)
    {
        $promoted = 0;
        $graduated = 0;

        // Get all active students
        $students = StudentProfile::where('is_active', true)
            ->where('status', 'AKTIF')
            ->whereNotNull('kelas_id')
            ->with('kelas')
            ->get();

        foreach ($students as $student) {
            if (!$student->kelas) {
                continue;
            }

            $currentTingkat = (int) $student->kelas->tingkat;

            // Siswa kelas 9 tidak naik, set status LULUS
            if ($currentTingkat >= 9) {
                $student->update([
                    'status' => 'LULUS',
                    'is_active' => false
                ]);
                $graduated++;
                continue;
            }

            // Naikkan tingkat: 7 → 8, 8 → 9
            $tingkatBaru = $currentTingkat + 1;

            // Cari kelas dengan tingkat baru dan nama kelas yang sama
            // Contoh: 7A → 8A, 8B → 9B
            $newKelas = Kelas::where('tingkat', $tingkatBaru)
                ->where('nama_kelas', $student->kelas->nama_kelas)
                ->first();

            // Jika tidak ada kelas dengan nama yang sama, ambil kelas pertama di tingkat tersebut
            if (!$newKelas) {
                $newKelas = Kelas::where('tingkat', $tingkatBaru)
                    ->orderBy('nama_kelas')
                    ->first();
            }

            // Update kelas siswa
            if ($newKelas) {
                $student->update([
                    'kelas_id' => $newKelas->id
                ]);
                $promoted++;
            }
        }

        return [
            'promoted' => $promoted,
            'graduated' => $graduated,
            'total' => $promoted + $graduated
        ];
    }
}
