<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Services\UserManagementService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\TeachersImport;
use App\Exports\TeachersTemplateExport;
use App\Exports\TeachersExport;
use App\Models\Semester;
use App\Models\TahunPelajaran;
use App\Helpers\ImageCompressor;

class AdminController extends Controller
{
    protected $userService;

    public function __construct(UserManagementService $userService)
    {
        $this->userService = $userService;
    }

    public function manageGuru(Request $request)
    {
        $search = $request->get('search');
        $mataPelajaranFilter = $request->get('mata_pelajaran');
        $statusKepegawaianFilter = $request->get('status_kepegawaian');
        $genderFilter = $request->get('gender');

        // get user with profile guru and mata pelajaran to avoid N+1
        $query = User::with(['guruProfile.kelas', 'guruProfile.mataPelajaran', 'guruProfile.mataPelajarans'])
            ->leftJoin('guru_profiles', 'users.id', '=', 'guru_profiles.user_id')
            ->select('users.*')
            ->whereIn('users.role', ['guru', 'kepala_sekolah']);

        // Search functionality
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('users.name', 'like', "%{$search}%")
                  ->orWhere('users.email', 'like', "%{$search}%")
                  ->orWhereHas('guruProfile', function($query) use ($search) {
                      $query->where('nip', 'like', "%{$search}%")
                            ->orWhere('kode_guru', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by mata pelajaran (bisa sebagai mapel utama atau mapel tambahan)
        if ($mataPelajaranFilter) {
            $query->whereHas('guruProfile', function($gp) use ($mataPelajaranFilter) {
                $gp->whereHas('mataPelajaran', function($q) use ($mataPelajaranFilter) {
                    $q->where('nama_mapel', 'like', "%{$mataPelajaranFilter}%");
                })->orWhereHas('mataPelajarans', function($q) use ($mataPelajaranFilter) {
                    $q->where('nama_mapel', 'like', "%{$mataPelajaranFilter}%");
                });
            });
        }

        // Filter by status kepegawaian
        if ($statusKepegawaianFilter) {
            $query->whereHas('guruProfile', function($q) use ($statusKepegawaianFilter) {
                $q->where('status_kepegawaian', $statusKepegawaianFilter);
            });
        }

        // Filter by gender
        if ($genderFilter) {
            $query->whereHas('guruProfile', function($q) use ($genderFilter) {
                $q->where('jenis_kelamin', $genderFilter);
            });
        }

        $teachers = $query->orderByRaw("CASE WHEN guru_profiles.kode_guru IS NULL OR guru_profiles.kode_guru = '' THEN 1 ELSE 0 END ASC")
            ->orderByRaw("CASE WHEN guru_profiles.kode_guru REGEXP '^[0-9]+$' THEN 0 ELSE 1 END ASC")
            ->orderByRaw("CAST(guru_profiles.kode_guru AS UNSIGNED) ASC")
            ->orderBy('guru_profiles.kode_guru', 'asc')
            ->orderBy('users.name', 'asc')
            ->paginate(15)
            ->withQueryString();

        // Get list mata pelajaran untuk filter
        $mataPelajaranList = \App\Models\MataPelajaran::where('is_active', true)->orderBy('nama_mapel', 'asc')->get();

        return view('admin.guru.index', compact('teachers', 'mataPelajaranList', 'search', 'mataPelajaranFilter', 'statusKepegawaianFilter'));
    }

    /**
     * AJAX endpoint for searching teachers - returns only table HTML
     */
    public function searchGuru(Request $request)
    {
        $query = User::whereIn('users.role', ['guru', 'kepala_sekolah'])
            ->leftJoin('guru_profiles', 'users.id', '=', 'guru_profiles.user_id')
            ->select('users.*')
            ->with(['guruProfile.kelas', 'guruProfile.mataPelajaran', 'guruProfile.mataPelajarans']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('users.name', 'LIKE', "%{$search}%")
                  ->orWhere('users.email', 'LIKE', "%{$search}%")
                  ->orWhereHas('guruProfile', function($sub) use ($search) {
                      $sub->where('nip', 'LIKE', "%{$search}%")
                          ->orWhere('kode_guru', 'LIKE', "%{$search}%")
                          ->orWhereHas('mataPelajaran', function($mp) use ($search) {
                              $mp->where('nama_mapel', 'LIKE', "%{$search}%");
                          })
                          ->orWhereHas('mataPelajarans', function($mp) use ($search) {
                              $mp->where('nama_mapel', 'LIKE', "%{$search}%");
                          });
                  });
            });
        }

        // Filter by status kepegawaian if needed
        if ($request->filled('status')) {
            $query->whereHas('guruProfile', function($q) use ($request) {
                $q->where('status_kepegawaian', $request->status);
            });
        }

        // Filter by gender if needed
        if ($request->filled('gender')) {
            $query->whereHas('guruProfile', function($q) use ($request) {
                $q->where('jenis_kelamin', $request->gender);
            });
        }

        // Filter by mata pelajaran if needed (utama maupun tambahan)
        if ($request->filled('mata_pelajaran')) {
            $mataPelajaran = $request->mata_pelajaran;
            $query->whereHas('guruProfile', function($gp) use ($mataPelajaran) {
                $gp->whereHas('mataPelajaran', function($q) use ($mataPelajaran) {
                    $q->where('nama_mapel', 'like', "%{$mataPelajaran}%");
                })->orWhereHas('mataPelajarans', function($q) use ($mataPelajaran) {
                    $q->where('nama_mapel', 'like', "%{$mataPelajaran}%");
                });
            });
        }

        $teachers = $query->orderByRaw("CASE WHEN guru_profiles.kode_guru IS NULL OR guru_profiles.kode_guru = '' THEN 1 ELSE 0 END ASC")
            ->orderByRaw("CASE WHEN guru_profiles.kode_guru REGEXP '^[0-9]+$' THEN 0 ELSE 1 END ASC")
            ->orderByRaw("CAST(guru_profiles.kode_guru AS UNSIGNED) ASC")
            ->orderBy('guru_profiles.kode_guru', 'asc')
            ->orderBy('users.name', 'asc')
            ->paginate(15)
            ->withQueryString();

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.guru.partials.table', compact('teachers'))->render(),
                'pagination' => view('admin.guru.partials.pagination', compact('teachers'))->render(),
                'info' => view('admin.guru.partials.table-info', compact('teachers'))->render(),
                'total' => $teachers->total(),
                'current_page' => $teachers->currentPage(),
                'filters' => $request->only(['search', 'status', 'gender', 'mata_pelajaran'])
            ]);
        }

        // Get list mata pelajaran untuk filter
        $mataPelajaranList = \App\Models\MataPelajaran::where('is_active', true)->orderBy('nama_mapel', 'asc')->get();

        return view('admin.guru.index', compact('teachers', 'mataPelajaranList'));
    }

    public function createGuru()
    {
        // Get all active master mata pelajaran
        $mataPelajarans = \App\Models\MataPelajaran::where('is_active', true)->orderBy('nama_mapel', 'asc')->get();

        // Get all kelas
        $kelasList = \App\Models\Kelas::orderBy('tingkat', 'asc')
            ->orderBy('nama_kelas', 'asc')
            ->get();

        return view('admin.guru.create', compact('mataPelajarans', 'kelasList'));
    }

    public function storeGuru(Request $request)
    {
        try {
            // Validation rules
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'nomor_induk' => 'required|string|max:50|unique:guru_profiles,nip',
                'kode_guru' => 'nullable|string|max:10|unique:guru_profiles,kode_guru',
                'email' => 'required|email|unique:users,email',
                'nomor_telepon' => 'nullable|string|max:20',
                'jenis_kelamin' => 'required|in:L,P',
                'tempat_lahir' => 'nullable|string|max:100',
                'tanggal_lahir' => 'nullable|date',
                'status_kepegawaian' => 'nullable|in:PNS,PPPK,HONORER',
                'golongan' => 'nullable|string|max:10',
                'jabatan_di_sekolah' => 'required|string|max:100',
                'mata_pelajaran' => 'nullable',
                'kelas_id' => 'nullable|exists:kelas,id',
                'password' => 'required|string|min:8|confirmed',
                'profile_photo' => 'nullable|file|mimes:jpeg,png,jpg,webp,heic,heif|max:10240',
                'can_manage_berita' => 'nullable|boolean',
                'can_manage_prestasi' => 'nullable|boolean',
            ]);

            // Ekstraksi mata pelajaran (bisa array dari multi-select maupun single id)
            $mapelInput = $request->input('mata_pelajaran');
            $mapelIds = [];
            if (is_array($mapelInput)) {
                $mapelIds = array_values(array_unique(array_filter(array_map('intval', $mapelInput))));
            } elseif (!empty($mapelInput)) {
                $mapelIds = [(int)$mapelInput];
            }
            if ($request->filled('mata_pelajaran_tambahan') && is_array($request->input('mata_pelajaran_tambahan'))) {
                foreach ($request->input('mata_pelajaran_tambahan') as $tId) {
                    if (!empty($tId)) $mapelIds[] = (int)$tId;
                }
                $mapelIds = array_values(array_unique(array_filter($mapelIds)));
            }

            $primaryMapelId = !empty($mapelIds) ? $mapelIds[0] : null;
            $additionalMapelIds = !empty($mapelIds) ? array_slice($mapelIds, 1) : [];

            // Handle profile photo upload untuk guru_profiles
            $fotoProfilPath = null;
            if ($request->hasFile('profile_photo')) {
                $file = $request->file('profile_photo');
                $photoPath = ImageCompressor::compressAndStore(
                    $file,
                    'profile_photos',
                    'guru_' . time() . '_' . uniqid(),
                    60,
                    1200
                );
                $fotoProfilPath = basename($photoPath);
            }

            // Tentukan role berdasarkan jabatan
            $role = 'guru'; // Default role
            if (isset($validatedData['jabatan_di_sekolah']) && $validatedData['jabatan_di_sekolah'] === 'Kepala Sekolah') {
                $role = 'kepala_sekolah';
            }

            $userData = [
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => $validatedData['password'],
                'role' => $role,
                'nomor_telepon' => $validatedData['nomor_telepon'],
                'jenis_kelamin' => $validatedData['jenis_kelamin'],
            ];

            $profileData = [
                'nip' => $validatedData['nomor_induk'],
                'kode_guru' => $validatedData['kode_guru'] ?? null,
                'tempat_lahir' => $validatedData['tempat_lahir'] ?? null,
                'tanggal_lahir' => $validatedData['tanggal_lahir'] ?? null,
                'status_kepegawaian' => $validatedData['status_kepegawaian'] ?? null,
                'golongan' => $validatedData['golongan'] ?? null,
                'jabatan_di_sekolah' => $validatedData['jabatan_di_sekolah'] ?? null,
                'mata_pelajaran_id' => $primaryMapelId,
                'mata_pelajaran_tambahan' => $additionalMapelIds,
                'kelas_id' => $validatedData['kelas_id'] ?? null,
                'foto_profil' => $fotoProfilPath, // Simpan ke guru_profiles.foto_profil
                'can_manage_berita' => $request->has('can_manage_berita'),
                'can_manage_prestasi' => $request->has('can_manage_prestasi'),
            ];

            $this->userService->createTeacher($userData, $profileData);

            Log::info('Teacher created successfully', ['name' => $userData['name']]);

            return redirect()->route('admin.guru.index')->with('success', 'Data guru berhasil ditambahkan!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Validation error when creating teacher', ['errors' => $e->errors()]);
            return back()->withErrors($e->errors())->withInput();

        } catch (\Exception $e) {
            Log::error('Error creating teacher', ['error' => $e->getMessage()]);
            return back()->with('error', 'Terjadi kesalahan saat menambahkan data guru. Silakan coba lagi.')->withInput();
        }
    }

    /**
     * Export teachers (XLSX)
     */
    public function exportGuru()
    {
        try {
            Log::info('Starting teacher export');

            $filename = 'data_guru_' . date('Ymd_His') . '.xlsx';

            Log::info('Export filename generated', ['filename' => $filename]);

            $export = new TeachersExport();

            Log::info('TeachersExport instance created');

            return Excel::download($export, $filename);

        } catch (\Exception $e) {
            Log::error('Error exporting teachers', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);
            return back()->with('error', 'Terjadi kesalahan saat mengekspor data guru: ' . $e->getMessage());
        }
    }

    /**
     * Download template for teacher import
     */
    public function downloadTemplateGuru()
    {
        return Excel::download(new TeachersTemplateExport, 'template_import_guru.xlsx');
    }

    /**
     * Import teachers from Excel file
     */
    public function importGuru(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,xlsm,xlsb,xlam,xltx,xltm,csv|max:5120', // 5MB max
        ]);

        try {
            $import = new TeachersImport();
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
     * Show teacher details
     */
    public function showGuru($id)
    {
        try {
            $teacher = User::whereIn('role', ['guru', 'kepala_sekolah'])
                ->with(['guruProfile.kelas', 'guruProfile.mataPelajaran', 'guruProfile.mataPelajarans'])
                ->findOrFail($id);

            return view('admin.guru.show', compact('teacher'));

        } catch (\Exception $e) {
            Log::error('Show guru error: ' . $e->getMessage());
            return redirect()->route('admin.guru.index')->with('error', 'Guru tidak ditemukan.');
        }
    }

    /**
     * Show edit teacher form
     */
    public function editGuru($id)
    {
        try {
            $teacher = User::whereIn('role', ['guru', 'kepala_sekolah'])
                ->with(['guruProfile.kelas', 'guruProfile.mataPelajaran', 'guruProfile.mataPelajarans'])
                ->findOrFail($id);

            // Get active semester
            // Get all active master mata pelajaran
            $mataPelajarans = \App\Models\MataPelajaran::where('is_active', true)->orderBy('nama_mapel', 'asc')->get();

            // Get all kelas
            $kelasList = \App\Models\Kelas::orderBy('tingkat', 'asc')
                ->orderBy('nama_kelas', 'asc')
                ->get();

            return view('admin.guru.edit', compact('teacher', 'mataPelajarans', 'kelasList'));

        } catch (\Exception $e) {
            Log::error('Edit guru form error: ' . $e->getMessage());
            return redirect()->route('admin.guru.index')->with('error', 'Guru tidak ditemukan.');
        }
    }

    /**
     * Update teacher data
     */
    public function updateGuru(Request $request, $id)
    {
        try {
            $teacher = User::whereIn('role', ['guru', 'kepala_sekolah'])->with(['guruProfile.mataPelajarans'])->findOrFail($id);
            $profileId = $teacher->guruProfile ? $teacher->guruProfile->id : null;

            // Validation rules
            $rules = [
                'name' => 'required|string|max:255',
                'nomor_induk' => 'required|string|max:50|unique:guru_profiles,nip,' . $profileId,
                'kode_guru' => 'nullable|string|max:10|unique:guru_profiles,kode_guru,' . $profileId,
                'email' => 'required|email|unique:users,email,' . $id,
                'jenis_kelamin' => 'required|in:L,P',
                'nomor_telepon' => 'nullable|string|max:20',
                'jabatan_di_sekolah' => 'nullable|string|max:100',
                'mata_pelajaran' => 'nullable',
                'status_kepegawaian' => 'nullable|in:PNS,PPPK,HONORER',
                'kelas_id' => 'nullable|exists:kelas,id',
                'profile_photo' => 'nullable|file|mimes:jpeg,png,jpg,webp,heic,heif|max:10240',
                'tempat_lahir' => 'nullable|string|max:100',
                'tanggal_lahir' => 'nullable|date',
                'golongan' => 'nullable|string|max:10',
                'can_manage_berita' => 'nullable|boolean',
                'can_manage_prestasi' => 'nullable|boolean',
            ];

            // Add password validation only if provided
            if ($request->filled('password')) {
                $rules['password'] = 'string|min:8|confirmed';
            }

            $request->validate($rules);

            // Ekstraksi mata pelajaran (bisa array dari multi-select maupun single id)
            $mapelInput = $request->input('mata_pelajaran');
            $mapelIds = [];
            if (is_array($mapelInput)) {
                $mapelIds = array_values(array_unique(array_filter(array_map('intval', $mapelInput))));
            } elseif (!empty($mapelInput)) {
                $mapelIds = [(int)$mapelInput];
            }
            if ($request->filled('mata_pelajaran_tambahan') && is_array($request->input('mata_pelajaran_tambahan'))) {
                foreach ($request->input('mata_pelajaran_tambahan') as $tId) {
                    if (!empty($tId)) $mapelIds[] = (int)$tId;
                }
                $mapelIds = array_values(array_unique(array_filter($mapelIds)));
            }

            $primaryMapelId = !empty($mapelIds) ? $mapelIds[0] : null;

            // Tentukan role berdasarkan jabatan
            $role = 'guru'; // Default role
            if ($request->filled('jabatan_di_sekolah') && $request->input('jabatan_di_sekolah') === 'Kepala Sekolah') {
                $role = 'kepala_sekolah';
            }

            // Update User
            $userData = [
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'role' => $role, // Update role berdasarkan jabatan
            ];
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->input('password'));
            }
            $teacher->update($userData);

            // Handle profile photo
            $photoPath = $teacher->guruProfile->foto_profil ?? null;
            if ($request->hasFile('profile_photo')) {
                if ($photoPath) {
                    $oldPath = str_starts_with($photoPath, 'profile_photos/') ? $photoPath : 'profile_photos/' . $photoPath;
                    if (Storage::disk('public')->exists($oldPath)) {
                        Storage::disk('public')->delete($oldPath);
                    }
                }
                $photo = $request->file('profile_photo');
                $compressedPath = ImageCompressor::compressAndStore(
                    $photo,
                    'profile_photos',
                    'guru_' . time() . '_' . $teacher->id . '_' . uniqid(),
                    60,
                    1200
                );
                $photoPath = basename($compressedPath);
            }

            // Update GuruProfile
            $profile = $teacher->guruProfile()->updateOrCreate(
                ['user_id' => $teacher->id],
                [
                    'nama' => $request->input('name'),
                    'nip' => $request->input('nomor_induk'),
                    'kode_guru' => $request->input('kode_guru'),
                    'email' => $request->input('email'),
                    'nomor_telepon' => $request->input('nomor_telepon'),
                    'jenis_kelamin' => $request->input('jenis_kelamin'),
                    'tempat_lahir' => $request->input('tempat_lahir'),
                    'tanggal_lahir' => $request->input('tanggal_lahir'),
                    'status_kepegawaian' => $request->input('status_kepegawaian'),
                    'golongan' => $request->input('golongan'),
                    'jabatan_di_sekolah' => $request->input('jabatan_di_sekolah'),
                    'mata_pelajaran_id' => $primaryMapelId,
                    'kelas_id' => $request->input('kelas_id'),
                    'foto_profil' => $photoPath,
                    'can_manage_berita' => $request->has('can_manage_berita'),
                    'can_manage_prestasi' => $request->has('can_manage_prestasi'),
                    'password' => $request->filled('password') ? Hash::make($request->input('password')) : ($teacher->guruProfile->password ?? $teacher->password),
                ]
            );

            // Sync all subjects to pivot table
            $profile->mataPelajarans()->sync($mapelIds);

            Log::info('Teacher updated successfully', ['teacher_id' => $teacher->id, 'updated_by' => auth()->id()]);

            return redirect()->route('admin.guru.index')
                           ->with('success', 'Data guru berhasil diperbarui!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                           ->withErrors($e->validator)
                           ->withInput();
        } catch (\Exception $e) {
            Log::error('Update guru error: ' . $e->getMessage());
            return redirect()->back()
                           ->with('error', 'Terjadi kesalahan saat memperbarui data guru.')
                           ->withInput();
        }
    }

    /**
     * Delete teacher
     */
    public function destroyGuru($id)
    {
        try {
            $teacher = User::whereIn('role', ['guru', 'kepala_sekolah'])->with('guruProfile')->findOrFail($id);

            // Delete profile photo if exists
            if ($teacher->guruProfile && $teacher->guruProfile->foto_profil && Storage::disk('public')->exists($teacher->guruProfile->foto_profil)) {
                Storage::disk('public')->delete($teacher->guruProfile->foto_profil);
            }

            $teacherName = $teacher->name;

            // Delete profile first (if not cascade)
            if ($teacher->guruProfile) {
                $teacher->guruProfile->delete();
            }

            $teacher->delete();

            Log::info('Teacher deleted successfully', ['teacher_name' => $teacherName, 'deleted_by' => auth()->id()]);

            return redirect()->route('admin.guru.index')
                           ->with('success', "Data guru {$teacherName} berhasil dihapus!");

        } catch (\Exception $e) {
            Log::error('Delete guru error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus data guru.');
        }
    }

    /**
     * API: Get mata pelajaran for autocomplete
     */
    public function getMataPelajaran(Request $request)
    {
        $search = $request->get('q', '');

        $query = \App\Models\MataPelajaran::where('is_active', true)
            ->where(function($q) use ($search) {
                $q->where('nama_mapel', 'LIKE', "%{$search}%")
                  ->orWhere('kode_mapel', 'LIKE', "%{$search}%");
            });

        $mataPelajaran = $query->orderBy('nama_mapel')
            ->limit(10)
            ->get(['id', 'nama_mapel', 'kode_mapel']);

        return response()->json($mataPelajaran);
    }

    /**
     * API: Get kelas for autocomplete
     */
    public function getKelas(Request $request)
    {
        $search = $request->get('q', '');

        $kelas = \App\Models\Kelas::where(function($query) use ($search) {
                $query->where('nama_kelas', 'LIKE', "%{$search}%")
                      ->orWhere('tingkat', 'LIKE', "%{$search}%");
            })
            ->orderBy('tingkat')
            ->orderBy('nama_kelas')
            ->limit(10)
            ->get(['id', 'nama_kelas', 'tingkat']);

        return response()->json($kelas);
    }
}
