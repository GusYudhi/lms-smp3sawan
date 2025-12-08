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
        if (!Schema::hasTable('jadwal_pelajarans')) {
            Schema::create('jadwal_pelajarans', function (Blueprint $table) {
                $table->id();
                $table->foreignId('kelas_id')->constrained('kelas')->onDelete('cascade');
                $table->foreignId('mata_pelajaran_id')->constrained('mata_pelajarans')->onDelete('cascade');
                $table->foreignId('guru_id')->constrained('users')->onDelete('cascade'); // Assuming teachers are in users table
                $table->string('hari'); // Senin, Selasa, etc.
                $table->time('jam_mulai');
                $table->time('jam_selesai');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_pelajarans');
    }
};
