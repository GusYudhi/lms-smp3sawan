<?php

namespace App\Exports;

use App\Models\Attendance;
use App\Models\Kelas;
use App\Models\StudentProfile;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class StudentsAttendanceExport implements WithMultipleSheets
{
    protected $tingkatKelas;
    protected $months;
    protected $tahunPelajaranId;

    /**
     * Constructor
     *
     * @param array $tingkatKelas Array of tingkat (7, 8, 9)
     * @param array $months Array of months ([7, 8] for Juli-Agustus or [1,2,3,4,5,6] for Januari-Juni)
     * @param int $tahunPelajaranId ID tahun pelajaran yang aktif
     */
    public function __construct($tingkatKelas, $months, $tahunPelajaranId)
    {
        $this->tingkatKelas = $tingkatKelas;
        $this->months = $months;
        $this->tahunPelajaranId = $tahunPelajaranId;
    }

    /**
     * Return array of sheets
     */
    public function sheets(): array
    {
        $sheets = [];

        // Get all kelas based on tingkat yang dipilih
        $kelasList = Kelas::whereIn('tingkat', $this->tingkatKelas)
            ->where('tahun_pelajaran_id', $this->tahunPelajaranId)
            ->orderBy('tingkat', 'asc')
            ->orderBy('nama_kelas', 'asc')
            ->get();

        // Buat sheet untuk setiap kelas
        foreach ($kelasList as $kelas) {
            $sheets[] = new StudentsAttendanceSheet($kelas, $this->months);
        }

        return $sheets;
    }
}
