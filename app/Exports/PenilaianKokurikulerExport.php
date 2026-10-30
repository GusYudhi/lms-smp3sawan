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

    public function __construct(Kelas $kelas, Semester $semester)
    {
        $this->kelas = $kelas;
        $this->semester = $semester;
    }

    public function sheets(): array
    {
        $students = StudentProfile::with('user')
            ->where('kelas_id', $this->kelas->id)
            ->where('is_active', true)
            ->orderBy('nis')
            ->get();

        $kokurikulerSchedules = FixedSchedule::where('semester_id', $this->semester->id)
            ->kokurikuler()
            ->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu')")
            ->orderBy('jam_ke')
            ->get();

        $penilaians = PenilaianKokurikuler::where('semester_id', $this->semester->id)
            ->where('kelas_id', $this->kelas->id)
            ->orderBy('tanggal')
            ->get();

        return [
            new PenilaianKokurikulerRekapSheet($this->kelas, $this->semester, $students, $kokurikulerSchedules, $penilaians),
            new PenilaianKokurikulerHarianSheet($this->kelas, $this->semester, $students, $penilaians),
        ];
    }
}

