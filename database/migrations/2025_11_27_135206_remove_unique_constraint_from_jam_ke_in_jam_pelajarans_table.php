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
        Schema::table('jam_pelajarans', function (Blueprint $table) {
            // Drop unique constraint from jam_ke column
            $table->dropUnique(['jam_ke']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jam_pelajarans', function (Blueprint $table) {
            // Restore unique constraint on jam_ke column
            $table->unique('jam_ke');
        });
    }
};
