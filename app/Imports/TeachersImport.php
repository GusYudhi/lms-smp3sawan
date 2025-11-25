<?php

namespace App\Imports;

use App\Models\User;
use App\Services\UserManagementService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

use Illuminate\Support\Str;

class TeachersImport implements ToModel, WithHeadingRow, WithBatchInserts, WithChunkReading, SkipsEmptyRows
{
    protected $userService;
    public $successCount = 0;
    public $failureCount = 0;
    public $errors = [];

    public function __construct()
    {
        $this->userService = app(UserManagementService::class);
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        try {
            // Normalize keys (handle different header formats)
            $row = array_change_key_case($row, CASE_LOWER);

            // Map possible column variations
            $nama = $row['nama_lengkap'] ?? $row['nama'] ?? null;
            $nip = $row['nip_nik'] ?? $row['nipnik'] ?? $row['nip'] ?? $row['nik'] ?? null;
            $telepon = $row['telepon'] ?? $row['nomor_telepon'] ?? $row['no_telepon'] ?? null;
            $jenis_kelamin = $row['jenis_kelamin'] ?? $row['kelamin'] ?? null;
            $tempat_lahir = $row['tempat_lahir'] ?? null;
            $tanggal_lahir = $row['tanggal_lahir'] ?? $row['tgl_lahir'] ?? null;
            $status_kepegawaian = $row['status_kepegawaian'] ?? $row['status'] ?? null;
            $golongan = $row['golongan'] ?? null;
            $mata_pelajaran = $row['mata_pelajaran'] ?? $row['mapel'] ?? null;
            $wali_kelas = $row['wali_kelas'] ?? null;

            // Validate required fields
            $validator = Validator::make([
                'nama' => $nama,
                'nip' => $nip,
            ], [
                'nama' => 'required|string|max:255',
                'nip' => 'nullable|string|max:50|unique:teacher_profiles,nip',
            ]);

            if ($validator->fails()) {
                $this->failureCount++;
                $this->errors[] = "Baris {$this->failureCount}: " . $nama . " - " . implode(', ', $validator->errors()->all());
                return null;
            }

            // Generate Email
            $email = $this->generateEmail($nama);

            // Normalize jenis kelamin (L/P)
            $jenis_kelamin = $this->normalizeGender($jenis_kelamin);

            // Parse mata pelajaran (could be comma-separated)
            $mata_pelajaran_array = [];
            if ($mata_pelajaran) {
                $mata_pelajaran_array = array_map('trim', explode(',', $mata_pelajaran));
            }

            // Generate default password
            $defaultPassword = '12345678';

            // Prepare user data
            $userData = [
                'name' => $nama,
                'email' => $email,
                'password' => $defaultPassword,
                'role' => 'guru',
                'nomor_telepon' => $telepon,
                'jenis_kelamin' => $jenis_kelamin,
            ];

            // Prepare profile data
            $profileData = [
                'nip' => $nip,
                'tempat_lahir' => $tempat_lahir,
                'tanggal_lahir' => $this->parseDate($tanggal_lahir),
                'status_kepegawaian' => $status_kepegawaian,
                'golongan' => $golongan,
                'mata_pelajaran' => $mata_pelajaran_array,
                'wali_kelas' => $wali_kelas,
            ];

            // Create teacher using UserManagementService
            $teacher = $this->userService->createTeacher($userData, $profileData);

            $this->successCount++;
            return $teacher;

        } catch (\Exception $e) {
            $this->failureCount++;
            $errorMsg = "Baris {$this->failureCount}: " . ($nama ?? 'Unknown') . " - " . $e->getMessage();
            $this->errors[] = $errorMsg;
            Log::error('Teacher Import Error: ' . $errorMsg);
            return null;
        }
    }

    private function generateEmail($name)
    {
        // Clean name and split into parts
        $cleanName = preg_replace('/[^a-zA-Z\s]/', '', $name);
        $parts = explode(' ', strtolower(trim($cleanName)));

        // Logic: Try to take 2nd and 3rd word if available
        if (count($parts) >= 3) {
            $emailUser = $parts[1] . '.' . $parts[2];
        } elseif (count($parts) == 2) {
            $emailUser = $parts[0] . '.' . $parts[1];
        } else {
            $emailUser = $parts[0];
        }

        $baseEmail = $emailUser . '@guru.id';
        $email = $baseEmail;
        $counter = 1;

        while (User::where('email', $email)->exists()) {
            $email = $emailUser . $counter . '@guru.id';
            $counter++;
        }

        return $email;
    }

    /**
     * Normalize gender value to L/P
     */
    private function normalizeGender($value)
    {
        if (empty($value)) {
            return null;
        }

        $value = strtolower(trim($value));

        if (in_array($value, ['l', 'laki-laki', 'laki', 'male', 'pria'])) {
            return 'L';
        }

        if (in_array($value, ['p', 'perempuan', 'female', 'wanita'])) {
            return 'P';
        }

        return null;
    }

    /**
     * Parse date from various formats
     */
    private function parseDate($date)
    {
        if (empty($date)) {
            return null;
        }

        try {
            // Handle Excel date serial number
            if (is_numeric($date)) {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($date)->format('Y-m-d');
            }

            // Try to parse common date formats
            $formats = ['Y-m-d', 'd-m-Y', 'd/m/Y', 'Y/m/d'];
            foreach ($formats as $format) {
                $parsed = \DateTime::createFromFormat($format, $date);
                if ($parsed !== false) {
                    return $parsed->format('Y-m-d');
                }
            }

            return null;
        } catch (\Exception $e) {
            Log::warning("Date parsing failed for: {$date}");
            return null;
        }
    }

    public function batchSize(): int
    {
        return 100;
    }

    public function chunkSize(): int
    {
        return 100;
    }
}
