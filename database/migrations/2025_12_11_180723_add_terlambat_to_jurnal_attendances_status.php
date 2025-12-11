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
        DB::statement("ALTER TABLE jurnal_attendances MODIFY COLUMN status ENUM('hadir', 'terlambat', 'sakit', 'izin', 'alpa') DEFAULT 'alpa'");
        DB::statement("ALTER TABLE jurnal_attendances MODIFY COLUMN status_awal ENUM('hadir', 'terlambat', 'sakit', 'izin', 'alpa', 'belum_absen') NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE jurnal_attendances MODIFY COLUMN status ENUM('hadir', 'sakit', 'izin', 'alpa') DEFAULT 'alpa'");
        DB::statement("ALTER TABLE jurnal_attendances MODIFY COLUMN status_awal ENUM('hadir', 'sakit', 'izin', 'alpa', 'belum_absen') NULL");
    }
};
