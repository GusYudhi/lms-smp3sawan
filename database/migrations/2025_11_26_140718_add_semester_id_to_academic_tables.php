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
        // Add semester_id to mata_pelajarans table if it doesn't exist
        if (Schema::hasTable('mata_pelajarans') && !Schema::hasColumn('mata_pelajarans', 'semester_id')) {
            Schema::table('mata_pelajarans', function (Blueprint $table) {
                $table->unsignedBigInteger('semester_id')->nullable()->after('id');
                $table->foreign('semester_id')->references('id')->on('semester')->onDelete('set null');
            });
        }

        // Check if jam_pelajarans needs semester_id (might already have it from previous migration)
        if (Schema::hasTable('jam_pelajarans') && !Schema::hasColumn('jam_pelajarans', 'semester_id')) {
            Schema::table('jam_pelajarans', function (Blueprint $table) {
                $table->unsignedBigInteger('semester_id')->nullable()->after('id');
                $table->foreign('semester_id')->references('id')->on('semester')->onDelete('set null');
            });
        }

        // Check if fixed_schedules needs semester_id (might already have it from previous migration)
        if (Schema::hasTable('fixed_schedules') && !Schema::hasColumn('fixed_schedules', 'semester_id')) {
            Schema::table('fixed_schedules', function (Blueprint $table) {
                $table->unsignedBigInteger('semester_id')->nullable()->after('id');
                $table->foreign('semester_id')->references('id')->on('semester')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove semester_id from mata_pelajarans
        if (Schema::hasTable('mata_pelajarans') && Schema::hasColumn('mata_pelajarans', 'semester_id')) {
            Schema::table('mata_pelajarans', function (Blueprint $table) {
                $table->dropForeign(['semester_id']);
                $table->dropColumn('semester_id');
            });
        }

        // Remove semester_id from jam_pelajarans if it was added here
        if (Schema::hasTable('jam_pelajarans') && Schema::hasColumn('jam_pelajarans', 'semester_id')) {
            Schema::table('jam_pelajarans', function (Blueprint $table) {
                $table->dropForeign(['semester_id']);
                $table->dropColumn('semester_id');
            });
        }

        // Remove semester_id from fixed_schedules if it was added here
        if (Schema::hasTable('fixed_schedules') && Schema::hasColumn('fixed_schedules', 'semester_id')) {
            Schema::table('fixed_schedules', function (Blueprint $table) {
                $table->dropForeign(['semester_id']);
                $table->dropColumn('semester_id');
            });
        }
    }
};
