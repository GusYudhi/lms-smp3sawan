<?php

namespace Tests\Feature;

use App\Models\GuruProfile;
use App\Models\MataPelajaran;
use App\Models\Semester;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AdminGuruMultiMapelTest extends TestCase
{
    use DatabaseTransactions;

    protected $admin;
    protected $mapel1;
    protected $mapel2;
    protected $mapel3;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::firstOrCreate(
            ['email' => 'admin_test_multimapel@admin.id'],
            [
                'name' => 'Admin MultiMapel Test',
                'password' => bcrypt('password123'),
                'role' => 'admin',
            ]
        );

        $this->mapel1 = MataPelajaran::firstOrCreate(
            ['kode_mapel' => 'TST1'],
            ['nama_mapel' => 'Test Mapel Utama', 'is_active' => true]
        );

        $this->mapel2 = MataPelajaran::firstOrCreate(
            ['kode_mapel' => 'TST2'],
            ['nama_mapel' => 'Test Mapel Tambahan A', 'is_active' => true]
        );

        $this->mapel3 = MataPelajaran::firstOrCreate(
            ['kode_mapel' => 'TST3'],
            ['nama_mapel' => 'Test Mapel Tambahan B', 'is_active' => true]
        );
    }

    public function test_admin_can_create_teacher_with_multi_select_subjects()
    {
        $response = $this->actingAs($this->admin)->from(route('admin.guru.create'))->post(route('admin.guru.store'), [
            'name' => 'Guru Multi Mapel Baru',
            'nomor_induk' => '9988776655',
            'kode_guru' => 'GMM',
            'email' => 'guru.multimapel@sekolah.id',
            'nomor_telepon' => '08123456789',
            'jenis_kelamin' => 'L',
            'jabatan_di_sekolah' => 'Guru',
            'mata_pelajaran' => [$this->mapel1->id, $this->mapel2->id, $this->mapel3->id],
            'status_kepegawaian' => 'PNS',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        if (session('errors')) {
            $this->fail('Validation errors: ' . json_encode(session('errors')->all()));
        }

        $response->assertRedirect(route('admin.guru.index'));

        $teacher = User::where('email', 'guru.multimapel@sekolah.id')->with('guruProfile.mataPelajarans')->first();
        $this->assertNotNull($teacher);
        $this->assertEquals($this->mapel1->id, $teacher->guruProfile->mata_pelajaran_id);

        $allSubjectIds = $teacher->guruProfile->mataPelajarans->pluck('id')->toArray();
        $this->assertContains($this->mapel1->id, $allSubjectIds);
        $this->assertContains($this->mapel2->id, $allSubjectIds);
        $this->assertContains($this->mapel3->id, $allSubjectIds);
    }

    public function test_admin_can_update_teacher_with_multi_select_subjects()
    {
        $teacher = User::create([
            'name' => 'Guru Update Mapel',
            'email' => 'guru.updatemapel@sekolah.id',
            'password' => bcrypt('password123'),
            'role' => 'guru',
        ]);

        $profile = GuruProfile::create([
            'user_id' => $teacher->id,
            'nip' => '1122334455',
            'kode_guru' => 'GUM',
            'nama' => 'Guru Update Mapel',
            'jenis_kelamin' => 'P',
            'mata_pelajaran_id' => $this->mapel1->id,
            'is_active' => true,
        ]);
        $profile->mataPelajarans()->sync([$this->mapel1->id]);

        $response = $this->actingAs($this->admin)->put(route('admin.guru.update', $teacher->id), [
            'name' => 'Guru Update Mapel Edited',
            'nomor_induk' => '1122334455',
            'kode_guru' => 'GUM',
            'email' => 'guru.updatemapel@sekolah.id',
            'jenis_kelamin' => 'P',
            'jabatan_di_sekolah' => 'Guru',
            'mata_pelajaran' => [$this->mapel2->id, $this->mapel3->id], // Mapel 2 jadi utama
            'status_kepegawaian' => 'PNS',
        ]);

        $response->assertRedirect(route('admin.guru.index'));

        $profile->refresh();
        $this->assertEquals($this->mapel2->id, $profile->mata_pelajaran_id);

        $allSubjectIds = $profile->mataPelajarans()->pluck('mata_pelajarans.id')->toArray();
        $this->assertEqualsCanonicalizing([$this->mapel2->id, $this->mapel3->id], $allSubjectIds);

        $additionalIds = $profile->mataPelajaransTambahan()->pluck('mata_pelajarans.id')->toArray();
        $this->assertEqualsCanonicalizing([$this->mapel3->id], $additionalIds);
        $this->assertNotContains($this->mapel2->id, $additionalIds);
    }

    public function test_duplicate_mapel_in_array_is_automatically_deduplicated()
    {
        $teacher = User::create([
            'name' => 'Guru Duplikat Mapel',
            'email' => 'guru.dup@sekolah.id',
            'password' => bcrypt('password123'),
            'role' => 'guru',
        ]);

        $profile = GuruProfile::create([
            'user_id' => $teacher->id,
            'nip' => '3344556677',
            'kode_guru' => 'DUP',
            'nama' => 'Guru Duplikat Mapel',
            'jenis_kelamin' => 'L',
            'mata_pelajaran_id' => $this->mapel1->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->put(route('admin.guru.update', $teacher->id), [
            'name' => 'Guru Duplikat Mapel',
            'nomor_induk' => '3344556677',
            'kode_guru' => 'DUP',
            'email' => 'guru.dup@sekolah.id',
            'jenis_kelamin' => 'L',
            'jabatan_di_sekolah' => 'Guru',
            'mata_pelajaran' => [$this->mapel1->id, $this->mapel1->id, $this->mapel2->id], // mapel1 dobel di input
            'status_kepegawaian' => 'PNS',
        ]);

        $response->assertRedirect(route('admin.guru.index'));

        $profile->refresh();
        $allSubjectIds = $profile->mataPelajarans()->pluck('mata_pelajarans.id')->toArray();
        $this->assertEqualsCanonicalizing([$this->mapel1->id, $this->mapel2->id], $allSubjectIds);
    }

    public function test_admin_guru_filter_matches_teachers_with_additional_subjects()
    {
        $teacher = User::create([
            'name' => 'Guru Filter Tambahan Test',
            'email' => 'guru.filtert@sekolah.id',
            'password' => bcrypt('password123'),
            'role' => 'guru',
        ]);

        $profile = GuruProfile::create([
            'user_id' => $teacher->id,
            'nip' => '5566778899',
            'kode_guru' => 'FLT',
            'nama' => 'Guru Filter Tambahan Test',
            'jenis_kelamin' => 'L',
            'mata_pelajaran_id' => $this->mapel1->id, // Utama: mapel1
            'is_active' => true,
        ]);
        // Tambahan: mapel2
        $profile->mataPelajarans()->sync([$this->mapel1->id, $this->mapel2->id]);

        // Cari via AJAX dengan filter mapel2
        $response = $this->actingAs($this->admin)->getJson(route('admin.guru.search', [
            'mata_pelajaran' => $this->mapel2->nama_mapel,
        ]), ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        $this->assertStringContainsString('Guru Filter Tambahan Test', $response->json('html'));
        $this->assertStringContainsString($this->mapel2->nama_mapel, $response->json('html'));
    }

    public function test_jadwal_dropdown_contains_all_taught_subjects_in_data_attribute()
    {
        $activeSemester = Semester::where('is_active', true)->first();
        if (!$activeSemester) {
            $this->markTestSkipped('Tidak ada semester aktif');
        }

        $teacher = User::create([
            'name' => 'Guru Jadwal Dropdown Multi',
            'email' => 'guru.dropdownmulti@sekolah.id',
            'password' => bcrypt('password123'),
            'role' => 'guru',
        ]);

        $profile = GuruProfile::create([
            'user_id' => $teacher->id,
            'nip' => '7788990011',
            'kode_guru' => 'JDM',
            'nama' => 'Guru Jadwal Dropdown Multi',
            'jenis_kelamin' => 'P',
            'mata_pelajaran_id' => $this->mapel1->id,
            'is_active' => true,
        ]);
        $profile->mataPelajarans()->sync([$this->mapel1->id, $this->mapel2->id]);

        $response = $this->actingAs($this->admin)->get(route('admin.jadwal.index', ['semester_id' => $activeSemester->id]));
        $response->assertStatus(200);

        // Pastikan opsi guru memiliki data-mapel-ids yang mencakup kedua ID mapel
        $content = $response->getContent();
        $this->assertStringContainsString('data-mapel-ids="[' . $this->mapel1->id . ',' . $this->mapel2->id . ']"', $content);
    }

    public function test_edit_teacher_view_renders_primary_subject_first_regardless_of_alphabetical_order()
    {
        // Buat mapel ZZZ (alfabet belakang) dan AAA (alfabet depan)
        $mapelZ = MataPelajaran::create([
            'kode_mapel' => 'ZZZ',
            'nama_mapel' => 'Z-Mapel Utama Belakang',
            'is_active' => true,
        ]);
        $mapelA = MataPelajaran::create([
            'kode_mapel' => 'AAA',
            'nama_mapel' => 'A-Mapel Tambahan Depan',
            'is_active' => true,
        ]);

        $teacher = User::create([
            'name' => 'Guru Cek Urutan Mapel',
            'email' => 'guru.urutan@sekolah.id',
            'password' => bcrypt('password123'),
            'role' => 'guru',
        ]);

        $profile = GuruProfile::create([
            'user_id' => $teacher->id,
            'nip' => '9900112233',
            'kode_guru' => 'URT',
            'nama' => 'Guru Cek Urutan Mapel',
            'jenis_kelamin' => 'L',
            'mata_pelajaran_id' => $mapelZ->id, // UTAMA adalah ZZZ (abjad belakang)
            'is_active' => true,
        ]);
        // Pivot menyimpan keduanya
        $profile->mataPelajarans()->sync([$mapelA->id, $mapelZ->id]);

        $response = $this->actingAs($this->admin)->get(route('admin.guru.edit', $teacher->id));
        $response->assertStatus(200);

        $html = $response->getContent();

        // Cari posisi option selected
        $posOptionZ = strpos($html, '<option value="' . $mapelZ->id . '" selected>');
        $posOptionA = strpos($html, '<option value="' . $mapelA->id . '" selected>');

        $this->assertNotFalse($posOptionZ, 'Option mapel utama ZZZ harus ada dan selected');
        $this->assertNotFalse($posOptionA, 'Option mapel tambahan AAA harus ada dan selected');

        // Pastikan mapel utama (Z) muncul LEBIH DULU dibanding mapel tambahan (A) di HTML select!
        $this->assertLessThan(
            $posOptionA,
            $posOptionZ,
            'Mapel Utama (Z) HARUS dirender sebelum Mapel Tambahan (A) meskipun secara alfabet A lebih dulu!'
        );
    }

    public function test_create_teacher_view_preserves_old_input_order_with_primary_subject_first()
    {
        $mapelZ = MataPelajaran::create([
            'kode_mapel' => 'ZCR',
            'nama_mapel' => 'Z-Create Mapel Utama',
            'is_active' => true,
        ]);
        $mapelA = MataPelajaran::create([
            'kode_mapel' => 'ACR',
            'nama_mapel' => 'A-Create Mapel Tambahan',
            'is_active' => true,
        ]);

        // Simulasikan session flash old('mata_pelajaran') dengan urutan Z lalu A
        $response = $this->actingAs($this->admin)
            ->withSession([
                '_old_input' => [
                    'mata_pelajaran' => [$mapelZ->id, $mapelA->id],
                ]
            ])
            ->get(route('admin.guru.create'));

        $response->assertStatus(200);

        $html = $response->getContent();
        $posOptionZ = strpos($html, '<option value="' . $mapelZ->id . '" selected>');
        $posOptionA = strpos($html, '<option value="' . $mapelA->id . '" selected>');

        $this->assertNotFalse($posOptionZ, 'Option ZZZ harus ada dan selected dari old input');
        $this->assertNotFalse($posOptionA, 'Option AAA harus ada dan selected dari old input');

        $this->assertLessThan(
            $posOptionA,
            $posOptionZ,
            'Pada create form dengan old input, Mapel Pertama (Z) HARUS dirender sebelum (A)!'
        );
    }
}
