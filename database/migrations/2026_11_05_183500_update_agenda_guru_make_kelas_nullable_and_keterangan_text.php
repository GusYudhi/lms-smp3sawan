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
        Schema::table('agenda_guru', function (Blueprint $table) {
            $table->string('kelas', 50)->nullable()->change();
            $table->text('keterangan')->nullable()->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agenda_guru', function (Blueprint $table) {
            $table->string('kelas', 50)->nullable(false)->change();
            $table->string('keterangan', 50)->nullable()->default('-')->change();
        });
    }
};

