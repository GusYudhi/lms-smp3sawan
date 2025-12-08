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
                'L',
                'Sawan',
                '1985-01-15',
                '081234567890',
                'PNS',
                'IV/a',
                'Guru',
                'Matematika',
                '7A'
            ],
            [
                'Siti Nurhaliza, S.Pd',
                '199003102015032002',
                'P',
                'Singaraja',
                '1990-03-10',
                '082345678901',
                'PNS',
                'III/c',
                'Wakil Kepala Sekolah Kurikulum',
                'Bahasa Indonesia',
                '8B'
            ],
            [
                'Made Suryawan, S.Pd',
                '3210567890123456',
                'L',
                'Denpasar',
                '1988-07-22',
                '083456789012',
                'GTT',
                '',
                'Guru',
                'Pendidikan Jasmani',
                ''
            ],
            [
                'Ketut Suhendra, S.Pd',
                '',
                'L',
                'Buleleng',
                '1992-05-18',
                '084567890123',
                'PPPK',
                'III/a',
                'Kepala Sekolah',
                'IPA',
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
            'JENIS KELAMIN',
            'TEMPAT LAHIR',
            'TANGGAL LAHIR',
            'TELEPON',
            'STATUS KEPEGAWAIAN',
            'GOLONGAN',
            'JABATAN DI SEKOLAH',
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
            'C' => 15,  // JENIS KELAMIN
            'D' => 20,  // TEMPAT LAHIR
            'E' => 15,  // TANGGAL LAHIR
            'F' => 15,  // TELEPON
            'G' => 20,  // STATUS KEPEGAWAIAN
            'H' => 12,  // GOLONGAN
            'I' => 30,  // JABATAN DI SEKOLAH
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
            'E' => NumberFormat::FORMAT_DATE_XLSX14, // TANGGAL LAHIR as date
            'F' => NumberFormat::FORMAT_TEXT, // TELEPON as text
        ];
    }
}
