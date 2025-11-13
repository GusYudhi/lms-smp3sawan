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
        // Mark the previous migration as ran
        DB::table('migrations')->insert([
            'migration' => '2025_11_13_081231_create_school_profiles_table',
            'batch' => 2
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('migrations')
            ->where('migration', '2025_11_13_081231_create_school_profiles_table')
            ->delete();
    }
};
