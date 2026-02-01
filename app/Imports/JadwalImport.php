<?php

namespace App\Imports;

use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\GuruProfile;
use App\Models\JamPelajaran;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Illuminate\Support\Facades\Log;

class JadwalImport implements WithMultipleSheets 
{
    protected $semesterId;
    public $successCount = 0;
    public $failureCount = 0;
    public $errors = [];
    public $missingTeachers = []; 
    public $changes = []; // Track modifications

    public function __construct($semesterId)
    {
        $this->semesterId = $semesterId;
    }

    public function sheets(): array
    {
        return [
            'KODE_MAPEL' => new MapelSheetImport($this->semesterId, $this),
            'KODE_GURU' => new GuruSheetImport($this),
            'INPUT_JADWAL' => new JadwalSheetImport($this->semesterId, $this)
        ];
    }
}

class MapelSheetImport implements ToCollection, WithHeadingRow
{
    protected $semesterId;
    protected $parent;

    public function __construct($semesterId, $parent)
    {
        $this->semesterId = $semesterId;
        $this->parent = $parent;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $kode = strtoupper(trim($row['kode_mapel'] ?? ''));
            $nama = trim($row['nama_mapel'] ?? '');

            if ($kode && $nama) {
                MataPelajaran::updateOrCreate(
                    ['kode_mapel' => $kode, 'semester_id' => $this->semesterId],
                    ['nama_mapel' => $nama]
                );
            }
        }
    }
}

class GuruSheetImport implements ToCollection, WithHeadingRow
{
    protected $parent;

    public function __construct($parent)
    {
        $this->parent = $parent;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $kode = strtoupper(trim($row['kode_guru'] ?? ''));
            $nama = trim($row['nama_guru'] ?? '');

            if ($kode && $nama) {
                // Try to find teacher by name
                $user = User::where('role', 'guru')
                    ->where('name', 'like', "%{$nama}%")
                    ->first();
                
                if ($user && $user->guruProfile) {
                    $oldCode = $user->guruProfile->kode_guru;
                    
                    // Normalize for comparison
                    $isDifferent = (strtoupper(trim($oldCode ?? '')) !== $kode);

                    if ($isDifferent) {
                        // Check collision
                        $existingOwner = GuruProfile::where('kode_guru', $kode)
                            ->where('id', '!=', $user->guruProfile->id)
                            ->first();

                        if ($existingOwner) {
                            $existingOwner->update(['kode_guru' => null]);
                        }

                        $user->guruProfile->update(['kode_guru' => $kode]);
                        
                        // Log Change
                        $this->parent->changes[] = [
                            'type' => 'guru_code',
                            'name' => $user->name,
                            'old' => $oldCode ?? '-',
                            'new' => $kode
                        ];
                    }
                } else {
                    // Track missing teacher
                    $this->parent->errors[] = "Sheet KODE_GURU: Guru '$nama' tidak ditemukan di database.";
                    $this->parent->failureCount++;
                }
            }
        }
    }
}

class JadwalSheetImport implements ToCollection, WithHeadingRow
{
    protected $semesterId;
    protected $parent;

    public function __construct($semesterId, $parent)
    {
        $this->semesterId = $semesterId;
        $this->parent = $parent;
    }

    public function headingRow(): int
    {
        return 2;
    }

    /**
     * Flexible Code Lookup (Handles '7' vs '07')
     */
    protected function lookupCode($map, $code)
    {
        $code = strtoupper(trim($code));
        
        // 1. Exact Match
        if (isset($map[$code])) return $map[$code];

        // 2. Try removing leading zeros (e.g. '07' -> '7')
        $noZeros = ltrim($code, '0');
        if (isset($map[$noZeros])) return $map[$noZeros];

        // 3. Try adding leading zero (e.g. '7' -> '07') - assuming max 2 digits for simple cases
        $padded = str_pad($code, 2, '0', STR_PAD_LEFT);
        if (isset($map[$padded])) return $map[$padded];

        return null;
    }

