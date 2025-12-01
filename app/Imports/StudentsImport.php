<?php

namespace App\Imports;

use App\Models\User;
use App\Models\StudentProfile;
use App\Services\UserManagementService;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Str;

class StudentsImport implements ToCollection, WithHeadingRow
{
    public $errors = [];
    public $successCount = 0;
    public $failureCount = 0;

    protected $userService;

    public function __construct()
    {
        $this->userService = new UserManagementService();
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            try {
                // Validate required fields
                $validator = Validator::make($row->toArray(), [
                    'nama' => 'required|string|max:255',
                    'nis' => 'nullable|max:50',
                    'nisn' => 'nullable|max:50',
                    'tempat_lahir' => 'nullable|string|max:255',
                    'tanggal_lahir' => 'nullable',
                    'jenis_kelamin' => 'required|in:L,P,Laki-laki,Perempuan',
                    'tingkat' => 'nullable|in:7,8,9',
                    'kelas' => 'nullable|max:10',
                ]);

                if ($validator->fails()) {
                    $this->errors[] = "Baris " . ($index + 2) . ": " . implode(', ', $validator->errors()->all());
                    $this->failureCount++;
                    continue;
                }

                // Convert numeric values to strings
                $nisValue = !empty($row['nis']) ? (string) $row['nis'] : null;
                $nisnValue = !empty($row['nisn']) ? (string) $row['nisn'] : null;
                $tingkatValue = !empty($row['tingkat']) ? (string) $row['tingkat'] : null;
                $kelasValue = !empty($row['kelas']) ? (string) $row['kelas'] : null;

                // Handle tahun_angkatan: use from Excel if provided, otherwise use current year
                $tahunAngkatan = !empty($row['tahun_angkatan']) ? (int) $row['tahun_angkatan'] : date('Y');

                // Generate Email
                $email = $this->generateEmail($row['nama']);

                // Check if NIS already exists
                if (!empty($nisValue)) {
                    $existingNis = StudentProfile::where('nis', $nisValue)->exists();
                    if ($existingNis) {
                        $this->errors[] = "Baris " . ($index + 2) . ": NIS '{$nisValue}' sudah digunakan";
                        $this->failureCount++;
                        continue;
                    }
                }

                // Validate gender format
                $allowedGenders = ['L', 'P', 'Laki-laki', 'Perempuan'];
                if (!in_array($row['jenis_kelamin'], $allowedGenders)) {
                    $this->errors[] = "Baris " . ($index + 2) . ": Jenis kelamin harus L, P, Laki-laki, atau Perempuan";
                    $this->failureCount++;
                    continue;
                }

                // Normalize gender to match student_profiles table (L/P format)
                $gender = in_array(strtoupper($row['jenis_kelamin']), ['L', 'LAKI-LAKI']) ? 'L' : 'P';

                // Prepare user data
                $userData = [
                    'name' => $row['nama'],
                    'email' => $email,
                    'password' => '12345678', // Default password
                ];

                // Prepare profile data
                $profileData = [
                    'nis' => $nisValue,
                    'nisn' => $nisnValue,
                    'tempat_lahir' => $row['tempat_lahir'] ?? null,
                    'tanggal_lahir' => $this->parseDate($row['tanggal_lahir'] ?? null),
                    'tingkat' => $tingkatValue,
                    'kelas' => $kelasValue,
                    'tahun_angkatan' => $tahunAngkatan,
                    'jenis_kelamin' => $gender,
                    'nomor_telepon_orangtua' => $row['telepon_orangtua'] ?? $row['nomor_telepon_orangtua'] ?? null,
                    'foto_profil' => null,
                ];

                // Create student using UserManagementService
                $this->userService->createStudent($userData, $profileData);

                $this->successCount++;
            } catch (\Exception $e) {
                $this->errors[] = "Baris " . ($index + 2) . ": Error - " . $e->getMessage();
                $this->failureCount++;
            }
        }
    }

    private function generateEmail($name)
    {
        // Clean name and split into parts
        $cleanName = preg_replace('/[^a-zA-Z\s]/', '', $name);
        $parts = explode(' ', strtolower(trim($cleanName)));

        // Logic: Try to take 2nd and 3rd word if available (e.g. Ida Bagus Yudhi Priyatna -> bagus.yudhi)
        // If not enough parts, fallback to standard first.last

        if (count($parts) >= 3) {
            // Take 2nd and 3rd parts (index 1 and 2)
            $emailUser = $parts[1] . '.' . $parts[2];
        } elseif (count($parts) == 2) {
            $emailUser = $parts[0] . '.' . $parts[1];
        } else {
            $emailUser = $parts[0];
        }

        $baseEmail = $emailUser . '@student.id';
        $email = $baseEmail;
        $counter = 1;

        while (User::where('email', $email)->exists()) {
            $email = $emailUser . $counter . '@student.id';
            $counter++;
        }

        return $email;
    }

    private function parseDate($date)
    {
        if (empty($date)) {
            return null;
        }

        try {
            // If it's an Excel serial date (numeric)
            if (is_numeric($date)) {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($date)->format('Y-m-d');
            }

            // Try different date formats
            $formats = ['Y-m-d', 'd/m/Y', 'd-m-Y', 'm/d/Y', 'm-d-Y'];
            foreach ($formats as $format) {
                $parsed = \DateTime::createFromFormat($format, $date);
                if ($parsed && $parsed->format($format) === $date) {
                    return $parsed->format('Y-m-d');
                }
            }
            return null;
        } catch (\Exception $e) {
            return null;
        }
    }
}
