<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;

    protected $table = 'kelas';
    protected $fillable = ['nama_kelas', 'tingkat'];

    public function jadwal()
    {
        return $this->hasMany(JadwalPelajaran::class);
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
    public static function findOrCreateByFullName($fullName)
    {
        // Extract tingkat (first character, e.g., "7", "8", "9")
        $tingkat = substr($fullName, 0, 1);
        // Extract nama_kelas (remaining characters, e.g., "A", "B", "C")
        $namaKelas = substr($fullName, 1);

        return self::firstOrCreate(
            [
                'tingkat' => $tingkat,
                'nama_kelas' => $namaKelas
            ]
        );
    }
}
