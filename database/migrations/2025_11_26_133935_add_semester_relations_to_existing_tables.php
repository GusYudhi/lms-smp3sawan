<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Truncate tables that will lose data (as agreed) - only if they exist
        if (Schema::hasTable('jadwal_pelajaran')) {
            DB::table('jadwal_pelajaran')->truncate();
        }
        if (Schema::hasTable('jadwal_tetap')) {
            DB::table('jadwal_tetap')->truncate();
        }

        // Add semester_id to jadwal_pelajaran
        if (Schema::hasTable('jadwal_pelajaran')) {
            Schema::table('jadwal_pelajaran', function (Blueprint $table) {
                $table->foreignId('semester_id')->after('id')->constrained('semester')->onDelete('cascade');
                $table->index('semester_id');
            });
        }

        // Add semester_id to jadwal_tetap
        if (Schema::hasTable('jadwal_tetap')) {
            Schema::table('jadwal_tetap', function (Blueprint $table) {
                $table->foreignId('semester_id')->after('id')->constrained('semester')->onDelete('cascade');
                $table->index('semester_id');
            });
        }

        // Add semester_id to jam_pelajaran (per semester)
        if (Schema::hasTable('jam_pelajaran')) {
            Schema::table('jam_pelajaran', function (Blueprint $table) {
                $table->foreignId('semester_id')->nullable()->after('id')->constrained('semester')->onDelete('cascade');
                $table->index('semester_id');
            });
        }

        // Add semester_id to mata_pelajaran (per semester)
        if (Schema::hasTable('mata_pelajaran')) {
            Schema::table('mata_pelajaran', function (Blueprint $table) {
                $table->foreignId('semester_id')->nullable()->after('id')->constrained('semester')->onDelete('cascade');
                $table->index('semester_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove semester_id from jadwal_pelajaran
        if (Schema::hasTable('jadwal_pelajaran') && Schema::hasColumn('jadwal_pelajaran', 'semester_id')) {
            Schema::table('jadwal_pelajaran', function (Blueprint $table) {
                $table->dropForeign(['semester_id']);
                $table->dropIndex(['semester_id']);
                $table->dropColumn('semester_id');
            });
        }

        // Remove semester_id from jadwal_tetap
        if (Schema::hasTable('jadwal_tetap') && Schema::hasColumn('jadwal_tetap', 'semester_id')) {
            Schema::table('jadwal_tetap', function (Blueprint $table) {
                $table->dropForeign(['semester_id']);
                $table->dropIndex(['semester_id']);
                $table->dropColumn('semester_id');
            });
        }

        // Remove semester_id from jam_pelajaran
        if (Schema::hasTable('jam_pelajaran') && Schema::hasColumn('jam_pelajaran', 'semester_id')) {
            Schema::table('jam_pelajaran', function (Blueprint $table) {
                $table->dropForeign(['semester_id']);
                $table->dropIndex(['semester_id']);
                $table->dropColumn('semester_id');
            });
        }

        // Remove semester_id from mata_pelajaran
        if (Schema::hasTable('mata_pelajaran') && Schema::hasColumn('mata_pelajaran', 'semester_id')) {
            Schema::table('mata_pelajaran', function (Blueprint $table) {
                $table->dropForeign(['semester_id']);
                $table->dropIndex(['semester_id']);
                $table->dropColumn('semester_id');
            });
        }
    }
};
