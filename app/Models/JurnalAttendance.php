<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JurnalAttendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'jurnal_mengajar_id',
        'student_profile_id',
        'status',
        'status_awal',
    ];

    /**
     * Relasi ke JurnalMengajar
     */
    public function jurnalMengajar()
    {
        return $this->belongsTo(JurnalMengajar::class);
    }

    /**
     * Relasi ke StudentProfile
     */
    public function studentProfile()
    {
        return $this->belongsTo(StudentProfile::class);
    }
}
