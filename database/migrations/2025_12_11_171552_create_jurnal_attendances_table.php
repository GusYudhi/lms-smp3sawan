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
        Schema::create('jurnal_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jurnal_mengajar_id')->constrained('jurnal_mengajar')->onDelete('cascade');
            $table->foreignId('student_profile_id')->constrained('student_profiles')->onDelete('cascade');
            $table->enum('status', ['hadir', 'sakit', 'izin', 'alpa'])->default('alpa');
            $table->enum('status_awal', ['hadir', 'sakit', 'izin', 'alpa', 'belum_absen'])->nullable();
            $table->timestamps();

            // Index untuk performa
            $table->index('jurnal_mengajar_id');
            $table->index('student_profile_id');
            $table->unique(['jurnal_mengajar_id', 'student_profile_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jurnal_attendances');
    }
};