    public function collection(Collection $rows)
    {
        // 1. Prepare Maps for Lookups (Refreshed after Mapel import)
        $kelasMap = Kelas::all()->mapWithKeys(function ($k) {
            return [Str::slug($k->tingkat . $k->nama_kelas, '_') => $k->id];
        });

        // Store keys as uppercase strings
        $mapelMap = MataPelajaran::where('semester_id', $this->semesterId)
            ->pluck('id', 'kode_mapel')->mapWithKeys(function ($item, $key) {
                return [strtoupper((string)$key) => $item];
            });

        $guruMap = GuruProfile::whereNotNull('kode_guru')->pluck('user_id', 'kode_guru')->mapWithKeys(function ($item, $key) {
            return [strtoupper((string)$key) => $item];
        });

        $lastHari = null;

        foreach ($rows as $index => $row) {
            try {
                $rowNumber = $index + 3;

                // 2. Handle Hari
                $rawHari = $row['hari'] ?? null;
                if (!empty($rawHari)) {
                    $lastHari = ucfirst(strtolower($rawHari));
                }
                $hari = $lastHari;

                $jamKe = $row['jam_ke'] ?? null;
                $waktu = $row['waktu'] ?? null;

                if (!$hari || !$jamKe) continue;

                // 2.5 Auto-Import Jam Pelajaran (Waktu)
                if ($waktu && $this->semesterId) {
                    $this->syncJamPelajaran($jamKe, $waktu);
                }

                // 3. Iterate Columns to find Classes
                foreach ($row as $key => $value) {
                    if (in_array($key, ['hari', 'jam_ke', 'waktu'])) continue;
                    if (empty($value)) continue;
                    if (!isset($kelasMap[$key])) continue;
                    
                    $kelasId = $kelasMap[$key];
                    $cellValue = trim($value);

                    if (strtoupper($cellValue) === 'UPACARA' || strtoupper($cellValue) === 'ISTIRAHAT') continue; 

                    $parts = preg_split('/\s+/', $cellValue);
                    if (count($parts) < 2) {
                        $this->parent->errors[] = "Baris $rowNumber, Kelas $key: Format salah '$cellValue'. Gunakan 'KODE_MAPEL KODE_GURU'";
                        $this->parent->failureCount++; 
                        continue;
                    }

                    $code1 = $parts[0];
                    $code2 = $parts[1];

                    $mapelId = null;
                    $guruId = null;

                    // Try Strategy A: Code1=Mapel, Code2=Guru
                    $m1 = $this->lookupCode($mapelMap, $code1);
                    $g1 = $this->lookupCode($guruMap, $code2);
                    
                    if ($m1 && $g1) {
                        $mapelId = $m1;
                        $guruId = $g1;
                    } else {
                        // Try Strategy B: Code1=Guru, Code2=Mapel
                        $g2 = $this->lookupCode($guruMap, $code1);
                        $m2 = $this->lookupCode($mapelMap, $code2);
                        
                        if ($g2 && $m2) {
                            $guruId = $g2;
                            $mapelId = $m2;
                        }
                    }

                    if (!$mapelId || !$guruId) {
                        $this->parent->errors[] = "Baris $rowNumber, Kelas $key: Kode Mapel/Guru tidak valid ($cellValue)";
                        $this->parent->failureCount++; 
                        continue;
                    }

                    JadwalPelajaran::updateOrCreate(
                        [
                            'semester_id' => $this->semesterId,
                            'kelas_id' => $kelasId,
                            'hari' => $hari,
                            'jam_ke' => $jamKe
                        ],
                        [
                            'mata_pelajaran_id' => $mapelId,
                            'guru_id' => $guruId
                        ]
                    );
                    
                    $this->parent->successCount++;
                }

            } catch (\Exception $e) {
                $this->parent->failureCount++;
                $this->parent->errors[] = "Baris $rowNumber: " . $e->getMessage();
            }
        }
    }

    /**
     * Sync Jam Pelajaran based on WAKTU column
     */
    protected function syncJamPelajaran($jamKe, $waktu)
    {
        // Format: "07.30 - 08.10" or "07:30-08:10"
        $times = preg_split('/[-\s]+/', str_replace('.', ':', $waktu));
        
        if (count($times) >= 2) {
            $mulai = date('H:i:s', strtotime($times[0]));
            $selesai = date('H:i:s', strtotime($times[1]));

            JamPelajaran::updateOrCreate(
                ['semester_id' => $this->semesterId, 'jam_ke' => $jamKe],
                ['jam_mulai' => $mulai, 'jam_selesai' => $selesai]
            );
        }
    }
}