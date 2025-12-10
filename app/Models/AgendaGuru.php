<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgendaGuru extends Model
{
    use HasFactory;

    protected $table = 'agenda_guru';

    protected $fillable = [
        'user_id',
        'tanggal',
        'kelas',
        'jam_mulai_id',
        'jam_selesai_id',
        'materi',
        'status_jurnal',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    /**
     * Get the user that owns the agenda.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get jam mulai
     */
    public function jamMulai()
    {
        return $this->belongsTo(JamPelajaran::class, 'jam_mulai_id');
    }

    /**
     * Get jam selesai
     */
    public function jamSelesai()
    {
        return $this->belongsTo(JamPelajaran::class, 'jam_selesai_id');
    }

    /**
     * Get the day name in Indonesian
     */
    public function getHariAttribute()
    {
        $days = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
        ];

        return $days[$this->tanggal->format('l')] ?? '';
    }

    /**
     * Get jam ke format (e.g., "1-2" or "3")
     */
    public function getJamKeAttribute()
    {
        $jamMulai = $this->jamMulai;
        $jamSelesai = $this->jamSelesai;

        if (!$jamMulai || !$jamSelesai) {
            return '-';
        }

        if ($jamMulai->jam_ke == $jamSelesai->jam_ke) {
            return $jamMulai->jam_ke;
        }
        return $jamMulai->jam_ke . '-' . $jamSelesai->jam_ke;
    }

    /**
     * Get jam display with time (e.g., "1-2 (07:00 - 08:30)")
     */
    public function getJamDisplayAttribute()
    {
        $jamMulai = $this->jamMulai;
        $jamSelesai = $this->jamSelesai;

        if (!$jamMulai || !$jamSelesai) {
            return '-';
        }

        $jamKe = $this->jam_ke;
        $waktu = $jamMulai->jam_mulai . ' - ' . $jamSelesai->jam_selesai;

        return $jamKe . ' (' . $waktu . ')';
    }
}
