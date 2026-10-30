<?php

namespace Tests\Feature;

use App\Models\Kelas;
use App\Services\UserManagementService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class KelasSanitizationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_find_or_create_by_full_name_resolves_existing_class_without_duplicate(): void
    {
        $existing = Kelas::where('tingkat', '7')->where('nama_kelas', 'A')->first();
        $this->assertNotNull($existing);

        $resolvedNormal = Kelas::findOrCreateByFullName('7A');
        $this->assertEquals($existing->id, $resolvedNormal->id);
        $this->assertEquals('7A', $resolvedNormal->full_name);

        $resolvedDuplicated = Kelas::findOrCreateByFullName('77A');
        $this->assertEquals($existing->id, $resolvedDuplicated->id);
        $this->assertEquals('7A', $resolvedDuplicated->full_name);
    }

    public function test_user_management_service_sanitizes_kelas_input_when_tingkat_is_prefixed(): void
    {
        $existing = Kelas::where('tingkat', '7')->where('nama_kelas', 'A')->first();
        $this->assertNotNull($existing);

        $initialKelasCount = Kelas::count();
        $service = new UserManagementService();

        // Simulate user typing tingkat = 7 and kelas = '7A'
        $student = $service->createStudent(
            [
                'name' => 'Siswa Test Sanitasi',
                'email' => 'siswa.sanitasi@example.com',
                'password' => '12345678',
            ],
            [
                'tingkat' => '7',
                'kelas' => '7A',
                'nis' => '999999999',
            ]
        );

        $this->assertEquals($existing->id, $student->studentProfile->kelas_id);
        $this->assertEquals('7A', $student->studentProfile->kelas->full_name);
        $this->assertEquals($initialKelasCount, Kelas::count());
    }
}
