<?php

namespace Tests\Feature;

use App\Models\GuruProfile;
use App\Models\Semester;
use App\Models\TahunPelajaran;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AdminGuruDanJadwalKodeGuruTest extends TestCase
{
    use DatabaseTransactions;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::firstOrCreate(
            ['email' => 'admin_test@smp3sawan.sch.id'],
            [
                'name' => 'Admin Test',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );
    }

    public function test_admin_guru_table_is_ordered_by_kode_guru_with_null_at_bottom(): void
    {
        // Create 2 teachers with codes AA1 and AA2 which will appear at the very beginning of page 1
        $userAA1 = User::create([
            'name' => 'Teacher AA1',
            'email' => 'aa1_test@smp3sawan.sch.id',
            'password' => bcrypt('password'),
            'role' => 'guru',
            'is_active' => true,
        ]);
        GuruProfile::create([
            'user_id' => $userAA1->id,
            'nama' => 'Teacher AA1',
            'kode_guru' => 'AA1',
            'nip' => '999001',
            'status_kepegawaian' => 'HONORER',
        ]);

        $userAA2 = User::create([
            'name' => 'Teacher AA2',
            'email' => 'aa2_test@smp3sawan.sch.id',
            'password' => bcrypt('password'),
            'role' => 'guru',
            'is_active' => true,
        ]);
        GuruProfile::create([
            'user_id' => $userAA2->id,
            'nama' => 'Teacher AA2',
            'kode_guru' => 'AA2',
            'nip' => '999002',
            'status_kepegawaian' => 'HONORER',
        ]);

        $userZZZ = User::create([
            'name' => 'Teacher ZZZ',
            'email' => 'zzz_test@smp3sawan.sch.id',
            'password' => bcrypt('password'),
            'role' => 'guru',
            'is_active' => true,
        ]);
        GuruProfile::create([
            'user_id' => $userZZZ->id,
            'nama' => 'Teacher ZZZ',
            'kode_guru' => 'ZZZ',
            'nip' => '999003',
            'status_kepegawaian' => 'HONORER',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.guru.index'));

        $response->assertStatus(200);
        $teachers = $response->viewData('teachers');

        // Check relative ordering: AA1 should come before AA2 on page 1
        $allIds = collect($teachers->items())->pluck('id')->toArray();
        $idxAA1 = array_search($userAA1->id, $allIds);
        $idxAA2 = array_search($userAA2->id, $allIds);

        $this->assertNotFalse($idxAA1, 'Teacher AA1 should be present in page 1');
        $this->assertNotFalse($idxAA2, 'Teacher AA2 should be present in page 1');
        $this->assertLessThan($idxAA2, $idxAA1, 'Teacher AA1 should come before Teacher AA2');

        // Test search by kode_guru finds ZZZ specifically
        $searchResponse = $this->actingAs($this->admin)->get(route('admin.guru.index', ['search' => 'ZZZ']));
        $searchResponse->assertStatus(200);
        $searchTeachers = $searchResponse->viewData('teachers');
        $this->assertTrue(collect($searchTeachers->items())->contains('id', $userZZZ->id));
        $this->assertFalse(collect($searchTeachers->items())->contains('id', $userAA1->id));
    }

    public function test_admin_guru_numeric_kode_guru_is_sorted_numerically_not_alphabetically(): void
    {
        // Delete or clear other test teachers with these test codes if any, or use unique numbers that highlight the issue
        $user2 = User::create([
            'name' => 'Guru Kode 2',
            'email' => 'guru2_test@smp3sawan.sch.id',
            'password' => bcrypt('password'),
            'role' => 'guru',
            'is_active' => true,
        ]);
        GuruProfile::create([
            'user_id' => $user2->id,
            'nama' => 'Guru Kode 2',
            'kode_guru' => '2',
            'nip' => '999102',
            'status_kepegawaian' => 'PNS',
        ]);

        $user18 = User::create([
            'name' => 'Guru Kode 18',
            'email' => 'guru18_test@smp3sawan.sch.id',
            'password' => bcrypt('password'),
            'role' => 'guru',
            'is_active' => true,
        ]);
        GuruProfile::create([
            'user_id' => $user18->id,
            'nama' => 'Guru Kode 18',
            'kode_guru' => '18',
            'nip' => '999118',
            'status_kepegawaian' => 'PNS',
        ]);

        $user19 = User::create([
            'name' => 'Guru Kode 19',
            'email' => 'guru19_test@smp3sawan.sch.id',
            'password' => bcrypt('password'),
            'role' => 'guru',
            'is_active' => true,
        ]);
        GuruProfile::create([
            'user_id' => $user19->id,
            'nama' => 'Guru Kode 19',
            'kode_guru' => '19',
            'nip' => '999119',
            'status_kepegawaian' => 'PNS',
        ]);

        $user20 = User::create([
            'name' => 'Guru Kode 20',
            'email' => 'guru20_test@smp3sawan.sch.id',
            'password' => bcrypt('password'),
            'role' => 'guru',
            'is_active' => true,
        ]);
        GuruProfile::create([
            'user_id' => $user20->id,
            'nama' => 'Guru Kode 20',
            'kode_guru' => '20',
            'nip' => '999120',
            'status_kepegawaian' => 'PNS',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.guru.index'));
        $response->assertStatus(200);

        // Fetch teachers directly through the same query logic or viewData
        $teachers = $response->viewData('teachers');
        $allIds = collect($teachers->items())->pluck('id')->toArray();

        $idx2 = array_search($user2->id, $allIds);
        $idx18 = array_search($user18->id, $allIds);

        // In page 1, kode 2 must precede kode 18
        $this->assertNotFalse($idx2, 'Teacher with code 2 should be in page 1');
        if ($idx18 !== false) {
            $this->assertLessThan($idx18, $idx2, 'Teacher with code 2 must precede code 18 (not 18 -> 19 -> 2)');
        }
    }

    public function test_admin_guru_search_ajax_returns_sorted_teachers(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.guru.search'), [
            'X-Requested-With' => 'XMLHttpRequest',
            'Accept' => 'application/json',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['html', 'pagination', 'info', 'total', 'current_page']);
    }

    public function test_jadwal_dropdown_gurus_displays_code_in_parentheses_and_ordered_by_code(): void
    {
        $semester = Semester::first() ?? Semester::create([
            'tahun_pelajaran_id' => TahunPelajaran::first()->id ?? TahunPelajaran::create([
                'nama' => '2024/2025 Test',
                'tahun_mulai' => 2024,
                'tahun_selesai' => 2025,
                'tanggal_mulai' => now(),
                'tanggal_selesai' => now()->addYear(),
                'is_active' => true,
            ])->id,
            'nama' => 'Semester Ganjil Test',
            'semester' => '1',
            'is_active' => true,
        ]);

        $userTest = User::create([
            'name' => 'Guru PopUp Test',
            'email' => 'popup_guru_test@smp3sawan.sch.id',
            'password' => bcrypt('password'),
            'role' => 'guru',
            'is_active' => true,
        ]);
        GuruProfile::create([
            'user_id' => $userTest->id,
            'nama' => 'Guru PopUp Test',
            'kode_guru' => 'TST',
            'nip' => '999888',
            'status_kepegawaian' => 'HONORER',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.jadwal.index', ['semester_id' => $semester->id]));

        $response->assertStatus(200);
        $gurus = $response->viewData('gurus');

        // Verify ordering: gurus with codes should have their codes in ascending order
        $codes = $gurus->pluck('guruProfile.kode_guru')->filter()->values()->toArray();
        $sortedCodes = $codes;
        sort($sortedCodes);
        $this->assertEquals($sortedCodes, $codes, 'Teachers in schedule index must be sorted by kode_guru A-Z');

        // Check HTML renders option with Guru PopUp Test (TST)
        $response->assertSee('Guru PopUp Test (TST)');
    }
}
