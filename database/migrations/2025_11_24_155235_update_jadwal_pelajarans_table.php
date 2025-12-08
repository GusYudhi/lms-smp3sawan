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
        Schema::table('jadwal_pelajarans', function (Blueprint $table) {
            $table->dropColumn(['jam_mulai', 'jam_selesai']);
            $table->integer('jam_ke')->after('hari');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jadwal_pelajarans', function (Blueprint $table) {
            $table->time('jam_mulai')->nullable();
            $table->time('jam_selesai')->nullable();
            $table->dropColumn('jam_ke');
        });
    }
};
