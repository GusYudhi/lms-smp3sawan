<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserManagementService;
use App\Imports\StudentsImport;
use App\Exports\StudentsTemplateExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
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
        $filters = $request->only(['jenis_kelamin', 'kelas']);

        $students = $this->userService->getStudentsWithProfiles($search, $filters);

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
        $filters = $request->only(['jenis_kelamin', 'kelas']);

        $students = $this->userService->getStudentsWithProfiles($search, $filters);

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
                'filters' => $request->only(['search', 'jenis_kelamin', 'kelas'])
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
            'nomor_telepon_orangtua' => 'nullable|string|max:30',
            'alamat' => 'nullable|string',
            'nama_orangtua_wali' => 'nullable|string|max:255',
            'pekerjaan_orangtua' => 'nullable|string|max:255',
        ]);

        $userData = $request->only(['name', 'email', 'password']);

        $profileData = $request->only([
            'nis', 'nisn', 'tempat_lahir', 'tanggal_lahir', 'tingkat', 'kelas',
            'nomor_telepon_orangtua', 'alamat', 'nama_orangtua_wali', 'pekerjaan_orangtua', 'jenis_kelamin'
        ]);

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('profile_photos', 'public');
            $profileData['foto_profil'] = $path;
        }

        $student = $this->userService->createStudent($userData, $profileData);

        return redirect()->route('admin.siswa.index')->with('success', 'Siswa berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $student = User::with('studentProfile')->where('role', 'siswa')->findOrFail($id);
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
            'nomor_telepon_orangtua' => 'nullable|string|max:30',
            'alamat' => 'nullable|string',
            'nama_orangtua_wali' => 'nullable|string|max:255',
            'pekerjaan_orangtua' => 'nullable|string|max:255',
        ]);

        $userData = $request->only(['name', 'email', 'password']);

        $profileData = $request->only([
            'nis', 'nisn', 'tempat_lahir', 'tanggal_lahir', 'tingkat', 'kelas',
            'nomor_telepon_orangtua', 'alamat', 'nama_orangtua_wali', 'pekerjaan_orangtua', 'jenis_kelamin'
        ]);

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('profile_photos', 'public');
            $profileData['foto_profil'] = $path;
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
     * Export students (CSV)
     */
    public function export(Request $request)
    {
        $search = $request->input('search');
        $filters = $request->only(['jenis_kelamin', 'kelas']);

        $students = User::with('studentProfile')
            ->where('role', 'siswa')
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhereHas('studentProfile', function ($sub) use ($search) {
                          $sub->where('nis', 'like', "%{$search}%")
                              ->orWhere('nisn', 'like', "%{$search}%");
                      });
                });
            })
            ->when(isset($filters['jenis_kelamin']), function ($query) use ($filters) {
                return $query->where('jenis_kelamin', $filters['jenis_kelamin']);
            })
            ->when(isset($filters['kelas']), function ($query) use ($filters) {
                return $query->whereHas('studentProfile', function ($q) use ($filters) {
                    $q->where('kelas', $filters['kelas']);
                });
            })
            ->orderBy('name')
            ->get();

        $filename = 'students_export_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $columns = ['Nama', 'NIS', 'NISN', 'Jenis Kelamin', 'Kelas', 'Nomor Telepon Orang Tua', 'Tanggal Lahir', 'Email'];

        $callback = function() use ($students, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($students as $student) {
                $profile = $student->studentProfile;
                $gender = $profile->jenis_kelamin === 'L' ? 'Laki-laki' : ($profile->jenis_kelamin === 'P' ? 'Perempuan' : '');

                fputcsv($file, [
                    $student->name,
                    $profile->nis ?? '',
                    $profile->nisn ?? '',
                    $gender,
                    $profile->kelas ? $profile->kelas->full_name : '',
                    $profile->nomor_telepon_orangtua ?? '',
                    $profile->tanggal_lahir ?? '',
                    $student->email ?? '',
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
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
}
