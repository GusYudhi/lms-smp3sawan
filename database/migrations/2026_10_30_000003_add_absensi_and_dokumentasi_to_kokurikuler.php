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
        if (!Schema::hasTable('kokurikuler_sessions')) {
            Schema::create('kokurikuler_sessions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('semester_id');
                $table->unsignedBigInteger('kelas_id');
                $table->unsignedBigInteger('fixed_schedule_id')->nullable();
                $table->unsignedBigInteger('penilai_user_id');
                $table->date('tanggal');
                $table->string('hari', 20);
                $table->string('tema');
                $table->text('deskripsi')->nullable();
                $table->string('foto_bukti')->nullable();
                $table->timestamps();

                $table->foreign('semester_id')->references('id')->on('semester')->onDelete('cascade');
                $table->foreign('kelas_id')->references('id')->on('kelas')->onDelete('cascade');
                $table->foreign('fixed_schedule_id')->references('id')->on('fixed_schedules')->onDelete('set null');
                $table->foreign('penilai_user_id')->references('id')->on('users')->onDelete('cascade');

                $table->unique(['kelas_id', 'tanggal'], 'uniq_kokurikuler_sessions_kelas_tgl');
                $table->index(['semester_id', 'kelas_id']);
            });
        }

        Schema::table('penilaian_kokurikuler', function (Blueprint $table) {
            if (!Schema::hasColumn('penilaian_kokurikuler', 'kokurikuler_session_id')) {
                $table->unsignedBigInteger('kokurikuler_session_id')->nullable()->after('id');
                $table->foreign('kokurikuler_session_id')
                    ->references('id')
                    ->on('kokurikuler_sessions')
                    ->onDelete('cascade');
            }

            if (!Schema::hasColumn('penilaian_kokurikuler', 'status_absensi')) {
                $table->string('status_absensi', 20)->default('hadir')->after('penilai_user_id');
            }

            $table->string('predikat', 10)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penilaian_kokurikuler', function (Blueprint $table) {
            if (Schema::hasColumn('penilaian_kokurikuler', 'kokurikuler_session_id')) {
                $table->dropForeign(['kokurikuler_session_id']);
                $table->dropColumn('kokurikuler_session_id');
            }

            if (Schema::hasColumn('penilaian_kokurikuler', 'status_absensi')) {
                $table->dropColumn('status_absensi');
            }
        });

        Schema::dropIfExists('kokurikuler_sessions');
    }
};

