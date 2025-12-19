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
        // Step 1: Add 'HONORER' to the enum list temporarily
        DB::statement("ALTER TABLE guru_profiles MODIFY COLUMN status_kepegawaian ENUM('PNS', 'PPPK', 'GTT', 'GTY', 'GTK', 'HONORER') NULL");

        // Step 2: Update existing records
        DB::table('guru_profiles')
            ->whereIn('status_kepegawaian', ['GTT', 'GTY'])
            ->update(['status_kepegawaian' => 'HONORER']);

        // Step 3: Remove 'GTT' and 'GTY' from the enum list
        DB::statement("ALTER TABLE guru_profiles MODIFY COLUMN status_kepegawaian ENUM('PNS', 'PPPK', 'HONORER', 'GTK') NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Step 1: Add 'GTT' and 'GTY' back to the enum list
        DB::statement("ALTER TABLE guru_profiles MODIFY COLUMN status_kepegawaian ENUM('PNS', 'PPPK', 'HONORER', 'GTK', 'GTT', 'GTY') NULL");

        // Step 2: Revert 'HONORER' to 'GTT' (lossy, but necessary to fit old schema)
        DB::table('guru_profiles')
            ->where('status_kepegawaian', 'HONORER')
            ->update(['status_kepegawaian' => 'GTT']);

        // Step 3: Remove 'HONORER' from the enum list
        DB::statement("ALTER TABLE guru_profiles MODIFY COLUMN status_kepegawaian ENUM('PNS', 'PPPK', 'GTT', 'GTY', 'GTK') NULL");
    }
};
