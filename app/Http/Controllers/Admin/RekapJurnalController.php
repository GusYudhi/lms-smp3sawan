<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\StudentProfile;
use App\Models\JurnalMengajar;
use Carbon\Carbon;

class RekapJurnalController extends Controller
{
    public function index(Request $request)
    {
        $kelas = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();
        
        // Filter mapel berdasarkan SEMESTER aktif (sesuai AdminController)
        $activeSemester = \App\Models\Semester::where('is_active', true)->first();
        
        $mapelQuery = MataPelajaran::orderBy('nama_mapel');
        
        if ($activeSemester) {
            $mapelQuery->where(function($q) use ($activeSemester) {
                $q->where('semester_id', $activeSemester->id)
                  ->orWhereNull('semester_id');
            });
        }
        
        $mapels = $mapelQuery->get();

        // Init data
        $students = collect();
        $headers = []; // Untuk header tabel dinamis
        $matrix = []; // Struktur: [student_id] => ['info' => $student, 'attendance' => [date_key => status], 'stats' => [H, S, I, A]]
        $stats = [
            'total_pertemuan' => 0,
            'avg_hadir' => 0,
            'total_izin_sakit' => 0,
            'total_alpha' => 0
        ];
        
        $isFiltered = false;

        if ($request->filled(['kelas_id', 'start_date', 'end_date'])) {
            $isFiltered = true;
            $request->validate([
                'kelas_id' => 'required|exists:kelas,id',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
            ]);

            $kelasId = $request->kelas_id;
            $startDate = $request->start_date;
            $endDate = $request->end_date;
            $mapelId = $request->mapel_id; // Bisa null (Semua Mapel)

            // 1. Ambil Siswa
            $students = StudentProfile::with('user')
                ->where('kelas_id', $kelasId)
                ->where('is_active', true)
                ->get()
                ->sortBy(function ($student) {
                    return $student->user->name ?? '';
                });

            // 2. Ambil Jurnal
            $query = JurnalMengajar::with(['jurnalAttendances', 'mataPelajaran'])
                ->where('kelas_id', $kelasId)
                ->whereBetween('tanggal', [$startDate, $endDate]);

            if ($mapelId && $mapelId !== 'all') {
                $query->where('mata_pelajaran_id', $mapelId);
            }

            $jurnals = $query->orderBy('tanggal')->orderBy('jam_ke_mulai')->get();

            // 3. Build Headers (Columns)
            foreach ($jurnals as $jurnal) {
                // Key unik untuk kolom: ID Jurnal
                // Kita pakai ID Jurnal supaya unik jika ada 2 mapel beda di hari yg sama (kalau filter All Mapel)
                $key = 'J-' . $jurnal->id;
                
                $headers[$key] = [
                    'id' => $jurnal->id,
                    'date' => $jurnal->tanggal->format('d/m'),
                    'full_date' => $jurnal->tanggal->format('d M Y'),
                    'mapel' => $jurnal->mataPelajaran->nama_mapel ?? '-',
                    'materi' => $jurnal->materi_pembelajaran
                ];
            }
            $stats['total_pertemuan'] = count($jurnals);

            // 4. Build Matrix & Calculate Stats
            $totalHadir = 0;
            $totalPossibleAttendance = 0; // Total (Siswa x Pertemuan)

            foreach ($students as $student) {
                $studentId = $student->id;
                $rowStats = ['H' => 0, 'S' => 0, 'I' => 0, 'A' => 0, 'T' => 0];
                $attendanceRow = [];

                foreach ($jurnals as $jurnal) {
                    $key = 'J-' . $jurnal->id;
                    
                    // Cari status siswa di jurnal ini
                    // Menggunakan collection method firstWhere agar tidak query ulang
                    $att = $jurnal->jurnalAttendances->firstWhere('student_profile_id', $studentId);
                    
                    $status = $att ? $att->status : '-'; // - jika tidak ada data
                    
                    // Count stats
                    if ($status == 'hadir') $rowStats['H']++;
                    elseif ($status == 'sakit') $rowStats['S']++;
                    elseif ($status == 'izin') $rowStats['I']++;
                    elseif ($status == 'alpa') $rowStats['A']++;
                    elseif ($status == 'terlambat') $rowStats['T']++;

                    $attendanceRow[$key] = $status;
                }

                $matrix[$studentId] = [
                    'info' => $student,
                    'attendance' => $attendanceRow,
                    'stats' => $rowStats
                ];

                // Global Stats Accumulation
                $stats['total_izin_sakit'] += ($rowStats['S'] + $rowStats['I']);
                $stats['total_alpha'] += $rowStats['A'];
                $totalHadir += $rowStats['H'];
            }

            // Calculate Average Attendance Percentage
            $totalPossibleAttendance = $students->count() * count($jurnals);
            if ($totalPossibleAttendance > 0) {
                $stats['avg_hadir'] = round(($totalHadir / $totalPossibleAttendance) * 100, 1);
            }
        }

        return view('admin.rekap-jurnal.index', compact(
            'kelas', 'mapels', 'students', 'headers', 'matrix', 'stats', 'isFiltered', 'request'
        ));
    }
}
