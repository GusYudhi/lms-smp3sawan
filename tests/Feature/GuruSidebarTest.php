<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\GuruProfile;
use App\Models\MataPelajaran;
use Tests\TestCase;

class GuruSidebarTest extends TestCase
{
    public function test_agenda_sidebar_item_is_highlighted_when_visiting_guru_agenda()
    {
        $mapel = MataPelajaran::firstOrCreate(
            ['kode_mapel' => 'SBGMAPEL'],
            ['nama_mapel' => 'Sidebar Guru Mapel', 'is_active' => true]
        );

        $teacher = User::factory()->create([
            'role' => 'guru',
            'password' => 'password',
        ]);

        GuruProfile::create([
            'user_id' => $teacher->id,
            'nama' => $teacher->name,
            'nip' => 'NIP' . rand(100000, 999999),
            'email' => $teacher->email,
            'is_active' => true,
            'mata_pelajaran_id' => $mapel->id,
        ]);

        $response = $this->actingAs($teacher)->get('/guru/agenda');

        $response->assertStatus(200);
        // Ensure the active link with Agenda Guru exists in sidebar
        $response->assertSee('nav-link active', false);
        $response->assertSee('Agenda Guru');
    }
}
