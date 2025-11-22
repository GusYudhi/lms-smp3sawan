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
        // Step 1: Rename teacher_profiles table to guru_profiles
        Schema::rename('teacher_profiles', 'guru_profiles');

        // Step 2: Modify guru_profiles table structure
        Schema::table('guru_profiles', function (Blueprint $table) {
            // Add new columns
            $table->string('nama')->after('user_id');
            $table->string('foto_profil')->nullable()->after('nip');
            $table->string('nomor_telepon')->nullable()->after('foto_profil');
            $table->string('email')->nullable()->after('nomor_telepon');
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable()->after('email');
            $table->string('password')->nullable()->after('wali_kelas');

            // Remove columns that we don't need anymore
            $table->dropColumn(['alamat', 'pendidikan_terakhir', 'tahun_mulai_mengajar', 'sertifikat']);
        });

        // Step 3: Remove unnecessary columns from users table
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'nomor_induk',
                'profile_photo',
                'nomor_telepon',
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

        // Step 4: Add id_kepala_sekolah to school_profiles table
        Schema::table('school_profiles', function (Blueprint $table) {
            $table->foreignId('id_kepala_sekolah')->nullable()->after('npsn')->constrained('guru_profiles')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Step 1: Remove id_kepala_sekolah from school_profiles
        Schema::table('school_profiles', function (Blueprint $table) {
            $table->dropForeign(['id_kepala_sekolah']);
            $table->dropColumn('id_kepala_sekolah');
        });

        // Step 2: Re-add columns to users table
        Schema::table('users', function (Blueprint $table) {
            $table->string('nomor_induk')->nullable()->after('role');
            $table->string('profile_photo')->nullable()->after('nomor_induk');
            $table->string('nomor_telepon')->nullable()->after('profile_photo');
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable()->after('nomor_telepon');
            $table->string('tempat_lahir')->nullable()->after('jenis_kelamin');
            $table->date('tanggal_lahir')->nullable()->after('tempat_lahir');
            $table->string('profile_photo_path')->nullable()->after('tanggal_lahir');
            $table->enum('status_kepegawaian', ['PNS', 'PPPK', 'GTT', 'GTY'])->nullable()->after('profile_photo_path');
            $table->string('golongan')->nullable()->after('status_kepegawaian');
            $table->json('mata_pelajaran')->nullable()->after('golongan');
            $table->string('wali_kelas')->nullable()->after('mata_pelajaran');
        });

        // Step 3: Restore guru_profiles table structure
        Schema::table('guru_profiles', function (Blueprint $table) {
            // Remove new columns
            $table->dropColumn(['nama', 'foto_profil', 'nomor_telepon', 'email', 'jenis_kelamin', 'password']);

            // Add back removed columns
            $table->string('alamat')->nullable()->after('tanggal_lahir');
            $table->string('pendidikan_terakhir')->nullable()->after('wali_kelas');
            $table->year('tahun_mulai_mengajar')->nullable()->after('pendidikan_terakhir');
            $table->string('sertifikat')->nullable()->after('tahun_mulai_mengajar');
        });

        // Step 4: Rename back to teacher_profiles
        Schema::rename('guru_profiles', 'teacher_profiles');
    }
};
