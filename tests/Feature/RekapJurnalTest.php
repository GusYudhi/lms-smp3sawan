<?php

namespace Tests\Feature;

use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\User;
use Tests\TestCase;

class RekapJurnalTest extends TestCase
{
    public function test_admin_can_access_rekap_jurnal_page_without_filters()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'password' => 'password',
        ]);

        $mapel = MataPelajaran::firstOrCreate(
            ['kode_mapel' => 'RJMAPEL'],
            ['nama_mapel' => 'Rekap Jurnal Mapel', 'is_active' => true]
        );

        $response = $this->actingAs($admin)->get(route('rekap-jurnal.index'));

        $response->assertStatus(200);
        $response->assertSee('Rekapitulasi Jurnal & Absensi');
        $response->assertSee('Rekap Jurnal Mapel');
    }

    public function test_admin_can_filter_rekap_jurnal()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'password' => 'password',
        ]);

        $kelas = Kelas::firstOrCreate(
            ['nama_kelas' => '7A'],
            ['tingkat' => 7]
        );

        $mapel = MataPelajaran::firstOrCreate(
            ['kode_mapel' => 'RJMAPEL2'],
            ['nama_mapel' => 'Rekap Jurnal Mapel 2', 'is_active' => true]
        );

        $response = $this->actingAs($admin)->get(route('rekap-jurnal.index', [
            'kelas_id' => $kelas->id,
            'mapel_id' => $mapel->id,
            'start_date' => now()->startOfMonth()->toDateString(),
            'end_date' => now()->toDateString(),
        ]));

        $response->assertStatus(200);
        $response->assertSee('Data Absensi Siswa');
    }
}
