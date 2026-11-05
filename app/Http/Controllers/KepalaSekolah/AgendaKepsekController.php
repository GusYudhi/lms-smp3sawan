<?php

namespace App\Http\Controllers\KepalaSekolah;

use App\Http\Controllers\Controller;
use App\Models\AgendaGuru;
use App\Models\JamPelajaran;
use App\Models\Semester;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AgendaKepsekController extends Controller
{
    /**
     * Display a listing of the agenda.
     */
    public function index(Request $request)
    {
        $query = AgendaGuru::where('user_id', Auth::id());

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('materi', 'like', '%' . $search . '%')
                  ->orWhere('keterangan', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('kelas')) {
            if ($request->kelas === 'non_kelas') {
                $query->whereNull('kelas');
            } else {
                $query->where('kelas', $request->kelas);
            }
        }

        if ($request->filled('status')) {
            $query->where('status_jurnal', $request->status);
        }

        if ($request->filled('keterangan')) {
            $query->where('keterangan', 'like', '%' . $request->keterangan . '%');
        }

        // Order by date descending (most recent first)
        $agendas = $query->with(['jamMulai', 'jamSelesai'])
                        ->orderBy('tanggal', 'desc')
                        ->orderBy('jam_mulai_id', 'asc')
                        ->paginate(15);

        // Get active semester and its jam pelajaran
        $activeSemester = Semester::where('is_active', true)->first();
        $jamPelajarans = [];
        $kelasList = [];

        if ($activeSemester) {
            $jamPelajarans = JamPelajaran::where('semester_id', $activeSemester->id)
                                        ->orderBy('jam_ke')
                                        ->get();

            // Get all kelas from active tahun pelajaran
            $kelasList = Kelas::where('tahun_pelajaran_id', $activeSemester->tahun_pelajaran_id)
                             ->orderBy('tingkat')
                             ->orderBy('nama_kelas')
                             ->get();
        }

        return view('kepala-sekolah.agenda.agenda-kepsek', compact('agendas', 'jamPelajarans', 'kelasList'));
    }

    /**
     * Store a newly created agenda in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'kelas' => 'nullable|string|max:50',
            'jam_mulai_id' => 'required|exists:jam_pelajarans,id',
            'jam_selesai_id' => 'required|exists:jam_pelajarans,id',
            'materi' => 'required|string',
            'status_jurnal' => 'required|in:selesai,belum_selesai',
            'keterangan' => 'nullable|string',
        ]);

        $validated['user_id'] = Auth::id();

        AgendaGuru::create($validated);

        return redirect()->route('kepala-sekolah.agenda')
                        ->with('success', 'Agenda berhasil ditambahkan!');
    }

    /**
     * Update the specified agenda in storage.
     */
    public function update(Request $request, $id)
    {
        $agenda = AgendaGuru::where('user_id', Auth::id())->findOrFail($id);

        $validated = $request->validate([
            'tanggal' => 'required|date',
            'kelas' => 'nullable|string|max:50',
            'jam_mulai_id' => 'required|exists:jam_pelajarans,id',
            'jam_selesai_id' => 'required|exists:jam_pelajarans,id',
            'materi' => 'required|string',
            'status_jurnal' => 'required|in:selesai,belum_selesai',
            'keterangan' => 'nullable|string',
        ]);

        $agenda->update($validated);

        return redirect()->route('kepala-sekolah.agenda')
                        ->with('success', 'Agenda berhasil diperbarui!');
    }

    /**
     * Remove the specified agenda from storage.
     */
    public function destroy($id)
    {
        $agenda = AgendaGuru::where('user_id', Auth::id())->findOrFail($id);
        $agenda->delete();

        return redirect()->route('kepala-sekolah.agenda')
                        ->with('success', 'Agenda berhasil dihapus!');
    }
}
