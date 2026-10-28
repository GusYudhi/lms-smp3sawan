<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MataPelajaran extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'mata_pelajarans';

    protected $fillable = [
        'kode_mapel',
        'nama_mapel',
        'is_universal',
        'is_active',
    ];

    protected $casts = [
        'is_universal' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function jadwalPelajarans()
    {
        return $this->hasMany(JadwalPelajaran::class);
    }

    /**
     * The teachers that teach this subject.
     */
    public function guruProfiles()
    {
        return $this->hasMany(GuruProfile::class, 'mata_pelajaran_id');
    }

    public function materiPelajarans()
    {
        return $this->hasMany(MateriPelajaran::class);
    }

    public function jurnalMengajars()
    {
        return $this->hasMany(JurnalMengajar::class);
    }

    /**
     * Cek apakah mata pelajaran memiliki riwayat akademik sehingga tidak boleh dihapus.
     */
    public function hasAcademicRecords(): bool
    {
        return $this->jadwalPelajarans()->exists()
            || $this->guruProfiles()->exists()
            || $this->materiPelajarans()->exists()
            || $this->jurnalMengajars()->exists();
    }
}
