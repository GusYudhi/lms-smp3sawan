<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingHero extends Model
{
    use HasFactory;

    protected $table = 'landing_heroes';

    protected $fillable = [
        'judul',
        'subjudul',
        'gambar',
        'urutan',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'urutan' => 'integer',
    ];
}

