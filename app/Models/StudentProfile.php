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
        'kelas_id',
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

    protected $appends = ['kelas_name'];

    /**
     * Get the user that owns the student profile.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the class that this student belongs to.
     */
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    /**
     * Get full class name (accessor for views)
     * Menggunakan getFullName() dari model Kelas
     */
    public function getKelasNameAttribute()
    {
        return $this->kelas ? $this->kelas->full_name : null;
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
        if (is_numeric($class)) {
            // If it's a kelas_id
            return $query->where('kelas_id', $class);
        } else {
            // If it's a full name like "7A", find the kelas first
            return $query->whereHas('kelas', function($q) use ($class) {
                if (strlen($class) == 1) {
                    // Only tingkat provided (e.g., "7")
                    $q->where('tingkat', $class);
                } else {
                    // Full class name (e.g., "7A")
                    $tingkat = substr($class, 0, 1);
                    $namaKelas = substr($class, 1);
                    $q->where('tingkat', $tingkat)->where('nama_kelas', $namaKelas);
                }
            });
        }
    }

    /**
     * Get the profile photo URL for this student
     */
    public function getProfilePhotoUrl()
    {
        if ($this->foto_profil && Storage::exists('public/profile_photos/' . $this->foto_profil)) {
            return Storage::url('profile_photos/' . $this->foto_profil);
        }

        // Fallback to user's photo if available
        if ($this->user && $this->user->profile_photo && Storage::exists('public/profile_photos/' . $this->user->profile_photo)) {
            return Storage::url('profile_photos/' . $this->user->profile_photo);
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
