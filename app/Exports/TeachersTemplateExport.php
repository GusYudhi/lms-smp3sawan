<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class TeachersTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths, WithColumnFormatting
{
    /**
     * @return array
     */
    public function array(): array
    {
        // Return sample data rows
        return [
            [
                'Dr. Ahmad Hidayat, M.Pd',
                '198501152010011001',
                'ahmad.hidayat@smp3sawan.sch.id',
                '081234567890',
                'L',
                'Sawan',
                '1985-01-15',
                'PNS',
                'IV/a',
                'Matematika',
                '7A'
            ],
            [
                'Siti Nurhaliza, S.Pd',
                '199003102015032002',
                'siti.nurhaliza@smp3sawan.sch.id',
                '082345678901',
                'P',
                'Singaraja',
                '1990-03-10',
                'PNS',
                'III/c',
                'Bahasa Indonesia',
                '8B'
            ],
            [
                'Made Suryawan, S.Pd',
                '3210567890123456',
                'made.suryawan@smp3sawan.sch.id',
                '083456789012',
                'L',
                'Denpasar',
                '1988-07-22',
                'Honorer',
                '',
                'Pendidikan Jasmani',
                ''
            ],
        ];
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'NAMA LENGKAP',
            'NIP/NIK',
            'EMAIL',
            'TELEPON',
            'JENIS KELAMIN',
            'TEMPAT LAHIR',
            'TANGGAL LAHIR',
            'STATUS KEPEGAWAIAN',
            'GOLONGAN',
            'MATA PELAJARAN',
            'WALI KELAS'
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold header
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4']
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ],
            // Add borders to all cells
            'A1:K100' => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    public function columnWidths(): array
    {
        return [
            'A' => 30,  // NAMA LENGKAP
            'B' => 20,  // NIP/NIK
            'C' => 35,  // EMAIL
            'D' => 15,  // TELEPON
            'E' => 15,  // JENIS KELAMIN
            'F' => 20,  // TEMPAT LAHIR
            'G' => 15,  // TANGGAL LAHIR
            'H' => 20,  // STATUS KEPEGAWAIAN
            'I' => 12,  // GOLONGAN
            'J' => 30,  // MATA PELAJARAN
            'K' => 12,  // WALI KELAS
        ];
    }

    /**
     * @return array
     */
    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_TEXT, // NIP/NIK as text
            'D' => NumberFormat::FORMAT_TEXT, // TELEPON as text
            'G' => NumberFormat::FORMAT_DATE_XLSX14, // TANGGAL LAHIR as date
        ];
    }
}
