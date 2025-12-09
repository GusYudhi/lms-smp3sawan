<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tabel tugas yang dibuat oleh kepala sekolah
        Schema::create('tugas_guru', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->dateTime('deadline');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade'); // kepala sekolah
            $table->enum('status', ['aktif', 'selesai', 'dibatalkan'])->default('aktif');
            $table->timestamps();

            $table->index('created_by');
            $table->index('deadline');
            $table->index('status');
        });

        // Tabel file lampiran tugas (dari kepala sekolah)
        Schema::create('tugas_guru_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tugas_guru_id')->constrained('tugas_guru')->onDelete('cascade');
            $table->string('nama_file');
            $table->string('file_path');
            $table->string('file_type')->nullable();
            $table->integer('file_size')->nullable(); // dalam bytes
            $table->timestamps();

            $table->index('tugas_guru_id');
        });

        // Tabel submission/pengumpulan tugas dari guru
        Schema::create('tugas_guru_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tugas_guru_id')->constrained('tugas_guru')->onDelete('cascade');
            $table->foreignId('guru_id')->constrained('users')->onDelete('cascade'); // user dengan role guru
            $table->text('konten_tugas')->nullable(); // jika guru ketik langsung
            $table->string('link_eksternal')->nullable(); // jika guru lampirkan link
            $table->enum('status_pengumpulan', ['draft', 'dikumpulkan', 'terlambat'])->default('draft');
            $table->dateTime('tanggal_submit')->nullable();
            $table->text('feedback')->nullable(); // feedback dari kepala sekolah
            $table->integer('nilai')->nullable(); // nilai opsional
            $table->timestamps();

            $table->index('tugas_guru_id');
            $table->index('guru_id');
            $table->index('status_pengumpulan');
            $table->unique(['tugas_guru_id', 'guru_id']); // satu guru hanya bisa submit sekali per tugas
        });

        // Tabel file lampiran submission (dari guru)
        Schema::create('tugas_guru_submission_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained('tugas_guru_submissions')->onDelete('cascade');
            $table->string('nama_file');
            $table->string('file_path');
            $table->string('file_type')->nullable();
            $table->integer('file_size')->nullable();
            $table->timestamps();

            $table->index('submission_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tugas_guru_submission_files');
        Schema::dropIfExists('tugas_guru_submissions');
        Schema::dropIfExists('tugas_guru_files');
        Schema::dropIfExists('tugas_guru');
    }
};
