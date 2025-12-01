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
        Schema::table('guru_attendances', function (Blueprint $table) {
            // Change status enum to include 'sakit'
            DB::statement("ALTER TABLE guru_attendances MODIFY COLUMN status ENUM('hadir', 'terlambat', 'izin', 'sakit', 'alpha') DEFAULT 'hadir'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guru_attendances', function (Blueprint $table) {
            // Revert back to original enum
            DB::statement("ALTER TABLE guru_attendances MODIFY COLUMN status ENUM('hadir', 'terlambat', 'izin', 'alpha') DEFAULT 'hadir'");
        });
    }
};
