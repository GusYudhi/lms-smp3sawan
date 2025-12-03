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
            if (!Schema::hasColumn('guru_profiles', 'jabatan_di_sekolah')) {
                $table->string('jabatan_di_sekolah')->nullable()->after('mata_pelajaran');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guru_profiles', function (Blueprint $table) {
            if (Schema::hasColumn('guru_profiles', 'jabatan_di_sekolah')) {
                $table->dropColumn('jabatan_di_sekolah');
            }
        });
    }
};
