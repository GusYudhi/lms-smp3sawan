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
        Schema::table('jadwal_pelajarans', function (Blueprint $table) {
            // Add semester_id column with foreign key
            $table->unsignedBigInteger('semester_id')->nullable()->after('id');
            $table->foreign('semester_id')
                  ->references('id')
                  ->on('semester')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jadwal_pelajarans', function (Blueprint $table) {
            // Drop foreign key and column
            $table->dropForeign(['semester_id']);
            $table->dropColumn('semester_id');
        });
    }
};
