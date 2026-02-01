<?php

namespace App\Exports;

use App\Models\GuruProfile;
use App\Models\MataPelajaran;
use App\Models\Kelas;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class JadwalTemplateExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new InputJadwalSheet(),
            new KodeGuruSheet(),
            new KodeMapelSheet(),
            new DaftarKelasSheet(),
        ];
    }
}

class InputJadwalSheet implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    public function collection()
    {
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $rows = [];
        
        // Get all classes to determine width
        $classes = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();
        $classCount = $classes->count();

        foreach ($days as $day) {
            for ($jam = 1; $jam <= 7; $jam++) {
                $row = [
                    $jam === 1 ? $day : '', // Only show day on first row (visual merge)
                    $jam,
                    '07.30 - 08.10', // Default example time
                ];
                
                // Fill empty slots for classes
                for ($i = 0; $i < $classCount; $i++) {
                    $row[] = ''; 
                }
                
                // Example data for first row
                if ($day === 'Senin' && $jam === 1) {
                    // Overwrite UPACARA for all classes? Or just example
                    for ($k = 3; $k < count($row); $k++) {
                        $row[$k] = 'UPACARA';
                    }
                }
                
                if ($day === 'Senin' && $jam === 2) {
                    $row[3] = 'MAT AHM'; // Example: Mapel MAT, Guru AHM
                }

                $rows[] = $row;
            }
        }

        return collect($rows);
    }

    public function headings(): array
    {
        // Dynamic Headers based on Classes
        $headers = ['HARI', 'JAM KE', 'WAKTU'];
        $classes = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();
        
        foreach ($classes as $kelas) {
            $headers[] = $kelas->tingkat . $kelas->nama_kelas;
        }

        return [
            ['JADWAL PELAJARAN (Format: KODE_MAPEL [Spasi] KODE_GURU)'], // Info Row
            $headers // Actual Header
        ];
    }

    public function title(): string
    {
        return 'INPUT_JADWAL';
    }

    public function styles(Worksheet $sheet)
    {
        // Merge Info Row
        $lastCol = $sheet->getHighestColumn();
        $sheet->mergeCells('A1:' . $lastCol . '1');
        
        // Style Header
        $sheet->getStyle('A2:' . $lastCol . '2')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        ]);

        // Center align HARI and JAM
        $sheet->getStyle('A:B')->getAlignment()->setHorizontal('center')->setVertical('center');
        
        // Border for grid
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle('A2:' . $lastCol . $lastRow)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        return [];
    }
}

class KodeGuruSheet implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    public function collection()
    {
        return GuruProfile::with('user')
            ->select('kode_guru', 'nama')
            ->whereNotNull('kode_guru')
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
    public function collection()
    {
        return MataPelajaran::select('kode_mapel', 'nama_mapel')->get();
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
    public function collection()
    {
        return Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get()->map(function ($kelas) {
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