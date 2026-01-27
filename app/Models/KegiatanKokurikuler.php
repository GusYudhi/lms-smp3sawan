<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KegiatanKokurikuler extends Model
{
    use HasFactory;

    protected $table = 'kegiatan_kokurikuler';

    protected $fillable = [
        'nama',
        'deskripsi',
        'foto',
        'link',
        'tipe',
        'tanggal',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];
}
