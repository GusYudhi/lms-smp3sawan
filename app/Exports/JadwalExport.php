<?php

namespace App\Exports;

use App\Models\Kelas;
use App\Models\JadwalPelajaran;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class JadwalExport implements FromCollection, WithHeadings, WithTitle, WithStyles
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
        
        // Get all classes
        $classes = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();
        
        // Eager load schedules
        $schedules = JadwalPelajaran::with(['mataPelajaran', 'guru.guruProfile'])
            ->where('semester_id', $this->semesterId)
            ->get()
            ->groupBy(['hari', 'jam_ke', 'kelas_id']);

        foreach ($days as $day) {
            for ($jam = 1; $jam <= 10; $jam++) {
                $row = [
                    $jam === 1 ? $day : '',
                    $jam,
                    '07.00 - 07.40', // Placeholder
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
        $classes = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();
        
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
        return 'JADWAL_PELAJARAN';
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