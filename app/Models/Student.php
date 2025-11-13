<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'password',
        'nisn',
        'nis',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'kelas',
        'nomor_telepon',
        'nomor_telepon_orangtua',
        'profile_photo',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}
