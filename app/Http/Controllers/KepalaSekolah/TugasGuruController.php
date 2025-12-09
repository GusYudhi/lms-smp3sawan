<?php

namespace App\Http\Controllers\KepalaSekolah;

use App\Http\Controllers\Controller;
use App\Models\TugasGuru;
use App\Models\TugasGuruFile;
use App\Models\TugasGuruSubmission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TugasGuruController extends Controller
{
    /**
     * Display a listing of tugas.
     */
    public function index(Request $request)
    {
        $query = TugasGuru::with(['creator', 'files'])
            ->withCount(['submissions' => function($q) {
                $q->where('status_pengumpulan', 'dikumpulkan');
            }]);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('judul', 'like', '%' . $request->search . '%')
                  ->orWhere('deskripsi', 'like', '%' . $request->search . '%');
            });
        }

        $tugasList = $query->orderBy('created_at', 'desc')->paginate(10);

        // Get total guru count for statistics
        $totalGuru = User::where('role', 'guru')->count();

        return view('kepala-sekolah.tugas-guru.index', compact('tugasList', 'totalGuru'));
    }

    /**
     * Show the form for creating a new tugas.
     */
    public function create()
    {
        return view('kepala-sekolah.tugas-guru.create');
    }

    /**
     * Store a newly created tugas in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'deadline' => 'required|date|after:now',
            'files.*' => 'nullable|file|max:10240', // max 10MB per file
        ], [
            'judul.required' => 'Judul tugas harus diisi',
            'deadline.required' => 'Deadline harus diisi',
            'deadline.after' => 'Deadline harus lebih dari waktu sekarang',
            'files.*.max' => 'Ukuran file maksimal 10MB',
        ]);

        try {
            // Create tugas
            $tugas = TugasGuru::create([
                'judul' => $validated['judul'],
                'deskripsi' => $validated['deskripsi'],
                'deadline' => $validated['deadline'],
                'created_by' => auth()->id(),
                'status' => 'aktif',
            ]);

            // Handle file uploads
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $originalName = $file->getClientOriginalName();
                    $fileName = time() . '_' . Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
                    $filePath = $file->storeAs('tugas-guru', $fileName, 'public');

                    TugasGuruFile::create([
                        'tugas_guru_id' => $tugas->id,
                        'nama_file' => $originalName,
                        'file_path' => $filePath,
                        'file_type' => $file->getClientMimeType(),
                        'file_size' => $file->getSize(),
                    ]);
                }
            }

            return redirect()->route('kepala-sekolah.tugas-guru.index')
                ->with('success', 'Tugas berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan tugas: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified tugas with submissions.
     */
    public function show($id)
    {
        $tugas = TugasGuru::with(['creator', 'files', 'submissions.guru.guruProfile', 'submissions.files'])
            ->findOrFail($id);

        // Get all guru for statistics
        $totalGuru = User::where('role', 'guru')->count();
        $submittedCount = $tugas->submissions()->where('status_pengumpulan', 'dikumpulkan')->count();
        $notSubmittedCount = $totalGuru - $submittedCount;

        return view('kepala-sekolah.tugas-guru.show', compact('tugas', 'totalGuru', 'submittedCount', 'notSubmittedCount'));
    }

    /**
     * Show the form for editing the specified tugas.
     */
    public function edit($id)
    {
        $tugas = TugasGuru::with('files')->findOrFail($id);

        return view('kepala-sekolah.tugas-guru.edit', compact('tugas'));
    }

    /**
     * Update the specified tugas in storage.
     */
    public function update(Request $request, $id)
    {
        $tugas = TugasGuru::findOrFail($id);

        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'deadline' => 'required|date',
            'status' => 'required|in:aktif,selesai,dibatalkan',
            'files.*' => 'nullable|file|max:10240',
        ], [
            'judul.required' => 'Judul tugas harus diisi',
            'deadline.required' => 'Deadline harus diisi',
            'files.*.max' => 'Ukuran file maksimal 10MB',
        ]);

        try {
            $tugas->update([
                'judul' => $validated['judul'],
                'deskripsi' => $validated['deskripsi'],
                'deadline' => $validated['deadline'],
                'status' => $validated['status'],
            ]);

            // Handle new file uploads
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $originalName = $file->getClientOriginalName();
                    $fileName = time() . '_' . Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
                    $filePath = $file->storeAs('tugas-guru', $fileName, 'public');

                    TugasGuruFile::create([
                        'tugas_guru_id' => $tugas->id,
                        'nama_file' => $originalName,
                        'file_path' => $filePath,
                        'file_type' => $file->getClientMimeType(),
                        'file_size' => $file->getSize(),
                    ]);
                }
            }

            return redirect()->route('kepala-sekolah.tugas-guru.show', $tugas->id)
                ->with('success', 'Tugas berhasil diupdate');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal mengupdate tugas: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified tugas from storage.
     */
    public function destroy($id)
    {
        try {
            $tugas = TugasGuru::findOrFail($id);

            // Delete all files
            foreach ($tugas->files as $file) {
                Storage::disk('public')->delete($file->file_path);
            }

            $tugas->delete();

            return redirect()->route('kepala-sekolah.tugas-guru.index')
                ->with('success', 'Tugas berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus tugas: ' . $e->getMessage());
        }
    }

    /**
     * Delete a specific file from tugas.
     */
    public function deleteFile($id)
    {
        try {
            $file = TugasGuruFile::findOrFail($id);
            $tugasId = $file->tugas_guru_id;

            // Delete file from storage
            Storage::disk('public')->delete($file->file_path);

            // Delete record
            $file->delete();

            return redirect()->route('kepala-sekolah.tugas-guru.edit', $tugasId)
                ->with('success', 'File berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus file: ' . $e->getMessage());
        }
    }

    /**
     * Show submission detail
     */
    public function showSubmission($id)
    {
        $submission = TugasGuruSubmission::with(['tugasGuru', 'guru.guruProfile', 'files'])
            ->findOrFail($id);

        return view('kepala-sekolah.tugas-guru.submission-detail', compact('submission'));
    }

    /**
     * Update feedback and nilai for submission
     */
    public function updateFeedback(Request $request, $id)
    {
        $validated = $request->validate([
            'feedback' => 'nullable|string',
            'nilai' => 'nullable|integer|min:0|max:100',
        ]);

        try {
            $submission = TugasGuruSubmission::findOrFail($id);
            $submission->update($validated);

            return redirect()->back()
                ->with('success', 'Feedback berhasil disimpan');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menyimpan feedback: ' . $e->getMessage());
        }
    }
}
