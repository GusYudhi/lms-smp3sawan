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
        Schema::create('teacher_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('nip')->nullable()->unique();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('alamat')->nullable();
            $table->enum('status_kepegawaian', ['PNS', 'PPPK', 'GTT', 'GTY'])->nullable();
            $table->string('golongan')->nullable();
            $table->json('mata_pelajaran')->nullable(); // Array of subjects
            $table->string('wali_kelas')->nullable();
            $table->string('pendidikan_terakhir')->nullable();
            $table->year('tahun_mulai_mengajar')->nullable();
            $table->string('sertifikat')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['nip', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_profiles');
    }
};
