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
        Schema::table('jurnal_mengajar', function (Blueprint $table) {
            // Hapus kolom lama
            $table->dropColumn(['jam_ke', 'jam_mulai', 'jam_selesai']);

            // Tambah kolom baru
            $table->integer('jam_ke_mulai')->after('hari');
            $table->integer('jam_ke_selesai')->after('jam_ke_mulai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jurnal_mengajar', function (Blueprint $table) {
            // Kembalikan kolom lama
            $table->integer('jam_ke')->after('hari');
            $table->time('jam_mulai')->after('jam_ke');
            $table->time('jam_selesai')->after('jam_mulai');

            // Hapus kolom baru
            $table->dropColumn(['jam_ke_mulai', 'jam_ke_selesai']);
        });
    }
};
