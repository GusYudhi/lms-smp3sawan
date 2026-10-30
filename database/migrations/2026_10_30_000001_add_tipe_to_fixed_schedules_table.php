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
        Schema::table('fixed_schedules', function (Blueprint $table) {
            if (!Schema::hasColumn('fixed_schedules', 'tipe')) {
                $table->string('tipe', 30)->default('umum')->after('keterangan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fixed_schedules', function (Blueprint $table) {
            if (Schema::hasColumn('fixed_schedules', 'tipe')) {
                $table->dropColumn('tipe');
            }
        });
    }
};

