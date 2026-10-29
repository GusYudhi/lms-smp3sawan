<?php

namespace Tests\Feature;

use App\Models\GuruProfile;
use App\Models\JadwalPelajaran;
use App\Models\JamPelajaran;
use App\Models\JurnalMengajar;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Semester;
use App\Models\TahunPelajaran;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class JurnalConsecutiveGroupingTest extends TestCase
{
    use DatabaseTransactions;

    protected $guru;

    protected $semester;

    protected $kelas7A;

    protected $mapelMtk;

    protected function setUp(): void
    {
        parent::setUp();

        $activeSemester = Semester::where('is_active', true)->first();
        if ($activeSemester) {
            $this->semester = $activeSemester;
            $tp = $activeSemester->tahunPelajaran;
        } else {
            $tp = TahunPelajaran::firstOrCreate(
                ['nama' => '2024/2025 Test TP Jurnal'],
                [
                    'tahun_mulai' => 2024,
                    'tahun_selesai' => 2025,
                    'tanggal_mulai' => now()->toDateString(),
                    'tanggal_selesai' => now()->addYear()->toDateString(),
                    'is_active' => true,
                ]
            );

            $this->semester = Semester::firstOrCreate(
                ['semester_ke' => 1, 'tahun_pelajaran_id' => $tp->id],
                [
                    'nama' => 'Ganjil Jurnal Test',
                    'tanggal_mulai' => now()->toDateString(),
                    'tanggal_selesai' => now()->addMonths(6)->toDateString(),
                    'is_active' => true,
                ]
            );
        }

        // Jam pelajaran 1 - 4
        JamPelajaran::firstOrCreate(
            ['jam_ke' => 1, 'semester_id' => $this->semester->id],
            ['jam_mulai' => '07:00:00', 'jam_selesai' => '07:40:00']
        );
        JamPelajaran::firstOrCreate(
            ['jam_ke' => 2, 'semester_id' => $this->semester->id],
            ['jam_mulai' => '07:40:00', 'jam_selesai' => '08:20:00']
        );
        JamPelajaran::firstOrCreate(
            ['jam_ke' => 3, 'semester_id' => $this->semester->id],
            ['jam_mulai' => '08:20:00', 'jam_selesai' => '09:00:00']
        );
        JamPelajaran::firstOrCreate(
            ['jam_ke' => 4, 'semester_id' => $this->semester->id],
            ['jam_mulai' => '09:15:00', 'jam_selesai' => '09:55:00']
        );

        $this->kelas7A = Kelas::firstOrCreate(
            ['tingkat' => 7, 'nama_kelas' => 'A'],
            ['tahun_angkatan' => 2024, 'tahun_pelajaran_id' => $tp->id]
        );

        $this->mapelMtk = MataPelajaran::firstOrCreate(
            ['kode_mapel' => 'MTKGROUP'],
            ['nama_mapel' => 'Matematika Grouping Test', 'is_active' => true]
        );

        $this->guru = User::firstOrCreate(
            ['email' => 'guru_grouping_test@guru.id'],
            ['name' => 'Guru Grouping Test', 'password' => bcrypt('password'), 'role' => 'guru']
        );

        GuruProfile::updateOrCreate(
            ['user_id' => $this->guru->id],
            [
                'nip' => '198701012015011099',
                'kode_guru' => 'GGT',
                'nama' => 'Guru Grouping Test',
                'status_kepegawaian' => 'PNS',
                'mata_pelajaran_id' => $this->mapelMtk->id,
                'is_active' => true,
            ]
        );
    }

    public function test_group_consecutive_schedules_merges_sequential_hours_into_single_block()
    {
        $today = Carbon::now()->locale('id')->dayName;

        // Buat jadwal berurutan: Jam 1, 2, 3 di Kelas 7A
        $s1 = JadwalPelajaran::create([
            'semester_id' => $this->semester->id,
            'kelas_id' => $this->kelas7A->id,
            'mata_pelajaran_id' => $this->mapelMtk->id,
            'guru_id' => $this->guru->id,
            'hari' => $today,
            'jam_ke' => 1,
        ]);
        $s2 = JadwalPelajaran::create([
            'semester_id' => $this->semester->id,
            'kelas_id' => $this->kelas7A->id,
            'mata_pelajaran_id' => $this->mapelMtk->id,
            'guru_id' => $this->guru->id,
            'hari' => $today,
            'jam_ke' => 2,
        ]);
        $s3 = JadwalPelajaran::create([
            'semester_id' => $this->semester->id,
            'kelas_id' => $this->kelas7A->id,
            'mata_pelajaran_id' => $this->mapelMtk->id,
            'guru_id' => $this->guru->id,
            'hari' => $today,
            'jam_ke' => 3,
        ]);

        $schedules = collect([$s3, $s1, $s2]); // Acak urutan
        $grouped = JadwalPelajaran::groupConsecutiveSchedules($schedules);

        $this->assertCount(1, $grouped);
        $block = $grouped->first();
        $this->assertEquals(1, $block['jam_ke_mulai']);
        $this->assertEquals(3, $block['jam_ke_selesai']);
        $this->assertEquals(3, $block['jumlah_jp']);
        $this->assertEquals([1, 2, 3], $block['jam_ke_array']);
    }

    public function test_group_consecutive_schedules_handles_separate_sessions_in_same_class()
    {
        $today = Carbon::now()->locale('id')->dayName;

        // Sesi 1: Jam 1 - 2
        JadwalPelajaran::create([
            'semester_id' => $this->semester->id,
            'kelas_id' => $this->kelas7A->id,
            'mata_pelajaran_id' => $this->mapelMtk->id,
            'guru_id' => $this->guru->id,
            'hari' => $today,
            'jam_ke' => 1,
        ]);
        JadwalPelajaran::create([
            'semester_id' => $this->semester->id,
            'kelas_id' => $this->kelas7A->id,
            'mata_pelajaran_id' => $this->mapelMtk->id,
            'guru_id' => $this->guru->id,
            'hari' => $today,
            'jam_ke' => 2,
        ]);

        // Sesi 2: Jam 4 (misal setelah jeda jam 3)
        JadwalPelajaran::create([
            'semester_id' => $this->semester->id,
            'kelas_id' => $this->kelas7A->id,
            'mata_pelajaran_id' => $this->mapelMtk->id,
            'guru_id' => $this->guru->id,
            'hari' => $today,
            'jam_ke' => 4,
        ]);

        $schedules = JadwalPelajaran::where('guru_id', $this->guru->id)
            ->where('hari', $today)
            ->get();

        $grouped = JadwalPelajaran::groupConsecutiveSchedules($schedules);

        $this->assertCount(2, $grouped);
        $this->assertEquals(1, $grouped[0]['jam_ke_mulai']);
        $this->assertEquals(2, $grouped[0]['jam_ke_selesai']);
        $this->assertEquals(2, $grouped[0]['jumlah_jp']);

        $this->assertEquals(4, $grouped[1]['jam_ke_mulai']);
        $this->assertEquals(4, $grouped[1]['jam_ke_selesai']);
        $this->assertEquals(1, $grouped[1]['jumlah_jp']);
    }

    public function test_group_consecutive_schedules_handles_duplicate_jam_ke_without_fragmenting_blocks()
    {
        $today = Carbon::now()->locale('id')->dayName;

        // Simulasi kondisi duplikasi: Jam 1 dan Jam 2 masing-masing memiliki 2 baris (misal dari multiple semester/import)
        $s1_a = JadwalPelajaran::create([
            'semester_id' => $this->semester->id,
            'kelas_id' => $this->kelas7A->id,
            'mata_pelajaran_id' => $this->mapelMtk->id,
            'guru_id' => $this->guru->id,
            'hari' => $today,
            'jam_ke' => 1,
        ]);
        $s1_b = JadwalPelajaran::create([
            'semester_id' => $this->semester->id,
            'kelas_id' => $this->kelas7A->id,
            'mata_pelajaran_id' => $this->mapelMtk->id,
            'guru_id' => $this->guru->id,
            'hari' => $today,
            'jam_ke' => 1,
        ]);
        $s2_a = JadwalPelajaran::create([
            'semester_id' => $this->semester->id,
            'kelas_id' => $this->kelas7A->id,
            'mata_pelajaran_id' => $this->mapelMtk->id,
            'guru_id' => $this->guru->id,
            'hari' => $today,
            'jam_ke' => 2,
        ]);
        $s2_b = JadwalPelajaran::create([
            'semester_id' => $this->semester->id,
            'kelas_id' => $this->kelas7A->id,
            'mata_pelajaran_id' => $this->mapelMtk->id,
            'guru_id' => $this->guru->id,
            'hari' => $today,
            'jam_ke' => 2,
        ]);

        $schedules = collect([$s1_a, $s1_b, $s2_a, $s2_b]);
        $grouped = JadwalPelajaran::groupConsecutiveSchedules($schedules);

        // Harus tetap menjadi 1 blok terpadu (Jam 1 - 2), TIDAK terpecah menjadi Jam 1, Jam 1-2, dan Jam 2
        $this->assertCount(1, $grouped);
        $block = $grouped->first();
        $this->assertEquals(1, $block['jam_ke_mulai']);
        $this->assertEquals(2, $block['jam_ke_selesai']);
        $this->assertEquals(2, $block['jumlah_jp']);
        $this->assertEquals([1, 2], $block['jam_ke_array']);
    }

    public function test_jurnal_create_page_only_queries_active_semester_schedules()
    {
        $today = Carbon::now()->locale('id')->dayName;

        // Semester lama yang tidak aktif
        $oldSemester = Semester::create([
            'nama' => 'Semester Lama Inaktif',
            'semester_ke' => 2,
            'tahun_pelajaran_id' => $this->semester->tahun_pelajaran_id,
            'tanggal_mulai' => now()->subYear()->toDateString(),
            'tanggal_selesai' => now()->subMonths(6)->toDateString(),
            'is_active' => false,
        ]);

        // Buat jadwal di semester lama (Jam 1 di Kelas 7A)
        JadwalPelajaran::create([
            'semester_id' => $oldSemester->id,
            'kelas_id' => $this->kelas7A->id,
            'mata_pelajaran_id' => $this->mapelMtk->id,
            'guru_id' => $this->guru->id,
            'hari' => $today,
            'jam_ke' => 1,
        ]);

        // Buat jadwal di semester aktif (Jam 1 dan Jam 2 di Kelas 7A)
        JadwalPelajaran::create([
            'semester_id' => $this->semester->id,
            'kelas_id' => $this->kelas7A->id,
            'mata_pelajaran_id' => $this->mapelMtk->id,
            'guru_id' => $this->guru->id,
            'hari' => $today,
            'jam_ke' => 1,
        ]);
        JadwalPelajaran::create([
            'semester_id' => $this->semester->id,
            'kelas_id' => $this->kelas7A->id,
            'mata_pelajaran_id' => $this->mapelMtk->id,
            'guru_id' => $this->guru->id,
            'hari' => $today,
            'jam_ke' => 2,
        ]);

        $response = $this->actingAs($this->guru)->get(route('guru.jurnal-mengajar.create'));
        $response->assertStatus(200);

        // Hanya boleh ada blok "Jam ke-1 - 2", TIDAK boleh ada blok ganda "Jam ke-1"
        $response->assertSee('Jam ke-1 - 2');
        $response->assertDontSee('Jam ke-1</h5>', false); // Cek header kartu tidak ada yang hanya Jam ke-1
    }

    public function test_guru_today_schedule_page_displays_grouped_blocks_and_wizard_link()
    {
        $today = Carbon::now()->locale('id')->dayName;

        // Beri guru jadwal Jam 1 dan 2 di Kelas 7A
        JadwalPelajaran::create([
            'semester_id' => $this->semester->id,
            'kelas_id' => $this->kelas7A->id,
            'mata_pelajaran_id' => $this->mapelMtk->id,
            'guru_id' => $this->guru->id,
            'hari' => $today,
            'jam_ke' => 1,
        ]);
        JadwalPelajaran::create([
            'semester_id' => $this->semester->id,
            'kelas_id' => $this->kelas7A->id,
            'mata_pelajaran_id' => $this->mapelMtk->id,
            'guru_id' => $this->guru->id,
            'hari' => $today,
            'jam_ke' => 2,
        ]);

        $response = $this->actingAs($this->guru)->get(route('guru.jadwal-mengajar.today'));
        $response->assertStatus(200);

        // Harus menampilkan badge 1 - 2 dan 2 JP
        $response->assertSee('1 - 2');
        $response->assertSee('2 JP');
        $response->assertSee('Belum Mengisi');

        // Parameter link wizard harus mencakup jam_ke_mulai=1 dan jam_ke_selesai=2
        $response->assertSee('jam_ke_mulai=1');
        $response->assertSee('jam_ke_selesai=2');
    }

    public function test_filling_journal_for_grouped_slot_marks_entire_slot_as_completed()
    {
        $today = Carbon::now()->locale('id')->dayName;

        JadwalPelajaran::create([
            'semester_id' => $this->semester->id,
            'kelas_id' => $this->kelas7A->id,
            'mata_pelajaran_id' => $this->mapelMtk->id,
            'guru_id' => $this->guru->id,
            'hari' => $today,
            'jam_ke' => 1,
        ]);
        JadwalPelajaran::create([
            'semester_id' => $this->semester->id,
            'kelas_id' => $this->kelas7A->id,
            'mata_pelajaran_id' => $this->mapelMtk->id,
            'guru_id' => $this->guru->id,
            'hari' => $today,
            'jam_ke' => 2,
        ]);

        // Simpan 1 jurnal untuk jam 1-2
        JurnalMengajar::create([
            'guru_id' => $this->guru->id,
            'kelas_id' => $this->kelas7A->id,
            'mata_pelajaran_id' => $this->mapelMtk->id,
            'tanggal' => Carbon::today(),
            'hari' => $today,
            'jam_ke_mulai' => 1,
            'jam_ke_selesai' => 2,
            'materi_pembelajaran' => 'Aljabar Lanjutan',
        ]);

        $response = $this->actingAs($this->guru)->get(route('guru.jadwal-mengajar.today'));
        $response->assertStatus(200);

        // Hanya boleh ada 1 baris sesi yang berstatus Sudah Mengisi
        $response->assertSee('Sudah Mengisi');
        $response->assertSee('Lihat Jurnal');
        $response->assertDontSee('Belum Mengisi');
        $response->assertSee('1 <small class="fs-6 text-muted">Sesi', false);
    }

    public function test_store_prevents_duplicate_overlapping_journal()
    {
        $today = Carbon::now()->locale('id')->dayName;

        // Buat 1 jurnal yang sudah ada di Jam 1-2
        $existing = JurnalMengajar::create([
            'guru_id' => $this->guru->id,
            'kelas_id' => $this->kelas7A->id,
            'mata_pelajaran_id' => $this->mapelMtk->id,
            'tanggal' => Carbon::today()->format('Y-m-d'),
            'hari' => $today,
            'jam_ke_mulai' => 1,
            'jam_ke_selesai' => 2,
            'materi_pembelajaran' => 'Aljabar Awal',
        ]);

        // Submit ulang jurnal yang beririsan (misal Jam 1-2 lagi)
        $response = $this->actingAs($this->guru)->post(route('guru.jurnal-mengajar.store'), [
            'tanggal' => Carbon::today()->format('Y-m-d'),
            'kelas_id' => $this->kelas7A->id,
            'mata_pelajaran_id' => $this->mapelMtk->id,
            'jam_ke_mulai' => 1,
            'jam_ke_selesai' => 2,
            'materi_pembelajaran' => 'Aljabar Duplikat',
            'foto_bukti' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
        ]);

        // Harus diredirect ke halaman show jurnal yang sudah ada dan tidak membuat duplikat di DB
        $response->assertRedirect(route('guru.jurnal-mengajar.show', $existing->id));
        $this->assertEquals(1, JurnalMengajar::where('guru_id', $this->guru->id)->count());
    }

    public function test_store_redirects_to_today_schedule_when_return_to_today_is_passed()
    {
        $response = $this->actingAs($this->guru)->post(route('guru.jurnal-mengajar.store'), [
            'tanggal' => Carbon::today()->format('Y-m-d'),
            'kelas_id' => $this->kelas7A->id,
            'mata_pelajaran_id' => $this->mapelMtk->id,
            'jam_ke_mulai' => 1,
            'jam_ke_selesai' => 2,
            'materi_pembelajaran' => 'Aljabar Sukses',
            'foto_bukti' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
            'return_to' => 'today',
        ]);

        $response->assertRedirect(route('guru.jadwal-mengajar.today'));
        $response->assertSessionHas('success');
    }
}
