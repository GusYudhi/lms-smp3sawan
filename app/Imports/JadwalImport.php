<?php

namespace App\Imports;

use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\GuruProfile;
use Illuminate\Support\Collection;
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

    public function __construct($semesterId)
    {
        $this->semesterId = $semesterId;
    }

    public function sheets(): array
    {
        return [
            0 => new JadwalSheetImport($this->semesterId, $this)
        ];
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

    public function collection(Collection $rows)
    {
        // 1. Prepare Maps for Lookups
        $kelasMap = Kelas::all()->mapWithKeys(function ($k) {
            // Slugify class name to match Excel header (e.g. "7A" -> "7a")
            return [Str::slug($k->tingkat . $k->nama_kelas, '_') => $k->id];
        });

        $mapelMap = MataPelajaran::pluck('id', 'kode_mapel')->mapWithKeys(function ($item, $key) {
            return [strtoupper($key) => $item];
        });

        $guruMap = GuruProfile::whereNotNull('kode_guru')->pluck('user_id', 'kode_guru')->mapWithKeys(function ($item, $key) {
            return [strtoupper($key) => $item];
        });

        $lastHari = null;

        foreach ($rows as $index => $row) {
            try {
                $rowNumber = $index + 3; // Row in Excel (Header is row 2, Data starts row 3)

                // 2. Handle Hari (Fill Down)
                // Note: Maatwebsite uses slugified keys. 'HARI' -> 'hari'
                $rawHari = $row['hari'] ?? null;
                if (!empty($rawHari)) {
                    $lastHari = ucfirst(strtolower($rawHari));
                }
                $hari = $lastHari;

                $jamKe = $row['jam_ke'] ?? null;

                if (!$hari || !$jamKe) continue;

                // 3. Iterate Columns to find Classes
                // $row is a Collection or Array.
                foreach ($row as $key => $value) {
                    // Skip non-class columns
                    if (in_array($key, ['hari', 'jam_ke', 'waktu'])) continue;
                    
                    // Skip empty cells
                    if (empty($value)) continue;

                    // Check if column key corresponds to a class
                    if (!isset($kelasMap[$key])) continue;
                    
                    $kelasId = $kelasMap[$key];
                    $cellValue = trim($value);

                    // 4. Parse Cell Value "MAPEL GURU" or "GURU MAPEL"
                    // Handle "UPACARA" etc
                    if (strtoupper($cellValue) === 'UPACARA' || strtoupper($cellValue) === 'ISTIRAHAT') {
                        // Optional: Create Fixed Schedule
                        continue; 
                    }

                    $parts = preg_split('/\s+/', $cellValue);
                    if (count($parts) < 2) {
                        $this->parent->errors[] = "Baris $rowNumber, Kelas $key: Format salah '$cellValue'. Gunakan 'KODE_MAPEL KODE_GURU'";
                        continue;
                    }

                    $code1 = strtoupper($parts[0]);
                    $code2 = strtoupper($parts[1]);

                    $mapelId = null;
                    $guruId = null;

                    // Try Strategy A: Code1=Mapel, Code2=Guru
                    if (isset($mapelMap[$code1]) && isset($guruMap[$code2])) {
                        $mapelId = $mapelMap[$code1];
                        $guruId = $guruMap[$code2];
                    } 
                    // Try Strategy B: Code1=Guru, Code2=Mapel
                    elseif (isset($guruMap[$code1]) && isset($mapelMap[$code2])) {
                        $guruId = $guruMap[$code1];
                        $mapelId = $mapelMap[$code2];
                    }

                    if (!$mapelId || !$guruId) {
                        $this->parent->errors[] = "Baris $rowNumber, Kelas $key: Kode Mapel/Guru tidak valid ($cellValue)";
                        continue;
                    }

                    // 5. Save to DB
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
}