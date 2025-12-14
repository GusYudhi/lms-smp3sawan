<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Semester;
use App\Models\TahunPelajaran;
use App\Models\MataPelajaran;
use App\Models\JamPelajaran;
use App\Models\FixedSchedule;
use Illuminate\Http\Request;

class SemesterController extends Controller
{
    /**
     * Show the form for creating a new semester
     */
    public function create(Request $request)
    {
        $tahunPelajaranId = $request->input('tahun_pelajaran_id');
        if (!$tahunPelajaranId) {
            // fallback: ambil tahun pelajaran aktif
            $tahunPelajaran = TahunPelajaran::where('is_active', 1)->first();
        } else {
            $tahunPelajaran = TahunPelajaran::findOrFail($tahunPelajaranId);
        }
        return view('admin.semester.create', compact('tahunPelajaran'));
    }

    /**
     * Store a newly created semester in storage
     */
    public function store(Request $request)
    {
        $request->validate([
            'tahun_pelajaran_id' => 'required|exists:tahun_pelajaran,id',
            'nama' => 'required|string|max:255',
            'semester_ke' => 'required|integer|in:1,2',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after:tanggal_mulai',
            'is_active' => 'nullable|boolean',
            'keterangan' => 'nullable|string',
        ]);

        $semester = Semester::create($request->only([
            'tahun_pelajaran_id', 'nama', 'semester_ke',
            'tanggal_mulai', 'tanggal_selesai', 'keterangan'
        ]));

        // If is_active, set as active
        if ($request->boolean('is_active')) {
            $semester->setActive();
        }

        // Jika semester ke-2 dan permintaan salin data dari semester 1
        if ($request->input('copy_from_semester_1') == '1' && $semester->semester_ke == 2) {
            // Cari semester 1 di tahun pelajaran yang sama
            $sourceSemester = Semester::where('tahun_pelajaran_id', $semester->tahun_pelajaran_id)
                ->where('semester_ke', 1)
                ->first();
            if ($sourceSemester) {
                // Cek apakah semester 1 punya data
                $sourceHasData = \App\Models\MataPelajaran::where('semester_id', $sourceSemester->id)->exists() ||
                                 \App\Models\JamPelajaran::where('semester_id', $sourceSemester->id)->exists() ||
                                 \App\Models\FixedSchedule::where('semester_id', $sourceSemester->id)->exists();
                if ($sourceHasData) {
                    DB::transaction(function () use ($sourceSemester, $semester) {
                        // Copy Mata Pelajaran
                        $mataPelajarans = \App\Models\MataPelajaran::where('semester_id', $sourceSemester->id)->get();
                        foreach ($mataPelajarans as $mapel) {
                            \App\Models\MataPelajaran::create([
                                'nama_mapel' => $mapel->nama_mapel,
                                'kode_mapel' => $mapel->kode_mapel,
                                'semester_id' => $semester->id,
                            ]);
                        }
                        // Copy Jam Pelajaran
                        $jamPelajarans = \App\Models\JamPelajaran::where('semester_id', $sourceSemester->id)->get();
                        foreach ($jamPelajarans as $jam) {
                            \App\Models\JamPelajaran::create([
                                'jam_ke' => $jam->jam_ke,
                                'jam_mulai' => $jam->jam_mulai,
                                'jam_selesai' => $jam->jam_selesai,
                                'semester_id' => $semester->id,
                            ]);
                        }
                        // Copy Fixed Schedules
                        $fixedSchedules = \App\Models\FixedSchedule::where('semester_id', $sourceSemester->id)->get();
                        foreach ($fixedSchedules as $schedule) {
                            \App\Models\FixedSchedule::create([
                                'hari' => $schedule->hari,
                                'jam_ke' => $schedule->jam_ke,
                                'keterangan' => $schedule->keterangan,
                                'semester_id' => $semester->id,
                            ]);
                        }
                    });
                    $msg = 'Semester berhasil ditambahkan dan data berhasil disalin dari Semester 1!';
                } else {
                    $msg = 'Semester berhasil ditambahkan, namun Semester 1 belum memiliki data untuk disalin!';
                }
            } else {
                $msg = 'Semester berhasil ditambahkan, namun Semester 1 tidak ditemukan!';
            }
            return redirect()->route('admin.tahun-pelajaran.dashboard', $request->tahun_pelajaran_id)
                ->with('success', $msg);
        }

        return redirect()->route('admin.tahun-pelajaran.dashboard', $request->tahun_pelajaran_id)
            ->with('success', 'Semester berhasil ditambahkan!');
    }

    /**
     * Display the dashboard for specific semester
     */
    public function dashboard($id)
    {
        $semester = Semester::with('tahunPelajaran')->findOrFail($id);

        // Get statistics
        $statistics = [
            'total_mata_pelajaran' => $semester->mataPelajaran()->count(),
            'total_jadwal' => $semester->jadwalPelajaran()->count(),
            'total_jam_pelajaran' => $semester->jamPelajaran()->count(),
            'total_fixed_schedule' => $semester->fixedSchedules()->count(),
        ];

        return view('admin.semester.dashboard', compact('semester', 'statistics'));
    }

