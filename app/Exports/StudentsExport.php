<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StudentsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $search;
    protected $filters;

    public function __construct($search = null, $filters = [])
    {
        $this->search = $search;
        $this->filters = $filters;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return User::with('studentProfile.kelas')
            ->where('role', 'siswa')
            ->when($this->search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhereHas('studentProfile', function ($sub) use ($search) {
                          $sub->where('nis', 'like', "%{$search}%")
                              ->orWhere('nisn', 'like', "%{$search}%");
                      });
                });
            })
            ->when(isset($this->filters['jenis_kelamin']), function ($query) {
                return $query->whereHas('studentProfile', function ($q) {
                    $q->where('jenis_kelamin', $this->filters['jenis_kelamin']);
                });
            })
            ->when(isset($this->filters['kelas']), function ($query) {
                return $query->whereHas('studentProfile', function ($q) {
                    $q->where('kelas_id', $this->filters['kelas']);
                });
            })
            ->orderBy('name')
            ->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'No',
            'Nama',
            'NIS',
            'NISN',
            'Jenis Kelamin',
            'Kelas',
            'Tahun Angkatan',
            'Tempat Lahir',
            'Tanggal Lahir',
            'No. Telepon Orang Tua',
            'Email',
            'Status'
        ];
    }

    /**
     * @var User $student
     */
    public function map($student): array
    {
        static $rowNumber = 0;
        $rowNumber++;

        $profile = $student->studentProfile;

        $gender = '';
        if ($profile && $profile->jenis_kelamin) {
            $gender = $profile->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan';
        }

        $status = '';
        if ($profile && $profile->status) {
            switch ($profile->status) {
                case 'AKTIF':
                    $status = 'Aktif';
                    break;
                case 'LULUS':
                    $status = 'Lulus';
                    break;
                case 'TIDAK_AKTIF':
                    $status = 'Tidak Aktif';
                    break;
                default:
                    $status = 'Aktif';
            }
        } else {
            $status = 'Aktif';
        }

        return [
            $rowNumber,
            $student->name ?? '',
            $profile->nis ?? '',
            $profile->nisn ?? '',
            $gender,
            $profile && $profile->kelas ? $profile->kelas->tingkat . $profile->kelas->nama_kelas : '',
            $profile->tahun_angkatan ?? '',
            $profile->tempat_lahir ?? '',
            $profile->tanggal_lahir ? \Carbon\Carbon::parse($profile->tanggal_lahir)->format('d-m-Y') : '',
            $profile->nomor_telepon_orangtua ?? '',
            $student->email ?? '',
            $status
        ];
    }

    /**
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }

    /**
     * @return array
     */
    public function columnWidths(): array
    {
        return [
            'A' => 5,   // No
            'B' => 30,  // Nama
            'C' => 15,  // NIS
            'D' => 15,  // NISN
            'E' => 15,  // Jenis Kelamin
            'F' => 12,  // Kelas
            'G' => 15,  // Tahun Angkatan
            'H' => 20,  // Tempat Lahir
            'I' => 15,  // Tanggal Lahir
            'J' => 20,  // No. Telepon
            'K' => 30,  // Email
            'L' => 15,  // Status
        ];
    }
}
