<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenilaianKokurikuler extends Model
{
    use HasFactory;

    protected $table = 'penilaian_kokurikuler';

    public const PREDIKAT_LABELS = [
        'B'   => 'Berkembang',
        'SB'  => 'Sedang Berkembang',
        'BSH' => 'Berkembang Sesuai Harapan',
        'SAB' => 'Sangat Berkembang',
    ];

    public const PREDIKAT_BADGES = [
        'B'   => 'secondary',
        'SB'  => 'info',
        'BSH' => 'primary',
        'SAB' => 'success',
    ];

    public const ABSENSI_LABELS = [
        'hadir'     => 'Hadir',
        'terlambat' => 'Terlambat',
        'sakit'     => 'Sakit',
        'izin'      => 'Izin',
        'alpa'      => 'Alpa',
    ];

    public const ABSENSI_BADGES = [
        'hadir'     => 'success',
        'terlambat' => 'warning text-dark',
        'sakit'     => 'warning text-dark',
        'izin'      => 'info text-dark',
        'alpa'      => 'danger',
    ];

    protected $fillable = [
        'kokurikuler_session_id',
        'semester_id',
        'kelas_id',
        'fixed_schedule_id',
        'tanggal',
        'hari',
        'tema',
        'student_user_id',
        'penilai_user_id',
        'status_absensi',
        'predikat',
        'catatan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function session()
    {
        return $this->belongsTo(KokurikulerSession::class, 'kokurikuler_session_id');
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function fixedSchedule()
    {
        return $this->belongsTo(FixedSchedule::class, 'fixed_schedule_id');
    }

    public function siswa()
    {
        return $this->belongsTo(User::class, 'student_user_id');
    }

    public function penilai()
    {
        return $this->belongsTo(User::class, 'penilai_user_id');
    }

    public function isPresent(): bool
    {
        return in_array($this->status_absensi ?? 'hadir', ['hadir', 'terlambat'], true);
    }

    public function getPredikatLabelAttribute(): string
    {
        if (!$this->predikat) {
            return self::ABSENSI_LABELS[$this->status_absensi] ?? '-';
        }
        return self::PREDIKAT_LABELS[$this->predikat] ?? $this->predikat;
    }

    public function getPredikatBadgeAttribute(): string
    {
        if (!$this->predikat) {
            return self::ABSENSI_BADGES[$this->status_absensi] ?? 'secondary';
        }
        return self::PREDIKAT_BADGES[$this->predikat] ?? 'secondary';
    }
}
