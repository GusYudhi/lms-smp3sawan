<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserManagementService;
use App\Imports\StudentsImport;
use App\Exports\StudentsTemplateExport;
use App\Exports\StudentsExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class SiswaController extends Controller
{
    protected $userService;

    public function __construct(UserManagementService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $filters = $request->only(['jenis_kelamin', 'kelas', 'status']);
        $perPage = $request->input('per_page', 15);

        $students = $this->userService->getStudentsWithProfiles($search, $filters, $perPage);

        // Get available classes for filter from kelas table
        $classes = \App\Models\Kelas::orderBy('tingkat')
            ->orderBy('nama_kelas')
            ->get()
            ->map(function($item) {
                return $item->full_name;
            })
            ->toArray();

        // Calculate statistics
        $totalSiswa = User::where('role', 'siswa')->count();
        $siswaAktif = User::where('role', 'siswa')
            ->whereHas('studentProfile', function($q) {
                $q->where('is_active', true);
            })->count();
        $siswaLakiLaki = User::where('role', 'siswa')
            ->whereHas('studentProfile', function($q) {
                $q->where('jenis_kelamin', 'L');
            })->count();
        $siswaPerempuan = User::where('role', 'siswa')
            ->whereHas('studentProfile', function($q) {
                $q->where('jenis_kelamin', 'P');
            })->count();

        return view('admin.siswa.index', compact('students', 'classes', 'totalSiswa', 'siswaAktif', 'siswaLakiLaki', 'siswaPerempuan'));
    }

    /**
     * AJAX endpoint for searching students - returns only table HTML
     */
    public function search(Request $request)
    {
        $search = $request->input('search');
        $filters = $request->only(['jenis_kelamin', 'kelas', 'status']);
        $perPage = $request->input('per_page', 15);

        $students = $this->userService->getStudentsWithProfiles($search, $filters, $perPage);

        // Get available classes for filter from kelas table
        $classes = \App\Models\Kelas::orderBy('tingkat')
            ->orderBy('nama_kelas')
            ->get()
            ->map(function($item) {
                return $item->full_name;
            })
            ->toArray();

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.siswa.partials.table', compact('students'))->render(),
                'pagination' => view('admin.siswa.partials.pagination', compact('students'))->render(),
                'info' => view('admin.siswa.partials.table-info', compact('students'))->render(),
                'total' => $students->total(),
                'current_page' => $students->currentPage(),
                'filters' => $request->only(['search', 'jenis_kelamin', 'kelas', 'status', 'per_page'])
            ]);
        }

        // Calculate statistics for non-ajax requests
        $totalSiswa = User::where('role', 'siswa')->count();
        $siswaAktif = User::where('role', 'siswa')
            ->whereHas('studentProfile', function($q) {
                $q->where('is_active', true);
            })->count();
        $siswaLakiLaki = User::where('role', 'siswa')->where('jenis_kelamin', 'Laki-laki')->count();
        $siswaPerempuan = User::where('role', 'siswa')->where('jenis_kelamin', 'Perempuan')->count();

        return view('admin.siswa.index', compact('students', 'classes', 'totalSiswa', 'siswaAktif', 'siswaLakiLaki', 'siswaPerempuan'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.siswa.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'jenis_kelamin' => 'nullable|in:L,P',
            'profile_photo' => 'nullable|image|max:2048',
            // Student profile fields
            'nis' => 'nullable|string|max:50|unique:student_profiles,nis',
            'nisn' => 'nullable|string|max:50',
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'tingkat' => 'required|string|in:7,8,9',
            'kelas' => 'required|string|max:10',
            'tahun_angkatan' => 'nullable|digits:4|integer|min:2000|max:' . (date('Y') + 1),
            'nomor_telepon_orangtua' => 'nullable|string|max:30',
            'alamat' => 'nullable|string',
            'nama_orangtua_wali' => 'nullable|string|max:255',
            'pekerjaan_orangtua' => 'nullable|string|max:255',
        ]);

        $userData = $request->only(['name', 'email', 'password']);

        $profileData = $request->only([
            'nis', 'nisn', 'tempat_lahir', 'tanggal_lahir', 'tingkat', 'kelas',
            'tahun_angkatan', 'nomor_telepon_orangtua', 'alamat', 'nama_orangtua_wali', 'pekerjaan_orangtua', 'jenis_kelamin'
        ]);

        // Handle profile photo upload with compression
        if ($request->hasFile('profile_photo')) {
            $photo = $request->file('profile_photo');

            // Compress and store as WebP with quality 60%
            $photoPath = \App\Helpers\ImageCompressor::compressAndStore(
                $photo,
                'profile_photos',
                time() . '_siswa_' . uniqid(),
                60,  // Quality 60%
                1200 // Max width 1200px
            );

            $profileData['foto_profil'] = basename($photoPath);
        }

        $student = $this->userService->createStudent($userData, $profileData);

        return redirect()->route('admin.siswa.index')->with('success', 'Siswa berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {
        $student = User::with(['studentProfile.kelas'])->where('role', 'siswa')->findOrFail($id);

        // Check if this is an AJAX request for canvas data
        if ($request->wantsJson() || $request->ajax()) {
            $profile = $student->studentProfile;
            $kelas = $profile && $profile->kelas ? $profile->kelas->full_name : '-';

            return response()->json([
                'id' => $student->id,
                'nama_lengkap' => $student->name,
                'nama' => $student->name,
                'nisn' => $profile->nisn ?? '-',
                'nis' => $profile->nis ?? '-',
                'kelas' => $kelas,
                'tempat_lahir' => $profile->tempat_lahir ?? '',
                'tanggal_lahir' => $profile->tanggal_lahir ? \Carbon\Carbon::parse($profile->tanggal_lahir)->format('d-m-Y') : '',
                'foto_url' => $profile->foto ? Storage::url($profile->foto) : null,
            ]);
        }

        return view('admin.siswa.show', compact('student'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $student = User::with('studentProfile')->where('role', 'siswa')->findOrFail($id);
        return view('admin.siswa.edit', compact('student'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $student = User::with('studentProfile')->where('role', 'siswa')->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $student->id,
            'password' => 'nullable|string|min:8|confirmed',
            'jenis_kelamin' => 'nullable|in:L,P',
            'profile_photo' => 'nullable|image|max:2048',
            // Student profile fields
            'nis' => 'nullable|string|max:50|unique:student_profiles,nis,' . ($student->studentProfile->id ?? 'NULL'),
            'nisn' => 'nullable|string|max:50',
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'tingkat' => 'required|string|in:7,8,9',
            'kelas' => 'required|string|max:10',
            'tahun_angkatan' => 'nullable|digits:4|integer|min:2000|max:' . (date('Y') + 1),
            'nomor_telepon_orangtua' => 'nullable|string|max:30',
            'alamat' => 'nullable|string',
            'nama_orangtua_wali' => 'nullable|string|max:255',
            'pekerjaan_orangtua' => 'nullable|string|max:255',
        ]);

        $userData = $request->only(['name', 'email', 'password']);

        $profileData = $request->only([
            'nis', 'nisn', 'tempat_lahir', 'tanggal_lahir', 'tingkat', 'kelas',
            'tahun_angkatan', 'nomor_telepon_orangtua', 'alamat', 'nama_orangtua_wali', 'pekerjaan_orangtua', 'jenis_kelamin'
        ]);

        // Handle profile photo upload with compression
        if ($request->hasFile('profile_photo')) {
            $photo = $request->file('profile_photo');

            // Delete old photo if exists
            if ($student->studentProfile && $student->studentProfile->foto_profil) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete('profile_photos/' . $student->studentProfile->foto_profil);
            }

            // Compress and store as WebP with quality 60%
            $photoPath = \App\Helpers\ImageCompressor::compressAndStore(
                $photo,
                'profile_photos',
                time() . '_siswa_' . $student->id . '_' . uniqid(),
                60,  // Quality 60%
                1200 // Max width 1200px
            );

            $profileData['foto_profil'] = basename($photoPath);
        }

        $student = $this->userService->updateStudent($student, $userData, $profileData);

        return redirect()->route('admin.siswa.show', $student->id)->with('success', 'Siswa berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $student = User::with('studentProfile')->where('role', 'siswa')->findOrFail($id);

        $this->userService->deleteUser($student);

        return redirect()->route('admin.siswa.index')->with('success', 'Siswa berhasil dihapus');
    }

    /**
     * Export students (XLSX)
     */
    public function export(Request $request)
    {
        $search = $request->input('search');
        $filters = $request->only(['jenis_kelamin', 'kelas']);

        $filename = 'data_siswa_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new StudentsExport($search, $filters), $filename);
    }

    /**
     * Download template for student import
     */
    public function downloadTemplate()
    {
        return Excel::download(new StudentsTemplateExport, 'template_import_siswa.xlsx');
    }

    /**
     * Import students from Excel file
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,xlsm,xlsb,xlam,xltx,xltm,csv|max:5120', // 5MB max
        ]);

        try {
            $import = new StudentsImport();
            Excel::import($import, $request->file('file'));

            $message = "Import selesai! ";
            $message .= "Berhasil: {$import->successCount} data, ";
            $message .= "Gagal: {$import->failureCount} data";

            if (!empty($import->errors)) {
                $errorMessage = implode("\n", array_slice($import->errors, 0, 10)); // Show first 10 errors
                if (count($import->errors) > 10) {
                    $errorMessage .= "\n... dan " . (count($import->errors) - 10) . " error lainnya";
                }

                return redirect()->back()
                    ->with('warning', $message)
                    ->with('import_errors', $errorMessage);
            }

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error saat import: ' . $e->getMessage());
        }
    }

    /**
     * Download bulk student ID cards as ZIP
     */
    public function downloadBulkIdCardZip(Request $request)
    {
        $request->validate([
            'cards' => 'required|array',
            'cards.*.id' => 'required|integer',
            'cards.*.name' => 'required|string',
            'cards.*.nisn' => 'nullable|string',
            'cards.*.nis' => 'required|string',
            'cards.*.kelas' => 'required|string',
            'cards.*.base64' => 'required|string',
        ]);

        try {
            $cards = $request->input('cards');

            // Group cards by class
            $cardsByClass = [];
            foreach ($cards as $card) {
                $kelas = $card['kelas'] ?? 'Tanpa_Kelas';
                if (!isset($cardsByClass[$kelas])) {
                    $cardsByClass[$kelas] = [];
                }
                $cardsByClass[$kelas][] = $card;
            }

            // Sort each class by NIS
            foreach ($cardsByClass as $kelas => &$classCards) {
                usort($classCards, function($a, $b) {
                    return strcmp($a['nis'], $b['nis']);
                });
            }

            // Create ZIP file
            $zipFileName = 'Kartu_Identitas_Siswa_' . date('Ymd_His') . '.zip';
            $zipFilePath = storage_path('app/temp/' . $zipFileName);

            $zip = new \ZipArchive();
            if ($zip->open($zipFilePath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
                throw new \Exception('Gagal membuat file ZIP');
            }

            // Process each class
            foreach ($cardsByClass as $kelas => $classCards) {
                // Create folder for this class
                $classFolderName = 'Kelas_' . str_replace(' ', '_', $kelas);
                $zip->addEmptyDir($classFolderName);

                // Process each card in this class
                foreach ($classCards as $card) {
                    try {
                        // Decode base64 image
                        $base64Data = $card['base64'];

                        // Remove data:image/png;base64, prefix if exists
                        if (strpos($base64Data, 'data:image/png;base64,') === 0) {
                            $base64Data = substr($base64Data, strlen('data:image/png;base64,'));
                        }

                        $imageData = base64_decode($base64Data);

                        if ($imageData === false) {
                            Log::warning("Failed to decode base64 for student ID: " . $card['id']);
                            continue;
                        }

                        // Create filename: NIS_Nama_Siswa.png
                        $nis = $card['nis'];
                        $name = preg_replace('/[^A-Za-z0-9_\-]/', '_', $card['name']);
                        $name = substr($name, 0, 40); // Limit name length
                        $fileName = "{$nis}_{$name}.png";

                        // Add directly to ZIP with class folder path (no temp files)
                        $zip->addFromString($classFolderName . '/' . $fileName, $imageData);

                    } catch (\Exception $e) {
                        Log::error("Error processing card for student ID {$card['id']}: " . $e->getMessage());
                        continue;
                    }
                }
            }

            $zip->close();

            // Check if ZIP file was created successfully
            if (!file_exists($zipFilePath)) {
                throw new \Exception('File ZIP tidak dapat dibuat');
            }

            Log::info("ZIP file created successfully: {$zipFilePath}, size: " . filesize($zipFilePath) . " bytes");

            // Return ZIP file as download
            return response()->download($zipFilePath, $zipFileName, [
                'Content-Type' => 'application/zip',
            ])->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            Log::error('Bulk ID card download error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat file ZIP: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload batch of ID cards (for batch processing)
     */
    public function uploadBatchIdCards(Request $request)
    {
        $request->validate([
            'session_id' => 'required|string',
            'batch_number' => 'required|integer',
            'total_batches' => 'required|integer',
            'cards' => 'required|array',
        ]);

        try {
            $sessionId = $request->input('session_id');
            $batchNumber = $request->input('batch_number');
            $cards = $request->input('cards');

            // Create session directory
            $sessionDir = storage_path('app/temp/batch_' . $sessionId);
            if (!file_exists($sessionDir)) {
                mkdir($sessionDir, 0755, true);
            }

            // Save batch data to file
            $batchFile = $sessionDir . '/batch_' . $batchNumber . '.json';
            file_put_contents($batchFile, json_encode($cards));

            Log::info("Batch {$batchNumber} saved for session {$sessionId}");

            return response()->json([
                'success' => true,
                'batch_number' => $batchNumber,
            ]);

        } catch (\Exception $e) {
            Log::error('Batch upload error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal upload batch: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Finalize batch processing and create ZIP
     */
    public function finalizeBatchDownload(Request $request)
    {
        $request->validate([
            'session_id' => 'required|string',
            'total_batches' => 'required|integer',
        ]);

        try {
            $sessionId = $request->input('session_id');
            $totalBatches = $request->input('total_batches');
            $sessionDir = storage_path('app/temp/batch_' . $sessionId);

            // Collect all batches
            $allCards = [];
            for ($i = 1; $i <= $totalBatches; $i++) {
                $batchFile = $sessionDir . '/batch_' . $i . '.json';
                if (!file_exists($batchFile)) {
                    throw new \Exception("Batch {$i} tidak ditemukan");
                }
                $batchCards = json_decode(file_get_contents($batchFile), true);
                $allCards = array_merge($allCards, $batchCards);
            }

            Log::info("All batches collected. Total cards: " . count($allCards));

            // Group cards by class
            $cardsByClass = [];
            foreach ($allCards as $card) {
                $kelas = $card['kelas'] ?? 'Tanpa_Kelas';
                if (!isset($cardsByClass[$kelas])) {
                    $cardsByClass[$kelas] = [];
                }
                $cardsByClass[$kelas][] = $card;
            }

            // Sort each class by NIS
            foreach ($cardsByClass as $kelas => &$classCards) {
                usort($classCards, function($a, $b) {
                    return strcmp($a['nis'], $b['nis']);
                });
            }

            // Create ZIP file
            $zipFileName = 'Kartu_Identitas_Siswa_' . date('Ymd_His') . '.zip';
            $zipFilePath = storage_path('app/temp/' . $zipFileName);

            $zip = new \ZipArchive();
            if ($zip->open($zipFilePath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
                throw new \Exception('Gagal membuat file ZIP');
            }

            // Process each class
            foreach ($cardsByClass as $kelas => $classCards) {
                // Create folder for this class
                $classFolderName = 'Kelas_' . str_replace(' ', '_', $kelas);
                $zip->addEmptyDir($classFolderName);

                Log::info("Processing class: {$kelas} with " . count($classCards) . " students");

                // Process each card in this class
                foreach ($classCards as $card) {
                    try {
                        // Decode base64 image
                        $base64Data = $card['base64'];

                        // Remove data:image/png;base64, prefix if exists
                        if (strpos($base64Data, 'data:image/png;base64,') === 0) {
                            $base64Data = substr($base64Data, strlen('data:image/png;base64,'));
                        }

                        $imageData = base64_decode($base64Data);

                        if ($imageData === false) {
                            Log::warning("Failed to decode base64 for student ID: " . $card['id']);
                            continue;
                        }

                        // Create filename: NIS_Nama_Siswa.png
                        $nis = $card['nis'];
                        $name = preg_replace('/[^A-Za-z0-9_\-]/', '_', $card['name']);
                        $name = substr($name, 0, 40); // Limit name length
                        $fileName = "{$nis}_{$name}.png";

                        // Add directly to ZIP with class folder path
                        $zip->addFromString($classFolderName . '/' . $fileName, $imageData);

                    } catch (\Exception $e) {
                        Log::error("Error processing card for student ID {$card['id']}: " . $e->getMessage());
                        continue;
                    }
                }
            }

            $zip->close();

            // Clean up batch files
            $files = glob($sessionDir . '/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    @unlink($file);
                }
            }
            @rmdir($sessionDir);

            $zipSize = filesize($zipFilePath);
            Log::info("ZIP file created: {$zipFilePath}, size: {$zipSize} bytes");

            // Return ZIP file as download
            return response()->download($zipFilePath, $zipFileName, [
                'Content-Type' => 'application/zip',
            ])->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            Log::error('Finalize batch error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat file ZIP: ' . $e->getMessage()
            ], 500);
        }
    }
}
