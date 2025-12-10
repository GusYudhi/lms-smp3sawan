<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\Log;

class TeachersExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        try {
            Log::info('Fetching teachers for export');

            $teachers = User::whereIn('role', ['guru', 'kepala_sekolah'])
                ->with(['guruProfile.kelas'])
                ->orderBy('name', 'asc')
                ->get();

            Log::info('Teachers fetched successfully', ['count' => $teachers->count()]);

            return $teachers;
        } catch (\Exception $e) {
            Log::error('Error fetching teachers collection', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'No',
            'Nama Lengkap',
            'NIP/NIK',
            'Email',
            'No. Telepon',
            'Jenis Kelamin',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Status Kepegawaian',
            'Golongan',
            'Jabatan di Sekolah',
            'Mata Pelajaran',
            'Wali Kelas',
            'Status Akun'
        ];
    }

    /**
     * @var User $teacher
     */
    public function map($teacher): array
    {
        try {
            static $rowNumber = 0;
            $rowNumber++;

            Log::info("Mapping teacher", ['row' => $rowNumber, 'teacher_id' => $teacher->id, 'name' => $teacher->name]);

            $profile = $teacher->guruProfile;

            if (!$profile) {
                Log::warning("Teacher has no profile", ['teacher_id' => $teacher->id]);
            }

            $jenisKelamin = '-';
            if ($profile && $profile->jenis_kelamin) {
                $jenisKelamin = $profile->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan';
            }

            // Mata Pelajaran adalah array, bukan relasi
            $mataPelajaran = '-';
            if ($profile && $profile->mata_pelajaran) {
                if (is_array($profile->mata_pelajaran)) {
                    $mataPelajaran = implode(', ', $profile->mata_pelajaran);
                } else {
                    $mataPelajaran = $profile->mata_pelajaran;
                }
            }

            $waliKelas = '-';
            if ($profile && $profile->kelas) {
                $waliKelas = ($profile->kelas->tingkat ?? '') . ' ' . ($profile->kelas->nama_kelas ?? '');
                $waliKelas = trim($waliKelas) ?: '-';
            }

            $statusAkun = ($profile && $profile->is_active) ? 'Aktif' : 'Tidak Aktif';

            $tanggalLahir = '-';
            if ($profile && $profile->tanggal_lahir) {
                try {
                    $tanggalLahir = \Carbon\Carbon::parse($profile->tanggal_lahir)->format('d-m-Y');
                } catch (\Exception $e) {
                    Log::warning("Invalid date format", ['teacher_id' => $teacher->id, 'date' => $profile->tanggal_lahir]);
                    $tanggalLahir = '-';
                }
            }

            $data = [
                $rowNumber,
                $teacher->name ?? '-',
                ($profile ? ($profile->nip ?? '-') : '-'),
                $teacher->email ?? '-',
                ($profile ? ($profile->nomor_telepon ?? '-') : '-'),
                $jenisKelamin,
                ($profile ? ($profile->tempat_lahir ?? '-') : '-'),
                $tanggalLahir,
                ($profile ? ($profile->status_kepegawaian ?? '-') : '-'),
                ($profile ? ($profile->golongan ?? '-') : '-'),
                ($profile ? ($profile->jabatan_di_sekolah ?? '-') : '-'),
                $mataPelajaran,
                $waliKelas,
                $statusAkun
            ];

            Log::info("Teacher mapped successfully", ['row' => $rowNumber]);

            return $data;

        } catch (\Exception $e) {
            Log::error('Error mapping teacher', [
                'teacher_id' => $teacher->id ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Return safe default values
            return [
                $rowNumber ?? 0,
                '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-'
            ];
        }
    }

    /**
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        try {
            Log::info('Applying styles to worksheet');
            return [
                // Style the first row as bold text
                1 => ['font' => ['bold' => true, 'size' => 12]],
            ];
        } catch (\Exception $e) {
            Log::error('Error applying styles', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * @return array
     */
    public function columnWidths(): array
    {
        try {
            Log::info('Setting column widths');
            return [
                'A' => 5,   // No
                'B' => 30,  // Nama Lengkap
                'C' => 20,  // NIP/NIK
                'D' => 30,  // Email
                'E' => 15,  // No. Telepon
                'F' => 15,  // Jenis Kelamin
                'G' => 20,  // Tempat Lahir
                'H' => 15,  // Tanggal Lahir
                'I' => 20,  // Status Kepegawaian
                'J' => 15,  // Golongan
                'K' => 20,  // Jabatan di Sekolah
                'L' => 25,  // Mata Pelajaran
                'M' => 15,  // Wali Kelas
                'N' => 15,  // Status Akun
            ];
        } catch (\Exception $e) {
            Log::error('Error setting column widths', ['error' => $e->getMessage()]);
            return [];
        }
    }
}
