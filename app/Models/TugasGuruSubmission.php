<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TugasGuruSubmission extends Model
{
    use HasFactory;

    protected $table = 'tugas_guru_submissions';

    protected $fillable = [
        'tugas_guru_id',
        'guru_id',
        'konten_tugas',
        'link_eksternal',
        'status_pengumpulan',
        'tanggal_submit',
        'feedback',
        'nilai',
    ];

    protected $casts = [
        'tanggal_submit' => 'datetime',
        'nilai' => 'integer',
    ];

    /**
     * Relationship: Submission belongs to tugas
     */
    public function tugasGuru()
    {
        return $this->belongsTo(TugasGuru::class, 'tugas_guru_id');
    }

    /**
     * Relationship: Submission belongs to guru
     */
    public function guru()
    {
        return $this->belongsTo(User::class, 'guru_id');
    }

    /**
     * Relationship: Submission memiliki banyak file lampiran
     */
    public function files()
    {
        return $this->hasMany(TugasGuruSubmissionFile::class, 'submission_id');
    }

    /**
     * Check if submission is late
     */
    public function isLate()
    {
        if ($this->tanggal_submit && $this->tugasGuru) {
            return $this->tanggal_submit > $this->tugasGuru->deadline;
        }
        return false;
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeAttribute()
    {
        return match($this->status_pengumpulan) {
            'draft' => 'warning',
            'dikumpulkan' => 'success',
            'terlambat' => 'danger',
            default => 'secondary',
        };
    }
}
