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
        // Hapus index yang ada jika exists
        try {
            Schema::table('student_profiles', function (Blueprint $table) {
                $table->dropIndex(['kelas', 'is_active']);
            });
        } catch (\Exception $e) {
            // Index tidak ada, skip
        }

        try {
            Schema::table('student_profiles', function (Blueprint $table) {
                $table->dropIndex(['kelas']);
            });
        } catch (\Exception $e) {
            // Index tidak ada, skip
        }

        // Tambah kolom baru kelas_id
        Schema::table('student_profiles', function (Blueprint $table) {
            $table->unsignedBigInteger('kelas_id')->nullable()->after('tanggal_lahir');
        });

        // Hapus kolom lama kelas
        Schema::table('student_profiles', function (Blueprint $table) {
            $table->dropColumn('kelas');
        });

        // Tambahkan foreign key dan index
        Schema::table('student_profiles', function (Blueprint $table) {
            $table->foreign('kelas_id')->references('id')->on('kelas')->onDelete('set null');
            $table->index(['kelas_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_profiles', function (Blueprint $table) {
            // Hapus foreign key dan index
            $table->dropForeign(['kelas_id']);
            $table->dropIndex(['kelas_id', 'is_active']);
        });

        // Tambah kolom kelas yang lama
        Schema::table('student_profiles', function (Blueprint $table) {
            $table->string('kelas')->nullable()->after('tanggal_lahir');
        });

        // Hapus kolom kelas_id
        Schema::table('student_profiles', function (Blueprint $table) {
            $table->dropColumn('kelas_id');
        });

        // Kembalikan index yang lama
        try {
            Schema::table('student_profiles', function (Blueprint $table) {
                $table->index('kelas');
                $table->index(['kelas', 'is_active']);
            });
        } catch (\Exception $e) {
            // Skip if error
        }
    }
};
