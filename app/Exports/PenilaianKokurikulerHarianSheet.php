<?php

namespace App\Exports;

use App\Models\Kelas;
use App\Models\Semester;
use Carbon\Carbon;
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

class PenilaianKokurikulerHarianSheet implements FromCollection, WithColumnWidths, WithHeadings, WithStyles, WithTitle
{
    protected Kelas $kelas;

    protected Semester $semester;

    protected Collection $students;

    protected Collection $kokurikulerSchedules;

    protected Collection $penilaians;

    protected Collection $sessions;

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

        // Unique dates & themes sorted chronologically
        $this->sessions = $penilaians
            ->groupBy(fn ($item) => Carbon::parse($item->tanggal)->format('Y-m-d'))
            ->map(function ($group, $dateStr) {
                $first = $group->first();

                return [
                    'tanggal' => $dateStr,
                    'hari' => $first->hari,
                    'tema' => $first->tema,
                ];
            })
            ->sortKeys()
            ->values();
    }

    public function title(): string
    {
        return 'Rincian Harian '.$this->kelas->full_name;
    }

    protected function getScheduleItems(): Collection
    {
        if ($this->kokurikulerSchedules->isNotEmpty()) {
            return $this->kokurikulerSchedules->map(fn ($ks) => [
                'hari' => $ks->hari,
                'keterangan' => $ks->keterangan,
            ])->values();
        }

        if ($this->sessions->isNotEmpty()) {
            return $this->sessions
                ->unique(fn ($s) => $s['hari'].'|'.$s['tema'])
                ->map(fn ($s) => [
                    'hari' => $s['hari'],
                    'keterangan' => $s['tema'],
                ])
                ->values();
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

        $sessionHeaders = [];
        foreach ($this->sessions as $s) {
            $formattedDate = Carbon::parse($s['tanggal'])->format('d/m/Y');
            $sessionHeaders[] = "{$s['hari']}, {$formattedDate}";
        }

        $filterInfo = [];
        if ($this->bulan !== 'all' && isset(\App\Http\Controllers\Guru\PenilaianKokurikulerController::INDONESIAN_MONTHS[(int) $this->bulan])) {
            $filterInfo[] = 'Bulan: '.\App\Http\Controllers\Guru\PenilaianKokurikulerController::INDONESIAN_MONTHS[(int) $this->bulan];
        }
        if ($this->hari !== 'all') {
            $filterInfo[] = 'Hari: '.$this->hari;
        }
        $filterSuffix = ! empty($filterInfo) ? ' | Filter: '.implode(', ', $filterInfo) : '';

        $rows = [
            ['RINCIAN HARIAN PENILAIAN KOKURIKULER KELAS '.strtoupper($this->kelas->full_name)],
            ['Semester: '.($this->semester->tahunPelajaran->nama ?? '').' - '.$this->semester->nama.$filterSuffix],
            ['Keterangan Rubrik: B = Berkembang | SB = Sedang Berkembang | BSH = Berkembang Sesuai Harapan | SAB = Sangat Berkembang'],
            [],
            ['Daftar Kegiatan Kokurikuler:'],
        ];

        foreach ($scheduleItems as $item) {
            $rows[] = ["• {$item['hari']} : {$item['keterangan']}"];
        }

        $rows[] = [];
        $rows[] = array_merge(['No', 'NIS', 'Nama Siswa'], $sessionHeaders);

        return $rows;
    }

    public function collection(): Collection
    {
        $rows = collect();

        foreach ($this->students as $index => $student) {
            $studentScores = $this->penilaians
                ->where('student_user_id', $student->user_id)
                ->keyBy(fn ($item) => Carbon::parse($item->tanggal)->format('Y-m-d'));

            $row = [
                $index + 1,
                $student->nis ?? '-',
                $student->user->name ?? '-',
            ];

            foreach ($this->sessions as $s) {
                $score = $studentScores->get($s['tanggal']);
                if (! $score) {
                    $row[] = '-';
                } else {
                    if (! empty($score->predikat)) {
                        $cell = $score->predikat;
                    } else {
                        $cell = strtoupper($score->status_absensi ?? '-');
                    }
                    if (! empty($score->catatan)) {
                        $cell .= " ({$score->catatan})";
                    }
                    $row[] = $cell;
                }
            }

            $rows->push($row);
        }

        return $rows;
    }

    public function columnWidths(): array
    {
        $widths = [
            'A' => 6,
            'B' => 14,
            'C' => 30,
        ];

        $sessionCount = $this->sessions->count();
        $colIndex = 4; // Column D

        for ($i = 0; $i < $sessionCount; $i++) {
            $colLetter = Coordinate::stringFromColumnIndex($colIndex++);
            $widths[$colLetter] = 18;
        }

        return $widths;
    }

    public function styles(Worksheet $sheet)
    {
        $scheduleCount = $this->getScheduleItems()->count();
        $tableHeaderRow = 7 + $scheduleCount;

        $highestColumn = $sheet->getHighestColumn();
        $highestRow = $sheet->getHighestRow();
        $mergeEndCol = Coordinate::columnIndexFromString($highestColumn) < 8 ? 'H' : $highestColumn;

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
                'startColor' => ['rgb' => '0D6EFD'],
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
                if (Coordinate::columnIndexFromString($highestColumn) >= 4) {
                    $sheet->getStyle("D{$dataStartRow}:{$highestColumn}{$highestRow}")
                        ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }
            }
        }

        return [];
    }
}
