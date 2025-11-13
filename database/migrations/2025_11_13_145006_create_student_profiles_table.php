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
        Schema::create('student_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('nis')->nullable()->unique();
            $table->string('nisn')->nullable()->index();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('kelas')->nullable()->index();
            $table->string('nomor_telepon_orangtua')->nullable();
            $table->string('alamat')->nullable();
            $table->string('nama_orangtua_wali')->nullable();
            $table->string('pekerjaan_orangtua')->nullable();
            $table->year('tahun_masuk')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['kelas', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_profiles');
    }
};
