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
        Schema::table('kegiatan_kokurikuler', function (Blueprint $table) {
            $table->string('link')->nullable()->after('foto');
            $table->enum('tipe', ['foto', 'pdf', 'link'])->default('foto')->after('link');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kegiatan_kokurikuler', function (Blueprint $table) {
            $table->dropColumn(['link', 'tipe']);
        });
    }
};