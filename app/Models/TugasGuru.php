<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TugasGuru extends Model
{
    use HasFactory;

    protected $table = 'tugas_guru';

    protected $fillable = [
        'judul',
        'deskripsi',
        'deadline',
        'created_by',
        'status',
    ];

    protected $casts = [
        'deadline' => 'datetime',
    ];

    /**
     * Relationship: Tugas dibuat oleh kepala sekolah
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relationship: Tugas memiliki banyak file lampiran
     */
    public function files()
    {
        return $this->hasMany(TugasGuruFile::class, 'tugas_guru_id');
    }

    /**
     * Relationship: Tugas memiliki banyak submission dari guru
     */
    public function submissions()
    {
        return $this->hasMany(TugasGuruSubmission::class, 'tugas_guru_id');
    }

    /**
     * Get submission count
     */
    public function getSubmissionCountAttribute()
    {
        return $this->submissions()->where('status_pengumpulan', 'dikumpulkan')->count();
    }

    /**
     * Check if deadline has passed
     */
    public function isExpired()
    {
        return $this->deadline < now();
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'aktif' => 'success',
            'selesai' => 'secondary',
            'dibatalkan' => 'danger',
            default => 'secondary',
        };
    }
}
