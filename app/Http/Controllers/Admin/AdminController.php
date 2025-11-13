<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class AdminController extends Controller
{
    public function manageGuru(Request $request)
    {
        $query = User::where('role', 'guru');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('nomor_induk', 'LIKE', "%{$search}%")
                  ->orWhere('mata_pelajaran', 'LIKE', "%{$search}%");
            });
        }

        // Filter by status kepegawaian if needed
        if ($request->filled('status')) {
            $query->where('status_kepegawaian', $request->status);
        }

        // Filter by gender if needed
        if ($request->filled('gender')) {
            $query->where('jenis_kelamin', $request->gender);
        }

        $teachers = $query->orderBy('name', 'asc')->paginate(15)->withQueryString();

        // Get unique subjects for filter dropdown (nanti bisa ditambahkan ketika ada tabel mata pelajaran)
        $subjects = collect([
            'Matematika', 'Bahasa Indonesia', 'Bahasa Inggris', 'IPA Fisika',
            'IPA Biologi', 'IPS Sejarah', 'PKN', 'Pendidikan Jasmani',
            'Seni Budaya', 'Prakarya'
        ]);

        return view('admin.guru.index', compact('teachers', 'subjects'));
    }

    /**
     * AJAX endpoint for searching teachers - returns only table HTML
     */
    public function searchGuru(Request $request)
    {
        $query = User::where('role', 'guru');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('nomor_induk', 'LIKE', "%{$search}%")
                  ->orWhere('mata_pelajaran', 'LIKE', "%{$search}%");
            });
        }

        // Filter by status kepegawaian if needed
        if ($request->filled('status')) {
            $query->where('status_kepegawaian', $request->status);
        }

        // Filter by gender if needed
        if ($request->filled('gender')) {
            $query->where('jenis_kelamin', $request->gender);
        }

        $teachers = $query->orderBy('name', 'asc')->paginate(15)->withQueryString();

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.guru.partials.table', compact('teachers'))->render(),
                'pagination' => view('admin.guru.partials.pagination', compact('teachers'))->render(),
                'info' => view('admin.guru.partials.table-info', compact('teachers'))->render(),
                'total' => $teachers->total(),
                'current_page' => $teachers->currentPage(),
                'filters' => $request->only(['search', 'status', 'gender'])
            ]);
        }

        // Get unique subjects for filter dropdown
        $subjects = collect([
            'Matematika', 'Bahasa Indonesia', 'Bahasa Inggris', 'IPA Fisika',
            'IPA Biologi', 'IPS Sejarah', 'PKN', 'Pendidikan Jasmani',
            'Seni Budaya', 'Prakarya'
        ]);

        return view('admin.guru.index', compact('teachers', 'subjects'));
    }

    public function createGuru()
    {
        return view('admin.guru.create');
    }

    public function storeGuru(Request $request)
    {
        try {
            // Validation rules
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'nomor_induk' => 'required|string|max:50|unique:users,nomor_induk',
                'email' => 'required|email|unique:users,email',
                'nomor_telepon' => 'nullable|string|max:20',
                'jenis_kelamin' => 'required|in:L,P',
                'tempat_lahir' => 'nullable|string|max:100',
                'tanggal_lahir' => 'nullable|date',
                'status_kepegawaian' => 'required|in:PNS,PPPK,Honorer',
                'golongan' => 'nullable|string|max:10',
                'mata_pelajaran' => 'required|string|max:100',
                'wali_kelas' => 'nullable|string|max:10',
                'password' => 'required|string|min:8|confirmed',
                'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
            ]);

            // Handle profile photo upload
            $profilePhotoPath = null;
            if ($request->hasFile('profile_photo')) {
                $file = $request->file('profile_photo');
                $filename = time() . '_' . $file->getClientOriginalName();
                $profilePhotoPath = $file->storeAs('photos', $filename, 'public');
            }

            // Create new teacher
            $teacher = User::create([
                'name' => $validatedData['name'],
                'nomor_induk' => $validatedData['nomor_induk'],
                'email' => $validatedData['email'],
                'nomor_telepon' => $validatedData['nomor_telepon'],
                'jenis_kelamin' => $validatedData['jenis_kelamin'],
                'tempat_lahir' => $validatedData['tempat_lahir'],
                'tanggal_lahir' => $validatedData['tanggal_lahir'],
                'status_kepegawaian' => $validatedData['status_kepegawaian'],
                'golongan' => $validatedData['golongan'],
                'mata_pelajaran' => $validatedData['mata_pelajaran'],
                'wali_kelas' => $validatedData['wali_kelas'],
                'role' => 'guru',
                'password' => Hash::make($validatedData['password']),
                'profile_photo_path' => $profilePhotoPath,
            ]);

            Log::info('Teacher created successfully', ['teacher_id' => $teacher->id, 'name' => $teacher->name]);

            return redirect()->route('admin.guru.index')->with('success', 'Data guru berhasil ditambahkan!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Validation error when creating teacher', ['errors' => $e->errors()]);
            return back()->withErrors($e->errors())->withInput();

        } catch (\Exception $e) {
            Log::error('Error creating teacher', ['error' => $e->getMessage()]);
            return back()->with('error', 'Terjadi kesalahan saat menambahkan data guru. Silakan coba lagi.')->withInput();
        }
    }

    public function exportGuru()
    {
        try {
            // Create simple CSV export
            $teachers = User::where('role', 'guru')
                          ->orderBy('name', 'asc')
                          ->get();

            $filename = 'data_guru_' . date('Y-m-d_H-i-s') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function() use ($teachers) {
                $file = fopen('php://output', 'w');

                // Add BOM for Excel UTF-8 support
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

                // Header row
                fputcsv($file, [
                    'No',
                    'Nama Lengkap',
                    'NIP/NIK',
                    'Email',
                    'No. Telepon',
                    'Jenis Kelamin',
                    'Tempat Lahir',
                    'Tanggal Lahir',
                    'Status Kepegawaian',
                    'Golongan',
                    'Mata Pelajaran',
                    'Wali Kelas',
                    'Status Akun'
                ]);

                // Data rows
                $no = 1;
                foreach ($teachers as $teacher) {
                    fputcsv($file, [
                        $no++,
                        $teacher->name ?? '-',
                        $teacher->nomor_induk ?? '-',
                        $teacher->email ?? '-',
                        $teacher->nomor_telepon ?? '-',
                        $teacher->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan',
                        $teacher->tempat_lahir ?? '-',
                        $teacher->tanggal_lahir ? date('d/m/Y', strtotime($teacher->tanggal_lahir)) : '-',
                        $teacher->status_kepegawaian ?? '-',
                        $teacher->golongan ?? '-',
                        $teacher->mata_pelajaran ?? '-',
                        $teacher->wali_kelas ?? '-',
                        'Aktif'
                    ]);
                }

                fclose($file);
            };

            Log::info('Data guru exported', ['total_teachers' => $teachers->count()]);

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            Log::error('Error exporting teacher data', ['error' => $e->getMessage()]);
            return back()->with('error', 'Terjadi kesalahan saat mengunduh data guru.');
        }
    }

    /**
     * Show teacher details
     */
    public function showGuru($id)
    {
        try {
            $teacher = User::where('role', 'guru')->findOrFail($id);

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
            $teacher = User::where('role', 'guru')->findOrFail($id);

            return view('admin.guru.edit', compact('teacher'));

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
            $teacher = User::where('role', 'guru')->findOrFail($id);

            // Validation rules
            $rules = [
                'name' => 'required|string|max:255',
                'nomor_induk' => 'required|string|max:20|unique:users,nomor_induk,' . $id,
                'email' => 'required|email|unique:users,email,' . $id,
                'jenis_kelamin' => 'required|in:laki-laki,perempuan',
                'nomor_telepon' => 'nullable|string|max:20',
                'mata_pelajaran' => 'required|string|max:100',
                'status_kepegawaian' => 'required|in:pns,honorer,kontrak,tetap_yayasan',
                'wali_kelas' => 'nullable|string|max:50',
                'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
            ];

            // Add password validation only if provided
            if ($request->filled('password')) {
                $rules['password'] = 'string|min:8|confirmed';
            }

            $request->validate($rules, [
                'name.required' => 'Nama lengkap harus diisi.',
                'nomor_induk.required' => 'Nomor induk harus diisi.',
                'nomor_induk.unique' => 'Nomor induk sudah digunakan.',
                'email.required' => 'Email harus diisi.',
                'email.unique' => 'Email sudah digunakan.',
                'jenis_kelamin.required' => 'Jenis kelamin harus dipilih.',
                'mata_pelajaran.required' => 'Mata pelajaran harus diisi.',
                'status_kepegawaian.required' => 'Status kepegawaian harus dipilih.',
                'profile_photo.image' => 'File foto harus berupa gambar.',
                'profile_photo.max' => 'Ukuran foto maksimal 2MB.',
                'password.min' => 'Password minimal 8 karakter.',
                'password.confirmed' => 'Konfirmasi password tidak sesuai.'
            ]);

            // Prepare data for update
            $data = [
                'name' => $request->input('name'),
                'nomor_induk' => $request->input('nomor_induk'),
                'email' => $request->input('email'),
                'jenis_kelamin' => $request->input('jenis_kelamin'),
                'nomor_telepon' => $request->input('nomor_telepon'),
                'mata_pelajaran' => $request->input('mata_pelajaran'),
                'status_kepegawaian' => $request->input('status_kepegawaian'),
                'wali_kelas' => $request->input('wali_kelas'),
            ];

            // Handle password update
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->input('password'));
            }

            // Handle profile photo upload
            if ($request->hasFile('profile_photo')) {
                // Delete old photo if exists
                if ($teacher->profile_photo && Storage::disk('public')->exists($teacher->profile_photo)) {
                    Storage::disk('public')->delete($teacher->profile_photo);
                }

                $photo = $request->file('profile_photo');
                $photoName = 'guru_' . $teacher->nomor_induk . '_' . time() . '.' . $photo->getClientOriginalExtension();
                $photoPath = $photo->storeAs('profile_photos', $photoName, 'public');
                $data['profile_photo'] = $photoPath;
            }

            // Update teacher data
            $teacher->update($data);

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
            $teacher = User::where('role', 'guru')->findOrFail($id);

            // Delete profile photo if exists
            if ($teacher->profile_photo && Storage::disk('public')->exists($teacher->profile_photo)) {
                Storage::disk('public')->delete($teacher->profile_photo);
            }

            $teacherName = $teacher->name;
            $teacher->delete();

            Log::info('Teacher deleted successfully', ['teacher_name' => $teacherName, 'deleted_by' => auth()->id()]);

            return redirect()->route('admin.guru.index')
                           ->with('success', "Data guru {$teacherName} berhasil dihapus!");

        } catch (\Exception $e) {
            Log::error('Delete guru error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus data guru.');
        }
    }
}
