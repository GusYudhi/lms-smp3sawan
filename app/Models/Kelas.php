<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;

    protected $table = 'kelas';
    protected $fillable = ['nama_kelas', 'tingkat', 'tahun_angkatan', 'tahun_pelajaran_id'];

    protected $casts = [
        'tahun_angkatan' => 'integer',
    ];

    /**
     * Relationship: Kelas belongs to Tahun Pelajaran
     */
    public function tahunPelajaran()
    {
        return $this->belongsTo(TahunPelajaran::class);
    }

    public function jadwal()
    {
        return $this->hasMany(JadwalPelajaran::class);
    }

    /**
     * Relationship: Kelas has one Wali Kelas (Guru)
     */
    public function waliKelas()
    {
        return $this->hasOne(GuruProfile::class, 'kelas_id');
    }

    public function students()
    {
        return $this->hasMany(StudentProfile::class, 'kelas_id');
    }

    /**
     * Get full class name (tingkat + nama_kelas)
     * e.g., "7A", "8B"
     */
    public function getFullNameAttribute()
    {
        return $this->tingkat . $this->nama_kelas;
    }

    /**
     * Find or create a class by full name (e.g., "7A", "8B")
     */
    public static function findOrCreateByFullName($fullName, $tahunAngkatan = null)
    {
        // Extract tingkat (first character, e.g., "7", "8", "9")
        $tingkat = substr($fullName, 0, 1);
        // Extract nama_kelas (remaining characters, e.g., "A", "B", "C")
        $namaKelas = substr($fullName, 1);

        $data = [
            'tingkat' => $tingkat,
            'nama_kelas' => $namaKelas
        ];

        if ($tahunAngkatan) {
            $data['tahun_angkatan'] = $tahunAngkatan;
        }

        return self::firstOrCreate($data);
    }

    /**
     * Scope: Filter by tahun angkatan
     */
    public function scopeByAngkatan($query, $tahun)
    {
        return $query->where('tahun_angkatan', $tahun);
    }

    /**
     * Method to promote class to next grade (naik kelas)
     * tingkat 7 -> 8, 8 -> 9, 9 -> lulus (return null or flag)
     */
    public function naikKelas($tahunPelajaranBaru = null)
    {
        if ($this->tingkat >= 9) {
            // Siswa lulus, tidak naik kelas lagi
            return null;
        }

        $this->tingkat = $this->tingkat + 1;

        if ($tahunPelajaranBaru) {
            $this->tahun_pelajaran_id = $tahunPelajaranBaru;
        }

        $this->save();

        return $this;
    }
}
