<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\JurnalMengajar;
use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\JamPelajaran;
use App\Models\MataPelajaran;
use App\Models\Attendance;
use App\Models\StudentProfile;
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

        $jurnals = JurnalMengajar::with(['kelas', 'mataPelajaran'])
            ->byGuru($guru->id)
            ->byBulan($bulan, $tahun)
            ->orderBy('tanggal', 'desc')
            ->orderBy('jam_ke', 'asc')
            ->paginate(20);

        return view('guru.jurnal-mengajar.index', compact('jurnals', 'bulan', 'tahun'));
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

        return view('guru.jurnal-mengajar.create', compact('jadwals', 'tanggal', 'hari'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'kelas_id' => 'required|exists:kelas,id',
            'jam_ke' => 'required|integer',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
            'mata_pelajaran_id' => 'required|exists:mata_pelajarans,id',
            'materi_pembelajaran' => 'required|string',
            'keterangan' => 'nullable|string',
            'foto_bukti' => 'required|string', // Base64 image
        ]);

        $hari = Carbon::parse($request->tanggal)->locale('id')->dayName;

        // Handle foto bukti upload
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
            'jam_ke' => $request->jam_ke,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'mata_pelajaran_id' => $request->mata_pelajaran_id,
            'materi_pembelajaran' => $request->materi_pembelajaran,
            'keterangan' => $request->keterangan,
            'foto_bukti' => $fotoPath,
        ]);

        return redirect()->route('guru.jurnal-mengajar.show', $jurnal->id)
            ->with('success', 'Jurnal mengajar berhasil disimpan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $jurnal = JurnalMengajar::with(['kelas', 'mataPelajaran', 'guru.guruProfile'])
            ->findOrFail($id);

        // Pastikan guru hanya bisa melihat jurnalnya sendiri
        if ($jurnal->guru_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Ambil data absensi siswa di kelas ini pada tanggal yang sama
        $siswaKelas = StudentProfile::where('kelas_id', $jurnal->kelas_id)
            ->with(['user'])
            ->get();

        $absensi = Attendance::whereIn('user_id', $siswaKelas->pluck('user_id'))
            ->whereDate('date', $jurnal->tanggal)
            ->get()
            ->keyBy('user_id');

        return view('guru.jurnal-mengajar.show', compact('jurnal', 'siswaKelas', 'absensi'));
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
}
