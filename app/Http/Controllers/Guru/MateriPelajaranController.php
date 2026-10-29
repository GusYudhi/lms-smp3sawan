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
        $mapels = MataPelajaran::where('is_active', true)->orderBy('nama_mapel')->get();
        $kelas = Kelas::orderBy('nama_kelas')->get();
        return view('guru.materi.create', compact('mapels', 'kelas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'mata_pelajaran_id' => 'required|exists:mata_pelajarans,id',
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tipe' => 'required|in:file,link',
            'file_path' => 'required_if:tipe,file|nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,webp,mp4,zip,rar|max:10240',
            'link' => 'required_if:tipe,link|nullable|url',
            'target_kelas' => 'required|string',
        ], [
            'file_path.mimes' => 'Format file materi tidak diizinkan. Gunakan dokumen, gambar, video mp4, atau arsip zip/rar.',
            'file_path.max' => 'Ukuran file materi maksimal 10MB.',
            'link.url' => 'Format tautan harus berupa URL yang valid.',
        ]);

        $data = [
            'mata_pelajaran_id' => $validated['mata_pelajaran_id'],
            'judul' => $validated['judul'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'tipe' => $validated['tipe'],
            'guru_id' => Auth::id(),
        ];

        // Handle File/Link
        if ($validated['tipe'] == 'file' && $request->hasFile('file_path')) {
            $data['file_path'] = $request->file('file_path')->store('materi-pelajaran', 'public');
            $data['link'] = null;
        } else if ($validated['tipe'] == 'link') {
            $data['file_path'] = null;
            $data['link'] = $validated['link'] ?? null;
        }

        // Handle Target Kelas
        $targetKelas = $validated['target_kelas'];
        if ($targetKelas == 'all_taught' || $targetKelas == 'all_global') {
            $data['kelas_id'] = null;
            $data['tingkat'] = null;
        } elseif (str_starts_with($targetKelas, 'grade_')) {
            $data['kelas_id'] = null;
            $data['tingkat'] = substr($targetKelas, 6);
        } elseif (str_starts_with($targetKelas, 'class_')) {
            $data['kelas_id'] = substr($targetKelas, 6);
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
