<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalPelajaran extends Model
{
    use HasFactory;

    protected $fillable = [
        'kelas_id',
        'mata_pelajaran_id',
        'guru_id',
        'hari',
        'jam_ke',
        'jam_pelajaran_id',
        'semester_id',
    ];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class);
    }

    public function guru()
    {
        return $this->belongsTo(User::class, 'guru_id');
    }

    public function jamPelajaran()
    {
        return $this->belongsTo(JamPelajaran::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    /**
     * Gabungkan jadwal yang berurutan dengan kelas dan mata pelajaran yang sama
     * menjadi satu blok/sesi (misal Jam 1-2 atau 1-3).
     *
     * @param \Illuminate\Support\Collection $schedules
     * @param \Illuminate\Support\Collection|null $existingJurnals
     * @return \Illuminate\Support\Collection
     */
    public static function groupConsecutiveSchedules($schedules, $existingJurnals = null)
    {
        if ($schedules->isEmpty()) {
            return collect();
        }

        // Deduplikasi jadwal berdasarkan kelas_id, mata_pelajaran_id, dan jam_ke
        // agar duplikasi data (misal re-import atau filter semester longgar) tidak merusak pengelompokan
        $unique = $schedules->unique(function ($item) {
            return $item->kelas_id . '_' . $item->mata_pelajaran_id . '_' . $item->jam_ke;
        });

        $sorted = $unique->sortBy('jam_ke')->values();
        $grouped = [];
        $current = null;

        foreach ($sorted as $item) {
            if ($current === null) {
                $current = [
                    'id' => $item->id,
                    'schedule_ids' => [$item->id],
                    'kelas_id' => $item->kelas_id,
                    'kelas' => $item->kelas,
                    'mata_pelajaran_id' => $item->mata_pelajaran_id,
                    'mataPelajaran' => $item->mataPelajaran,
                    'guru_id' => $item->guru_id,
                    'guru' => $item->guru,
                    'hari' => $item->hari,
                    'jam_ke_mulai' => $item->jam_ke,
                    'jam_ke_selesai' => $item->jam_ke,
                    'jam_ke_array' => [$item->jam_ke],
                    'jumlah_jp' => 1,
                    'has_jurnal' => false,
                    'jurnal' => null,
                ];
                continue;
            }

            // Jika kelas sama dan mapel sama
            if (
                $current['kelas_id'] == $item->kelas_id &&
                $current['mata_pelajaran_id'] == $item->mata_pelajaran_id
            ) {
                // Jika jam_ke sudah tercakup di dalam blok ini (duplikasi), jangan pecah grup
                if (in_array($item->jam_ke, $current['jam_ke_array'])) {
                    if (!in_array($item->id, $current['schedule_ids'])) {
                        $current['schedule_ids'][] = $item->id;
                    }
                    continue;
                }

                // Jika jam_ke bersambung (+1)
                if ($item->jam_ke == ($current['jam_ke_selesai'] + 1)) {
                    $current['jam_ke_selesai'] = $item->jam_ke;
                    $current['jam_ke_array'][] = $item->jam_ke;
                    $current['jumlah_jp'] = count($current['jam_ke_array']);
                    $current['schedule_ids'][] = $item->id;
                    continue;
                }
            }

            $grouped[] = $current;
            $current = [
                'id' => $item->id,
                'schedule_ids' => [$item->id],
                'kelas_id' => $item->kelas_id,
                'kelas' => $item->kelas,
                'mata_pelajaran_id' => $item->mata_pelajaran_id,
                'mataPelajaran' => $item->mataPelajaran,
                'guru_id' => $item->guru_id,
                'guru' => $item->guru,
                'hari' => $item->hari,
                'jam_ke_mulai' => $item->jam_ke,
                'jam_ke_selesai' => $item->jam_ke,
                'jam_ke_array' => [$item->jam_ke],
                'jumlah_jp' => 1,
                'has_jurnal' => false,
                'jurnal' => null,
            ];
        }

        if ($current !== null) {
            $grouped[] = $current;
        }

        // Cocokkan dengan jurnal yang sudah diisi bila parameter disertakan
        if ($existingJurnals && $existingJurnals->isNotEmpty()) {
            foreach ($grouped as &$group) {
                $matched = $existingJurnals->first(function ($jurnal) use ($group) {
                    return $jurnal->kelas_id == $group['kelas_id']
                        && $jurnal->mata_pelajaran_id == $group['mata_pelajaran_id']
                        && ($jurnal->jam_ke_mulai <= $group['jam_ke_selesai'] && $jurnal->jam_ke_selesai >= $group['jam_ke_mulai']);
                });

                if ($matched) {
                    $group['has_jurnal'] = true;
                    $group['jurnal'] = $matched;
                }
            }
            unset($group);
        }

        return collect($grouped);
    }
}
