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
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PenilaianKokurikulerHarianSheet implements FromCollection, WithTitle, WithHeadings, WithStyles, WithColumnWidths
{
    protected Kelas $kelas;
    protected Semester $semester;
    protected Collection $students;
    protected Collection $penilaians;
    protected Collection $sessions;

    public function __construct(
        Kelas $kelas,
        Semester $semester,
        Collection $students,
        Collection $penilaians
    ) {
        $this->kelas = $kelas;
        $this->semester = $semester;
        $this->students = $students;
        $this->penilaians = $penilaians;

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
        return 'Rincian Harian ' . $this->kelas->full_name;
    }

    public function headings(): array
    {
        $sessionHeaders = [];
        foreach ($this->sessions as $s) {
            $formattedDate = Carbon::parse($s['tanggal'])->format('d/m/Y');
            $sessionHeaders[] = "{$s['hari']}, {$formattedDate} ({$s['tema']})";
        }

        return [
            ['RINCIAN HARIAN PENILAIAN KOKURIKULER KELAS ' . strtoupper($this->kelas->full_name)],
            ['Semester: ' . ($this->semester->tahunPelajaran->nama ?? '') . ' - ' . $this->semester->nama],
            [],
            array_merge(['No', 'NIS', 'Nama Siswa'], $sessionHeaders),
        ];
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
                if (!$score) {
                    $row[] = '-';
                } else {
                    $cell = $score->predikat;
                    if (!empty($score->catatan)) {
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
        return [
            'A' => 6,
            'B' => 14,
            'C' => 32,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $highestColumn = $sheet->getHighestColumn();
        $highestRow = $sheet->getHighestRow();

        $sheet->mergeCells("A1:{$highestColumn}1");
        $sheet->mergeCells("A2:{$highestColumn}2");

        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(13);
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(11);

        $sheet->getStyle("A4:{$highestColumn}4")->applyFromArray([
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

        if ($highestRow >= 4) {
            $sheet->getStyle("A4:{$highestColumn}{$highestRow}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC'],
                    ],
                ],
            ]);
        }

        return [];
    }
}

