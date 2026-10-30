<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FixedSchedule extends Model
{
    use HasFactory;

    public const TIPE_UMUM = 'umum';
    public const TIPE_KOKURIKULER = 'kokurikuler';

    protected $fillable = [
        'hari',
        'jam_ke',
        'keterangan',
        'tipe',
        'semester_id',
    ];

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function scopeKokurikuler($query)
    {
        return $query->where('tipe', self::TIPE_KOKURIKULER);
    }

    public function isKokurikuler(): bool
    {
        return $this->tipe === self::TIPE_KOKURIKULER;
    }

    /**
     * Group consecutive fixed schedules with the same day, type, and description into single unified items.
     *
     * @param iterable $schedules
     * @return \Illuminate\Support\Collection
     */
    public static function groupConsecutive($schedules)
    {
        $collection = collect($schedules);
        if ($collection->isEmpty()) {
            return collect();
        }

        $dayOrder = ['Senin' => 1, 'Selasa' => 2, 'Rabu' => 3, 'Kamis' => 4, 'Jumat' => 5, 'Sabtu' => 6];
        $grouped = collect();

        $byDay = $collection->groupBy('hari')->sortBy(function ($items, $day) use ($dayOrder) {
            return $dayOrder[$day] ?? 99;
        });

        foreach ($byDay as $day => $dayItems) {
            $sortedItems = $dayItems->sortBy('jam_ke')->values();
            $currentGroup = null;

            foreach ($sortedItems as $item) {
                if ($currentGroup === null) {
                    $currentGroup = [
                        'id' => $item->id,
                        'ids' => [$item->id],
                        'hari' => $item->hari,
                        'jam_ke' => (int) $item->jam_ke,
                        'jam_selesai' => (int) $item->jam_ke,
                        'tipe' => $item->tipe,
                        'keterangan' => $item->keterangan,
                        'semester_id' => $item->semester_id,
                    ];
                } else {
                    $isSameActivity = ($item->tipe === $currentGroup['tipe'] && trim((string)$item->keterangan) === trim((string)$currentGroup['keterangan']));
                    $isConsecutive = ((int) $item->jam_ke === $currentGroup['jam_selesai'] + 1);

                    if ($isSameActivity && $isConsecutive) {
                        $currentGroup['ids'][] = $item->id;
                        $currentGroup['jam_selesai'] = (int) $item->jam_ke;
                    } else {
                        $grouped->push(self::formatGroupObject($currentGroup));
                        $currentGroup = [
                            'id' => $item->id,
                            'ids' => [$item->id],
                            'hari' => $item->hari,
                            'jam_ke' => (int) $item->jam_ke,
                            'jam_selesai' => (int) $item->jam_ke,
                            'tipe' => $item->tipe,
                            'keterangan' => $item->keterangan,
                            'semester_id' => $item->semester_id,
                        ];
                    }
                }
            }

            if ($currentGroup !== null) {
                $grouped->push(self::formatGroupObject($currentGroup));
            }
        }

        return $grouped;
    }

    /**
     * Group kokurikuler schedules by day so each day only represents one weekly activity item.
     *
     * @param iterable $schedules
     * @return \Illuminate\Support\Collection
     */
    public static function groupKokurikulerByDay($schedules)
    {
        $groupedConsecutive = self::groupConsecutive($schedules);
        $dayOrder = ['Senin' => 1, 'Selasa' => 2, 'Rabu' => 3, 'Kamis' => 4, 'Jumat' => 5, 'Sabtu' => 6];

        return $groupedConsecutive->groupBy('hari')->map(function ($items) {
            $first = $items->first();
            $allIds = $items->pluck('ids')->flatten()->all();
            $minJam = $items->min('jam_ke');
            $maxJam = $items->max('jam_selesai');
            $jamLabel = ($minJam === $maxJam)
                ? "Jam ke-{$minJam}"
                : "Jam ke-{$minJam} s/d {$maxJam}";

            return (object) [
                'id' => $first->id,
                'ids' => $allIds,
                'hari' => $first->hari,
                'jam_ke' => $minJam,
                'jam_selesai' => $maxJam,
                'jam_label' => $jamLabel,
                'tipe' => self::TIPE_KOKURIKULER,
                'keterangan' => $first->keterangan,
                'semester_id' => $first->semester_id,
            ];
        })->sortBy(function ($item) use ($dayOrder) {
            return $dayOrder[$item->hari] ?? 99;
        })->values();
    }

    protected static function formatGroupObject(array $data)
    {
        $jamLabel = ($data['jam_ke'] === $data['jam_selesai'])
            ? "Jam ke-{$data['jam_ke']}"
            : "Jam ke-{$data['jam_ke']} s/d {$data['jam_selesai']}";

        return (object) array_merge($data, [
            'jam_label' => $jamLabel,
            'is_multijam' => ($data['jam_ke'] !== $data['jam_selesai']),
            'total_jam' => ($data['jam_selesai'] - $data['jam_ke'] + 1),
        ]);
    }
}
