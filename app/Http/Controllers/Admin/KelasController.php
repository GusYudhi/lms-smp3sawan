<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\GuruProfile;
use App\Models\TahunPelajaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KelasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Eager load waliKelas, guruPendamping, and count students
        $kelas = Kelas::with(['waliKelas', 'guruPendamping', 'students'])
            ->withCount('students')
            ->orderBy('tingkat')
            ->orderBy('nama_kelas')
            ->get();
        
        // Get active teachers for dropdown in create/edit modal
        // Order by name
        $gurus = GuruProfile::where('is_active', true)
            ->where('status_kepegawaian', '!=', 'KEPALA_SEKOLAH') // Assuming Kepsek cannot be wali kelas
            ->with(['kelas', 'kelasPendamping'])
            ->orderBy('nama')
            ->get();

        $tahunPelajarans = TahunPelajaran::orderByDesc('tahun_mulai')->get();
        $activeTahunPelajaran = TahunPelajaran::where('is_active', true)->first();

        return view('admin.kelas.index', compact('kelas', 'gurus', 'tahunPelajarans', 'activeTahunPelajaran'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tingkat' => 'required|in:7,8,9',
            'nama_kelas' => 'required|string|max:10',
            'tahun_angkatan' => 'required|numeric|min:2000',
            'wali_kelas_id' => 'nullable|exists:guru_profiles,id',
            'guru_pendamping_id' => [
                'nullable',
                'exists:guru_profiles,id',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value && $request->filled('wali_kelas_id') && $value == $request->wali_kelas_id) {
                        $fail('Guru Pendamping tidak boleh sama dengan Wali Kelas.');
                    }
                    if ($value) {
                        $guru = GuruProfile::find($value);
                        if ($guru && !empty($guru->kelas_id)) {
                            $fail('Guru yang dipilih sudah menjadi Wali Kelas sehingga tidak dapat menjadi Guru Pendamping.');
                        }
                    }
                },
            ],
            'tahun_pelajaran_id' => 'nullable|exists:tahun_pelajaran,id'
        ]);

        DB::beginTransaction();
        try {
            // 1. Create Kelas
            $kelas = Kelas::create([
                'tingkat' => $request->tingkat,
                'nama_kelas' => $request->nama_kelas,
                'tahun_angkatan' => $request->tahun_angkatan,
                'tahun_pelajaran_id' => $request->tahun_pelajaran_id,
                'guru_pendamping_id' => $request->guru_pendamping_id,
            ]);

            // 2. Assign Wali Kelas if selected
            if ($request->filled('wali_kelas_id')) {
                $guru = GuruProfile::find($request->wali_kelas_id);
                
                // Jika guru ini sebelumnya guru pendamping, otomatis lepas
                Kelas::where('guru_pendamping_id', $guru->id)->update(['guru_pendamping_id' => null]);

                $guru->kelas_id = $kelas->id;
                $guru->save();
            }

            DB::commit();
            return redirect()->route('admin.kelas.index')->with('success', 'Kelas berhasil ditambahkan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menambahkan kelas: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'tingkat' => 'required|in:7,8,9',
            'nama_kelas' => 'required|string|max:10',
            'tahun_angkatan' => 'required|numeric|min:2000',
            'wali_kelas_id' => 'nullable|exists:guru_profiles,id',
            'guru_pendamping_id' => [
                'nullable',
                'exists:guru_profiles,id',
                function ($attribute, $value, $fail) use ($request, $id) {
                    if ($value && $request->filled('wali_kelas_id') && $value == $request->wali_kelas_id) {
                        $fail('Guru Pendamping tidak boleh sama dengan Wali Kelas.');
                    }
                    if ($value) {
                        $guru = GuruProfile::find($value);
                        // Guru tidak boleh sudah menjadi wali kelas di kelas manapun
                        if ($guru && !empty($guru->kelas_id) && $guru->kelas_id != $id) {
                            $fail('Guru yang dipilih sudah menjadi Wali Kelas di kelas lain sehingga tidak dapat menjadi Guru Pendamping.');
                        }
                    }
                },
            ],
            'tahun_pelajaran_id' => 'nullable|exists:tahun_pelajaran,id'
        ]);

        DB::beginTransaction();
        try {
            $kelas = Kelas::findOrFail($id);
            
            // 1. Update Basic Info & Guru Pendamping
            $kelas->update([
                'tingkat' => $request->tingkat,
                'nama_kelas' => $request->nama_kelas,
                'tahun_angkatan' => $request->tahun_angkatan,
                'tahun_pelajaran_id' => $request->tahun_pelajaran_id,
                'guru_pendamping_id' => $request->guru_pendamping_id,
            ]);

            // 2. Handle Wali Kelas Logic
            $currentWaliKelas = $kelas->waliKelas; 
            
            if ($currentWaliKelas) {
                if (!$request->wali_kelas_id || ($request->wali_kelas_id != $currentWaliKelas->id)) {
                    $currentWaliKelas->kelas_id = null;
                    $currentWaliKelas->save();
                }
            }

            if ($request->filled('wali_kelas_id')) {
                $newWaliKelas = GuruProfile::findOrFail($request->wali_kelas_id);
                
                // Jika guru ini sebelumnya menjadi guru pendamping di kelas manapun, otomatis lepas
                Kelas::where('guru_pendamping_id', $newWaliKelas->id)->update(['guru_pendamping_id' => null]);

                $newWaliKelas->kelas_id = $kelas->id;
                $newWaliKelas->save();
            }

            DB::commit();
            return redirect()->route('admin.kelas.index')->with('success', 'Data kelas berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memperbarui kelas: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $kelas = Kelas::withCount('students')->findOrFail($id);

        if ($kelas->students_count > 0) {
            return redirect()->back()->with('error', 'Tidak dapat menghapus kelas yang masih memiliki siswa.');
        }

        DB::beginTransaction();
        try {
            // 1. Detach Wali Kelas if exists
            if ($kelas->waliKelas) {
                $kelas->waliKelas->update(['kelas_id' => null]);
            }

            // 2. Delete Kelas
            $kelas->delete();

            DB::commit();
            return redirect()->route('admin.kelas.index')->with('success', 'Kelas berhasil dihapus.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menghapus kelas: ' . $e->getMessage());
        }
    }
}
