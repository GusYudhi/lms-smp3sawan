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
}
