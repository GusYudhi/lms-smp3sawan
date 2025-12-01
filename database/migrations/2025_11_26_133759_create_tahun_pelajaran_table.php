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
        Schema::create('tahun_pelajaran', function (Blueprint $table) {
            $table->id();
            $table->string('nama'); // e.g., "2024/2025"
            $table->year('tahun_mulai'); // e.g., 2024
            $table->year('tahun_selesai'); // e.g., 2025
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->boolean('is_active')->default(false);
            $table->text('keterangan')->nullable();
            $table->timestamps();

            // Index untuk performa
            $table->index('is_active');
            $table->index(['tahun_mulai', 'tahun_selesai']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tahun_pelajaran');
    }
};
