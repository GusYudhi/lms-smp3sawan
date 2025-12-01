<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JamPelajaran extends Model
{
    use HasFactory;

    protected $fillable = [
        'jam_ke',
        'jam_mulai',
        'jam_selesai',
        'semester_id',
    ];

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }
}
