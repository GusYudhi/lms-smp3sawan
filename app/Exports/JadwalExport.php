<?php

namespace App\Exports;

use App\Models\Kelas;
use App\Models\JadwalPelajaran;
use App\Models\GuruProfile;
use App\Models\MataPelajaran;
use App\Models\Semester;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class JadwalExport implements WithMultipleSheets
{
    protected $semesterId;

    public function __construct($semesterId)
    {
        $this->semesterId = $semesterId;
    }

    public function sheets(): array
    {
        return [
            new JadwalDataSheet($this->semesterId),
            new KodeGuruSheet($this->semesterId),
            new KodeMapelSheet($this->semesterId),
            new DaftarKelasSheet($this->semesterId),
        ];
    }
}

class JadwalDataSheet implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    protected $semesterId;

    public function __construct($semesterId)
    {
        $this->semesterId = $semesterId;
    }

    public function collection()
    {
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $rows = [];
        
        // Get semester to find relevant classes
        $semester = Semester::find($this->semesterId);
        $tahunPelajaranId = $semester ? $semester->tahun_pelajaran_id : null;

        // Get classes for this academic year
        $classes = Kelas::orderBy('tingkat')->orderBy('nama_kelas');
        if ($tahunPelajaranId) {
            $classes->where('tahun_pelajaran_id', $tahunPelajaranId);
        }
        $classes = $classes->get();
        
        // Eager load schedules
        $schedules = JadwalPelajaran::with(['mataPelajaran', 'guru.guruProfile'])
            ->where('semester_id', $this->semesterId)
            ->get()
            ->groupBy(['hari', 'jam_ke', 'kelas_id']);

        // Get Jam Pelajaran details to populate WAKTU
        $jamPelajarans = \App\Models\JamPelajaran::where('semester_id', $this->semesterId)
            ->get()
            ->keyBy('jam_ke');

        foreach ($days as $day) {
            for ($jam = 1; $jam <= 10; $jam++) {
                $waktu = '';
                if (isset($jamPelajarans[$jam])) {
                    $jp = $jamPelajarans[$jam];
                    $waktu = substr($jp->jam_mulai, 0, 5) . ' - ' . substr($jp->jam_selesai, 0, 5);
                } else {
                    $waktu = '00:00 - 00:00';
                }

                $row = [
                    $jam === 1 ? $day : '', // Merge visual
                    $jam,
                    $waktu, 
                ];
                
                foreach ($classes as $kelas) {
                    $cellValue = '';
                    if (isset($schedules[$day][$jam][$kelas->id])) {
                        $schedule = $schedules[$day][$jam][$kelas->id]->first();
                        $mapelCode = $schedule->mataPelajaran->kode_mapel ?? '?';
                        $guruCode = $schedule->guru->guruProfile->kode_guru ?? '?';
                        $cellValue = "$mapelCode $guruCode";
                    }
                    $row[] = $cellValue;
                }
                
                $rows[] = $row;
            }
        }

        return collect($rows);
    }

    public function headings(): array
    {
        $headers = ['HARI', 'JAM KE', 'WAKTU'];
        
        $semester = Semester::find($this->semesterId);
        $tahunPelajaranId = $semester ? $semester->tahun_pelajaran_id : null;

        $classes = Kelas::orderBy('tingkat')->orderBy('nama_kelas');
        if ($tahunPelajaranId) {
            $classes->where('tahun_pelajaran_id', $tahunPelajaranId);
        }
        $classes = $classes->get();
        
        foreach ($classes as $kelas) {
            $headers[] = $kelas->tingkat . $kelas->nama_kelas;
        }

        return [
            ['JADWAL PELAJARAN SEMESTER INI'],
            $headers
        ];
    }

    public function title(): string
    {
        return 'INPUT_JADWAL';
    }

    public function styles(Worksheet $sheet)
    {
        $lastCol = $sheet->getHighestColumn();
        $sheet->mergeCells('A1:' . $lastCol . '1');
        
        $sheet->getStyle('A2:' . $lastCol . '2')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        ]);

        $sheet->getStyle('A:B')->getAlignment()->setHorizontal('center')->setVertical('center');
        
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle('A2:' . $lastCol . $lastRow)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        return [];
    }
}

class KodeGuruSheet implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    protected $semesterId;

    public function __construct($semesterId)
    {
        $this->semesterId = $semesterId;
    }

    public function collection()
    {
        // Sort by kode_guru numerically
        return GuruProfile::with('user')
            ->where('is_active', true)
            ->whereNotNull('kode_guru')
            ->select('kode_guru', 'nama')
            ->orderByRaw('CAST(kode_guru AS UNSIGNED) ASC')
            ->orderBy('kode_guru', 'asc')
            ->get()
            ->map(function ($guru) {
                return [
                    'kode' => $guru->kode_guru,
                    'nama' => $guru->nama
                ];
            });
    }

    public function headings(): array
    {
        return ['KODE GURU', 'NAMA GURU'];
    }

    public function title(): string
    {
        return 'KODE_GURU';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'ED7D31']],
            ],
        ];
    }
}

class KodeMapelSheet implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    protected $semesterId;

    public function __construct($semesterId)
    {
        $this->semesterId = $semesterId;
    }

    public function collection()
    {
        return MataPelajaran::where('semester_id', $this->semesterId)
            ->orWhereNull('semester_id')
            ->select('kode_mapel', 'nama_mapel')
            ->get();
    }

    public function headings(): array
    {
        return ['KODE MAPEL', 'NAMA MAPEL'];
    }

    public function title(): string
    {
        return 'KODE_MAPEL';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '70AD47']],
            ],
        ];
    }
}

class DaftarKelasSheet implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    protected $semesterId;

    public function __construct($semesterId)
    {
        $this->semesterId = $semesterId;
    }

    public function collection()
    {
        $semester = Semester::find($this->semesterId);
        $tahunPelajaranId = $semester ? $semester->tahun_pelajaran_id : null;

        $query = Kelas::orderBy('tingkat')->orderBy('nama_kelas');
        
        if ($tahunPelajaranId) {
            $query->where('tahun_pelajaran_id', $tahunPelajaranId);
        }

        return $query->get()->map(function ($kelas) {
            return [
                'nama_lengkap' => $kelas->tingkat . $kelas->nama_kelas
            ];
        });
    }

    public function headings(): array
    {
        return ['DAFTAR KELAS'];
    }

    public function title(): string
    {
        return 'DAFTAR_KELAS';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'A5A5A5']],
            ],
        ];
    }
}