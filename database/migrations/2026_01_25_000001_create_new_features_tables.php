<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('kegiatan_kokurikuler')) {
            Schema::create('kegiatan_kokurikuler', function (Blueprint $table) {
                $table->id();
                $table->string('nama');
                $table->text('deskripsi');
                $table->string('foto')->nullable();
                $table->date('tanggal');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('materi_pelajarans')) {
            Schema::create('materi_pelajarans', function (Blueprint $table) {
                $table->id();
                $table->foreignId('guru_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('mata_pelajaran_id')->constrained('mata_pelajarans')->onDelete('cascade');
                $table->foreignId('kelas_id')->nullable()->constrained('kelas')->onDelete('set null');
                $table->string('judul');
                $table->text('deskripsi')->nullable();
                $table->string('file_path');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('prestasi')) {
            Schema::create('prestasi', function (Blueprint $table) {
                $table->id();
                $table->string('judul');
                $table->text('deskripsi');
                $table->string('foto')->nullable();
                $table->date('tanggal');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('berita')) {
            Schema::create('berita', function (Blueprint $table) {
                $table->id();
                $table->string('judul');
                $table->text('konten');
                $table->string('foto')->nullable();
                $table->date('tanggal');
                $table->foreignId('penulis_id')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('saran')) {
            Schema::create('saran', function (Blueprint $table) {
                $table->id();
                $table->string('nama_pengirim');
                $table->string('email_pengirim')->nullable();
                $table->text('isi_saran');
                $table->enum('status', ['belum_dibaca', 'sudah_dibaca'])->default('belum_dibaca');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('galeri')) {
            Schema::create('galeri', function (Blueprint $table) {
                $table->id();
                $table->string('judul')->nullable();
                $table->string('file_path');
                $table->enum('tipe', ['foto', 'video'])->default('foto');
                $table->text('deskripsi')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('galeri');
        Schema::dropIfExists('saran');
        Schema::dropIfExists('berita');
        Schema::dropIfExists('prestasi');
        Schema::dropIfExists('materi_pelajarans');
        Schema::dropIfExists('kegiatan_kokurikuler');
    }
};
