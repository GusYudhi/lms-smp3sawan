<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $table = 'attendance';

    protected $fillable = [
        'user_id',
        'date',
        'time',
        'status',
        'created_by',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'time' => 'datetime',
    ];

    /**
     * Get the student that owns the attendance.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the teacher who recorded this attendance.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
