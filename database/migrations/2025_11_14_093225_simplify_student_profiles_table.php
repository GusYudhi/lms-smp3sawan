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
        Schema::table('student_profiles', function (Blueprint $table) {
            // Drop columns that are not needed based on new requirements
            $table->dropColumn([
                'alamat',
                'nama_orangtua_wali',
                'pekerjaan_orangtua',
                'tahun_masuk',
                'nomor_telepon_siswa'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_profiles', function (Blueprint $table) {
            // Restore the dropped columns
            $table->string('alamat')->nullable();
            $table->string('nama_orangtua_wali')->nullable();
            $table->string('pekerjaan_orangtua')->nullable();
            $table->year('tahun_masuk')->nullable();
            $table->string('nomor_telepon_siswa')->nullable();
        });
    }
};
