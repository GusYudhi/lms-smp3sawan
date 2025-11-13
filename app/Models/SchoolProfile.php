<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'visi',
        'misi',
        'alamat',
        'telepon',
        'email',
        'website',
        'maps_latitude',
        'maps_longitude',
        'kepala_sekolah',
        'tahun_berdiri',
        'akreditasi',
        'npsn'
    ];

    protected $casts = [
        'misi' => 'array', // Automatically cast JSON to array
        'maps_latitude' => 'float',
        'maps_longitude' => 'float',
        'tahun_berdiri' => 'integer'
    ];
}
