<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\MateriPelajaran;
use App\Models\MataPelajaran;
use App\Models\Kelas;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MateriPelajaranController extends Controller
{
    public function index()
    {
        $materis = MateriPelajaran::where('guru_id', Auth::id())
            ->with(['mataPelajaran', 'kelas'])
            ->latest()
            ->get();
        return view('guru.materi.index', compact('materis'));
    }

    public function create()
    {
        $activeSemester = Semester::where('is_active', true)->first();
        $mapels = collect();

        if ($activeSemester) {
            $mapels = MataPelajaran::where('semester_id', $activeSemester->id)->get();
        }

        if ($mapels->isEmpty()) {
            $mapels = MataPelajaran::all();
        }

        $kelas = Kelas::orderBy('nama_kelas')->get();
        return view('guru.materi.create', compact('mapels', 'kelas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'mata_pelajaran_id' => 'required|exists:mata_pelajarans,id',
            'judul' => 'required',
            'tipe' => 'required|in:file,link',
            'file_path' => 'required_if:tipe,file|nullable|file|max:10240',
            'link' => 'required_if:tipe,link|nullable|url',
            'target_kelas' => 'required',
        ]);

        $data = $request->except(['file_path', 'target_kelas']);
        $data['guru_id'] = Auth::id();

        // Handle File/Link
        if ($request->tipe == 'file' && $request->hasFile('file_path')) {
            $data['file_path'] = $request->file('file_path')->store('materi-pelajaran', 'public');
            $data['link'] = null;
        } else if ($request->tipe == 'link') {
            $data['file_path'] = null;
        }

        // Handle Target Kelas
        if ($request->target_kelas == 'all_taught') {
            $data['kelas_id'] = null;
            $data['tingkat'] = null;
        } elseif ($request->target_kelas == 'all_global') {
            $data['kelas_id'] = null;
            $data['tingkat'] = null;
        } elseif (str_starts_with($request->target_kelas, 'grade_')) {
            $data['kelas_id'] = null;
            $data['tingkat'] = substr($request->target_kelas, 6);
        } elseif (str_starts_with($request->target_kelas, 'class_')) {
            $data['kelas_id'] = substr($request->target_kelas, 6);
            $data['tingkat'] = null;
        }

        MateriPelajaran::create($data);

        return redirect()->route('guru.materi.index')->with('success', 'Materi berhasil diupload');
    }

    public function destroy($id)
    {
        $materi = MateriPelajaran::where('id', $id)->where('guru_id', Auth::id())->firstOrFail();

        if ($materi->file_path) {
            Storage::disk('public')->delete($materi->file_path);
        }

        $materi->delete();

        return redirect()->route('guru.materi.index')->with('success', 'Materi berhasil dihapus');
    }
}
