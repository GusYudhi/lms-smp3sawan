<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MataPelajaran extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_mapel',
        'nama_mapel',
        'semester_id',
        'is_universal'
    ];

    protected $casts = [
        'is_universal' => 'boolean',
    ];

    public function jadwalPelajarans()
    {
        return $this->hasMany(JadwalPelajaran::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    /**
     * The teachers that teach this subject.
     */
    public function guruProfiles()
    {
        return $this->hasMany(GuruProfile::class, 'mata_pelajaran_id');
    }
}
