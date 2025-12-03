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
        // Step 1: Add profile_photo column to users table if not exists
        if (!Schema::hasColumn('users', 'profile_photo')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('profile_photo')->nullable()->after('role');
            });
        }

        // Step 2: Migrate data from student_profiles.foto_profil to users.profile_photo
        DB::statement('
            UPDATE users u
            INNER JOIN student_profiles sp ON u.id = sp.user_id
            SET u.profile_photo = sp.foto_profil
            WHERE sp.foto_profil IS NOT NULL AND u.profile_photo IS NULL
        ');

        // Step 3: Drop foto_profil column from student_profiles
        if (Schema::hasColumn('student_profiles', 'foto_profil')) {
            Schema::table('student_profiles', function (Blueprint $table) {
                $table->dropColumn('foto_profil');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Step 1: Add back foto_profil column to student_profiles
        if (!Schema::hasColumn('student_profiles', 'foto_profil')) {
            Schema::table('student_profiles', function (Blueprint $table) {
                $table->string('foto_profil')->nullable()->after('nomor_telepon_orangtua');
            });
        }

        // Step 2: Migrate data back from users.profile_photo to student_profiles.foto_profil
        DB::statement('
            UPDATE student_profiles sp
            INNER JOIN users u ON sp.user_id = u.id
            SET sp.foto_profil = u.profile_photo
            WHERE u.profile_photo IS NOT NULL AND sp.foto_profil IS NULL
        ');

        // Step 3: Drop profile_photo column from users
        if (Schema::hasColumn('users', 'profile_photo')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('profile_photo');
            });
        }
    }
};
