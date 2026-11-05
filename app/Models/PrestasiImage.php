<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrestasiImage extends Model
{
    use HasFactory;

    protected $table = 'prestasi_images';

    protected $fillable = [
        'prestasi_id',
        'foto',
        'urutan',
    ];

    public function prestasi()
    {
        return $this->belongsTo(Prestasi::class, 'prestasi_id');
    }
}

