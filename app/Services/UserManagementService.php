<?php

namespace App\Services;

use App\Models\User;
use App\Models\StudentProfile;
use App\Models\GuruProfile;
use App\Models\Kelas;
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
            ]);

            // Handle kelas: find or create based on tingkat + nama_kelas
            $kelasId = null;
            if (isset($profileData['tingkat']) && isset($profileData['kelas'])) {
                $fullKelasName = $profileData['tingkat'] . $profileData['kelas'];
                $kelas = Kelas::findOrCreateByFullName($fullKelasName);
                $kelasId = $kelas->id;
            } elseif (isset($profileData['kelas'])) {
                // If only kelas provided (already in full format like "7A")
                $kelas = Kelas::findOrCreateByFullName($profileData['kelas']);
                $kelasId = $kelas->id;
            }

            // Create student profile
            $user->studentProfile()->create([
                'nis' => $profileData['nis'] ?? null,
                'nisn' => $profileData['nisn'] ?? null,
                'tempat_lahir' => $profileData['tempat_lahir'] ?? null,
                'tanggal_lahir' => $profileData['tanggal_lahir'] ?? null,
                'kelas_id' => $kelasId,
                'nomor_telepon_orangtua' => $profileData['nomor_telepon_orangtua'] ?? null,
                'foto_profil' => $profileData['foto_profil'] ?? null,
                'jenis_kelamin' => $profileData['jenis_kelamin'] ?? null,
                'tahun_angkatan' => $profileData['tahun_angkatan'] ?? null,
                'is_active' => true,
            ]);

            return $user->load('studentProfile.kelas');
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
                'role' => $userData['role'] ?? 'guru',
            ]);

            // Create teacher profile
            $user->guruProfile()->create([
                'nama' => $userData['name'], // Added to guru_profiles
                'nip' => $profileData['nip'] ?? null,
                'email' => $userData['email'], // Added to guru_profiles
                'password' => Hash::make($userData['password']), // Added to guru_profiles
                'nomor_telepon' => $userData['nomor_telepon'] ?? null,
                'jenis_kelamin' => $userData['jenis_kelamin'] ?? null,
                'foto_profil' => $userData['profile_photo'] ?? null,
                'tempat_lahir' => $profileData['tempat_lahir'] ?? null,
                'tanggal_lahir' => $profileData['tanggal_lahir'] ?? null,
                'alamat' => $profileData['alamat'] ?? null,
                'status_kepegawaian' => $profileData['status_kepegawaian'] ?? null,
                'jabatan_di_sekolah' => $profileData['jabatan_di_sekolah'] ?? null,
                'golongan' => $profileData['golongan'] ?? null,
                'mata_pelajaran_id' => $profileData['mata_pelajaran_id'] ?? null,
                'kelas_id' => $profileData['kelas_id'] ?? null,
                'pendidikan_terakhir' => $profileData['pendidikan_terakhir'] ?? null,
                'tahun_mulai_mengajar' => $profileData['tahun_mulai_mengajar'] ?? null,
                'sertifikat' => $profileData['sertifikat'] ?? null,
                'is_active' => true,
            ]);

            return $user->load('guruProfile');
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
            ];

            if (isset($userData['email'])) {
                $userUpdate['email'] = $userData['email'];
            }

            if (!empty($userData['password'])) {
                $userUpdate['password'] = Hash::make($userData['password']);
            }

            $user->update($userUpdate);

            // Handle photo replacement if new photo is provided
            if (isset($profileData['foto_profil'])) {
                // Delete old photo if exists in profile
                if ($user->studentProfile && $user->studentProfile->foto_profil) {
                    Storage::disk('public')->delete($user->studentProfile->foto_profil);
                }
            }

            // Handle kelas: find or create based on tingkat + nama_kelas
            $kelasId = null;
            if (isset($profileData['tingkat']) && isset($profileData['kelas'])) {
                $fullKelasName = $profileData['tingkat'] . $profileData['kelas'];
                $kelas = Kelas::findOrCreateByFullName($fullKelasName);
                $kelasId = $kelas->id;
            } elseif (isset($profileData['kelas'])) {
                // If only kelas provided (already in full format like "7A")
                $kelas = Kelas::findOrCreateByFullName($profileData['kelas']);
                $kelasId = $kelas->id;
            }

            // Update or create student profile
            $user->studentProfile()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nis' => $profileData['nis'] ?? null,
                    'nisn' => $profileData['nisn'] ?? null,
                    'tempat_lahir' => $profileData['tempat_lahir'] ?? null,
                    'tanggal_lahir' => $profileData['tanggal_lahir'] ?? null,
                    'kelas_id' => $kelasId,
                    'nomor_telepon_orangtua' => $profileData['nomor_telepon_orangtua'] ?? null,
                    'alamat' => $profileData['alamat'] ?? null,
                    'nama_orangtua_wali' => $profileData['nama_orangtua_wali'] ?? null,
                    'pekerjaan_orangtua' => $profileData['pekerjaan_orangtua'] ?? null,
                    'tahun_masuk' => $profileData['tahun_masuk'] ?? null,
                    'jenis_kelamin' => $profileData['jenis_kelamin'] ?? null,
                    'tahun_angkatan' => $profileData['tahun_angkatan'] ?? null,
                    'foto_profil' => $profileData['foto_profil'] ?? ($user->studentProfile->foto_profil ?? null),
                ]
            );

            return $user->load('studentProfile.kelas');
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
            $user->guruProfile()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nip' => $profileData['nip'] ?? null,
                    'tempat_lahir' => $profileData['tempat_lahir'] ?? null,
                    'tanggal_lahir' => $profileData['tanggal_lahir'] ?? null,
                    'alamat' => $profileData['alamat'] ?? null,
                    'status_kepegawaian' => $profileData['status_kepegawaian'] ?? null,
                    'golongan' => $profileData['golongan'] ?? null,
                    'mata_pelajaran_id' => $profileData['mata_pelajaran_id'] ?? null,
                    'kelas_id' => $profileData['kelas_id'] ?? null,
                    'pendidikan_terakhir' => $profileData['pendidikan_terakhir'] ?? null,
                    'tahun_mulai_mengajar' => $profileData['tahun_mulai_mengajar'] ?? null,
                    'sertifikat' => $profileData['sertifikat'] ?? null,
                ]
            );

            return $user->load('guruProfile');
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
            $user->guruProfile()?->delete();

            // Delete user
            return $user->delete();
        });
    }

    /**
     * Get students with profiles for listing
     */
    public function getStudentsWithProfiles($search = null, $filters = [], $perPage = 15)
    {
        $query = User::with('studentProfile.kelas')
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
            $query->whereHas('studentProfile', function ($q) use ($filters) {
                $q->where('jenis_kelamin', $filters['jenis_kelamin']);
            });
        }

        if (isset($filters['kelas'])) {
            $kelas = $filters['kelas'];

            $query->whereHas('studentProfile.kelas', function ($q) use ($kelas) {
                if (strlen($kelas) == 1 && is_numeric($kelas)) {
                    // Filter hanya berdasarkan tingkat (misal: "7", "8", "9")
                    $q->where('tingkat', $kelas);
                } else {
                    // Filter berdasarkan kelas lengkap (misal: "7A", "8B")
                    $tingkat = substr($kelas, 0, 1);
                    $namaKelas = substr($kelas, 1);
                    $q->where('tingkat', $tingkat)->where('nama_kelas', $namaKelas);
                }
            });
        }

        if (isset($filters['status'])) {
            $query->whereHas('studentProfile', function ($q) use ($filters) {
                $q->where('status', $filters['status']);
            });
        }

        // Validate perPage value
        $perPage = in_array($perPage, [15, 20, 50, 100, 300, 500, 1000]) ? $perPage : 15;

        return $query->paginate($perPage)->appends(request()->query());
    }

    /**
     * Get teachers with profiles for listing
     */
    public function getTeachersWithProfiles($search = null, $filters = [])
    {
        $query = User::with('guruProfile')
            ->whereIn('role', ['guru', 'kepala_sekolah'])
            ->orderBy('name');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('guruProfile', function ($sub) use ($search) {
                      $sub->where('nip', 'like', "%{$search}%");
                  });
            });
        }

        if (isset($filters['status_kepegawaian'])) {
            $query->whereHas('guruProfile', function ($q) use ($filters) {
                $q->where('status_kepegawaian', $filters['status_kepegawaian']);
            });
        }

        return $query->paginate(15);
    }
}
