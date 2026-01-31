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
        'nip',
        'nama', // redundant with user but kept for history
        'tempat_lahir',
        'tanggal_lahir',
        'alamat',
        'status_kepegawaian',
        'golongan',
        'jabatan_di_sekolah',
        'kelas_id', // wali kelas
        'mata_pelajaran_id', // Single subject
        'pendidikan_terakhir',
        'tahun_mulai_mengajar',
        'sertifikat',
        'foto_profil',
        'nomor_telepon',
        'email',
        'jenis_kelamin',
        'is_active',
        'password'
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Get the subject taught by the teacher.
     */
    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class, 'mata_pelajaran_id');
    }

    /**
     * Get subjects as a comma-separated string (backward compatibility).
     */
    public function getSubjectsStringAttribute()
    {
        return $this->mataPelajaran ? $this->mataPelajaran->nama_mapel : '-';
    }

    /**
     * Scope active teachers
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
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
     * Get the position (jabatan) at school
     */
    public function getJabatanDiSekolahAttribute()
    {
        return $this->attributes['jabatan_di_sekolah'] ?? null;
    }

    /**
     * Get school where this teacher is kepala sekolah
     */
    public function schoolAsKepalaSekolah()
    {
        return $this->hasOne(SchoolProfile::class, 'id_kepala_sekolah');
    }

    /**
     * Get the kelas that this teacher is wali kelas of
     */
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }
}
