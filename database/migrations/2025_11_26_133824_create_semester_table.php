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
        Schema::create('semester', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_pelajaran_id')->constrained('tahun_pelajaran')->onDelete('cascade');
            $table->string('nama'); // e.g., "Ganjil", "Genap"
            $table->tinyInteger('semester_ke'); // 1 atau 2
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->boolean('is_active')->default(false);
            $table->text('keterangan')->nullable();
            $table->timestamps();

            // Index untuk performa
            $table->index('tahun_pelajaran_id');
            $table->index('is_active');
            $table->index(['tahun_pelajaran_id', 'semester_ke']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('semester');
    }
};
