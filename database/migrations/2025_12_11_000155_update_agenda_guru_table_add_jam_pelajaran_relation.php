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
        Schema::table('agenda_guru', function (Blueprint $table) {
            // Drop old columns
            $table->dropColumn(['jam_mulai', 'jam_selesai']);

            // Add new foreign key columns
            $table->foreignId('jam_mulai_id')->after('kelas')->constrained('jam_pelajarans')->onDelete('cascade');
            $table->foreignId('jam_selesai_id')->after('jam_mulai_id')->constrained('jam_pelajarans')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agenda_guru', function (Blueprint $table) {
            // Drop foreign keys
            $table->dropForeign(['jam_mulai_id']);
            $table->dropForeign(['jam_selesai_id']);
            $table->dropColumn(['jam_mulai_id', 'jam_selesai_id']);

            // Restore old columns
            $table->integer('jam_mulai')->after('kelas');
            $table->integer('jam_selesai')->after('jam_mulai');
        });
    }
};
