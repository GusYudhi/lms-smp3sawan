<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Berita extends Model
{
    use HasFactory;

    protected $table = 'berita';

    protected $fillable = [
        'judul',
        'konten',
        'foto',
        'tanggal',
        'penulis_id',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function penulis()
    {
        return $this->belongsTo(User::class, 'penulis_id');
    }
}
