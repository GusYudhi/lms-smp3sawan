<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    use HasFactory;

    protected $table = 'semester';

    protected $fillable = [
        'tahun_pelajaran_id',
        'nama',
        'semester_ke',
        'tanggal_mulai',
        'tanggal_selesai',
        'is_active',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'is_active' => 'boolean',
        'semester_ke' => 'integer',
    ];

    /**
     * Relationship: Semester belongs to Tahun Pelajaran
     */
    public function tahunPelajaran()
    {
        return $this->belongsTo(TahunPelajaran::class);
    }

    /**
     * Relationship: Semester has many Jadwal Pelajaran
     */
    public function jadwalPelajaran()
    {
        return $this->hasMany(JadwalPelajaran::class);
    }

    /**
     * Relationship: Semester has many Fixed Schedules (Jadwal Tetap)
     */
    public function fixedSchedules()
    {
        return $this->hasMany(FixedSchedule::class);
    }

    /**
     * Relationship: Semester has many Jam Pelajaran
     */
    public function jamPelajaran()
    {
        return $this->hasMany(JamPelajaran::class);
    }

    /**
     * Scope: Get only active semester
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the currently active semester
     */
    public static function getActive()
    {
        return static::where('is_active', true)
            ->whereHas('tahunPelajaran', function ($query) {
                $query->where('is_active', true);
            })
            ->first() ?? static::where('is_active', true)->first();
    }

    /**
     * Set this semester as active (and deactivate all other semesters)
     */
    public function setActive()
    {
        // Deactivate all other semesters across all academic years
        static::where('id', '!=', $this->id)->update(['is_active' => false]);

        // Activate this one
        $this->update(['is_active' => true]);

        // Also make sure the parent tahun pelajaran is active
        if ($this->tahunPelajaran) {
            $this->tahunPelajaran->setActive(false);
        }

        return $this;
    }

    /**
     * Get full display name with tahun pelajaran
     * e.g., "2024/2025 Ganjil"
     */
    public function getFullNameAttribute()
    {
        return $this->tahunPelajaran->nama . ' - ' . $this->nama;
    }
}
