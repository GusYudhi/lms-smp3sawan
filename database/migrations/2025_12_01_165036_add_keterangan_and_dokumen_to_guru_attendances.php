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
        Schema::table('guru_attendances', function (Blueprint $table) {
            // Rename catatan to keterangan
            $table->renameColumn('catatan', 'keterangan');
            $table->string('dokumen_path')->nullable()->after('photo_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guru_attendances', function (Blueprint $table) {
            $table->renameColumn('keterangan', 'catatan');
            $table->dropColumn('dokumen_path');
        });
    }
};
