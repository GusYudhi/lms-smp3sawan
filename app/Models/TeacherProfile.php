<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nip',
        'tempat_lahir',
        'tanggal_lahir',
        'alamat',
        'status_kepegawaian',
        'golongan',
        'mata_pelajaran',
        'wali_kelas',
        'pendidikan_terakhir',
        'tahun_mulai_mengajar',
        'sertifikat',
        'is_active',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'mata_pelajaran' => 'array',
        'tahun_mulai_mengajar' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get the user that owns the teacher profile.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope active teachers
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get subjects as comma-separated string
     */
    public function getSubjectsStringAttribute()
    {
        return is_array($this->mata_pelajaran) ? implode(', ', $this->mata_pelajaran) : '';
    }
}
