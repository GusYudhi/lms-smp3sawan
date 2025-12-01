<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FixedSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'hari',
        'jam_ke',
        'keterangan',
        'semester_id',
    ];

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }
}
