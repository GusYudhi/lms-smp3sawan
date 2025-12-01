<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuruAttendance extends Model
{
    use HasFactory;

    protected $table = 'guru_attendances';

    protected $fillable = [
        'user_id',
        'tanggal',
        'waktu_absen',
        'status',
        'photo_path',
        'latitude',
        'longitude',
        'distance_from_school',
        'accuracy'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'waktu_absen' => 'datetime',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'distance_from_school' => 'decimal:2',
        'accuracy' => 'decimal:2'
    ];

    /**
     * Get the user that owns the attendance
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get status badge HTML
     */
    public function getStatusBadge()
    {
        $badges = [
            'hadir' => '<span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Hadir</span>',
            'terlambat' => '<span class="badge bg-warning"><i class="fas fa-clock me-1"></i>Terlambat</span>',
            'izin' => '<span class="badge bg-info"><i class="fas fa-file-medical me-1"></i>Izin</span>',
            'alpha' => '<span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i>Alpha</span>'
        ];

        return $badges[$this->status] ?? '<span class="badge bg-secondary">-</span>';
    }

    /**
     * Get photo URL
     */
    public function getPhotoUrl()
    {
        if ($this->photo_path) {
            return asset('storage/' . $this->photo_path);
        }
        return null;
    }
}
