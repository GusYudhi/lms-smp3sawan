<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'nomor_induk',
        'nomor_telepon',
        'profile_photo',
        'profile_photo_path',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'status_kepegawaian',
        'golongan',
        'mata_pelajaran',
        'wali_kelas',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Check if user is admin
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is kepala sekolah
     */
    public function isKepalaSekolah()
    {
        return $this->role === 'kepala_sekolah';
    }

    /**
     * Check if user is guru
     */
    public function isGuru()
    {
        return $this->role === 'guru';
    }

    /**
     * Check if user is siswa
     */
    public function isSiswa()
    {
        return $this->role === 'siswa';
    }

    /**
     * Get role display name
     */
    public function getRoleDisplayName()
    {
        return match($this->role) {
            'admin' => 'Administrator',
            'kepala_sekolah' => 'Kepala Sekolah',
            'guru' => 'Guru',
            'siswa' => 'Siswa',
            default => 'User'
        };
    }

    /**
     * Get nomor induk label based on role
     */
    public function getNomorIndukLabel()
    {
        return match($this->role) {
            'siswa' => 'NISN',
            'guru', 'kepala_sekolah' => 'NIP',
            'admin' => 'ID Admin',
            default => 'Nomor Induk'
        };
    }

    /**
     * Get profile photo URL
     */
    public function getProfilePhotoUrl()
    {
        if ($this->profile_photo) {
            return asset('storage/profile_photos/' . $this->profile_photo);
        }
        return asset('assets/image/profile-default.svg');
    }
}
