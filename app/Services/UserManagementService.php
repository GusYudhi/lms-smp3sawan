<?php

namespace App\Services;

use App\Models\User;
use App\Models\StudentProfile;
use App\Models\TeacherProfile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserManagementService
{
    /**
     * Create a new student with user account and profile
     */
    public function createStudent(array $userData, array $profileData = []): User
    {
        return DB::transaction(function () use ($userData, $profileData) {
            // Create user account with siswa role
            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'] ?? null,
                'password' => Hash::make($userData['password']),
                'role' => 'siswa',
                'nomor_telepon' => $userData['nomor_telepon'] ?? null,
                'jenis_kelamin' => $userData['jenis_kelamin'] ?? null,
                'profile_photo' => $userData['profile_photo'] ?? null,
            ]);

            // Create student profile
            $user->studentProfile()->create([
                'nis' => $profileData['nis'] ?? null,
                'nisn' => $profileData['nisn'] ?? null,
                'tempat_lahir' => $profileData['tempat_lahir'] ?? null,
                'tanggal_lahir' => $profileData['tanggal_lahir'] ?? null,
                'kelas' => $profileData['kelas'] ?? null,
                'nomor_telepon_orangtua' => $profileData['nomor_telepon_orangtua'] ?? null,
                'alamat' => $profileData['alamat'] ?? null,
                'nama_orangtua_wali' => $profileData['nama_orangtua_wali'] ?? null,
                'pekerjaan_orangtua' => $profileData['pekerjaan_orangtua'] ?? null,
                'tahun_masuk' => $profileData['tahun_masuk'] ?? now()->year,
                'is_active' => true,
            ]);

            return $user->load('studentProfile');
        });
    }

    /**
     * Create a new teacher with user account and profile
     */
    public function createTeacher(array $userData, array $profileData = []): User
    {
        return DB::transaction(function () use ($userData, $profileData) {
            // Create user account with guru role
            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => Hash::make($userData['password']),
                'role' => $userData['role'] ?? 'guru', // Could be 'guru' or 'kepala_sekolah'
                'nomor_telepon' => $userData['nomor_telepon'] ?? null,
                'jenis_kelamin' => $userData['jenis_kelamin'] ?? null,
                'profile_photo' => $userData['profile_photo'] ?? null,
            ]);

            // Create teacher profile
            $user->teacherProfile()->create([
                'nip' => $profileData['nip'] ?? null,
                'tempat_lahir' => $profileData['tempat_lahir'] ?? null,
                'tanggal_lahir' => $profileData['tanggal_lahir'] ?? null,
                'alamat' => $profileData['alamat'] ?? null,
                'status_kepegawaian' => $profileData['status_kepegawaian'] ?? null,
                'golongan' => $profileData['golongan'] ?? null,
                'mata_pelajaran' => $profileData['mata_pelajaran'] ?? [],
                'wali_kelas' => $profileData['wali_kelas'] ?? null,
                'pendidikan_terakhir' => $profileData['pendidikan_terakhir'] ?? null,
                'tahun_mulai_mengajar' => $profileData['tahun_mulai_mengajar'] ?? null,
                'sertifikat' => $profileData['sertifikat'] ?? null,
                'is_active' => true,
            ]);

            return $user->load('teacherProfile');
        });
    }

    /**
     * Update student data
     */
    public function updateStudent(User $user, array $userData, array $profileData = []): User
    {
        return DB::transaction(function () use ($user, $userData, $profileData) {
            // Update user data
            $userUpdate = [
                'name' => $userData['name'],
                'nomor_telepon' => $userData['nomor_telepon'] ?? null,
                'jenis_kelamin' => $userData['jenis_kelamin'] ?? null,
            ];

            if (isset($userData['email'])) {
                $userUpdate['email'] = $userData['email'];
            }

            if (!empty($userData['password'])) {
                $userUpdate['password'] = Hash::make($userData['password']);
            }

            if (isset($userData['profile_photo'])) {
                // Delete old photo if exists
                if ($user->profile_photo) {
                    Storage::disk('public')->delete($user->profile_photo);
                }
                $userUpdate['profile_photo'] = $userData['profile_photo'];
            }

            $user->update($userUpdate);

            // Update or create student profile
            $user->studentProfile()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nis' => $profileData['nis'] ?? null,
                    'nisn' => $profileData['nisn'] ?? null,
                    'tempat_lahir' => $profileData['tempat_lahir'] ?? null,
                    'tanggal_lahir' => $profileData['tanggal_lahir'] ?? null,
                    'kelas' => $profileData['kelas'] ?? null,
                    'nomor_telepon_orangtua' => $profileData['nomor_telepon_orangtua'] ?? null,
                    'alamat' => $profileData['alamat'] ?? null,
                    'nama_orangtua_wali' => $profileData['nama_orangtua_wali'] ?? null,
                    'pekerjaan_orangtua' => $profileData['pekerjaan_orangtua'] ?? null,
                    'tahun_masuk' => $profileData['tahun_masuk'] ?? null,
                ]
            );

            return $user->load('studentProfile');
        });
    }

    /**
     * Update teacher data
     */
    public function updateTeacher(User $user, array $userData, array $profileData = []): User
    {
        return DB::transaction(function () use ($user, $userData, $profileData) {
            // Update user data
            $userUpdate = [
                'name' => $userData['name'],
                'email' => $userData['email'],
                'nomor_telepon' => $userData['nomor_telepon'] ?? null,
                'jenis_kelamin' => $userData['jenis_kelamin'] ?? null,
            ];

            if (!empty($userData['password'])) {
                $userUpdate['password'] = Hash::make($userData['password']);
            }

            if (isset($userData['profile_photo'])) {
                // Delete old photo if exists
                if ($user->profile_photo) {
                    Storage::disk('public')->delete($user->profile_photo);
                }
                $userUpdate['profile_photo'] = $userData['profile_photo'];
            }

            $user->update($userUpdate);

            // Update or create teacher profile
            $user->teacherProfile()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nip' => $profileData['nip'] ?? null,
                    'tempat_lahir' => $profileData['tempat_lahir'] ?? null,
                    'tanggal_lahir' => $profileData['tanggal_lahir'] ?? null,
                    'alamat' => $profileData['alamat'] ?? null,
                    'status_kepegawaian' => $profileData['status_kepegawaian'] ?? null,
                    'golongan' => $profileData['golongan'] ?? null,
                    'mata_pelajaran' => $profileData['mata_pelajaran'] ?? [],
                    'wali_kelas' => $profileData['wali_kelas'] ?? null,
                    'pendidikan_terakhir' => $profileData['pendidikan_terakhir'] ?? null,
                    'tahun_mulai_mengajar' => $profileData['tahun_mulai_mengajar'] ?? null,
                    'sertifikat' => $profileData['sertifikat'] ?? null,
                ]
            );

            return $user->load('teacherProfile');
        });
    }

    /**
     * Delete user and all related data
     */
    public function deleteUser(User $user): bool
    {
        return DB::transaction(function () use ($user) {
            // Delete profile photo if exists
            if ($user->profile_photo) {
                Storage::disk('public')->delete($user->profile_photo);
            }

            // Delete profiles (cascade should handle this, but being explicit)
            $user->studentProfile()?->delete();
            $user->teacherProfile()?->delete();

            // Delete user
            return $user->delete();
        });
    }

    /**
     * Get students with profiles for listing
     */
    public function getStudentsWithProfiles($search = null, $filters = [])
    {
        $query = User::with('studentProfile')
            ->where('role', 'siswa')
            ->orderBy('name');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('studentProfile', function ($sub) use ($search) {
                      $sub->where('nis', 'like', "%{$search}%")
                          ->orWhere('nisn', 'like', "%{$search}%");
                  });
            });
        }

        if (isset($filters['jenis_kelamin'])) {
            $query->where('jenis_kelamin', $filters['jenis_kelamin']);
        }

        if (isset($filters['kelas'])) {
            $query->whereHas('studentProfile', function ($q) use ($filters) {
                $q->where('kelas', $filters['kelas']);
            });
        }

        return $query->paginate(15);
    }

    /**
     * Get teachers with profiles for listing
     */
    public function getTeachersWithProfiles($search = null, $filters = [])
    {
        $query = User::with('teacherProfile')
            ->whereIn('role', ['guru', 'kepala_sekolah'])
            ->orderBy('name');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('teacherProfile', function ($sub) use ($search) {
                      $sub->where('nip', 'like', "%{$search}%");
                  });
            });
        }

        if (isset($filters['status_kepegawaian'])) {
            $query->whereHas('teacherProfile', function ($q) use ($filters) {
                $q->where('status_kepegawaian', $filters['status_kepegawaian']);
            });
        }

        return $query->paginate(15);
    }
}
