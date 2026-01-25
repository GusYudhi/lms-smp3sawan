<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Saran extends Model
{
    use HasFactory;

    protected $table = 'saran';

    protected $fillable = [
        'nama_pengirim',
        'email_pengirim',
        'isi_saran',
        'status',
    ];
}
