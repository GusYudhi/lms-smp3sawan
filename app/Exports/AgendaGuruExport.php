<?php

namespace App\Exports;

use App\Models\AgendaGuru;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class AgendaGuruExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $userId;
    protected $filters;
    private $rowNumber = 0;

    public const INDONESIAN_DAYS = [
        'Sunday'    => 'Minggu',
        'Monday'    => 'Senin',
        'Tuesday'   => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday'  => 'Kamis',
        'Friday'    => 'Jumat',
        'Saturday'  => 'Sabtu',
    ];

    public function __construct($userId, array $filters = [])
    {
        $this->userId = $userId;
        $this->filters = $filters;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = AgendaGuru::with(['jamMulai', 'jamSelesai'])
            ->where('user_id', $this->userId);

        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('materi', 'like', '%' . $search . '%')
                  ->orWhere('keterangan', 'like', '%' . $search . '%');
            });
        }

        if (!empty($this->filters['kelas'])) {
            if ($this->filters['kelas'] === 'non_kelas') {
                $query->whereNull('kelas');
            } else {
                $query->where('kelas', $this->filters['kelas']);
            }
        }

        if (!empty($this->filters['status'])) {
            $query->where('status_jurnal', $this->filters['status']);
        }

        if (!empty($this->filters['keterangan'])) {
            $query->where('keterangan', 'like', '%' . $this->filters['keterangan'] . '%');
        }

        return $query->orderBy('tanggal', 'desc')
            ->orderBy('jam_mulai_id', 'asc')
            ->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'No',
            'Tanggal',
            'Hari',
            'Jam Ke',
            'Waktu',
            'Kelas',
            'Materi / Aktivitas Pembelajaran',
            'Status Jurnal',
            'Keterangan',
        ];
    }

    /**
     * @param AgendaGuru $agenda
     * @return array
     */
    public function map($agenda): array
    {
        $this->rowNumber++;

        $dateCarbon = Carbon::parse($agenda->tanggal);
        $dayEnglish = $dateCarbon->format('l');
        $dayName = self::INDONESIAN_DAYS[$dayEnglish] ?? $dayEnglish;

        $jamKe = $agenda->jamMulai ? $agenda->jamMulai->jam_ke : '-';
        if ($agenda->jamSelesai && $agenda->jamSelesai->id !== $agenda->jamMulai?->id) {
            $jamKe .= ' - ' . $agenda->jamSelesai->jam_ke;
        }

        $waktu = '-';
        if ($agenda->jamMulai) {
            $waktu = Carbon::parse($agenda->jamMulai->waktu_mulai)->format('H:i');
            if ($agenda->jamSelesai) {
                $waktu .= ' - ' . Carbon::parse($agenda->jamSelesai->waktu_selesai)->format('H:i');
            }
        }

        return [
            $this->rowNumber,
            $dateCarbon->format('d/m/Y'),
            $dayName,
            $jamKe,
            $waktu,
            $agenda->kelas ?: 'Non-Kelas',
            $agenda->materi,
            $agenda->status_jurnal === 'selesai' ? 'Selesai' : 'Belum Selesai',
            $agenda->keterangan ?: '-',
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '1B6EC2'],
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }
}

