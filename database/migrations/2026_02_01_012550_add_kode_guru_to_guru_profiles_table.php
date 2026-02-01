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
            $table->string('kode_guru', 10)->nullable()->unique()->after('nip');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guru_profiles', function (Blueprint $table) {
            $table->dropColumn('kode_guru');
        });
    }
};