<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\TugasGuru;
use App\Models\TugasGuruSubmission;
use App\Models\TugasGuruSubmissionFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TugasGuruController extends Controller
{
    /**
     * Display a listing of tugas for guru.
     */
    public function index(Request $request)
    {
        $query = TugasGuru::with(['files', 'submissions' => function($q) {
            $q->where('guru_id', auth()->id());
        }])
        ->where('status', 'aktif');

        // Filter by status pengumpulan
        if ($request->filled('status')) {
            $query->whereHas('submissions', function($q) use ($request) {
                $q->where('guru_id', auth()->id())
                  ->where('status_pengumpulan', $request->status);
            });
        }

        // Search
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('judul', 'like', '%' . $request->search . '%')
                  ->orWhere('deskripsi', 'like', '%' . $request->search . '%');
            });
        }

        $tugasList = $query->orderBy('deadline', 'asc')->paginate(10);

        // Add submission info for each tugas
        foreach ($tugasList as $tugas) {
            $submission = $tugas->submissions->first();
            $tugas->my_submission = $submission;
            $tugas->submission_status = $submission ? $submission->status_pengumpulan : 'belum';
        }

        return view('guru.tugas-guru.index', compact('tugasList'));
    }

    /**
     * Display the specified tugas detail.
     */
    public function show($id)
    {
        $tugas = TugasGuru::with(['creator', 'files'])->findOrFail($id);

        // Get current user's submission if exists
        $submission = TugasGuruSubmission::with('files')
            ->where('tugas_guru_id', $id)
            ->where('guru_id', auth()->id())
            ->first();

        return view('guru.tugas-guru.show', compact('tugas', 'submission'));
    }

    /**
     * Store or update submission.
     */
    public function submit(Request $request, $id)
    {
        $tugas = TugasGuru::findOrFail($id);

        $validated = $request->validate([
            'konten_tugas' => 'nullable|string',
            'link_eksternal' => 'nullable|url',
            'files.*' => 'nullable|file|max:10240', // max 10MB
        ], [
            'link_eksternal.url' => 'Link eksternal harus berupa URL yang valid',
            'files.*.max' => 'Ukuran file maksimal 10MB',
        ]);

        // Validate at least one input method
        if (empty($validated['konten_tugas']) && empty($validated['link_eksternal']) && !$request->hasFile('files')) {
            return redirect()->back()
                ->with('error', 'Anda harus mengisi konten tugas, link eksternal, atau mengupload file');
        }

        try {
            // Check if submission already exists
            $submission = TugasGuruSubmission::firstOrNew([
                'tugas_guru_id' => $id,
                'guru_id' => auth()->id(),
            ]);

            // Determine status
            $status = now() > $tugas->deadline ? 'terlambat' : 'dikumpulkan';

            $submission->fill([
                'konten_tugas' => $validated['konten_tugas'],
                'link_eksternal' => $validated['link_eksternal'],
                'status_pengumpulan' => $status,
                'tanggal_submit' => now(),
            ]);

            $submission->save();

            // Handle file uploads
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $originalName = $file->getClientOriginalName();
                    $fileName = time() . '_' . Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
                    $filePath = $file->storeAs('tugas-guru-submissions', $fileName, 'public');

                    TugasGuruSubmissionFile::create([
                        'submission_id' => $submission->id,
                        'nama_file' => $originalName,
                        'file_path' => $filePath,
                        'file_type' => $file->getClientMimeType(),
                        'file_size' => $file->getSize(),
                    ]);
                }
            }

            return redirect()->route('guru.tugas-guru.show', $id)
                ->with('success', 'Tugas berhasil dikumpulkan');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal mengumpulkan tugas: ' . $e->getMessage());
        }
    }

    /**
     * Show submission detail (for guru to view their own submission)
     */
    public function showSubmission($id)
    {
        $submission = TugasGuruSubmission::with(['tugasGuru.files', 'files'])
            ->where('guru_id', auth()->id())
            ->findOrFail($id);

        return view('guru.tugas-guru.submission-detail', compact('submission'));
    }

    /**
     * Delete a file from submission
     */
    public function deleteFile($id)
    {
        try {
            $file = TugasGuruSubmissionFile::findOrFail($id);

            // Check if file belongs to current user's submission
            $submission = $file->submission;
            if ($submission->guru_id !== auth()->id()) {
                return redirect()->back()
                    ->with('error', 'Anda tidak memiliki akses untuk menghapus file ini');
            }

            $tugasId = $submission->tugas_guru_id;

            // Delete file from storage
            Storage::disk('public')->delete($file->file_path);

            // Delete record
            $file->delete();

            return redirect()->route('guru.tugas-guru.show', $tugasId)
                ->with('success', 'File berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus file: ' . $e->getMessage());
        }
    }

    /**
     * Delete submission (if not yet submitted or in draft)
     */
    public function deleteSubmission($id)
    {
        try {
            $submission = TugasGuruSubmission::where('guru_id', auth()->id())
                ->findOrFail($id);

            $tugasId = $submission->tugas_guru_id;

            // Delete all files
            foreach ($submission->files as $file) {
                Storage::disk('public')->delete($file->file_path);
            }

            $submission->delete();

            return redirect()->route('guru.tugas-guru.show', $tugasId)
                ->with('success', 'Submission berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus submission: ' . $e->getMessage());
        }
    }
}
