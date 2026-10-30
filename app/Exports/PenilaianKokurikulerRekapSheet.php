<?php

namespace App\Exports;

use App\Models\Kelas;
use App\Models\PenilaianKokurikuler;
use App\Models\Semester;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PenilaianKokurikulerRekapSheet implements FromCollection, WithColumnWidths, WithHeadings, WithStyles, WithTitle
{
    protected Kelas $kelas;

    protected Semester $semester;

    protected Collection $students;

    protected Collection $kokurikulerSchedules;

    protected Collection $penilaians;

    protected string $bulan;

    protected string $hari;

    public function __construct(
        Kelas $kelas,
        Semester $semester,
        Collection $students,
        Collection $kokurikulerSchedules,
        Collection $penilaians,
        string $bulan = 'all',
        string $hari = 'all'
    ) {
        $this->kelas = $kelas;
        $this->semester = $semester;
        $this->students = $students;
        $this->kokurikulerSchedules = $kokurikulerSchedules;
        $this->penilaians = $penilaians;
        $this->bulan = $bulan;
        $this->hari = $hari;
    }

    public function title(): string
    {
        return 'Rekap Tema '.$this->kelas->full_name;
    }

    protected function getScheduleItems(): Collection
    {
        if ($this->kokurikulerSchedules->isNotEmpty()) {
            return $this->kokurikulerSchedules->map(fn ($ks) => [
                'hari' => $ks->hari,
                'keterangan' => $ks->keterangan,
            ])->values();
        }

        $fallbackDays = ($this->hari !== 'all') ? [$this->hari] : ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];

        return collect($fallbackDays)->map(fn ($h) => [
            'hari' => $h,
            'keterangan' => $h === 'Senin' ? 'Pembiasaan Upacara Bendera' : "Tema {$h}",
        ]);
    }

    public function headings(): array
    {
        $scheduleItems = $this->getScheduleItems();
        $dayHeaders = $scheduleItems->pluck('hari')->all();

        $filterInfo = [];
        if ($this->bulan !== 'all' && isset(\App\Http\Controllers\Guru\PenilaianKokurikulerController::INDONESIAN_MONTHS[(int) $this->bulan])) {
            $filterInfo[] = 'Bulan: '.\App\Http\Controllers\Guru\PenilaianKokurikulerController::INDONESIAN_MONTHS[(int) $this->bulan];
        }
        if ($this->hari !== 'all') {
            $filterInfo[] = 'Hari: '.$this->hari;
        }
        $filterSuffix = ! empty($filterInfo) ? ' | Filter: '.implode(', ', $filterInfo) : '';

        $rows = [
            ['REKAP PENILAIAN KOKURIKULER KELAS '.strtoupper($this->kelas->full_name)],
            ['Semester: '.($this->semester->tahunPelajaran->nama ?? '').' - '.$this->semester->nama.$filterSuffix],
            ['Keterangan Rubrik: B = Berkembang | SB = Sedang Berkembang | BSH = Berkembang Sesuai Harapan | SAB = Sangat Berkembang'],
            [],
            ['Daftar Kegiatan Kokurikuler:'],
        ];

        foreach ($scheduleItems as $item) {
            $rows[] = ["• {$item['hari']} : {$item['keterangan']}"];
        }

        $rows[] = [];
        $rows[] = array_merge(
            ['No', 'NIS', 'NISN', 'Nama Siswa'],
            $dayHeaders,
            ['Total Dinilai', 'Predikat Dominan']
        );

        return $rows;
    }

    public function collection(): Collection
    {
        $rows = collect();
        $daysList = $this->getScheduleItems()->pluck('hari')->all();

        foreach ($this->students as $index => $student) {
            $studentScores = $this->penilaians
                ->where('student_user_id', $student->user_id)
                ->whereNotNull('predikat')
                ->where('predikat', '!=', '');

            $row = [
                $index + 1,
                $student->nis ?? '-',
                $student->nisn ?? '-',
                $student->user->name ?? '-',
            ];

            foreach ($daysList as $hari) {
                $dayScores = $studentScores->where('hari', $hari);
                if ($dayScores->isEmpty()) {
                    $row[] = '-';
                } else {
                    $dominant = $this->getDominantPredikat($dayScores);
                    $row[] = "{$dominant} ({$dayScores->count()}x)";
                }
            }

            $totalCount = $studentScores->count();
            $overallDominant = $totalCount > 0 ? $this->getDominantPredikat($studentScores) : '-';
            $overallLabel = isset(PenilaianKokurikuler::PREDIKAT_LABELS[$overallDominant])
                ? "{$overallDominant} (".PenilaianKokurikuler::PREDIKAT_LABELS[$overallDominant].')'
                : '-';

            $row[] = $totalCount;
            $row[] = $overallLabel;

            $rows->push($row);
        }

        return $rows;
    }

    protected function getDominantPredikat(Collection $scores): string
    {
        $weights = ['B' => 1, 'SB' => 2, 'BSH' => 3, 'SAB' => 4];
        $counts = $scores->groupBy('predikat')->map->count();

        $bestPredikat = 'BSH';
        $maxCount = -1;

        foreach ($counts as $predikat => $count) {
            if ($count > $maxCount || ($count === $maxCount && ($weights[$predikat] ?? 0) > ($weights[$bestPredikat] ?? 0))) {
                $maxCount = $count;
                $bestPredikat = $predikat;
            }
        }

        return $bestPredikat;
    }

    public function columnWidths(): array
    {
        $widths = [
            'A' => 6,
            'B' => 14,
            'C' => 16,
            'D' => 30,
        ];

        $dayCount = $this->getScheduleItems()->count();
        $colIndex = 5; // Column E

        for ($i = 0; $i < $dayCount; $i++) {
            $colLetter = Coordinate::stringFromColumnIndex($colIndex++);
            $widths[$colLetter] = 14;
        }

        $totalCol = Coordinate::stringFromColumnIndex($colIndex++);
        $dominantCol = Coordinate::stringFromColumnIndex($colIndex);

        $widths[$totalCol] = 15;
        $widths[$dominantCol] = 30;

        return $widths;
    }

    public function styles(Worksheet $sheet)
    {
        $scheduleCount = $this->getScheduleItems()->count();
        $tableHeaderRow = 7 + $scheduleCount;

        $highestColumn = $sheet->getHighestColumn();
        $highestRow = $sheet->getHighestRow();
        $mergeEndCol = Coordinate::ColumnIndexFromString($highestColumn) < 8 ? 'H' : $highestColumn;

        $sheet->mergeCells("A1:{$mergeEndCol}1");
        $sheet->mergeCells("A2:{$mergeEndCol}2");
        $sheet->mergeCells("A3:{$mergeEndCol}3");
        $sheet->mergeCells("A5:{$mergeEndCol}5");

        for ($r = 6; $r < 6 + $scheduleCount; $r++) {
            $sheet->mergeCells("A{$r}:{$mergeEndCol}{$r}");
            $sheet->getStyle("A{$r}")->getFont()->setSize(10);
        }

        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(13);
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('A3')->getFont()->setItalic(true)->setSize(10);
        $sheet->getStyle('A5')->getFont()->setBold(true)->setSize(10);

        $sheet->getStyle("A{$tableHeaderRow}:{$highestColumn}{$tableHeaderRow}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1E6B3A'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        if ($highestRow >= $tableHeaderRow) {
            $sheet->getStyle("A{$tableHeaderRow}:{$highestColumn}{$highestRow}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC'],
                    ],
                ],
            ]);

            if ($highestRow > $tableHeaderRow) {
                $dataStartRow = $tableHeaderRow + 1;
                $sheet->getStyle("A{$dataStartRow}:A{$highestRow}")
                    ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("E{$dataStartRow}:{$highestColumn}{$highestRow}")
                    ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }
        }

        return [];
    }
}
