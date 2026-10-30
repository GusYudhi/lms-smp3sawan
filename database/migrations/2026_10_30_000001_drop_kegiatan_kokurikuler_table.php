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
        Schema::dropIfExists('kegiatan_kokurikuler');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('kegiatan_kokurikuler')) {
            Schema::create('kegiatan_kokurikuler', function (Blueprint $table) {
                $table->id();
                $table->string('nama');
                $table->text('deskripsi');
                $table->string('foto')->nullable();
                $table->string('link')->nullable();
                $table->string('tipe')->default('foto');
                $table->date('tanggal');
                $table->timestamps();
            });
        }
    }
};

