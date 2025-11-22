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
        // Remove the existing foreign key constraint
        Schema::table('school_profiles', function (Blueprint $table) {
            $table->dropForeign(['id_kepala_sekolah']);
            $table->dropColumn('id_kepala_sekolah');
        });

        // Add it back with correct constraint
        Schema::table('school_profiles', function (Blueprint $table) {
            $table->unsignedBigInteger('id_kepala_sekolah')->nullable()->after('npsn');
            $table->foreign('id_kepala_sekolah')->references('id')->on('guru_profiles')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('school_profiles', function (Blueprint $table) {
            $table->dropForeign(['id_kepala_sekolah']);
            $table->dropColumn('id_kepala_sekolah');
        });
    }
};
