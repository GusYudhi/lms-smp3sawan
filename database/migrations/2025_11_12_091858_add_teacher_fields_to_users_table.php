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
        Schema::table('users', function (Blueprint $table) {
            // Personal Information Fields
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable()->after('email');
            $table->string('tempat_lahir')->nullable()->after('jenis_kelamin');
            $table->date('tanggal_lahir')->nullable()->after('tempat_lahir');
            $table->string('profile_photo_path')->nullable()->after('tanggal_lahir');

            // Employment Information Fields (untuk guru)
            $table->enum('status_kepegawaian', ['PNS', 'PPPK', 'Honorer'])->nullable()->after('profile_photo_path');
            $table->string('golongan', 10)->nullable()->after('status_kepegawaian');
            $table->string('mata_pelajaran', 100)->nullable()->after('golongan');
            $table->string('wali_kelas', 10)->nullable()->after('mata_pelajaran');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'jenis_kelamin',
                'tempat_lahir',
                'tanggal_lahir',
                'profile_photo_path',
                'status_kepegawaian',
                'golongan',
                'mata_pelajaran',
                'wali_kelas'
            ]);
        });
    }
};
