<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TugasGuruSubmissionFile extends Model
{
    use HasFactory;

    protected $table = 'tugas_guru_submission_files';

    protected $fillable = [
        'submission_id',
        'nama_file',
        'file_path',
        'file_type',
        'file_size',
    ];

    /**
     * Relationship: File belongs to submission
     */
    public function submission()
    {
        return $this->belongsTo(TugasGuruSubmission::class, 'submission_id');
    }

    /**
     * Get formatted file size
     */
    public function getFormattedSizeAttribute()
    {
        $size = $this->file_size;

        if ($size < 1024) {
            return $size . ' B';
        } elseif ($size < 1048576) {
            return round($size / 1024, 2) . ' KB';
        } else {
            return round($size / 1048576, 2) . ' MB';
        }
    }
}
