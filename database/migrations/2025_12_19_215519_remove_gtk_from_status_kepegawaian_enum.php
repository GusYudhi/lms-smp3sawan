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
        // Step 1: Update existing 'GTK' records to 'HONORER'
        DB::table('guru_profiles')
            ->where('status_kepegawaian', 'GTK')
            ->update(['status_kepegawaian' => 'HONORER']);

        // Step 2: Remove 'GTK' from the enum list
        DB::statement("ALTER TABLE guru_profiles MODIFY COLUMN status_kepegawaian ENUM('PNS', 'PPPK', 'HONORER') NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Step 1: Add 'GTK' back to the enum list
        DB::statement("ALTER TABLE guru_profiles MODIFY COLUMN status_kepegawaian ENUM('PNS', 'PPPK', 'HONORER', 'GTK') NULL");

        // Note: We cannot accurately revert 'HONORER' back to 'GTK' as we don't know which ones were originally GTK.
    }
};
