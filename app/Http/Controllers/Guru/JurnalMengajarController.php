<?php

namespace App\Http\Controllers\Guru;

use App\Helpers\ImageCompressor;
use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\JadwalPelajaran;
use App\Models\JamPelajaran;
use App\Models\JurnalAttendance;
use App\Models\JurnalMengajar;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Semester;
use App\Models\StudentProfile;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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

        // Check if teacher is Wali Kelas (granted access to entire angkatan/tingkat)
        $isWali = $guru->isWaliKelas();
        $waliKelas = $isWali ? $guru->waliKelasOf() : null;
        $angkatanKelasIds = [];
        if ($waliKelas && $waliKelas->tingkat) {
            $angkatanQuery = Kelas::where('tingkat', $waliKelas->tingkat);
            if ($waliKelas->tahun_pelajaran_id) {
                $angkatanQuery->where('tahun_pelajaran_id', $waliKelas->tahun_pelajaran_id);
            }
            $angkatanKelasIds = $angkatanQuery->pluck('id')->toArray();
        }

        $query = JurnalMengajar::with(['kelas', 'mataPelajaran', 'jamPelajaranMulai', 'jamPelajaranSelesai', 'guru'])
            ->byBulan($bulan, $tahun);

        if (!empty($angkatanKelasIds)) {
            $query->where(function ($q) use ($guru, $angkatanKelasIds) {
                $q->where('guru_id', $guru->id)
                  ->orWhereIn('kelas_id', $angkatanKelasIds);
            });
        } else {
            $query->byGuru($guru->id);
        }

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

        // Ambil daftar kelas dan mata pelajaran yang relevan untuk filter
        if (!empty($angkatanKelasIds)) {
            $kelasList = Kelas::whereIn('id', function($sub) use ($guru, $angkatanKelasIds) {
                $sub->select('kelas_id')
                    ->from('jurnal_mengajar')
                    ->where('guru_id', $guru->id)
                    ->orWhereIn('kelas_id', $angkatanKelasIds);
            })->orWhereIn('id', $angkatanKelasIds)
              ->orderBy('tingkat')
              ->orderBy('nama_kelas')
              ->get();

            $mataPelajaranList = MataPelajaran::whereIn('id', function($sub) use ($guru, $angkatanKelasIds) {
                $sub->select('mata_pelajaran_id')
                    ->from('jurnal_mengajar')
                    ->where('guru_id', $guru->id)
                    ->orWhereIn('kelas_id', $angkatanKelasIds);
            })->orderBy('nama_mapel')->get();
        } else {
            // Guru biasa: dropdown kelas mencakup seluruh kelas yang diajar (dari jadwal dan jurnal)
            $taughtKelasIds = \Illuminate\Support\Facades\DB::table('jadwal_pelajarans')
                ->where('guru_id', $guru->id)
                ->distinct()
                ->pluck('kelas_id')
                ->toArray();
            $journalKelasIds = JurnalMengajar::where('guru_id', $guru->id)
                ->distinct()
                ->pluck('kelas_id')
                ->toArray();
            $allKelasIds = array_values(array_unique(array_merge($taughtKelasIds, $journalKelasIds)));

            $kelasList = Kelas::whereIn('id', $allKelasIds)
                ->orderBy('tingkat')
                ->orderBy('nama_kelas')
                ->get();

            // Dropdown mapel mencakup seluruh mapel yang diajar (dari jadwal, profil/pivot, dan jurnal)
            $taughtMapelIds = \Illuminate\Support\Facades\DB::table('jadwal_pelajarans')
                ->where('guru_id', $guru->id)
                ->distinct()
                ->pluck('mata_pelajaran_id')
                ->toArray();
            $journalMapelIds = JurnalMengajar::where('guru_id', $guru->id)
                ->distinct()
                ->pluck('mata_pelajaran_id')
                ->toArray();
            $profileMapelIds = $guru->guruProfile ? $guru->guruProfile->all_mata_pelajaran_ids : [];
            $allMapelIds = array_values(array_unique(array_merge($taughtMapelIds, $journalMapelIds, $profileMapelIds)));

            $mataPelajaranList = MataPelajaran::whereIn('id', $allMapelIds)
                ->orderBy('nama_mapel')
                ->get();
        }

        return view('guru.jurnal-mengajar.index', compact('jurnals', 'bulan', 'tahun', 'kelasId', 'mataPelajaranId', 'kelasList', 'mataPelajaranList', 'isWali', 'waliKelas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $guru = Auth::user();
        $tanggal = $request->get('tanggal', date('Y-m-d'));
        $hari = Carbon::parse($tanggal)->locale('id')->dayName;

        // Ambil data semester aktif
        $semesterAktif = Semester::getActive();

        // Ambil jadwal mengajar guru untuk hari tertentu (dibatasi semester aktif)
        $jadwalQuery = JadwalPelajaran::with(['kelas', 'mataPelajaran'])
            ->where('guru_id', $guru->id)
            ->where('hari', $hari);

        if ($semesterAktif) {
            $jadwalQuery->where('semester_id', $semesterAktif->id);
        }

        $jadwals = $jadwalQuery->orderBy('jam_ke')->get();

        // Ambil jurnal yang sudah diisi di hari ini
        $jurnalSudahDiisi = JurnalMengajar::where('guru_id', $guru->id)
            ->whereDate('tanggal', $tanggal)
            ->get();

        // Gabungkan jadwal yang berurutan dengan kelas dan mapel yang sama
        $groupedJadwals = JadwalPelajaran::groupConsecutiveSchedules($jadwals, $jurnalSudahDiisi);

        $jurnalBelumDiisi = $groupedJadwals->where('has_jurnal', false)->values();
        $jurnalSudahDiisiCollection = $groupedJadwals->where('has_jurnal', true)->map(function ($item) {
            return [
                'jadwal' => $item,
                'jurnal' => $item['jurnal'],
            ];
        })->values();

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
        $returnTo = $request->get('return_to', 'create');

        // Validasi parameter
        if (! $tanggal || ! $kelasId || ! $mataPelajaranId || ! $jamKeMulai || ! $jamKeSelesai) {
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
            ->where('is_active', true)
            ->get()
            ->sortBy(function ($siswa) {
                return $siswa->user->name ?? '';
            });

        // Ambil absensi pagi siswa
        $absensiPagi = Attendance::whereIn('user_id', $siswaKelas->pluck('user_id'))
            ->whereDate('date', $tanggal)
            ->get()
            ->keyBy('user_id');

        // Cari jurnal mengajar sebelumnya di kelas dan tanggal yang sama (jam_ke_selesai <= jam_ke_mulai)
        $jurnalSebelumnya = JurnalMengajar::with(['jurnalAttendances', 'mataPelajaran'])
            ->where('kelas_id', $kelasId)
            ->whereDate('tanggal', $tanggal)
            ->where('jam_ke_selesai', '<=', $jamKeMulai)
            ->orderBy('jam_ke_selesai', 'desc')
            ->first();

        // Hitung default status dan metadata acuan untuk setiap siswa
        $defaultAbsensiSiswa = [];
        $acuanKehadiran = null;

        if ($jurnalSebelumnya && $jurnalSebelumnya->jurnalAttendances->isNotEmpty()) {
            $prevAttendances = $jurnalSebelumnya->jurnalAttendances->keyBy('student_profile_id');
            $labelJam = 'Jam ' . $jurnalSebelumnya->jam_ke_mulai . ($jurnalSebelumnya->jam_ke_mulai != $jurnalSebelumnya->jam_ke_selesai ? '-' . $jurnalSebelumnya->jam_ke_selesai : '');
            $acuanKehadiran = [
                'tipe' => 'jurnal_sebelumnya',
                'label' => $labelJam,
                'mapel' => $jurnalSebelumnya->mataPelajaran->nama_mapel ?? 'Mapel Sebelumnya',
                'jurnal_id' => $jurnalSebelumnya->id,
            ];

            foreach ($siswaKelas as $siswa) {
                $prevAtt = $prevAttendances->get($siswa->id);
                $prevStatus = $prevAtt ? $prevAtt->status : null;

                // Konversi: 'terlambat' di jam sebelumnya menjadi 'hadir' di jam berikutnya
                // Status sakit, izin, alpa dipertahankan
                if ($prevStatus === 'terlambat') {
                    $defaultStatus = 'hadir';
                } elseif (in_array($prevStatus, ['sakit', 'izin', 'alpa'])) {
                    $defaultStatus = $prevStatus;
                } elseif ($prevStatus === 'hadir') {
                    $defaultStatus = 'hadir';
                } else {
                    // Fallback jika belum tercatat di jurnal sebelumnya
                    $pagi = $absensiPagi->get($siswa->user_id);
                    $statusPagi = $pagi ? ($pagi->status === 'alpha' ? 'alpa' : $pagi->status) : 'belum_absen';
                    if ($statusPagi === 'terlambat') {
                        $defaultStatus = 'terlambat';
                    } elseif (in_array($statusPagi, ['sakit', 'izin', 'alpa'])) {
                        $defaultStatus = $statusPagi;
                    } elseif ($statusPagi === 'hadir') {
                        $defaultStatus = 'hadir';
                    } else {
                        $defaultStatus = 'alpa';
                    }
                }

                $defaultAbsensiSiswa[$siswa->id] = [
                    'default_status' => $defaultStatus,
                    'status_acuan' => $prevStatus ?? 'alpa',
                    'label_acuan' => $acuanKehadiran['label'] . ' (' . ($prevStatus ? ucfirst($prevStatus) : 'Alpa') . ')',
                ];
            }
        } else {
            // Jam pertama atau belum ada jurnal sebelumnya -> Mengacu ke Absensi Pagi
            $acuanKehadiran = [
                'tipe' => 'absensi_pagi',
                'label' => 'Absensi Pagi',
                'mapel' => null,
                'jurnal_id' => null,
            ];

            foreach ($siswaKelas as $siswa) {
                $pagi = $absensiPagi->get($siswa->user_id);
                $statusPagi = $pagi ? ($pagi->status === 'alpha' ? 'alpa' : $pagi->status) : 'belum_absen';

                if ($statusPagi === 'terlambat') {
                    $defaultStatus = 'terlambat';
                } elseif (in_array($statusPagi, ['sakit', 'izin', 'alpa'])) {
                    $defaultStatus = $statusPagi;
                } elseif ($statusPagi === 'hadir') {
                    $defaultStatus = 'hadir';
                } else {
                    // Belum absen pagi -> default 'alpa'
                    $defaultStatus = 'alpa';
                }

                $defaultAbsensiSiswa[$siswa->id] = [
                    'default_status' => $defaultStatus,
                    'status_acuan' => $statusPagi,
                    'label_acuan' => $statusPagi === 'belum_absen' ? 'Belum Absen (Alpa)' : ucfirst($statusPagi),
                ];
            }
        }

        // Ambil jam pelajaran untuk display
        $semesterAktif = Semester::getActive();
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
            'jamPelajarans',
            'returnTo',
            'defaultAbsensiSiswa',
            'acuanKehadiran'
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
            'jam_ke_selesai' => 'required|integer|gte:jam_ke_mulai',
            'mata_pelajaran_id' => 'required|exists:mata_pelajarans,id',
            'materi_pembelajaran' => 'required|string',
            'keterangan' => 'nullable|string',
            'foto_bukti' => 'required|string', // Base64 image
        ]);

        $hari = Carbon::parse($request->tanggal)->locale('id')->dayName;

        // Cek apakah sudah pernah mengisi jurnal di slot jam yang beririsan untuk guru, tanggal, dan kelas ini
        $existingJurnal = JurnalMengajar::where('guru_id', Auth::id())
            ->whereDate('tanggal', $request->tanggal)
            ->where('kelas_id', $request->kelas_id)
            ->where('mata_pelajaran_id', $request->mata_pelajaran_id)
            ->where(function ($query) use ($request) {
                $query->where('jam_ke_mulai', '<=', $request->jam_ke_selesai)
                    ->where('jam_ke_selesai', '>=', $request->jam_ke_mulai);
            })
            ->first();

        if ($existingJurnal) {
            return redirect()->route('guru.jurnal-mengajar.show', $existingJurnal->id)
                ->with('info', 'Jurnal mengajar untuk jam pelajaran ini sudah pernah diisi.');
        }

        // Validasi jam pelajaran exists
        $semesterAktif = Semester::getActive();

        if (! $semesterAktif) {
            return back()->withErrors(['semester' => 'Semester aktif tidak ditemukan.'])->withInput();
        }

        // Cek apakah jam pelajaran ada
        $jamPelajaranMulai = JamPelajaran::where('semester_id', $semesterAktif->id)
            ->where('jam_ke', $request->jam_ke_mulai)
            ->first();

        $jamPelajaranSelesai = JamPelajaran::where('semester_id', $semesterAktif->id)
            ->where('jam_ke', $request->jam_ke_selesai)
            ->first();

        if (! $jamPelajaranMulai || ! $jamPelajaranSelesai) {
            return back()->withErrors(['jam' => 'Data jam pelajaran tidak ditemukan.'])->withInput();
        }        // Handle foto bukti upload
        $fotoPath = null;
        if ($request->has('foto_bukti') && $request->foto_bukti) {
            try {
                // Compress and save image with explicit parameters
                $compressedImage = ImageCompressor::compressBase64Image(
                    $request->foto_bukti,
                    60,   // Quality 60%
                    1200  // Max width 1200px - MUST resize!
                );

                // Generate unique filename
                $filename = 'jurnal_'.Auth::id().'_'.time().'.webp';
                $path = 'jurnal_mengajar/'.$filename;

                // Save to storage
                Storage::disk('public')->put($path, $compressedImage);
                $fotoPath = $path;
            } catch (\Exception $e) {
                return back()->withErrors(['foto_bukti' => 'Gagal mengupload foto: '.$e->getMessage()]);
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

        // Cari jurnal mengajar sebelumnya di kelas dan tanggal yang sama
        $jurnalSebelumnya = JurnalMengajar::with('jurnalAttendances')
            ->where('kelas_id', $request->kelas_id)
            ->whereDate('tanggal', $request->tanggal)
            ->where('jam_ke_selesai', '<=', $request->jam_ke_mulai)
            ->orderBy('jam_ke_selesai', 'desc')
            ->first();

        $prevAttendances = ($jurnalSebelumnya && $jurnalSebelumnya->jurnalAttendances->isNotEmpty())
            ? $jurnalSebelumnya->jurnalAttendances->keyBy('student_profile_id')
            : null;

        // Buat data absensi siswa dari input form (jika ada) atau dari jurnal sebelumnya / absensi harian
        if ($request->has('absensi') && is_array($request->absensi)) {
            // Dari wizard form - ada input absensi manual
            foreach ($request->absensi as $studentProfileId => $statusAbsensi) {
                $siswa = StudentProfile::find($studentProfileId);
                if ($siswa) {
                    if ($prevAttendances && $prevAttendances->has($studentProfileId)) {
                        $statusAwal = $prevAttendances->get($studentProfileId)->status;
                    } else {
                        $absensiHarian = Attendance::where('user_id', $siswa->user_id)
                            ->whereDate('date', $request->tanggal)
                            ->first();

                        $statusAwal = $absensiHarian ? ($absensiHarian->status === 'alpha' ? 'alpa' : $absensiHarian->status) : 'belum_absen';
                    }

                    JurnalAttendance::create([
                        'jurnal_mengajar_id' => $jurnal->id,
                        'student_profile_id' => $studentProfileId,
                        'status' => $statusAbsensi,
                        'status_awal' => $statusAwal,
                    ]);

                    // Sinkronisasi status absensi jurnal ke absensi harian jika belum scan pagi
                    $this->syncToDailyAttendance($siswa->user_id, $request->tanggal, $statusAbsensi, $request->jam_ke_mulai);
                }
            }
        } else {
            // Fallback - buat otomatis dari jurnal sebelumnya atau absensi harian (untuk form lama / API)
            $siswaKelas = StudentProfile::where('kelas_id', $request->kelas_id)
                ->where('is_active', true)
                ->get();

            foreach ($siswaKelas as $siswa) {
                if ($prevAttendances && $prevAttendances->has($siswa->id)) {
                    $prevStatus = $prevAttendances->get($siswa->id)->status;
                    $statusAwal = $prevStatus;
                    // Konversi: 'terlambat' di jam sebelumnya menjadi 'hadir' di jam berikutnya
                    $status = ($prevStatus === 'terlambat') ? 'hadir' : $prevStatus;
                } else {
                    $absensiHarian = Attendance::where('user_id', $siswa->user_id)
                        ->whereDate('date', $request->tanggal)
                        ->first();

                    $statusAwal = $absensiHarian ? ($absensiHarian->status === 'alpha' ? 'alpa' : $absensiHarian->status) : 'belum_absen';

                    // Default alpa jika tidak ada scan
                    $status = 'alpa';
                    if ($absensiHarian) {
                        if (in_array($absensiHarian->status, ['sakit', 'izin', 'alpha', 'alpa'])) {
                            $status = $absensiHarian->status === 'alpha' ? 'alpa' : $absensiHarian->status;
                        } elseif ($absensiHarian->status === 'terlambat') {
                            $status = 'terlambat';
                        } elseif ($absensiHarian->status === 'hadir') {
                            $status = 'hadir';
                        }
                    }
                }

                JurnalAttendance::create([
                    'jurnal_mengajar_id' => $jurnal->id,
                    'student_profile_id' => $siswa->id,
                    'status' => $status,
                    'status_awal' => $statusAwal,
                ]);

                // Sinkronisasi status absensi jurnal ke absensi harian jika belum scan pagi
                $this->syncToDailyAttendance($siswa->user_id, $request->tanggal, $status, $request->jam_ke_mulai);
            }
        }

        if ($request->get('return_to') === 'today') {
            return redirect()->route('guru.jadwal-mengajar.today')
                ->with('success', 'Jurnal mengajar berhasil disimpan.');
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

            // Sinkronisasi status absensi jurnal ke absensi harian jika belum scan pagi
            $siswa = StudentProfile::find($request->student_profile_id);
            if ($siswa) {
                $this->syncToDailyAttendance($siswa->user_id, $jurnal->tanggal, $request->status, $jurnal->jam_ke_mulai);
            }
        }

        return back()->with('success', 'Absensi siswa berhasil diperbarui!');
    }

    /**
     * Sinkronisasi status absensi jurnal ke absensi harian (attendance) jika belum ada scan pagi
     */
    protected function syncToDailyAttendance($userId, $tanggal, $statusAbsensi, $jamKeMulai)
    {
        $existingDaily = Attendance::where('user_id', $userId)
            ->whereDate('date', $tanggal)
            ->first();

        if ($statusAbsensi === 'hadir') {
            if (!$existingDaily) {
                // Belum ada absen pagi, tapi hadir di kelas -> otomatis tercatat TERLAMBAT di harian
                Attendance::create([
                    'user_id' => $userId,
                    'date' => $tanggal,
                    'time' => now(),
                    'status' => 'terlambat',
                    'notes' => 'Hadir di kelas jam ke-' . $jamKeMulai . ' (Terlambat)',
                    'created_by' => Auth::id(),
                ]);
            }
        } elseif (in_array($statusAbsensi, ['sakit', 'izin'])) {
            if (!$existingDaily) {
                // Belum ada absen pagi, ada keterangan sakit/izin di kelas -> catat sakit/izin di harian
                Attendance::create([
                    'user_id' => $userId,
                    'date' => $tanggal,
                    'time' => null,
                    'status' => $statusAbsensi,
                    'notes' => 'Dicatat dari Jurnal Mengajar Jam ke-' . $jamKeMulai,
                    'created_by' => Auth::id(),
                ]);
            }
            // Jika sudah ada record 'hadir' atau 'terlambat' di pagi hari, TIDAK ditimpa!
        }
    }

    /**
     * Convert HEIC image to JPG for client-side compression
     */
    public function convertHeic(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file|max:10240', // max 10MB
            ]);

            $file = $request->file('file');
            $extension = strtolower($file->getClientOriginalExtension());

            // Verify it's a HEIC file
            if (! in_array($extension, ['heic', 'heif'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'File must be HEIC or HEIF format',
                ], 400);
            }

            // Convert HEIC to JPG and store temporarily
            $convertedPath = ImageCompressor::convertHeicToJpgTemp($file);

            if (! $convertedPath) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to convert HEIC file',
                ], 500);
            }

            // Read the converted JPG file and return as base64
            $fullPath = storage_path('app/'.$convertedPath);

            if (! file_exists($fullPath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Converted file not found',
                ], 500);
            }

            $imageData = file_get_contents($fullPath);
            $base64 = 'data:image/jpeg;base64,'.base64_encode($imageData);

            // Clean up temporary converted file
            Storage::delete($convertedPath);

            return response()->json([
                'success' => true,
                'image' => $base64,
                'message' => 'HEIC converted successfully',
            ]);

        } catch (\Exception $e) {
            Log::error('HEIC Conversion Error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error converting HEIC: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Print jurnal mengajar
     */
    public function print(Request $request)
    {
        $guru = Auth::user();
        $bulan = $request->get('bulan', date('m'));
        $tahun = $request->get('tahun', date('Y'));

        $jurnals = JurnalMengajar::with(['kelas', 'mataPelajaran', 'jamPelajaranMulai', 'jamPelajaranSelesai'])
            ->byGuru($guru->id)
            ->byBulan($bulan, $tahun)
            ->orderBy('tanggal', 'asc')
            ->orderBy('jam_ke_mulai', 'asc')
            ->get();

        return view('guru.jurnal-mengajar.print', compact('jurnals', 'bulan', 'tahun', 'guru'));
    }
}
