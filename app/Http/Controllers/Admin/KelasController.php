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
        // Eager load waliKelas and count students
        $kelas = Kelas::with(['waliKelas', 'students'])
            ->withCount('students')
            ->orderBy('tingkat')
            ->orderBy('nama_kelas')
            ->get();
        
        // Get active teachers for dropdown in create/edit modal
        // Order by name
        $gurus = GuruProfile::where('is_active', true)
            ->where('status_kepegawaian', '!=', 'KEPALA_SEKOLAH') // Assuming Kepsek cannot be wali kelas
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
            'tahun_pelajaran_id' => 'nullable|exists:tahun_pelajaran,id'
        ]);

        DB::beginTransaction();
        try {
            // 1. Create Kelas
            $kelas = Kelas::create([
                'tingkat' => $request->tingkat,
                'nama_kelas' => $request->nama_kelas,
                'tahun_angkatan' => $request->tahun_angkatan,
                'tahun_pelajaran_id' => $request->tahun_pelajaran_id
            ]);

            // 2. Assign Wali Kelas if selected
            if ($request->filled('wali_kelas_id')) {
                $guru = GuruProfile::find($request->wali_kelas_id);
                
                // If this guru is already wali kelas of another class, we might want to unassign it first
                // OR we can allow one guru to be wali kelas for multiple classes (unlikely but possible logic)
                // Here, we assume one guru = one wali kelas.
                // Reset any class this guru might be managing
                // GuruProfile::where('id', $guru->id)->update(['kelas_id' => null]); NOT NEEDED if we just overwrite

                // Update guru's kelas_id
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
            'wali_kelas_id' => 'nullable|exists:guru_profiles,id', // Can be null (uncouple)
            'tahun_pelajaran_id' => 'nullable|exists:tahun_pelajaran,id'
        ]);

        DB::beginTransaction();
        try {
            $kelas = Kelas::findOrFail($id);
            
            // 1. Update Basic Info
            $kelas->update([
                'tingkat' => $request->tingkat,
                'nama_kelas' => $request->nama_kelas,
                'tahun_angkatan' => $request->tahun_angkatan,
                'tahun_pelajaran_id' => $request->tahun_pelajaran_id
            ]);

            // 2. Handle Wali Kelas Logic
            
            // A. If user selected "No Wali Kelas" (null) or selected a DIFFERENT wali kelas
            // We must first detach the OLD wali kelas if exists
            $currentWaliKelas = $kelas->waliKelas; 
            
            if ($currentWaliKelas) {
                // If we are changing to null OR changing to a different guru ID
                if (!$request->wali_kelas_id || ($request->wali_kelas_id != $currentWaliKelas->id)) {
                    $currentWaliKelas->kelas_id = null;
                    $currentWaliKelas->save();
                }
            }

            // B. If user selected a NEW wali kelas
            if ($request->filled('wali_kelas_id')) {
                // Check if the selected guru is already assigned to THIS class? 
                // We already handled "same guru" logic implicitly above (didn't detach).
                // But we must handle if this guru was assigned to ANOTHER class previously.
                
                $newWaliKelas = GuruProfile::findOrFail($request->wali_kelas_id);
                
                // Optional: If new wali kelas was assigned to another class, should we detach them from that old class?
                // Usually yes, one teacher = one class.
                if ($newWaliKelas->kelas_id && $newWaliKelas->kelas_id != $kelas->id) {
                     // This guru was wali kelas of another class. We can either:
                     // 1. Prevent it (Validation error)
                     // 2. Automatically move them (Detach from old class) -> We choose this.
                     // No specific action needed, just overwriting 'kelas_id' will automatically "detach" them from the old class in the DB relationship perspective.
                }

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
