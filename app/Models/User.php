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
        'password' => 'hashed',
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

    // relasi ke kelas
    public function kelas() {
        return $this->hasOneThrough(
            Kelas::class,
            StudentProfile::class,
            'user_id', // Foreign key on student_profiles table
            'id',       // Foreign key on kelas table
            'id',       // Local key on users table
            'kelas_id'  // Local key on student_profiles table
        );
    }

    /**
     * Get profile photo URL
     */
    public function getProfilePhotoUrl()
    {
        // For students, try to get photo from student profile first
        if ($this->role === 'siswa' && $this->studentProfile) {
            return $this->studentProfile->getProfilePhotoUrl();
        }

        // For guru/kepala sekolah, get photo from guru profile
        if (in_array($this->role, ['guru', 'kepala_sekolah']) && $this->guruProfile) {
            return $this->guruProfile->getProfilePhotoUrl();
        }

        // Fallback to a simple placeholder
        return "https://ui-avatars.com/api/?name=" . urlencode($this->name) .
               "&background=e9ecef&color=495057&size=120";
    }    /**
     * Get the student profile for the user.
     */
    public function studentProfile()
    {
        return $this->hasOne(StudentProfile::class);
    }

    /**
     * Get the teacher profile for the user.
     */
    public function guruProfile()
    {
        return $this->hasOne(GuruProfile::class);
    }

    /**
     * Get the teacher profile for the user (alias for backward compatibility).
     */
    public function teacherProfile()
    {
        return $this->guruProfile();
    }

    /**
     * Check if user has teacher profile
     */
    public function hasTeacherProfile()
    {
        return $this->guruProfile()->exists();
    }

    /**
     * Check if user has guru profile
     */
    public function hasGuruProfile()
    {
        return $this->guruProfile()->exists();
    }

    /**
     * Check if user has student profile
     */
    public function hasStudentProfile()
    {
        return $this->studentProfile()->exists();
    }

    /**
     * Get specific profile based on role
     */
    public function getProfile()
    {
        return match($this->role) {
            'siswa' => $this->studentProfile,
            'guru', 'kepala_sekolah' => $this->guruProfile,
            default => null
        };
    }
}
