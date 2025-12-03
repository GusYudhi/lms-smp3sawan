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
        Schema::table('guru_profiles', function (Blueprint $table) {
            // Drop old wali_kelas column (string)
            $table->dropColumn('wali_kelas');

            // Add new kelas_id column as foreign key
            $table->foreignId('kelas_id')->nullable()->after('mata_pelajaran')->constrained('kelas')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guru_profiles', function (Blueprint $table) {
            // Drop foreign key and kelas_id column
            $table->dropForeign(['kelas_id']);
            $table->dropColumn('kelas_id');

            // Restore old wali_kelas column
            $table->string('wali_kelas')->nullable()->after('mata_pelajaran');
        });
    }
};
