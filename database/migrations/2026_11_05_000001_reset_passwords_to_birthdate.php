<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Reset password akun siswa dan guru menjadi tanggal lahir format DDMMYYYY (contoh: 07112004).
     * Fallback untuk tanggal lahir NULL: smp3sawan123.
     * Role admin dan kepala_sekolah DIKECUALIKAN untuk menjaga keamanan akses manajemen.
     */
    public function up(): void
    {
        $defaultFallbackPassword = 'smp3sawan123';
        $defaultHashed = Hash::make($defaultFallbackPassword);

        // 1. Reset Akun Siswa
        $students = DB::table('users')
            ->join('student_profiles', 'users.id', '=', 'student_profiles.user_id')
            ->where('users.role', 'siswa')
            ->select('users.id as user_id', 'student_profiles.tanggal_lahir')
            ->get();

        foreach ($students as $student) {
            $rawDob = $student->tanggal_lahir;
            if ($rawDob) {
                try {
                    $dobFormatted = Carbon::parse($rawDob)->format('dmY');
                    $newPasswordHash = Hash::make($dobFormatted);
                } catch (\Exception $e) {
                    $newPasswordHash = $defaultHashed;
                }
            } else {
                $newPasswordHash = $defaultHashed;
            }

            DB::table('users')
                ->where('id', $student->user_id)
                ->update([
                    'password' => $newPasswordHash,
                    'updated_at' => now(),
                ]);
        }

        // 2. Reset Akun Guru
        $teachers = DB::table('users')
            ->join('guru_profiles', 'users.id', '=', 'guru_profiles.user_id')
            ->where('users.role', 'guru')
            ->select('users.id as user_id', 'guru_profiles.id as guru_profile_id', 'guru_profiles.tanggal_lahir')
            ->get();

        foreach ($teachers as $teacher) {
            $rawDob = $teacher->tanggal_lahir;
            if ($rawDob) {
                try {
                    $dobFormatted = Carbon::parse($rawDob)->format('dmY');
                    $newPasswordHash = Hash::make($dobFormatted);
                } catch (\Exception $e) {
                    $newPasswordHash = $defaultHashed;
                }
            } else {
                $newPasswordHash = $defaultHashed;
            }

            DB::table('users')
                ->where('id', $teacher->user_id)
                ->update([
                    'password' => $newPasswordHash,
                    'updated_at' => now(),
                ]);

            // Sinkronkan juga pada kolom password di tabel guru_profiles jika kolom ada
            DB::table('guru_profiles')
                ->where('id', $teacher->guru_profile_id)
                ->update([
                    'password' => $newPasswordHash,
                    'updated_at' => now(),
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Operasi reset password hash satu arah tidak dapat dibalikkan ke plaintext semula secara otomatis.
    }
};

