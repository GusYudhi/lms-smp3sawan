<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

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
        'foto_profil',
        'jenis_kelamin',
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

    /**
     * Get the profile photo URL for this student
     */
    public function getProfilePhotoUrl()
    {
        if ($this->foto_profil && Storage::exists($this->foto_profil)) {
            return Storage::url($this->foto_profil);
        }

        // Fallback to user's photo if available
        if ($this->user && $this->user->profile_photo && Storage::exists($this->user->profile_photo)) {
            return Storage::url($this->user->profile_photo);
        }

        // Default avatar based on gender
        $defaultAvatar = $this->jenis_kelamin === 'P' ? 'avatars/female-default.png' : 'avatars/male-default.png';

        // If default avatar exists, use it, otherwise use a generic one
        if (Storage::exists('public/' . $defaultAvatar)) {
            return Storage::url($defaultAvatar);
        }

        // Fallback to a simple placeholder
        return "https://ui-avatars.com/api/?name=" . urlencode($this->user->name ?? 'Student') .
               "&background=e9ecef&color=495057&size=120";
    }

    /**
     * Get gender display name
     */
    public function getGenderDisplayName()
    {
        return match($this->jenis_kelamin) {
            'L' => 'Laki-laki',
            'P' => 'Perempuan',
            default => 'Belum diisi'
        };
    }
}
