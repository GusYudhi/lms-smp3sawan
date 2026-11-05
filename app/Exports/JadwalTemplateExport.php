<?php

namespace App\Exports;

use App\Models\FixedSchedule;
use App\Models\GuruProfile;
use App\Models\JadwalPelajaran;
use App\Models\JamPelajaran;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Semester;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class JadwalTemplateExport implements WithMultipleSheets
{
    protected $semesterId;

    public function __construct($semesterId = null)
    {
        $this->semesterId = $semesterId ?: Semester::getActive()?->id;
    }

    public function sheets(): array
    {
        return [
            new InputJadwalSheet($this->semesterId),
            new TemplateKodeGuruSheet($this->semesterId),
            new TemplateKodeMapelSheet($this->semesterId),
            new TemplateDaftarKelasSheet($this->semesterId),
        ];
    }
}

class InputJadwalSheet implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    protected $semesterId;
    protected $fixedCells = [];

    public function __construct($semesterId = null)
    {
        $this->semesterId = $semesterId;
    }

    public function collection()
    {
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $rows = [];
        $this->fixedCells = [];

        $semester = $this->semesterId ? Semester::find($this->semesterId) : Semester::getActive();
        $tahunPelajaranId = $semester ? $semester->tahun_pelajaran_id : null;

        // Ambil kelas sesuai tahun pelajaran semester terkait
        $classesQuery = Kelas::orderBy('tingkat')->orderBy('nama_kelas');
        if ($tahunPelajaranId) {
            $classesQuery->where('tahun_pelajaran_id', $tahunPelajaranId);
        }
        $classes = $classesQuery->get();
        if ($classes->isEmpty()) {
            $classes = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();
        }

        // Ambil Jam Pelajaran & Fixed Schedule
        $jamPelajarans = $this->semesterId
            ? JamPelajaran::where('semester_id', $this->semesterId)->get()->keyBy('jam_ke')
            : collect();

        $fixedSchedules = $this->semesterId
            ? FixedSchedule::where('semester_id', $this->semesterId)->get()
            : collect();

        // Ambil jadwal pelajaran yang sudah terisi saat ini
        $schedules = $this->semesterId
            ? JadwalPelajaran::with(['mataPelajaran', 'guru.guruProfile'])
                ->where('semester_id', $this->semesterId)
                ->get()
                ->groupBy(['hari', 'jam_ke', 'kelas_id'])
            : collect();

        // Rentang Jam: Pastikan Jam ke-0 selalu terakomodasi (0 sampai max jam atau minimal 8)
        $existingJams = $jamPelajarans->keys()->merge($fixedSchedules->pluck('jam_ke'));
        $maxJam = $existingJams->isNotEmpty() ? max(8, $existingJams->max()) : 8;
        $slotNumbers = collect(range(0, $maxJam))->unique()->sort()->values();

        $currentRow = 3; // Baris 1: Info, Baris 2: Header, Baris 3: Data mulai

        foreach ($days as $day) {
            $isFirstJamOfDay = true;

            foreach ($slotNumbers as $jam) {
                // Tentukan teks waktu
                $waktu = '';
                if (isset($jamPelajarans[$jam])) {
                    $jp = $jamPelajarans[$jam];
                    $waktu = substr($jp->jam_mulai, 0, 5) . ' - ' . substr($jp->jam_selesai, 0, 5);
                } else {
                    if ($jam === 0) {
                        $waktu = '07:00 - 07:30';
                    } else {
                        $waktu = '07:30 - 08:15';
                    }
                }

                $row = [
                    $isFirstJamOfDay ? $day : '', // Visual merge untuk hari
                    $jam,
                    $waktu,
                ];

                // Cek apakah ada jadwal tetap pada hari & jam ini
                $fixed = $fixedSchedules->where('hari', $day)->where('jam_ke', $jam)->first();

                // Kolom kelas dimulai dari index 4 (Kolom D)
                $colIdx = 4;
                foreach ($classes as $kelas) {
                    $cellValue = '';

                    if ($fixed) {
                        // Tanda jadwal tetap
                        $keterangan = strtoupper(trim($fixed->keterangan ?: 'JADWAL TETAP'));
                        $cellValue = $keterangan;

                        $coord = Coordinate::stringFromColumnIndex($colIdx) . $currentRow;
                        $this->fixedCells[] = $coord;
                    } else {
                        // Cek apakah sudah terisi jadwal pelajaran
                        if (isset($schedules[$day][$jam][$kelas->id])) {
                            $schedule = $schedules[$day][$jam][$kelas->id]->first();
                            $mapelCode = $schedule->mataPelajaran->kode_mapel ?? '';
                            $guruCode = $schedule->guru->guruProfile->kode_guru ?? '';
                            if ($mapelCode && $guruCode) {
                                $cellValue = "{$mapelCode} {$guruCode}";
                            }
                        }
                    }

                    $row[] = $cellValue;
                    $colIdx++;
                }

                $rows[] = $row;
                $isFirstJamOfDay = false;
                $currentRow++;
            }
        }

        return collect($rows);
    }

    public function headings(): array
    {
        $headers = ['HARI', 'JAM KE', 'WAKTU'];

        $semester = $this->semesterId ? Semester::find($this->semesterId) : Semester::getActive();
        $tahunPelajaranId = $semester ? $semester->tahun_pelajaran_id : null;

        $classesQuery = Kelas::orderBy('tingkat')->orderBy('nama_kelas');
        if ($tahunPelajaranId) {
            $classesQuery->where('tahun_pelajaran_id', $tahunPelajaranId);
        }
        $classes = $classesQuery->get();
        if ($classes->isEmpty()) {
            $classes = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();
        }

        foreach ($classes as $kelas) {
            $headers[] = $kelas->tingkat . $kelas->nama_kelas;
        }

        return [
            ['JADWAL PELAJARAN (Format Pengisian: KODE_MAPEL [Spasi] KODE_GURU | Abu-abu: Jadwal Tetap Jangan Diisi)'],
            $headers,
        ];
    }

    public function title(): string
    {
        return 'INPUT_JADWAL';
    }

    public function styles(Worksheet $sheet)
    {
        $lastCol = $sheet->getHighestColumn();
        $sheet->mergeCells('A1:' . $lastCol . '1');

        // Style Baris Judul
        $sheet->getStyle('A1:' . $lastCol . '1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => '333333'], 'size' => 11],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        ]);

        // Style Baris Header
        $sheet->getStyle('A2:' . $lastCol . '2')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '1B6EC2']],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        ]);

        // Center align HARI, JAM KE, WAKTU
        $sheet->getStyle('A:C')->getAlignment()->setHorizontal('center')->setVertical('center');

        // Border grid
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle('A2:' . $lastCol . $lastRow)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Beri warna latar belakang khusus dan font italic untuk Jadwal Tetap
        foreach ($this->fixedCells as $cell) {
            $sheet->getStyle($cell)->applyFromArray([
                'font' => [
                    'bold' => true,
                    'italic' => true,
                    'color' => ['rgb' => '475569'],
                    'size' => 9,
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E2E8F0'], // Slate-200 abu-abu elegan
                ],
                'alignment' => [
                    'horizontal' => 'center',
                    'vertical' => 'center',
                ],
            ]);
        }

        return [];
    }
}

