<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TahunPelajaran extends Model
{
    use HasFactory;

    protected $table = 'tahun_pelajaran';

    protected $fillable = [
        'nama',
        'tahun_mulai',
        'tahun_selesai',
        'tanggal_mulai',
        'tanggal_selesai',
        'is_active',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'is_active' => 'boolean',
        'tahun_mulai' => 'integer',
        'tahun_selesai' => 'integer',
    ];

    /**
     * Relationship: Tahun Pelajaran has many Semester
     */
    public function semester()
    {
        return $this->hasMany(Semester::class);
    }

    /**
     * Relationship: Tahun Pelajaran has many Kelas
     */
    public function kelas()
    {
        return $this->hasMany(Kelas::class);
    }

    /**
     * Scope: Get only active tahun pelajaran
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the currently active tahun pelajaran
     */
    public static function getActive()
    {
        return static::where('is_active', true)->first();
    }

    /**
     * Set this tahun pelajaran as active (and deactivate others)
     *
     * @param bool $syncSemester Whether to automatically sync/activate one of its semesters
     */
    public function setActive(bool $syncSemester = true)
    {
        // Deactivate all other tahun pelajaran
        static::where('id', '!=', $this->id)->update(['is_active' => false]);

        // Activate this one
        $this->update(['is_active' => true]);

        if ($syncSemester) {
            $activeSemester = $this->semester()->where('is_active', true)->first()
                ?? $this->semester()->orderBy('semester_ke')->first();

            if ($activeSemester) {
                // Deactivate all other semesters globally
                Semester::where('id', '!=', $activeSemester->id)->update(['is_active' => false]);
                $activeSemester->update(['is_active' => true]);
            }
        }

        return $this;
    }

    /**
     * Get full display name
     * e.g., "2024/2025"
     */
    public function getFullNameAttribute()
    {
        return $this->nama;
    }
}
