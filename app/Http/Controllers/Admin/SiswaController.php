<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class SiswaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Student::orderBy('name');

        if ($request->filled('search')) {
            $q = $request->input('search');
            $query->where(function($sub) use ($q) {
            $sub->where('name', 'like', "%{$q}%")
                ->orWhere('nisn', 'like', "%{$q}%")
                ->orWhere('nis', 'like', "%{$q}%");
            });
        }

        if ($request->filled('jenis_kelamin')) {
            $query->where('jenis_kelamin', $request->input('jenis_kelamin'));
        }

        if ($request->filled('kelas')) {
            $query->where('kelas', $request->input('kelas'));
        }

        $students = $query->paginate(15)->withQueryString();

        // Simple classes list (derive from existing students)
        $classes = Student::distinct()->pluck('kelas')->filter()->values()->all();

        return view('admin.siswa.index', compact('students', 'classes'));
    }

    /**
     * AJAX endpoint for searching students - returns only table HTML
     */
    public function search(Request $request)
    {
        $query = Student::orderBy('name');

        if ($request->filled('search')) {
            $q = $request->input('search');
            $query->where(function($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('nisn', 'like', "%{$q}%")
                    ->orWhere('nis', 'like', "%{$q}%");
            });
        }

        if ($request->filled('jenis_kelamin')) {
            $query->where('jenis_kelamin', $request->input('jenis_kelamin'));
        }

        if ($request->filled('kelas')) {
            $query->where('kelas', $request->input('kelas'));
        }

        $students = $query->paginate(15)->withQueryString();
        $classes = Student::distinct()->pluck('kelas')->filter()->values()->all();

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

        return view('admin.siswa.index', compact('students', 'classes'));
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
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:students,email',
            'nis' => 'nullable|string|max:50|unique:students,nis',
            'nisn' => 'nullable|string|max:50',
            'jenis_kelamin' => 'nullable|string|max:20',
            'kelas' => 'nullable|string|max:50',
            'tanggal_lahir' => 'nullable|date',
            'nomor_telepon' => 'nullable|string|max:30',
            'nomor_telepon_orangtua' => 'nullable|string|max:30',
            'profile_photo' => 'nullable|image|max:2048',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('profile_photos', 'public');
            $data['profile_photo'] = $path;
        }

        // Hash password
        $data['password'] = Hash::make($data['password']);

        $student = Student::create($data);

        return redirect()->route('admin.siswa.index')->with('success', 'Siswa berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $student = Student::findOrFail($id);
        return view('admin.siswa.show', compact('student'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $student = Student::findOrFail($id);
        return view('admin.siswa.edit', compact('student'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $student = Student::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:students,email,' . $student->id,
            'nis' => 'nullable|string|max:50|unique:students,nis,' . $student->id,
            'nisn' => 'nullable|string|max:50',
            'jenis_kelamin' => 'nullable|string|max:20',
            'kelas' => 'nullable|string|max:50',
            'tanggal_lahir' => 'nullable|date',
            'nomor_telepon' => 'nullable|string|max:30',
            'nomor_telepon_orangtua' => 'nullable|string|max:30',
            'profile_photo' => 'nullable|image|max:2048',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        if ($request->hasFile('profile_photo')) {
            if ($student->profile_photo) {
                Storage::disk('public')->delete($student->profile_photo);
            }
            $path = $request->file('profile_photo')->store('profile_photos', 'public');
            $data['profile_photo'] = $path;
        }

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $student->update($data);

        return redirect()->route('admin.siswa.show', $student->id)->with('success', 'Siswa berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $student = Student::findOrFail($id);

        if ($student->profile_photo) {
            Storage::disk('public')->delete($student->profile_photo);
        }

        $student->delete();

        return redirect()->route('admin.siswa.index')->with('success', 'Siswa berhasil dihapus');
    }

    /**
     * Export students (CSV)
     */
    public function export(Request $request)
    {
        $query = Student::orderBy('name');

        if ($request->filled('search')) {
            $q = $request->input('search');
            $query->where(function($sub) use ($q) {
            $sub->where('name', 'like', "%{$q}%")
                ->orWhere('nisn', 'like', "%{$q}%")
                ->orWhere('nis', 'like', "%{$q}%");
            });
        }

        if ($request->filled('jenis_kelamin')) {
            $query->where('jenis_kelamin', $request->input('jenis_kelamin'));
        }

        if ($request->filled('kelas')) {
            $query->where('kelas', $request->input('kelas'));
        }

        $students = $query->get();

        $filename = 'students_export_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $columns = ['Nama','NIS','NISN','Jenis Kelamin','Kelas','Nomor Telepon Orang Tua','Tanggal Lahir','Telepon','Email'];

        $callback = function() use ($students, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($students as $s) {
                fputcsv($file, [
                    $s->name,
                    $s->nis,
                    $s->nisn,
                    $s->jenis_kelamin,
                    $s->kelas,
                    $s->nomor_telepon_orangtua,
                    $s->tanggal_lahir,
                    $s->nomor_telepon,
                    $s->email,
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
     }
 }