    /**
     * Show the form for editing the specified semester
     */
    public function edit($id)
    {
        $semester = Semester::with('tahunPelajaran')->findOrFail($id);
        return view('admin.semester.edit', compact('semester'));
    }

    /**
     * Update the specified semester in storage
     */
    public function update(Request $request, $id)
    {
        $semester = Semester::findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:255',
            'semester_ke' => 'required|integer|in:1,2',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after:tanggal_mulai',
            'keterangan' => 'nullable|string',
        ]);

        $semester->update($request->only([
            'nama', 'semester_ke', 'tanggal_mulai', 'tanggal_selesai', 'keterangan'
        ]));

        return redirect()->route('admin.tahun-pelajaran.dashboard', $semester->tahun_pelajaran_id)
            ->with('success', 'Semester berhasil diperbarui!');
    }

    /**
     * Set semester as active
     */
    public function setActive($id)
    {
        $semester = Semester::findOrFail($id);
        $semester->setActive();

        return redirect()->back()
            ->with('success', 'Semester "' . $semester->full_name . '" berhasil diaktifkan!');
    }

    /**
     * Remove the specified semester from storage
     */
    public function destroy($id)
    {
        $semester = Semester::findOrFail($id);

        if ($semester->is_active) {
            return redirect()->back()
                ->with('error', 'Tidak dapat menghapus Semester yang sedang aktif!');
        }

        $nama = $semester->full_name;
        $tahunPelajaranId = $semester->tahun_pelajaran_id;
        $semester->delete();

        return redirect()->route('admin.tahun-pelajaran.dashboard', $tahunPelajaranId)
            ->with('success', 'Semester "' . $nama . '" berhasil dihapus!');
    }

    /**
     * Import data from Semester 1 to Semester 2 in the same tahun pelajaran
     */
    public function importFromSemester1($id)
    {
        $targetSemester = Semester::findOrFail($id);

        // Check if this is semester 2
        if ($targetSemester->semester_ke != 2) {
            return redirect()->back()
                ->with('error', 'Import hanya dapat dilakukan untuk Semester 2 (Genap)!');
        }

        // Check if already has data
        $hasData = MataPelajaran::where('semester_id', $targetSemester->id)->exists() ||
                   JamPelajaran::where('semester_id', $targetSemester->id)->exists() ||
                   FixedSchedule::where('semester_id', $targetSemester->id)->exists();

        if ($hasData) {
            return redirect()->back()
                ->with('error', 'Semester ini sudah memiliki data. Hapus data terlebih dahulu jika ingin import ulang!');
        }

        // Find Semester 1 in the same tahun pelajaran
        $sourceSemester = Semester::where('tahun_pelajaran_id', $targetSemester->tahun_pelajaran_id)
            ->where('semester_ke', 1)
            ->first();

        if (!$sourceSemester) {
            return redirect()->back()
                ->with('error', 'Semester Ganjil tidak ditemukan!');
        }

        // Check if source has data
        $sourceHasData = MataPelajaran::where('semester_id', $sourceSemester->id)->exists() ||
                         JamPelajaran::where('semester_id', $sourceSemester->id)->exists() ||
                         FixedSchedule::where('semester_id', $sourceSemester->id)->exists();

        if (!$sourceHasData) {
            return redirect()->back()
                ->with('error', 'Semester Ganjil belum memiliki data untuk di-copy!');
        }

        DB::transaction(function () use ($sourceSemester, $targetSemester) {
            // Copy Mata Pelajaran
            $mataPelajarans = MataPelajaran::where('semester_id', $sourceSemester->id)->get();
            foreach ($mataPelajarans as $mapel) {
                MataPelajaran::create([
                    'nama_mapel' => $mapel->nama_mapel,
                    'kode_mapel' => $mapel->kode_mapel,
                    'semester_id' => $targetSemester->id,
                ]);
            }

            // Copy Jam Pelajaran
            $jamPelajarans = JamPelajaran::where('semester_id', $sourceSemester->id)->get();
            foreach ($jamPelajarans as $jam) {
                JamPelajaran::create([
                    'jam_ke' => $jam->jam_ke,
                    'jam_mulai' => $jam->jam_mulai,
                    'jam_selesai' => $jam->jam_selesai,
                    'semester_id' => $targetSemester->id,
                ]);
            }

            // Copy Fixed Schedules
            $fixedSchedules = FixedSchedule::where('semester_id', $sourceSemester->id)->get();
            foreach ($fixedSchedules as $schedule) {
                FixedSchedule::create([
                    'hari' => $schedule->hari,
                    'jam_ke' => $schedule->jam_ke,
                    'keterangan' => $schedule->keterangan,
                    'semester_id' => $targetSemester->id,
                ]);
            }
        });

        return redirect()->back()
            ->with('success', 'Data berhasil di-import dari Semester Ganjil!');
    }
}
