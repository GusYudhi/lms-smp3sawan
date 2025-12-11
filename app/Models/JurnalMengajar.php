<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JurnalMengajar extends Model
{
    use HasFactory;

    protected $table = 'jurnal_mengajar';

    protected $fillable = [
        'guru_id',
        'kelas_id',
        'mata_pelajaran_id',
        'tanggal',
        'hari',
        'jam_ke_mulai',
        'jam_ke_selesai',
        'materi_pembelajaran',
        'keterangan',
        'foto_bukti',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    /**
     * Relasi ke Guru (User)
     */
    public function guru()
    {
        return $this->belongsTo(User::class, 'guru_id');
    }

    /**
     * Relasi ke Kelas
     */
    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    /**
     * Relasi ke Mata Pelajaran
     */
    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class);
    }

    /**
     * Relasi ke JamPelajaran (mulai)
     */
    public function jamPelajaranMulai()
    {
        return $this->belongsTo(JamPelajaran::class, 'jam_ke_mulai', 'jam_ke')
                    ->where('semester_id', function($query) {
                        $query->select('id')
                              ->from('semester')
                              ->where('is_active', true)
                              ->limit(1);
                    });
    }

    /**
     * Relasi ke JamPelajaran (selesai)
     */
    public function jamPelajaranSelesai()
    {
        return $this->belongsTo(JamPelajaran::class, 'jam_ke_selesai', 'jam_ke')
                    ->where('semester_id', function($query) {
                        $query->select('id')
                              ->from('semester')
                              ->where('is_active', true)
                              ->limit(1);
                    });
    }

    /**
     * Accessor untuk mendapatkan jam_mulai dari relasi
     */
    public function getJamMulaiAttribute()
    {
        return $this->jamPelajaranMulai ? $this->jamPelajaranMulai->jam_mulai : null;
    }

    /**
     * Accessor untuk mendapatkan jam_selesai dari relasi
     */
    public function getJamSelesaiAttribute()
    {
        return $this->jamPelajaranSelesai ? $this->jamPelajaranSelesai->jam_selesai : null;
    }

    /**
     * Relasi ke JurnalAttendance
     */
    public function jurnalAttendances()
    {
        return $this->hasMany(JurnalAttendance::class);
    }

    /**
     * Scope untuk filter berdasarkan guru
     */
    public function scopeByGuru($query, $guruId)
    {
        return $query->where('guru_id', $guruId);
    }

    /**
     * Scope untuk filter berdasarkan tanggal
     */
    public function scopeByTanggal($query, $tanggal)
    {
        return $query->whereDate('tanggal', $tanggal);
    }

    /**
     * Scope untuk filter berdasarkan bulan
     */
    public function scopeByBulan($query, $bulan, $tahun)
    {
        return $query->whereMonth('tanggal', $bulan)
                    ->whereYear('tanggal', $tahun);
    }
}
