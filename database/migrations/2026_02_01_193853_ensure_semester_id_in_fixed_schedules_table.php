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
        if (Schema::hasTable('fixed_schedules')) {
            Schema::table('fixed_schedules', function (Blueprint $table) {
                if (!Schema::hasColumn('fixed_schedules', 'semester_id')) {
                    $table->foreignId('semester_id')->nullable()->after('id')->constrained('semester')->onDelete('cascade');
                    $table->index('semester_id');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('fixed_schedules')) {
            Schema::table('fixed_schedules', function (Blueprint $table) {
                if (Schema::hasColumn('fixed_schedules', 'semester_id')) {
                    $table->dropForeign(['semester_id']);
                    $table->dropIndex(['semester_id']);
                    $table->dropColumn('semester_id');
                }
            });
        }
    }
};