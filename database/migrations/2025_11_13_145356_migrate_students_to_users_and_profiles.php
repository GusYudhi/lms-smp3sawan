<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get all students from the students table
        $students = DB::table('students')->get();

        foreach ($students as $student) {
            // Create user record
            $userId = DB::table('users')->insertGetId([
                'name' => $student->name,
                'email' => $student->email,
                'password' => $student->password ?: Hash::make('password123'), // Default password if none exists
                'role' => 'siswa',
                'nomor_telepon' => $student->nomor_telepon,
                'jenis_kelamin' => $student->jenis_kelamin,
                'profile_photo' => $student->profile_photo,
                'created_at' => $student->created_at,
                'updated_at' => $student->updated_at,
            ]);

            // Create student profile record
            DB::table('student_profiles')->insert([
                'user_id' => $userId,
                'nis' => $student->nis,
                'nisn' => $student->nisn,
                'tempat_lahir' => $student->tempat_lahir,
                'tanggal_lahir' => $student->tanggal_lahir,
                'kelas' => $student->kelas,
                'nomor_telepon_orangtua' => $student->nomor_telepon_orangtua,
                'is_active' => $student->is_active ?? true,
                'created_at' => $student->created_at,
                'updated_at' => $student->updated_at,
            ]);
        }

        echo "Migrated " . count($students) . " students to users and student_profiles tables.\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove student users and their profiles
        $studentUsers = DB::table('users')->where('role', 'siswa')->pluck('id');

        DB::table('student_profiles')->whereIn('user_id', $studentUsers)->delete();
        DB::table('users')->where('role', 'siswa')->delete();

        echo "Removed migrated student data from users and student_profiles tables.\n";
    }
};
