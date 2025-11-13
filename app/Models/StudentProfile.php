<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nis',
        'nisn',
        'tempat_lahir',
        'tanggal_lahir',
        'kelas',
        'nomor_telepon_orangtua',
        'alamat',
        'nama_orangtua_wali',
        'pekerjaan_orangtua',
        'tahun_masuk',
        'is_active',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tahun_masuk' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get the user that owns the student profile.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope active students
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope by class
     */
    public function scopeByClass($query, $class)
    {
        return $query->where('kelas', $class);
    }
}
