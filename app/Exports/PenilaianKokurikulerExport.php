<?php

namespace App\Exports;

use App\Models\FixedSchedule;
use App\Models\Kelas;
use App\Models\PenilaianKokurikuler;
use App\Models\Semester;
use App\Models\StudentProfile;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class PenilaianKokurikulerExport implements WithMultipleSheets
{
    protected Kelas $kelas;
    protected Semester $semester;
    protected string $bulan;
    protected string $hari;

    public function __construct(Kelas $kelas, Semester $semester, string $bulan = 'all', string $hari = 'all')
    {
        $this->kelas = $kelas;
        $this->semester = $semester;
        $this->bulan = $bulan;
        $this->hari = $hari;
    }

    public function sheets(): array
    {
        $students = StudentProfile::with('user')
            ->where('kelas_id', $this->kelas->id)
            ->where('is_active', true)
            ->orderBy('nis')
            ->get();

        $schedQuery = FixedSchedule::where('semester_id', $this->semester->id)
            ->kokurikuler()
            ->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu')")
            ->orderBy('jam_ke');

        if ($this->hari !== 'all') {
            $schedQuery->where('hari', $this->hari);
        }

        $kokurikulerSchedules = $schedQuery->get();

        $penilaianQuery = PenilaianKokurikuler::where('semester_id', $this->semester->id)
            ->where('kelas_id', $this->kelas->id);

        if ($this->bulan !== 'all' && is_numeric($this->bulan)) {
            $penilaianQuery->whereMonth('tanggal', (int) $this->bulan);
        }

        if ($this->hari !== 'all') {
            $penilaianQuery->where('hari', $this->hari);
        }

        $penilaians = $penilaianQuery->orderBy('tanggal')->get();

        return [
            new PenilaianKokurikulerRekapSheet(
                $this->kelas,
                $this->semester,
                $students,
                $kokurikulerSchedules,
                $penilaians,
                $this->bulan,
                $this->hari
            ),
            new PenilaianKokurikulerHarianSheet(
                $this->kelas,
                $this->semester,
                $students,
                $penilaians,
                $this->bulan,
                $this->hari
            ),
        ];
    }
}
