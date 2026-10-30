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
        Schema::create('penilaian_kokurikuler', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('semester_id');
            $table->unsignedBigInteger('kelas_id');
            $table->unsignedBigInteger('fixed_schedule_id')->nullable();
            $table->date('tanggal');
            $table->string('hari', 20);
            $table->string('tema');
            $table->unsignedBigInteger('student_user_id');
            $table->unsignedBigInteger('penilai_user_id');
            $table->string('predikat', 10); // B, SB, BSH, SAB
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->foreign('semester_id')->references('id')->on('semester')->onDelete('cascade');
            $table->foreign('kelas_id')->references('id')->on('kelas')->onDelete('cascade');
            $table->foreign('fixed_schedule_id')->references('id')->on('fixed_schedules')->onDelete('set null');
            $table->foreign('student_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('penilai_user_id')->references('id')->on('users')->onDelete('cascade');

            $table->unique(['kelas_id', 'student_user_id', 'tanggal'], 'uniq_penilaian_kokurikuler_siswa_tgl');
            $table->index(['semester_id', 'kelas_id', 'tanggal']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penilaian_kokurikuler');
    }
};

