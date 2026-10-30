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

    public function __construct(
        Kelas $kelas,
        Semester $semester,
        Collection $students,
        Collection $kokurikulerSchedules,
        Collection $penilaians
    ) {
        $this->kelas = $kelas;
        $this->semester = $semester;
        $this->students = $students;
        $this->kokurikulerSchedules = $kokurikulerSchedules;
        $this->penilaians = $penilaians;
    }

    public function title(): string
    {
        return 'Rekap Tema '.$this->kelas->full_name;
    }

    public function headings(): array
    {
        $temaHeaders = [];
        foreach ($this->kokurikulerSchedules as $ks) {
            $temaHeaders[] = "{$ks->hari} ({$ks->keterangan})";
        }

        if (empty($temaHeaders)) {
            foreach (['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'] as $hari) {
                $temaHeaders[] = "Tema {$hari}";
            }
        }

        return [
            ['REKAP PENILAIAN KOKURIKULER KELAS '.strtoupper($this->kelas->full_name)],
            ['Semester: '.($this->semester->tahunPelajaran->nama ?? '').' - '.$this->semester->nama],
            ['Keterangan Rubrik: B = Berkembang | SB = Sedang Berkembang | BSH = Berkembang Sesuai Harapan | SAB = Sangat Berkembang'],
            [],
            array_merge(
                ['No', 'NIS', 'NISN', 'Nama Siswa'],
                $temaHeaders,
                ['Total Pertemuan Dinilai', 'Predikat Dominan']
            ),
        ];
    }

    public function collection(): Collection
    {
        $rows = collect();
        $daysList = $this->kokurikulerSchedules->isNotEmpty()
            ? $this->kokurikulerSchedules->pluck('hari')->all()
            : ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];

        foreach ($this->students as $index => $student) {
            $studentScores = $this->penilaians->where('student_user_id', $student->user_id);

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
                    $label = PenilaianKokurikuler::PREDIKAT_LABELS[$dominant] ?? $dominant;
                    $row[] = "{$dominant} ({$label}) - {$dayScores->count()}x";
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
        return [
            'A' => 6,
            'B' => 14,
            'C' => 16,
            'D' => 32,
            'E' => 28,
            'F' => 28,
            'G' => 28,
            'H' => 28,
            'I' => 22,
            'J' => 30,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $highestColumn = $sheet->getHighestColumn();
        $highestRow = $sheet->getHighestRow();

        $sheet->mergeCells("A1:{$highestColumn}1");
        $sheet->mergeCells("A2:{$highestColumn}2");
        $sheet->mergeCells("A3:{$highestColumn}3");

        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(13);
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('A3')->getFont()->setItalic(true)->setSize(10);

        $sheet->getStyle("A5:{$highestColumn}5")->applyFromArray([
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

        if ($highestRow >= 5) {
            $sheet->getStyle("A5:{$highestColumn}{$highestRow}")->applyFromArray([
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
