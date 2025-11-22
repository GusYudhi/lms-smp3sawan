<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class GuruProfile extends Model
{
    use HasFactory;

    protected $table = 'guru_profiles';

    protected $fillable = [
        'user_id',
        'nama',
        'nip',
        'foto_profil',
        'nomor_telepon',
        'email',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'status_kepegawaian',
        'golongan',
        'mata_pelajaran',
        'wali_kelas',
        'password',
        'is_active',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'mata_pelajaran' => 'array',
        'is_active' => 'boolean',
        'password' => 'hashed',
    ];

    /**
     * Get the user that owns the guru profile.
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

    /**
     * Get profile photo URL
     */
    public function getProfilePhotoUrl()
    {
        if ($this->foto_profil && Storage::exists('public/profile_photos/' . $this->foto_profil)) {
            return Storage::url('profile_photos/' . $this->foto_profil);
        }

        // Default avatar based on gender
        $defaultAvatar = $this->jenis_kelamin === 'P' ? 'avatars/female-default.png' : 'avatars/male-default.png';

        // If default avatar exists, use it, otherwise use a generic one
        if (Storage::exists('public/' . $defaultAvatar)) {
            return Storage::url($defaultAvatar);
        }

        // Fallback to a simple placeholder
        return "https://ui-avatars.com/api/?name=" . urlencode($this->nama) .
               "&background=e9ecef&color=495057&size=120";
    }

    /**
     * Check if this teacher is kepala sekolah
     */
    public function isKepalaSekolah()
    {
        return $this->user && $this->user->role === 'kepala_sekolah';
    }

    /**
     * Get school where this teacher is kepala sekolah
     */
    public function schoolAsKepalaSekolah()
    {
        return $this->hasOne(SchoolProfile::class, 'id_kepala_sekolah');
    }
}
