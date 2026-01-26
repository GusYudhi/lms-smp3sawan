<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\StudentProfile;
use App\Models\JurnalMengajar;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RekapJurnalController extends Controller
{
    public function index(Request $request)
    {
        $kelas = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();
        $mapels = MataPelajaran::orderBy('nama_mapel')->get();

        // Data awal (kosong jika belum difilter)
        $students = collect();
        $headers = [];
        $attendanceData = [];
        $rekapInfo = null;

        if ($request->has(['kelas_id', 'start_date', 'end_date'])) {
            $request->validate([
                'kelas_id' => 'required|exists:kelas,id',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'mapel_id' => 'nullable', // bisa null atau 'all' atau id
            ]);

            $kelasId = $request->kelas_id;
            $startDate = $request->start_date;
            $endDate = $request->end_date;
            $mapelId = $request->mapel_id;

            // 1. Ambil Siswa
            $students = StudentProfile::with('user')
                ->where('kelas_id', $kelasId)
                ->where('is_active', true)
                ->get()
                ->sortBy(function ($student) {
                    return $student->user->name ?? '';
                });

            // 2. Query Jurnal Mengajar
            $query = JurnalMengajar::with(['jurnalAttendances', 'mataPelajaran'])
                ->where('kelas_id', $kelasId)
                ->whereBetween('tanggal', [$startDate, $endDate]);

            if ($mapelId && $mapelId !== 'all') {
                $query->where('mata_pelajaran_id', $mapelId);
                $mode = 'specific_mapel';
            } else {
                $mode = 'all_mapel';
            }

            $jurnals = $query->orderBy('tanggal', 'asc')->get();

            // 3. Proses Data untuk View
            if ($mode === 'specific_mapel') {
                // Skenario A: Kolom adalah Tanggal
                // Group by tanggal untuk header
                foreach ($jurnals as $jurnal) {
                    $dateKey = $jurnal->tanggal->format('Y-m-d');
                    // Simpan header jika belum ada
                    if (!isset($headers[$dateKey])) {
                        $headers[$dateKey] = [
                            'label' => $jurnal->tanggal->format('d/m'),
                            'full_date' => $jurnal->tanggal->format('d F Y'),
                            'jam' => $jurnal->jam_mulai . '-' . $jurnal->jam_selesai, // Optional
                            'materi' => $jurnal->materi_pembelajaran // Optional tooltip
                        ];
                    }
                }

                // Mapping Absensi
                foreach ($jurnals as $jurnal) {
                    $dateKey = $jurnal->tanggal->format('Y-m-d');
                    
                    foreach ($jurnal->jurnalAttendances as $attendance) {
                        $studentId = $attendance->student_profile_id;
                        // Format: [student_id][tanggal] = status
                        $attendanceData[$studentId][$dateKey] = $attendance->status;
                    }
                }

            } else {
                // Skenario B: Kolom adalah Mapel (Semua Mapel)
                // Disini kita ambil semua mapel unik yang muncul di jurnal pada rentang tanggal tersebut
                
                // Urutkan mapel berdasarkan nama agar rapi di tabel
                $uniqueMapelIds = $jurnals->pluck('mata_pelajaran_id')->unique();
                $headerMapels = MataPelajaran::whereIn('id', $uniqueMapelIds)->orderBy('nama_mapel')->get();

                foreach ($headerMapels as $m) {
                    $headers[$m->id] = [
                        'label' => $m->nama_mapel,
                        'kode' => $m->kode_mapel
                    ];
                }

                // Mapping Absensi
                foreach ($jurnals as $jurnal) {
                    $mapelId = $jurnal->mata_pelajaran_id;
                    $dateFormatted = $jurnal->tanggal->format('d/m');

                    foreach ($jurnal->jurnalAttendances as $attendance) {
                        $studentId = $attendance->student_profile_id;
                        
                        // Karena bisa ada beberapa hari untuk satu mapel dalam rentang tanggal,
                        // kita perlu strategi display.
                        // Opsi 1: Tampilkan status terakhir.
                        // Opsi 2: Append string (misal H (26/1)). 
                        // Kita pakai append string jika lebih dari 1 pertemuan, agar informatif.
                        
                        if (!isset($attendanceData[$studentId][$mapelId])) {
                            $attendanceData[$studentId][$mapelId] = [];
                        }

                        // Simpan array status untuk diproses di view
                        $attendanceData[$studentId][$mapelId][] = [
                            'status' => $attendance->status,
                            'date' => $dateFormatted
                        ];
                    }
                }
            }

            // Info tambahan untuk judul laporan
            $selectedKelas = Kelas::find($kelasId);
            $selectedMapel = ($mapelId && $mapelId !== 'all') ? MataPelajaran::find($mapelId) : null;
            
            $rekapInfo = [
                'kelas' => $selectedKelas->full_name,
                'mapel' => $selectedMapel ? $selectedMapel->nama_mapel : 'Semua Mata Pelajaran',
                'periode' => Carbon::parse($startDate)->format('d M Y') . ' s/d ' . Carbon::parse($endDate)->format('d M Y'),
            ];
        }

        return view('rekap-jurnal.index', compact(
            'kelas', 
            'mapels', 
            'students', 
            'headers', 
            'attendanceData', 
            'rekapInfo',
            'request'
        ));
    }
}
