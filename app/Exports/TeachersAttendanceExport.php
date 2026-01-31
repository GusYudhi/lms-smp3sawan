<?php

namespace App\Exports;

use App\Models\GuruAttendance;
use App\Models\GuruProfile;
use App\Models\User;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class TeachersAttendanceExport implements FromCollection, WithTitle, WithHeadings, WithStyles, WithColumnWidths
{
    protected $months;
    protected $dates;
    protected $year;
    protected $monthRanges;

    /**
     * Constructor
     *
     * @param array $months Array of months ([7, 8] for Juli-Agustus or [1,2,3,4,5,6] for Januari-Juni)
     */
    public function __construct($months)
    {
        $this->months = $months;
        $this->dates = $this->generateDates();
        $this->year = $this->getYear();
    }

    /**
     * Generate dates for selected months (only school days: Monday-Saturday)
     */
    protected function generateDates()
    {
        $dates = [];

        foreach ($this->months as $month) {
            $year = $this->getYearForMonth($month);
            $startDate = Carbon::create($year, $month, 1);
            $endDate = $startDate->copy()->endOfMonth();

            for ($date = $startDate->copy(); $date <= $endDate; $date->addDay()) {
                // Skip Sunday (0)
                if ($date->dayOfWeek != 0) {
                    $dates[] = $date->copy();
                }
            }
        }

        return $dates;
    }

    /**
     * Get year for month (handle semester transition)
     */
    protected function getYearForMonth($month)
    {
        // Bulan 7-12 (Juli-Desember) = tahun sekarang
        // Bulan 1-6 (Januari-Juni) = tahun depan
        $currentYear = Carbon::now()->year;

        if ($month >= 7) {
            return $currentYear;
        } else {
            return $currentYear + 1;
        }
    }

    /**
     * Get year range for display
     */
    protected function getYear()
    {
        if (count($this->months) == 0) return Carbon::now()->year;

        $firstMonth = $this->months[0];
        $lastMonth = end($this->months);

        $firstYear = $this->getYearForMonth($firstMonth);
        $lastYear = $this->getYearForMonth($lastMonth);

        if ($firstYear == $lastYear) {
            return $firstYear;
        } else {
            return $firstYear . '/' . $lastYear;
        }
    }

    /**
     * Return collection of data
     */
    public function collection()
    {
        // Get all active teachers (guru only, not kepala sekolah or admin)
        $teachers = User::with('guruProfile')
            ->where('role', 'guru')
            ->whereHas('guruProfile', function($query) {
                $query->where('is_active', true);
            })
            ->orderBy('name', 'asc')
            ->get();

        $data = [];
        $no = 1;

        foreach ($teachers as $teacher) {
            $guruProfile = $teacher->guruProfile;

            // Get NIP or NIK
            $nipNik = $guruProfile->nip ?? '-';

            // Get mata pelajaran (Relation)
            $mataPelajaran = $guruProfile->mataPelajaran ? $guruProfile->mataPelajaran->nama_mapel : '-';

            $row = [
                $no++,
                $nipNik,
                $teacher->name ?? '-',
                $guruProfile->jenis_kelamin == 'laki-laki' ? 'L' : 'P',
                $mataPelajaran,
            ];

            // Add attendance status for each date
            foreach ($this->dates as $date) {
                $attendance = GuruAttendance::where('user_id', $teacher->id)
                    ->whereDate('tanggal', $date->format('Y-m-d'))
                    ->first();

                if ($attendance) {
                    // Convert status to code
                    $status = $this->getStatusCode($attendance->status);
                } else {
                    $status = '-';
                }

                $row[] = $status;
            }

            $data[] = $row;
        }

        return collect($data);
    }

    /**
     * Convert status to single letter code
     */
    protected function getStatusCode($status)
    {
        $codes = [
            'hadir' => 'H',
            'sakit' => 'S',
            'izin' => 'I',
            'alpha' => 'A',
            'alpa' => 'A',
            'terlambat' => 'H', // Terlambat tetap dihitung hadir
        ];

        return $codes[strtolower($status)] ?? '-';
    }

    /**
     * Return sheet title
     */
    public function title(): string
    {
        return 'Rekap Absensi Guru';
    }

    /**
     * Return headings
     */
    public function headings(): array
    {
        // Buat header untuk bulan (merged header)
        $monthHeaders = [];
        $currentMonth = null;
        $monthStartCol = 6; // Start after column E (NOMOR, NIP/NIK, NAMA, L/P, MAPEL)
        $monthRanges = [];

        foreach ($this->dates as $index => $date) {
            $month = $date->format('F Y');

            if ($currentMonth !== $month) {
                if ($currentMonth !== null) {
                    $monthRanges[] = [
                        'month' => $currentMonth,
                        'start' => $monthStartCol,
                        'end' => $monthStartCol + count($monthHeaders) - 1
                    ];
                }
                $currentMonth = $month;
                $monthStartCol = 6 + $index;
                $monthHeaders = [];
            }

            $monthHeaders[] = $date->format('d/m');
        }

        // Add last month range
        if ($currentMonth !== null) {
            $monthRanges[] = [
                'month' => $currentMonth,
                'start' => $monthStartCol,
                'end' => $monthStartCol + count($monthHeaders) - 1
            ];
        }

        // Store month ranges for styling
        $this->monthRanges = $monthRanges;

        // Row 1: Month headers (will be merged)
        $row1 = ['', '', '', '', ''];
        foreach ($this->dates as $date) {
            $row1[] = $date->format('F Y');
        }

        // Row 2: Main headers
        $row2 = ['NOMOR', 'NIP/NIK', 'NAMA GURU', 'L/P', 'MATA PELAJARAN'];

        // Row 3: Date headers (Tanggal)
        $row3 = ['', '', '', '', 'Tanggal'];
        foreach ($this->dates as $date) {
            $row3[] = $date->format('d/m');
        }

        return [$row1, $row2, $row3];
    }

    /**
     * Apply styles to worksheet
     */
    public function styles(Worksheet $sheet)
    {
        $lastColumn = $this->getColumnLetter(5 + count($this->dates));
        $lastRow = $sheet->getHighestRow();

        // Merge month headers in row 1
        if (isset($this->monthRanges)) {
            foreach ($this->monthRanges as $range) {
                $startCol = $this->getColumnLetter($range['start']);
                $endCol = $this->getColumnLetter($range['end']);
                $sheet->mergeCells($startCol . '1:' . $endCol . '1');

                // Set month text
                $sheet->setCellValue($startCol . '1', $range['month']);
            }
        }

        // Merge cells for row 2
        $sheet->mergeCells('A1:A2'); // NOMOR
        $sheet->mergeCells('B1:B2'); // NIP/NIK
        $sheet->mergeCells('C1:C2'); // NAMA GURU
        $sheet->mergeCells('D1:D2'); // L/P
        $sheet->mergeCells('E1:E2'); // MATA PELAJARAN

        // Style header rows (1-3)
        $sheet->getStyle('A1:' . $lastColumn . '3')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 11,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E8F5E9'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);

        // Style month headers (row 1) with larger font
        $sheet->getStyle('A1:' . $lastColumn . '1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 14,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4CAF50'],
            ],
            'font' => [
                'bold' => true,
                'size' => 14,
                'color' => ['rgb' => 'FFFFFF'],
            ],
        ]);

        // Style data rows
        if ($lastRow > 3) {
            $sheet->getStyle('A4:' . $lastColumn . $lastRow)->applyFromArray([
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ],
            ]);

            // Left align for name and mapel column
            $sheet->getStyle('C4:C' . $lastRow)->applyFromArray([
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                ],
            ]);
            $sheet->getStyle('E4:E' . $lastRow)->applyFromArray([
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                ],
            ]);
        }

        // Set row heights
        $sheet->getRowDimension(1)->setRowHeight(30);
        $sheet->getRowDimension(2)->setRowHeight(25);
        $sheet->getRowDimension(3)->setRowHeight(25);

        return [];
    }

    /**
     * Set column widths
     */
    public function columnWidths(): array
    {
        $widths = [
            'A' => 8,  // NOMOR
            'B' => 15, // NIP/NIK
            'C' => 30, // NAMA GURU
            'D' => 6,  // L/P
            'E' => 25, // MATA PELAJARAN
        ];

        // Set width for date columns
        $column = 'F';
        foreach ($this->dates as $date) {
            $widths[$column] = 8;
            $column++;
        }

        return $widths;
    }

    /**
     * Get column letter from number (1=A, 2=B, etc.)
     */
    protected function getColumnLetter($columnNumber)
    {
        $letter = '';
        while ($columnNumber > 0) {
            $temp = ($columnNumber - 1) % 26;
            $letter = chr($temp + 65) . $letter;
            $columnNumber = ($columnNumber - $temp - 1) / 26;
        }
        return $letter;
    }
}
