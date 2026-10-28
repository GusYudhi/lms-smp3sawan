<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\GuruProfile;
use App\Models\StudentProfile;
use App\Models\MataPelajaran;
use Tests\TestCase;

class ProfileAccessTest extends TestCase
{
    public function test_authenticated_teacher_can_access_profile_page()
    {
        $mapel = MataPelajaran::firstOrCreate(
            ['kode_mapel' => 'TESTMP'],
            ['nama_mapel' => 'Mata Pelajaran Test', 'is_active' => true]
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

        $response = $this->actingAs($teacher)->get('/profile');
        $response->assertStatus(200);
        $response->assertSee('Mata Pelajaran Test');
    }

    public function test_authenticated_student_can_access_profile_page()
    {
        $student = User::factory()->create([
            'role' => 'siswa',
            'password' => 'password',
        ]);

        StudentProfile::create([
            'user_id' => $student->id,
            'nisn' => 'NISN' . rand(100000, 999999),
            'nama_lengkap' => $student->name,
        ]);

        $response = $this->actingAs($student)->get('/profile');
        $response->assertStatus(200);
    }
}

