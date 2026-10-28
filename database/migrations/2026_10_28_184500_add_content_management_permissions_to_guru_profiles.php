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
        Schema::table('guru_profiles', function (Blueprint $table) {
            $table->boolean('can_manage_berita')->default(false)->after('is_active');
            $table->boolean('can_manage_prestasi')->default(false)->after('can_manage_berita');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guru_profiles', function (Blueprint $table) {
            $table->dropColumn(['can_manage_berita', 'can_manage_prestasi']);
        });
    }
};
