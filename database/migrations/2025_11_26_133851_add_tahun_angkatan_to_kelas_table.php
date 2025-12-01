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
        Schema::table('kelas', function (Blueprint $table) {
            $table->year('tahun_angkatan')->nullable()->after('nama_kelas'); // Tahun pertama masuk
            $table->foreignId('tahun_pelajaran_id')->nullable()->after('tahun_angkatan')->constrained('tahun_pelajaran')->onDelete('set null');

            // Index untuk performa
            $table->index('tahun_angkatan');
            $table->index('tahun_pelajaran_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kelas', function (Blueprint $table) {
            $table->dropForeign(['tahun_pelajaran_id']);
            $table->dropIndex(['tahun_angkatan']);
            $table->dropIndex(['tahun_pelajaran_id']);
            $table->dropColumn(['tahun_angkatan', 'tahun_pelajaran_id']);
        });
    }
};
