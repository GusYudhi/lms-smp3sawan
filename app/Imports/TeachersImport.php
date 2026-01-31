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
            $jenis_kelamin = $row['jenis_kelamin'] ?? $row['kelamin'] ?? null;
            $tempat_lahir = $row['tempat_lahir'] ?? null;
            $tanggal_lahir = $row['tanggal_lahir'] ?? $row['tgl_lahir'] ?? null;
            $telepon = $row['telepon'] ?? $row['nomor_telepon'] ?? $row['no_telepon'] ?? null;
            $status_kepegawaian = $row['status_kepegawaian'] ?? $row['status'] ?? null;
            $golongan = $row['golongan'] ?? null;
            $jabatan_di_sekolah = $row['jabatan_di_sekolah'] ?? $row['jabatan'] ?? null;
            $mata_pelajaran = $row['mata_pelajaran'] ?? $row['mapel'] ?? null;
            $wali_kelas = $row['wali_kelas'] ?? null;

            // Skip empty rows
            if (empty($nama) || empty($nip)) {
                return null;
            }

            // Validate required fields
            $validator = Validator::make([
                'nama' => $nama,
                'nip' => $nip,
                'jenis_kelamin' => $jenis_kelamin,
                'status_kepegawaian' => $status_kepegawaian,
            ], [
                'nama' => 'required|string|max:255',
                'nip' => 'nullable|string|max:50|unique:guru_profiles,nip',
                'jenis_kelamin' => 'required|in:L,P,Laki-laki,Perempuan,laki-laki,perempuan',
                'status_kepegawaian' => 'required|string',
            ]);

            if ($validator->fails()) {
                $this->failureCount++;
                $this->errors[] = "Baris dengan nama {$nama}: " . implode(', ', $validator->errors()->all());
                return null;
            }

            // Generate Email
            $email = $this->generateEmail($nama);

            // Normalize jenis kelamin (L/P)
            $jenis_kelamin = $this->normalizeGender($jenis_kelamin);

            // Normalize status kepegawaian
            $status_kepegawaian = $this->normalizeStatus($status_kepegawaian);

            // Resolve mata pelajaran ID
            $mata_pelajaran_id = null;
            if ($mata_pelajaran) {
                $mata_pelajaran_id = $this->findMataPelajaranId($mata_pelajaran);
            }

            // Determine role based on jabatan
            $role = 'guru';
            if ($jabatan_di_sekolah && strtolower($jabatan_di_sekolah) === 'kepala sekolah') {
                $role = 'kepala_sekolah';
            }

            // Parse wali kelas to find kelas_id
            $kelas_id = null;
            if ($wali_kelas) {
                $kelas_id = $this->findKelasId($wali_kelas);
            }

            // Generate default password
            $defaultPassword = '12345678';

            // Prepare user data
            $userData = [
                'name' => $nama,
                'email' => $email,
                'password' => $defaultPassword,
                'role' => $role,
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
                'jabatan_di_sekolah' => $jabatan_di_sekolah,
                'mata_pelajaran_id' => $mata_pelajaran_id,
                'kelas_id' => $kelas_id,
            ];

            // Create teacher using UserManagementService
            $this->userService->createTeacher($userData, $profileData);

            $this->successCount++;
            return null;

        } catch (\Exception $e) {
            $this->failureCount++;
            $errorMsg = "Baris dengan nama " . ($nama ?? 'Unknown') . ": " . $e->getMessage();
            $this->errors[] = $errorMsg;
            Log::error('Teacher Import Error: ' . $errorMsg);
            return null;
        }
    }

    /**
     * Find Mata Pelajaran ID by name
     */
    private function findMataPelajaranId($name)
    {
        if (empty($name)) {
            return null;
        }

        $name = trim($name);
        
        // Find subject in database
        $mapel = \App\Models\MataPelajaran::where('nama_mapel', 'LIKE', $name)
            ->first();

        if ($mapel) {
            return $mapel->id;
        }

        Log::warning("Mata Pelajaran tidak ditemukan: {$name}");
        return null;
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
     * Normalize status kepegawaian
     */
    private function normalizeStatus($value)
    {
        if (empty($value)) {
            return null;
        }

        $value = trim($value);
        $lowerValue = strtolower($value);

        // Map common terms to Enum values
        if ($lowerValue === 'honorer' || $lowerValue === 'kontrak') {
            return 'GTT'; // Map Honorer to GTT (Guru Tidak Tetap)
        }

        if ($lowerValue === 'tetap yayasan') {
            return 'GTY';
        }

        // If it's already a valid Enum value (case-insensitive check)
        $validStatuses = ['PNS', 'PPPK', 'GTT', 'GTY', 'GTK', 'HONORER'];
        foreach ($validStatuses as $status) {
            if (strtolower($status) === $lowerValue) {
                return $status;
            }
        }

        // Return original if no match found (will likely fail validation if invalid)
        return $value;
    }

    /**
     * Find kelas ID from wali_kelas string (e.g., "7A", "8 B", "9C")
     */
    private function findKelasId($waliKelas)
    {
        if (empty($waliKelas)) {
            return null;
        }

        // Clean and normalize the input
        $waliKelas = trim($waliKelas);
        $waliKelas = str_replace(' ', '', $waliKelas); // Remove spaces

        // Extract tingkat (first digit) and nama_kelas (remaining characters)
        if (preg_match('/^(\d+)([A-Za-z])$/', $waliKelas, $matches)) {
            $tingkat = $matches[1];
            $namaKelas = strtoupper($matches[2]);

            // Find kelas in database
            $kelas = \App\Models\Kelas::where('tingkat', $tingkat)
                ->where('nama_kelas', $namaKelas)
                ->first();

            if ($kelas) {
                return $kelas->id;
            }

            Log::warning("Kelas tidak ditemukan: tingkat={$tingkat}, nama={$namaKelas}");
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
