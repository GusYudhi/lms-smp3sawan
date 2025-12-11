<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\JurnalMengajar;
use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\JamPelajaran;
use App\Models\MataPelajaran;
use App\Models\Semester;
use App\Models\Attendance;
use App\Models\StudentProfile;
use App\Models\JurnalAttendance;
use App\Helpers\ImageCompressor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class JurnalMengajarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $guru = Auth::user();
        $bulan = $request->get('bulan', date('m'));
        $tahun = $request->get('tahun', date('Y'));
        $kelasId = $request->get('kelas_id');
        $mataPelajaranId = $request->get('mata_pelajaran_id');

        $query = JurnalMengajar::with(['kelas', 'mataPelajaran', 'jamPelajaranMulai', 'jamPelajaranSelesai'])
            ->byGuru($guru->id)
            ->byBulan($bulan, $tahun);

        // Filter berdasarkan kelas jika dipilih
        if ($kelasId) {
            $query->where('kelas_id', $kelasId);
        }

        // Filter berdasarkan mata pelajaran jika dipilih
        if ($mataPelajaranId) {
            $query->where('mata_pelajaran_id', $mataPelajaranId);
        }

        $jurnals = $query->orderBy('tanggal', 'desc')
            ->orderBy('jam_ke_mulai', 'asc')
            ->paginate(20)
            ->appends($request->except('page'));

        // Ambil daftar kelas dan mata pelajaran yang pernah diajar oleh guru
        $kelasList = JurnalMengajar::select('kelas_id')
            ->where('guru_id', $guru->id)
            ->distinct()
            ->with('kelas')
            ->get()
            ->pluck('kelas')
            ->sortBy('full_name');

        $mataPelajaranList = JurnalMengajar::select('mata_pelajaran_id')
            ->where('guru_id', $guru->id)
            ->distinct()
            ->with('mataPelajaran')
            ->get()
            ->pluck('mataPelajaran')
            ->sortBy('nama_mapel');

        return view('guru.jurnal-mengajar.index', compact('jurnals', 'bulan', 'tahun', 'kelasId', 'mataPelajaranId', 'kelasList', 'mataPelajaranList'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $guru = Auth::user();
        $tanggal = $request->get('tanggal', date('Y-m-d'));
        $hari = Carbon::parse($tanggal)->locale('id')->dayName;

        // Ambil jadwal mengajar guru untuk hari tertentu
        $jadwals = JadwalPelajaran::with(['kelas', 'mataPelajaran'])
            ->where('guru_id', $guru->id)
            ->where('hari', $hari)
            ->orderBy('jam_ke')
            ->get();

        // Ambil jurnal yang sudah diisi di hari ini
        $jurnalSudahDiisi = JurnalMengajar::where('guru_id', $guru->id)
            ->whereDate('tanggal', $tanggal)
            ->get()
            ->keyBy(function ($item) {
                return $item->kelas_id . '_' . $item->mata_pelajaran_id . '_' . $item->jam_ke_mulai . '_' . $item->jam_ke_selesai;
            });

        // Gabungkan jadwal yang berurutan dengan kelas dan mapel yang sama
        $groupedJadwals = [];
        foreach ($jadwals as $jadwal) {
            $key = $jadwal->kelas_id . '_' . $jadwal->mata_pelajaran_id;

            if (!isset($groupedJadwals[$key])) {
                // Jadwal pertama untuk kombinasi kelas + mapel ini
                $groupedJadwals[$key] = [
                    'id' => $jadwal->id,
                    'kelas_id' => $jadwal->kelas_id,
                    'kelas' => $jadwal->kelas,
                    'mata_pelajaran_id' => $jadwal->mata_pelajaran_id,
                    'mataPelajaran' => $jadwal->mataPelajaran,
                    'jam_ke_mulai' => $jadwal->jam_ke,
                    'jam_ke_selesai' => $jadwal->jam_ke,
                    'jam_ke_array' => [$jadwal->jam_ke],
                ];
            } else {
                // Cek apakah jam_ke nya berurutan
                $lastJamKe = $groupedJadwals[$key]['jam_ke_selesai'];
                if ($jadwal->jam_ke == $lastJamKe + 1) {
                    // Jam berurutan, gabungkan
                    $groupedJadwals[$key]['jam_ke_selesai'] = $jadwal->jam_ke;
                    $groupedJadwals[$key]['jam_ke_array'][] = $jadwal->jam_ke;
                } else {
                    // Jam tidak berurutan, buat grup baru dengan suffix
                    $newKey = $key . '_' . $jadwal->jam_ke;
                    $groupedJadwals[$newKey] = [
                        'id' => $jadwal->id,
                        'kelas_id' => $jadwal->kelas_id,
                        'kelas' => $jadwal->kelas,
                        'mata_pelajaran_id' => $jadwal->mata_pelajaran_id,
                        'mataPelajaran' => $jadwal->mataPelajaran,
                        'jam_ke_mulai' => $jadwal->jam_ke,
                        'jam_ke_selesai' => $jadwal->jam_ke,
                        'jam_ke_array' => [$jadwal->jam_ke],
                    ];
                }
            }
        }

        // Filter jurnal yang belum diisi dan yang sudah diisi
        $jurnalBelumDiisi = [];
        $jurnalSudahDiisiCollection = [];

        foreach ($groupedJadwals as $key => $jadwal) {
            $jurnalKey = $jadwal['kelas_id'] . '_' . $jadwal['mata_pelajaran_id'] . '_' . $jadwal['jam_ke_mulai'] . '_' . $jadwal['jam_ke_selesai'];

            if (isset($jurnalSudahDiisi[$jurnalKey])) {
                $jurnalSudahDiisiCollection[] = [
                    'jadwal' => $jadwal,
                    'jurnal' => $jurnalSudahDiisi[$jurnalKey]
                ];
            } else {
                $jurnalBelumDiisi[] = $jadwal;
            }
        }

        // Convert to collection
        $jurnalBelumDiisi = collect($jurnalBelumDiisi);
        $jurnalSudahDiisiCollection = collect($jurnalSudahDiisiCollection);

        // Ambil data jam pelajaran dari semester aktif
        $semesterAktif = Semester::where('is_active', true)->first();
        $jamPelajarans = [];

        if ($semesterAktif) {
            $jamPelajarans = JamPelajaran::where('semester_id', $semesterAktif->id)
                ->orderBy('jam_ke')
                ->get()
                ->keyBy('jam_ke'); // Key by jam_ke untuk mudah di-mapping
        }

        return view('guru.jurnal-mengajar.create', compact('jurnalBelumDiisi', 'jurnalSudahDiisiCollection', 'tanggal', 'hari', 'jamPelajarans'));
    }

    /**
     * Show wizard form for creating jurnal with attendance
     */
    public function createWizard(Request $request)
    {
        $guru = Auth::user();
        $tanggal = $request->get('tanggal');
        $kelasId = $request->get('kelas_id');
        $mataPelajaranId = $request->get('mata_pelajaran_id');
        $jamKeMulai = $request->get('jam_ke_mulai');
        $jamKeSelesai = $request->get('jam_ke_selesai');

        // Validasi parameter
        if (!$tanggal || !$kelasId || !$mataPelajaranId || !$jamKeMulai || !$jamKeSelesai) {
            return redirect()->route('guru.jurnal-mengajar.create')
                ->withErrors(['error' => 'Parameter tidak lengkap']);
        }

        // Ambil data kelas, mata pelajaran
        $kelas = Kelas::findOrFail($kelasId);
        $mataPelajaran = MataPelajaran::findOrFail($mataPelajaranId);
        $hari = Carbon::parse($tanggal)->locale('id')->dayName;

        // Ambil siswa di kelas dengan relasi user dan sorting berdasarkan nama user
        $siswaKelas = StudentProfile::with('user')
            ->where('kelas_id', $kelasId)
            ->get()
            ->sortBy(function($siswa) {
                return $siswa->user->name ?? '';
            });

        // Ambil absensi pagi siswa
        $absensiPagi = Attendance::whereIn('user_id', $siswaKelas->pluck('user_id'))
            ->whereDate('date', $tanggal)
            ->get()
            ->keyBy('user_id');

        // Ambil jam pelajaran untuk display
        $semesterAktif = Semester::where('is_active', true)->first();
        $jamPelajarans = [];

        if ($semesterAktif) {
            $jamPelajarans = JamPelajaran::where('semester_id', $semesterAktif->id)
                ->orderBy('jam_ke')
                ->get()
                ->keyBy('jam_ke');
        }

        return view('guru.jurnal-mengajar.wizard', compact(
            'tanggal',
            'hari',
            'kelas',
            'mataPelajaran',
            'jamKeMulai',
            'jamKeSelesai',
            'siswaKelas',
            'absensiPagi',
            'jamPelajarans'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'kelas_id' => 'required|exists:kelas,id',
            'jam_ke_mulai' => 'required|integer',
            'jam_ke_selesai' => 'required|integer',
            'mata_pelajaran_id' => 'required|exists:mata_pelajarans,id',
            'materi_pembelajaran' => 'required|string',
            'keterangan' => 'nullable|string',
            'foto_bukti' => 'required|string', // Base64 image
        ]);

        $hari = Carbon::parse($request->tanggal)->locale('id')->dayName;

        // Validasi jam pelajaran exists
        $semesterAktif = Semester::where('is_active', true)->first();

        if (!$semesterAktif) {
            return back()->withErrors(['semester' => 'Semester aktif tidak ditemukan.'])->withInput();
        }

        // Cek apakah jam pelajaran ada
        $jamPelajaranMulai = JamPelajaran::where('semester_id', $semesterAktif->id)
            ->where('jam_ke', $request->jam_ke_mulai)
            ->first();

        $jamPelajaranSelesai = JamPelajaran::where('semester_id', $semesterAktif->id)
            ->where('jam_ke', $request->jam_ke_selesai)
            ->first();

        if (!$jamPelajaranMulai || !$jamPelajaranSelesai) {
            return back()->withErrors(['jam' => 'Data jam pelajaran tidak ditemukan.'])->withInput();
        }        // Handle foto bukti upload
        $fotoPath = null;
        if ($request->has('foto_bukti') && $request->foto_bukti) {
            try {
                // Compress and save image
                $compressedImage = ImageCompressor::compressBase64Image($request->foto_bukti, 80);

                // Generate unique filename
                $filename = 'jurnal_' . Auth::id() . '_' . time() . '.webp';
                $path = 'jurnal_mengajar/' . $filename;

                // Save to storage
                Storage::disk('public')->put($path, $compressedImage);
                $fotoPath = $path;
            } catch (\Exception $e) {
                return back()->withErrors(['foto_bukti' => 'Gagal mengupload foto: ' . $e->getMessage()]);
            }
        }

        $jurnal = JurnalMengajar::create([
            'guru_id' => Auth::id(),
            'tanggal' => $request->tanggal,
            'hari' => $hari,
            'kelas_id' => $request->kelas_id,
            'jam_ke_mulai' => $request->jam_ke_mulai,
            'jam_ke_selesai' => $request->jam_ke_selesai,
            'mata_pelajaran_id' => $request->mata_pelajaran_id,
            'materi_pembelajaran' => $request->materi_pembelajaran,
            'keterangan' => $request->keterangan,
            'foto_bukti' => $fotoPath,
        ]);

        // Buat data absensi siswa dari input form (jika ada) atau dari absensi harian
        if ($request->has('absensi') && is_array($request->absensi)) {
            // Dari wizard form - ada input absensi manual
            foreach ($request->absensi as $studentProfileId => $statusAbsensi) {
                $siswa = StudentProfile::find($studentProfileId);
                if ($siswa) {
                    // Cek absensi pagi
                    $absensiHarian = Attendance::where('user_id', $siswa->user_id)
                        ->whereDate('date', $request->tanggal)
                        ->first();

                    $statusAwal = $absensiHarian ? $absensiHarian->status : 'belum_absen';

                    JurnalAttendance::create([
                        'jurnal_mengajar_id' => $jurnal->id,
                        'student_profile_id' => $studentProfileId,
                        'status' => $statusAbsensi,
                        'status_awal' => $statusAwal,
                    ]);
                }
            }
        } else {
            // Fallback - buat otomatis dari absensi harian (untuk form lama)
            $siswaKelas = StudentProfile::where('kelas_id', $request->kelas_id)->get();

            foreach ($siswaKelas as $siswa) {
                $absensiHarian = Attendance::where('user_id', $siswa->user_id)
                    ->whereDate('date', $request->tanggal)
                    ->first();

                $statusAwal = $absensiHarian ? $absensiHarian->status : 'belum_absen';
                // Jika terlambat atau hadir di pagi, default hadir. Selain itu alpa
                $status = ($absensiHarian && in_array($absensiHarian->status, ['hadir', 'terlambat'])) ? 'hadir' : 'alpa';

                JurnalAttendance::create([
                    'jurnal_mengajar_id' => $jurnal->id,
                    'student_profile_id' => $siswa->id,
                    'status' => $status,
                    'status_awal' => $statusAwal,
                ]);
            }
        }

        return redirect()->route('guru.jurnal-mengajar.create', ['tanggal' => $request->tanggal])
            ->with('success', 'Jurnal mengajar berhasil disimpan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $jurnal = JurnalMengajar::with(['kelas', 'mataPelajaran', 'guru.guruProfile', 'jamPelajaranMulai', 'jamPelajaranSelesai', 'jurnalAttendances.studentProfile.user'])
            ->findOrFail($id);

        // Pastikan guru hanya bisa melihat jurnalnya sendiri
        if ($jurnal->guru_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('guru.jurnal-mengajar.show', compact('jurnal'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $jurnal = JurnalMengajar::with(['kelas', 'mataPelajaran'])
            ->findOrFail($id);

        // Pastikan guru hanya bisa edit jurnalnya sendiri
        if ($jurnal->guru_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('guru.jurnal-mengajar.edit', compact('jurnal'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $jurnal = JurnalMengajar::findOrFail($id);

        // Pastikan guru hanya bisa update jurnalnya sendiri
        if ($jurnal->guru_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'materi_pembelajaran' => 'required|string',
            'keterangan' => 'nullable|string',
        ]);

        $jurnal->update([
            'materi_pembelajaran' => $request->materi_pembelajaran,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('guru.jurnal-mengajar.show', $jurnal->id)
            ->with('success', 'Jurnal mengajar berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $jurnal = JurnalMengajar::findOrFail($id);

        // Pastikan guru hanya bisa hapus jurnalnya sendiri
        if ($jurnal->guru_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $jurnal->delete();

        return redirect()->route('guru.jurnal-mengajar.index')
            ->with('success', 'Jurnal mengajar berhasil dihapus!');
    }

    /**
     * Update absensi siswa
     */
    public function updateAbsensi(Request $request, $jurnalId)
    {
        $jurnal = JurnalMengajar::findOrFail($jurnalId);

        // Pastikan guru hanya bisa update absensi jurnalnya sendiri
        if ($jurnal->guru_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'status' => 'required|in:hadir,sakit,izin,alpa',
            'notes' => 'nullable|string',
        ]);

        // Update atau create absensi
        Attendance::updateOrCreate(
            [
                'user_id' => $request->user_id,
                'date' => $jurnal->tanggal,
            ],
            [
                'status' => $request->status,
                'time' => now(),
                'created_by' => Auth::id(),
                'notes' => $request->notes,
            ]
        );

        return back()->with('success', 'Absensi berhasil diperbarui!');
    }

    /**
     * Tampilkan halaman absensi siswa untuk jurnal
     */
    public function showAbsensi($jurnalId)
    {
        $jurnal = JurnalMengajar::with(['kelas', 'mataPelajaran', 'jurnalAttendances.studentProfile.user'])
            ->findOrFail($jurnalId);

        // Pastikan guru hanya bisa melihat absensi jurnalnya sendiri
        if ($jurnal->guru_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('guru.jurnal-mengajar.absensi', compact('jurnal'));
    }

    /**
     * Update absensi siswa di jurnal
     */
    public function updateJurnalAbsensi(Request $request, $jurnalId)
    {
        $jurnal = JurnalMengajar::findOrFail($jurnalId);

        // Pastikan guru hanya bisa update absensi jurnalnya sendiri
        if ($jurnal->guru_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'student_profile_id' => 'required|exists:student_profiles,id',
            'status' => 'required|in:hadir,sakit,izin,alpa',
        ]);

        // Update absensi jurnal
        $jurnalAttendance = JurnalAttendance::where('jurnal_mengajar_id', $jurnalId)
            ->where('student_profile_id', $request->student_profile_id)
            ->first();

        if ($jurnalAttendance) {
            $jurnalAttendance->update(['status' => $request->status]);
        }

        return back()->with('success', 'Absensi siswa berhasil diperbarui!');
    }
}

