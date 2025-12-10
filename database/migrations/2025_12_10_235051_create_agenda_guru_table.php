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
        Schema::create('agenda_guru', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('tanggal');
            $table->string('kelas', 50);
            $table->integer('jam_mulai');
            $table->integer('jam_selesai');
            $table->text('materi');
            $table->enum('status_jurnal', ['selesai', 'belum_selesai'])->default('belum_selesai');
            $table->string('keterangan', 50)->nullable()->default('-');
            $table->timestamps();

            // Index untuk performa query
            $table->index('user_id');
            $table->index('tanggal');
            $table->index('status_jurnal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agenda_guru');
    }
};
