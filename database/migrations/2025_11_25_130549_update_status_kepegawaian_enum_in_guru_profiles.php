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
        Schema::table('guru_profiles', function (Blueprint $table) {
            DB::statement("ALTER TABLE guru_profiles MODIFY COLUMN status_kepegawaian ENUM('PNS', 'PPPK', 'GTT', 'GTY', 'GTK') NULL");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guru_profiles', function (Blueprint $table) {
            // Update any records using the new enum value to null before reverting
            DB::table('guru_profiles')->where('status_kepegawaian', 'GTK')->update(['status_kepegawaian' => null]);

            DB::statement("ALTER TABLE guru_profiles MODIFY COLUMN status_kepegawaian ENUM('PNS', 'PPPK', 'GTT', 'GTY') NULL");
        });
    }
};
