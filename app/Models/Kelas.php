<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;

    protected $table = 'kelas';
    protected $fillable = ['nama_kelas', 'tingkat', 'tahun_angkatan', 'tahun_pelajaran_id', 'guru_pendamping_id'];

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

    /**
     * Relationship: Kelas belongs to Guru Pendamping (Guru)
     */
    public function guruPendamping()
    {
        return $this->belongsTo(GuruProfile::class, 'guru_pendamping_id');
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
     * Find or create a class by full name (e.g., "7A", "8B", "77A", "Kelas 7A")
     */
    public static function findOrCreateByFullName($fullName, $tahunAngkatan = null)
    {
        $clean = trim(preg_replace('/^kelas\s*/i', '', trim((string)$fullName)));

        // Handle possible duplication like "77A", "7-A", "7 A"
        if (preg_match('/^([789])+\s*[-_]?\s*([A-Za-z0-9]+)$/i', $clean, $matches)) {
            $tingkat = substr($clean, 0, 1);
            $namaKelas = strtoupper($matches[2]);
        } else {
            $tingkat = substr($clean, 0, 1);
            $namaKelas = strtoupper(substr($clean, 1));
        }

        // Always check if existing class already matches tingkat & nama_kelas
        $existing = self::where('tingkat', $tingkat)->where('nama_kelas', $namaKelas)->first();
        if ($existing) {
            return $existing;
        }

        $data = [
            'tingkat' => $tingkat,
            'nama_kelas' => $namaKelas
        ];

        if ($tahunAngkatan) {
            $data['tahun_angkatan'] = $tahunAngkatan;
        }

        return self::create($data);
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
