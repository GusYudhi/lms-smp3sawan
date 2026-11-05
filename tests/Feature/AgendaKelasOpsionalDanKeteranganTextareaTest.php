<?php

namespace Tests\Feature;

use App\Models\AgendaGuru;
use App\Models\GuruProfile;
use App\Models\JamPelajaran;
use App\Models\Semester;
use App\Models\TahunPelajaran;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AgendaKelasOpsionalDanKeteranganTextareaTest extends TestCase
{
    use DatabaseTransactions;

    protected User $guru;
    protected User $kepsek;
    protected JamPelajaran $jam1;
    protected JamPelajaran $jam2;

    protected function setUp(): void
    {
        parent::setUp();

        $tp = TahunPelajaran::firstOrCreate(
            ['nama' => '2026/2027 Test Agenda'],
            [
                'tahun_mulai' => 2026,
                'tahun_selesai' => 2027,
                'tanggal_mulai' => '2026-07-15',
                'tanggal_selesai' => '2027-06-30',
                'is_active' => true,
            ]
        );

        $semester = Semester::firstOrCreate(
            ['tahun_pelajaran_id' => $tp->id, 'semester_ke' => 1],
            [
                'nama' => 'Semester Ganjil',
                'tanggal_mulai' => '2026-07-15',
                'tanggal_selesai' => '2026-12-20',
                'is_active' => true,
            ]
        );

        $this->jam1 = JamPelajaran::firstOrCreate(
            ['semester_id' => $semester->id, 'jam_ke' => 1],
            ['jam_mulai' => '07:30', 'jam_selesai' => '08:15']
        );

        $this->jam2 = JamPelajaran::firstOrCreate(
            ['semester_id' => $semester->id, 'jam_ke' => 2],
            ['jam_mulai' => '08:15', 'jam_selesai' => '09:00']
        );

        // User Guru
        $this->guru = User::create([
            'name' => 'Guru Agenda Test',
            'email' => 'guru.agenda.test@smp3sawan.sch.id',
            'password' => bcrypt('password'),
            'role' => 'guru',
        ]);
        GuruProfile::create([
            'user_id' => $this->guru->id,
            'nama' => 'Guru Agenda Test',
            'nip' => '198701012026011001',
            'kode_guru' => 'GAG',
        ]);

        // User Kepala Sekolah
        $this->kepsek = User::create([
            'name' => 'Kepsek Agenda Test',
            'email' => 'kepsek.agenda.test@smp3sawan.sch.id',
            'password' => bcrypt('password'),
            'role' => 'kepala_sekolah',
        ]);
    }

    public function test_guru_dapat_membuat_agenda_tanpa_kelas_dan_keterangan_textarea_panjang(): void
    {
        $keteranganPanjang = "Agenda rapat dinas mingguan bersama pengawas sekolah.\n"
            . "Membahas evaluasi kurikulum dan persiapan asesmen sumatif akhir semester.\n"
            . "Catatan: Dihadiri oleh seluruh dewan guru dan perwakilan komite sekolah lebih dari 100 karakter.";

        $response = $this->actingAs($this->guru)->post(route('guru.agenda.store'), [
            'tanggal' => '2026-11-10',
            'kelas' => null, // Opsional / Kosong
            'jam_mulai_id' => $this->jam1->id,
            'jam_selesai_id' => $this->jam2->id,
            'materi' => 'Rapat Dewan Guru & Evaluasi Bulanan',
            'status_jurnal' => 'selesai',
            'keterangan' => $keteranganPanjang,
        ]);

        $response->assertRedirect(route('guru.agenda'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('agenda_guru', [
            'user_id' => $this->guru->id,
            'kelas' => null,
            'materi' => 'Rapat Dewan Guru & Evaluasi Bulanan',
            'keterangan' => $keteranganPanjang,
        ]);
    }

    public function test_guru_dapat_mengupdate_agenda_menjadi_tanpa_kelas_dan_keterangan_textarea(): void
    {
        $agenda = AgendaGuru::create([
            'user_id' => $this->guru->id,
            'tanggal' => '2026-11-11',
            'kelas' => '7A',
            'jam_mulai_id' => $this->jam1->id,
            'jam_selesai_id' => $this->jam1->id,
            'materi' => 'Materi Pembuka',
            'status_jurnal' => 'belum_selesai',
            'keterangan' => 'Catatan awal',
        ]);

        $keteranganUpdate = "Diganti menjadi kegiatan supervisi umum tanpa kelas.\nKeterangan multi baris revisi.";

        $response = $this->actingAs($this->guru)->put(route('guru.agenda.update', $agenda->id), [
            'tanggal' => '2026-11-11',
            'kelas' => null,
            'jam_mulai_id' => $this->jam1->id,
            'jam_selesai_id' => $this->jam2->id,
            'materi' => 'Materi Pembuka Revisi',
            'status_jurnal' => 'selesai',
            'keterangan' => $keteranganUpdate,
        ]);

        $response->assertRedirect(route('guru.agenda'));
        $response->assertSessionHas('success');

        $agenda->refresh();
        $this->assertNull($agenda->kelas);
        $this->assertSame('Materi Pembuka Revisi', $agenda->materi);
        $this->assertSame($keteranganUpdate, $agenda->keterangan);
    }

    public function test_kepala_sekolah_dapat_membuat_dan_mengupdate_agenda_tanpa_kelas_dan_keterangan_textarea(): void
    {
        $keterangan = "Monitoring sarana prasarana laboratorium IPA dan perpustakaan sekolah.";

        $response = $this->actingAs($this->kepsek)->post(route('kepala-sekolah.agenda.store'), [
            'tanggal' => '2026-11-12',
            'kelas' => '', // Empty string should be converted to null or saved as null
            'jam_mulai_id' => $this->jam1->id,
            'jam_selesai_id' => $this->jam2->id,
            'materi' => 'Supervisi Sarana Laboratorium',
            'status_jurnal' => 'selesai',
            'keterangan' => $keterangan,
        ]);

        $response->assertRedirect(route('kepala-sekolah.agenda'));
        $response->assertSessionHas('success');

        $agenda = AgendaGuru::where('user_id', $this->kepsek->id)->latest('id')->first();
        $this->assertNotNull($agenda);
        $this->assertEmpty($agenda->kelas);
        $this->assertSame($keterangan, $agenda->keterangan);

        // Test update
        $updateResponse = $this->actingAs($this->kepsek)->put(route('kepala-sekolah.agenda.update', $agenda->id), [
            'tanggal' => '2026-11-12',
            'kelas' => null,
            'jam_mulai_id' => $this->jam1->id,
            'jam_selesai_id' => $this->jam2->id,
            'materi' => 'Supervisi Sarana Laboratorium (Selesai)',
            'status_jurnal' => 'selesai',
            'keterangan' => "Catatan supervisi:\n1. AC lab perlu servis\n2. Mikroskop lengkap",
        ]);

        $updateResponse->assertRedirect(route('kepala-sekolah.agenda'));
        $agenda->refresh();
        $this->assertNull($agenda->kelas);
        $this->assertStringContainsString('AC lab perlu servis', $agenda->keterangan);
    }

    public function test_tampilan_dan_pencarian_agenda_non_kelas_serta_export_excel(): void
    {
        $agendaNonKelas = AgendaGuru::create([
            'user_id' => $this->guru->id,
            'tanggal' => '2026-11-13',
            'kelas' => null,
            'jam_mulai_id' => $this->jam1->id,
            'jam_selesai_id' => $this->jam2->id,
            'materi' => 'Workshop Penyusunan Modul Ajar',
            'status_jurnal' => 'selesai',
            'keterangan' => 'Narasumber dari Balai Guru Penggerak',
        ]);

        // 1. View Guru Agenda Index
        $viewGuru = $this->actingAs($this->guru)->get(route('guru.agenda'));
        $viewGuru->assertStatus(200);
        $viewGuru->assertSee('Non-Kelas');
        $viewGuru->assertSee('Workshop Penyusunan Modul Ajar');
        $viewGuru->assertSee('Narasumber dari Balai Guru Penggerak');

        // 2. Search by Keterangan
        $searchGuru = $this->actingAs($this->guru)->get(route('guru.agenda', ['search' => 'Guru Penggerak']));
        $searchGuru->assertStatus(200);
        $searchGuru->assertSee('Workshop Penyusunan Modul Ajar');

        // 3. Filter by non_kelas
        $filterNonKelas = $this->actingAs($this->guru)->get(route('guru.agenda', ['kelas' => 'non_kelas']));
        $filterNonKelas->assertStatus(200);
        $filterNonKelas->assertSee('Workshop Penyusunan Modul Ajar');

        // 4. View Kepala Sekolah Agenda Index
        $agendaKepsek = AgendaGuru::create([
            'user_id' => $this->kepsek->id,
            'tanggal' => '2026-11-13',
            'kelas' => null,
            'jam_mulai_id' => $this->jam1->id,
            'jam_selesai_id' => $this->jam2->id,
            'materi' => 'Rapat Koordinasi MKKS',
            'status_jurnal' => 'selesai',
            'keterangan' => 'Bertempat di SMP Negeri 1 Sawan',
        ]);

        $viewKepsek = $this->actingAs($this->kepsek)->get(route('kepala-sekolah.agenda'));
        $viewKepsek->assertStatus(200);
        $viewKepsek->assertSee('Non-Kelas');
        $viewKepsek->assertSee('Rapat Koordinasi MKKS');

        // 5. Kepala Sekolah Melihat Agenda Guru Read-Only
        $viewKepsekLihatGuru = $this->actingAs($this->kepsek)->get(route('kepala-sekolah.agenda-guru.index'));
        $viewKepsekLihatGuru->assertStatus(200);
        $viewKepsekLihatGuru->assertSee('Non-Kelas');

        $viewKepsekDetailGuru = $this->actingAs($this->kepsek)->get(route('kepala-sekolah.agenda-guru.show', $agendaNonKelas->id));
        $viewKepsekDetailGuru->assertStatus(200);
        $viewKepsekDetailGuru->assertSee('Non-Kelas');
        $viewKepsekDetailGuru->assertSee('Narasumber dari Balai Guru Penggerak');

        // 6. Export Excel
        $exportResponse = $this->actingAs($this->guru)->get(route('guru.agenda.export'));
        $exportResponse->assertStatus(200);
    }
}