class TemplateKodeGuruSheet implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    protected $semesterId;

    public function __construct($semesterId = null)
    {
        $this->semesterId = $semesterId;
    }

    public function collection()
    {
        return GuruProfile::with('user:id,name')
            ->where('is_active', true)
            ->whereNotNull('kode_guru')
            ->select('id', 'user_id', 'kode_guru', 'nama')
            ->orderByRaw("CASE WHEN kode_guru REGEXP '^[0-9]+$' THEN 0 ELSE 1 END ASC")
            ->orderByRaw('CAST(kode_guru AS UNSIGNED) ASC')
            ->orderBy('kode_guru', 'asc')
            ->get()
            ->map(function ($guru) {
                return [
                    'kode' => $guru->kode_guru,
                    'nama' => $guru->nama ?: ($guru->user->name ?? '-'),
                ];
            });
    }

    public function headings(): array
    {
        return ['KODE GURU', 'NAMA GURU'];
    }

    public function title(): string
    {
        return 'KODE_GURU';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'ED7D31']],
            ],
        ];
    }
}

class TemplateKodeMapelSheet implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    protected $semesterId;

    public function __construct($semesterId = null)
    {
        $this->semesterId = $semesterId;
    }

    public function collection()
    {
        return MataPelajaran::select('kode_mapel', 'nama_mapel')->where('is_active', true)->get();
    }

    public function headings(): array
    {
        return ['KODE MAPEL', 'NAMA MAPEL'];
    }

    public function title(): string
    {
        return 'KODE_MAPEL';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '70AD47']],
            ],
        ];
    }
}

class TemplateDaftarKelasSheet implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    protected $semesterId;

    public function __construct($semesterId = null)
    {
        $this->semesterId = $semesterId;
    }

    public function collection()
    {
        $semester = $this->semesterId ? Semester::find($this->semesterId) : Semester::getActive();
        $tahunPelajaranId = $semester ? $semester->tahun_pelajaran_id : null;

        $classesQuery = Kelas::orderBy('tingkat')->orderBy('nama_kelas');
        if ($tahunPelajaranId) {
            $classesQuery->where('tahun_pelajaran_id', $tahunPelajaranId);
        }
        $classes = $classesQuery->get();
        if ($classes->isEmpty()) {
            $classes = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();
        }

        return $classes->map(function ($kelas) {
            return [
                'nama_lengkap' => $kelas->tingkat . $kelas->nama_kelas,
            ];
        });
    }

    public function headings(): array
    {
        return ['DAFTAR KELAS'];
    }

    public function title(): string
    {
        return 'DAFTAR_KELAS';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'A5A5A5']],
            ],
        ];
    }
}
