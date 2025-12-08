<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class StudentsTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnFormatting
{
    /**
     * @return array
     */
    public function array(): array
    {
        return [
            [
                'Ahmad Budi Santoso',
                '2024001',
                '0012345678901',
                'Jakarta',
                '2010-05-15',
                'L',
                '7',
                'A',
                '2024',
                'Kosongkan',
                'Kosongkan'
            ],
            [
                'Siti Nurhaliza',
                '2024002',
                '0012345678902',
                'Bandung',
                '2010-08-22',
                'P',
                '7',
                'B',
                '2024',
                'Kosongkan',
                'Kosongkan'
            ]
        ];
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'NAMA',
            'NIS',
            'NISN',
            'TEMPAT LAHIR',
            'TANGGAL LAHIR',
            'JENIS KELAMIN',
            'TINGKAT',
            'KELAS',
            'TAHUN ANGKATAN',
            'EMAIL',
            'PASWORD'
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        // Format NIS and NISN columns as text to prevent Excel from converting to numbers
        $sheet->getStyle('B:C')->getNumberFormat()->setFormatCode('@');

        return [
            // Style the first row as header
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'color' => ['rgb' => '4472C4']]],
        ];
    }

    /**
     * @return array
     */
    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_TEXT, // NIS as text
            'C' => NumberFormat::FORMAT_TEXT, // NISN as text
            'G' => NumberFormat::FORMAT_TEXT, // TINGKAT as text
            'H' => NumberFormat::FORMAT_TEXT, // KELAS as text
            'I' => NumberFormat::FORMAT_TEXT, // TAHUN ANGKATAN as text
        ];
    }
}
