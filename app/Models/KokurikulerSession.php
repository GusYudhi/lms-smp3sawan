<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KokurikulerSession extends Model
{
    use HasFactory;

    protected $table = 'kokurikuler_sessions';

    protected $fillable = [
        'semester_id',
        'kelas_id',
        'fixed_schedule_id',
        'penilai_user_id',
        'tanggal',
        'hari',
        'tema',
        'deskripsi',
        'foto_bukti',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function semester()
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function fixedSchedule()
    {
        return $this->belongsTo(FixedSchedule::class, 'fixed_schedule_id');
    }

    public function penilai()
    {
        return $this->belongsTo(User::class, 'penilai_user_id');
    }

    public function penilaians()
    {
        return $this->hasMany(PenilaianKokurikuler::class, 'kokurikuler_session_id');
    }
}

