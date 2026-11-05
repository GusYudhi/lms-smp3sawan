<?php

namespace App\Imports;

use App\Models\GuruProfile;
use App\Models\JadwalPelajaran;
use App\Models\JamPelajaran;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\SkipsUnknownSheets;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class JadwalImport implements SkipsUnknownSheets, WithMultipleSheets
{
    protected $semesterId;

    protected $skippedSlots = [];

    public $successCount = 0;

    public $failureCount = 0;

    public $errors = [];

    public $missingTeachers = [];

    public $changes = []; // Track modifications

    public function __construct($semesterId, array $skippedSlots = [])
    {
        $this->semesterId = $semesterId;
        $this->skippedSlots = $skippedSlots;
    }

    public function sheets(): array
    {
        return [
            'KODE_MAPEL' => new MapelSheetImport($this->semesterId, $this),
            'KODE_GURU' => new GuruSheetImport($this),
            'INPUT_JADWAL' => new JadwalSheetImport($this->semesterId, $this, $this->skippedSlots),
        ];
    }

    public function onUnknownSheet($sheetName)
    {
        if ($sheetName === 'INPUT_JADWAL') {
            $this->errors[] = "Sheet 'INPUT_JADWAL' tidak ditemukan di file Excel. Pastikan menggunakan format template jadwal yang sesuai.";
            $this->failureCount++;
        }
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
                MataPelajaran::withTrashed()->updateOrCreate(
                    ['kode_mapel' => $kode],
                    ['nama_mapel' => $nama, 'is_active' => true, 'deleted_at' => null]
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
                // Try to find teacher profile by profile name or user name (handles both guru and kepala_sekolah)
                $guruProfile = GuruProfile::with('user')
                    ->where(function ($q) use ($nama) {
                        $q->where('nama', 'like', "%{$nama}%")
                            ->orWhereHas('user', function ($uq) use ($nama) {
                                $uq->where('name', 'like', "%{$nama}%");
                            });
                    })
                    ->first();

                if ($guruProfile) {
                    $oldCode = $guruProfile->kode_guru;

                    // Normalize for comparison
                    $isDifferent = (strtoupper(trim($oldCode ?? '')) !== $kode);

                    if ($isDifferent) {
                        // Check collision
                        $existingOwner = GuruProfile::where('kode_guru', $kode)
                            ->where('id', '!=', $guruProfile->id)
                            ->first();

                        if ($existingOwner) {
                            $existingOwner->update(['kode_guru' => null]);
                        }

                        $guruProfile->update(['kode_guru' => $kode]);

                        // Log Change
                        $displayName = $guruProfile->nama ?: ($guruProfile->user->name ?? $nama);
                        $this->parent->changes[] = [
                            'type' => 'guru_code',
                            'name' => $displayName,
                            'old' => $oldCode ?? '-',
                            'new' => $kode,
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

    protected $skippedSlots = [];

    protected $assignedInSheet = [];

    public function __construct($semesterId, $parent, array $skippedSlots = [])
    {
        $this->semesterId = $semesterId;
        $this->parent = $parent;
        $this->skippedSlots = $skippedSlots;
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
        if (isset($map[$code])) {
            return $map[$code];
        }

        // 2. Try removing leading zeros (e.g. '07' -> '7')
        $noZeros = ltrim($code, '0');
        if (isset($map[$noZeros])) {
            return $map[$noZeros];
        }

        // 3. Try adding leading zero (e.g. '7' -> '07') - assuming max 2 digits for simple cases
        $padded = str_pad($code, 2, '0', STR_PAD_LEFT);
        if (isset($map[$padded])) {
            return $map[$padded];
        }

        return null;
    }

    public function collection(Collection $rows)
    {
        // 1. Prepare Maps for Lookups (Refreshed after Mapel import)
        $allKelas = Kelas::all();
        $kelasMap = $allKelas->mapWithKeys(function ($k) {
            return [Str::slug($k->tingkat.$k->nama_kelas, '_') => $k->id];
        });
        $kelasObjMap = $allKelas->keyBy('id');

        // Store keys as uppercase strings
        $mapelMap = MataPelajaran::pluck('id', 'kode_mapel')->mapWithKeys(function ($item, $key) {
            return [strtoupper((string) $key) => $item];
        });

        $guruMap = GuruProfile::whereNotNull('kode_guru')->pluck('user_id', 'kode_guru')->mapWithKeys(function ($item, $key) {
            return [strtoupper((string) $key) => $item];
        });

        // Preload Fixed Schedules and Existing Lessons for this semester
        $fixedSchedules = $this->semesterId
            ? \App\Models\FixedSchedule::where('semester_id', $this->semesterId)->get()
            : collect();

        $existingJadwals = $this->semesterId
            ? JadwalPelajaran::with(['mataPelajaran', 'guru.guruProfile'])
                ->where('semester_id', $this->semesterId)
                ->get()
                ->groupBy(['hari', 'jam_ke', 'kelas_id'])
            : collect();

        $lastHari = null;

        foreach ($rows as $index => $row) {
            try {
                $rowNumber = $index + 3;

                // 2. Handle Hari
                $rawHari = $row['hari'] ?? null;
                if (! empty($rawHari)) {
                    $lastHari = ucfirst(strtolower(trim($rawHari)));
                }
                $hari = $lastHari;

                // Tangani jam_ke secara aman termasuk jam ke-0
                if (! $hari || ! isset($row['jam_ke']) || $row['jam_ke'] === '') {
                    continue;
                }
                $jamKe = (int) $row['jam_ke'];
                $waktu = $row['waktu'] ?? null;

                // 2.5 Auto-Import Jam Pelajaran (Waktu)
                if ($waktu && $this->semesterId) {
                    $this->syncJamPelajaran($jamKe, $waktu);
                }

                // 3. Iterate Columns to find Classes
                foreach ($row as $key => $value) {
                    if (in_array($key, ['hari', 'jam_ke', 'waktu'])) {
                        continue;
                    }
                    if ($value === null || trim((string) $value) === '') {
                        continue;
                    }
                    if (! isset($kelasMap[$key])) {
                        continue;
                    }

                    $kelasId = $kelasMap[$key];
                    $cellValue = trim((string) $value);

                    // Lewati jika merupakan slot Jadwal Tetap atau cell penanda jadwal tetap
                    $cellUpper = strtoupper($cellValue);
                    $isFixedSlot = $fixedSchedules->where('hari', $hari)->where('jam_ke', $jamKe)->isNotEmpty();
                    $isFixedKeyword = in_array($cellUpper, ['UPACARA', 'ISTIRAHAT', 'LITERASI', 'KOKURIKULER', 'P5', 'SENAM', 'APEL', 'JADWAL TETAP'])
                        || str_contains($cellUpper, 'JADWAL TETAP')
                        || str_contains($cellUpper, 'ISTIRAHAT');

                    if ($isFixedSlot || $isFixedKeyword) {
                        continue;
                    }

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

                    if (! $mapelId || ! $guruId) {
                        $this->parent->errors[] = "Baris $rowNumber, Kelas $key: Kode Mapel/Guru tidak valid ($cellValue)";
                        $this->parent->failureCount++;

                        continue;
                    }

                    $slotKey = "{$hari}_{$jamKe}_{$kelasId}";

                    // Jika admin menonaktifkan toggle perubahan untuk slot ini, lewati tanpa menimpa database
                    if (! empty($this->skippedSlots) && in_array($slotKey, $this->skippedSlots)) {
                        continue;
                    }

                    // Check if teacher is already assigned in another class at the same day & hour in this import sheet
                    $teacherSlotKey = "{$hari}_{$jamKe}_{$guruId}";
                    if (isset($this->assignedInSheet[$teacherSlotKey])) {
                        $conflictInfo = $this->assignedInSheet[$teacherSlotKey];
                        $conflictClass = is_array($conflictInfo) ? ($conflictInfo['class'] ?? '?') : $conflictInfo;
                        $this->parent->errors[] = "Baris $rowNumber, Kelas $key: Guru ($code2) bentrok di file Excel! Sudah dijadwalkan di Kelas $conflictClass pada hari $hari jam ke-$jamKe.";
                        $this->parent->failureCount++;

                        // Tandai juga slot terdahulu di daftar perubahan bahwa slot tersebut terlibat bentrok
                        if (is_array($conflictInfo) && isset($conflictInfo['change_index'])) {
                            $chIdx = $conflictInfo['change_index'];
                            if (isset($this->parent->changes[$chIdx])) {
                                $this->parent->changes[$chIdx]['is_conflict'] = true;
                                $this->parent->changes[$chIdx]['conflict_note'] = "Bentrok dengan Kelas $key";
                            }
                        }

                        continue;
                    }

                    $changeIndex = count($this->parent->changes);
                    $this->assignedInSheet[$teacherSlotKey] = [
                        'class' => $key,
                        'slot_key' => $slotKey,
                        'change_index' => $changeIndex,
                    ];

                    // Lacak perubahan jadwal pelajaran (Baru vs Diperbarui)
                    $kelasObj = $kelasObjMap[$kelasId] ?? null;
                    $kelasName = $kelasObj ? ($kelasObj->tingkat . $kelasObj->nama_kelas) : $key;

                    $existing = isset($existingJadwals[$hari][$jamKe][$kelasId])
                        ? $existingJadwals[$hari][$jamKe][$kelasId]->first()
                        : null;

                    if ($existing) {
                        if ($existing->mata_pelajaran_id != $mapelId || $existing->guru_id != $guruId) {
                            $oldMapel = $existing->mataPelajaran->kode_mapel ?? '?';
                            $oldGuru = $existing->guru->guruProfile->kode_guru ?? '?';
                            $this->parent->changes[] = [
                                'type' => 'jadwal_update',
                                'kelas_id' => $kelasId,
                                'slot_key' => $slotKey,
                                'kelas' => $kelasName,
                                'hari' => $hari,
                                'jam_ke' => $jamKe,
                                'name' => "Kelas {$kelasName} ({$hari}, Jam ke-{$jamKe})",
                                'old' => "{$oldMapel} {$oldGuru}",
                                'new' => "{$code1} {$code2}",
                                'action' => 'Diperbarui',
                            ];
                        }
                    } else {
                        $this->parent->changes[] = [
                            'type' => 'jadwal_new',
                            'kelas_id' => $kelasId,
                            'slot_key' => $slotKey,
                            'kelas' => $kelasName,
                            'hari' => $hari,
                            'jam_ke' => $jamKe,
                            'name' => "Kelas {$kelasName} ({$hari}, Jam ke-{$jamKe})",
                            'old' => '(Kosong)',
                            'new' => "{$code1} {$code2}",
                            'action' => 'Baru',
                        ];
                    }

                    JadwalPelajaran::updateOrCreate(
                        [
                            'semester_id' => $this->semesterId,
                            'kelas_id' => $kelasId,
                            'hari' => $hari,
                            'jam_ke' => $jamKe,
                        ],
                        [
                            'mata_pelajaran_id' => $mapelId,
                            'guru_id' => $guruId,
                        ]
                    );

                    $this->parent->successCount++;
                }

            } catch (\Exception $e) {
                $this->parent->failureCount++;
                $this->parent->errors[] = "Baris $rowNumber: ".$e->getMessage();
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
