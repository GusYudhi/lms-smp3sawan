<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\StudentProfile;
use App\Helpers\ImageCompressor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    /**
     * Display the user's profile page.
     */
    public function show()
    {
        /** @var User $user */
        $user = Auth::user();

        // Eager load profile relationships based on role
        if ($user->role === 'siswa') {
            $user->load('studentProfile.kelas');
        } elseif (in_array($user->role, ['guru', 'kepala_sekolah'])) {
            $user->load('guruProfile.kelas');
        }

        // Get all kelas for wali kelas selection
        $kelasList = \App\Models\Kelas::orderBy('tingkat', 'asc')
            ->orderBy('nama_kelas', 'asc')
            ->get();

        // Get active semester
        $activeSemester = \App\Models\Semester::where('is_active', true)->first();

        // Get all mata pelajaran for selection based on active semester
        $mataPelajarans = \App\Models\MataPelajaran::query();

        if ($activeSemester) {
            $mataPelajarans->where(function($q) use ($activeSemester) {
                $q->where('semester_id', $activeSemester->id)
                  ->orWhereNull('semester_id');
            });
        }

        $mataPelajarans = $mataPelajarans->orderBy('nama_mapel', 'asc')->get();

        return view('profile.profile-section', compact('user', 'kelasList', 'mataPelajarans'));
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        try {
            /** @var User $user */
            $user = Auth::user(); // Menggunakan Auth::user() langsung

            Log::info('Profile update started', [
                'user_id' => $user->id,
                'role' => $user->role,
                'data' => $request->except(['_token', 'password', 'password_confirmation'])
            ]);

            if ($user->role === 'siswa') {
                // Validation rules for students (simplified) - removed photo handling
                $validated = $request->validate([
                    'name' => 'required|string|max:255',
                    'tempat_lahir' => 'nullable|string|max:100',
                    'tanggal_lahir' => 'nullable|date|before:today',
                    'nomor_telepon_orangtua' => 'nullable|string|max:20',
                    'jenis_kelamin' => 'nullable|in:L,P',
                ], [
                    'name.required' => 'Nama lengkap wajib diisi',
                    'name.max' => 'Nama lengkap maksimal 255 karakter',
                    'tempat_lahir.max' => 'Tempat lahir maksimal 100 karakter',
                    'tanggal_lahir.date' => 'Format tanggal lahir tidak valid',
                    'tanggal_lahir.before' => 'Tanggal lahir harus sebelum hari ini',
                    'nomor_telepon_orangtua.max' => 'Nomor telepon orang tua maksimal 20 karakter',
                    'jenis_kelamin.in' => 'Jenis kelamin harus Laki-laki (L) atau Perempuan (P)',
                ]);

                // Update user data
                $user->name = $validated['name'];
                $user->save();

                // Update student profile (without photo handling)
                $profileData = [
                    'tempat_lahir' => $validated['tempat_lahir'],
                    'tanggal_lahir' => $validated['tanggal_lahir'],
                    'nomor_telepon_orangtua' => $validated['nomor_telepon_orangtua'],
                    'jenis_kelamin' => $validated['jenis_kelamin'],
                ];

                $user->studentProfile()->updateOrCreate(
                    ['user_id' => $user->id],
                    $profileData
                );

            } else {
                // Get current guru profile ID if exists
                $guruProfileId = $user->guruProfile ? $user->guruProfile->id : null;

                // Validation rules for teachers, admin, etc.
                $validated = $request->validate([
                    'name' => 'required|string|max:255',
                    'email' => 'required|email|max:255|unique:users,email,' . $user->id,
                    'nip' => 'nullable|string|max:50' . ($guruProfileId ? '|unique:guru_profiles,nip,' . $guruProfileId : '|unique:guru_profiles,nip'),
                    'nomor_telepon' => 'nullable|string|max:20',
                    'jenis_kelamin' => 'nullable|in:L,P',
                    'tempat_lahir' => 'nullable|string|max:100',
                    'tanggal_lahir' => 'nullable|date|before:today',
                    'status_kepegawaian' => 'nullable|string|in:PNS,PPPK,HONORER',
                    'golongan' => 'nullable|string|max:20',
                    'mata_pelajaran' => 'nullable|string|max:100',
                    'kelas_id' => 'nullable|exists:kelas,id',
                    'profile_photo' => 'nullable|file|mimes:jpeg,jpg,png,webp,heic,heif|max:5120', // 5MB
                ], [
                    'name.required' => 'Nama lengkap wajib diisi',
                    'name.max' => 'Nama lengkap maksimal 255 karakter',
                    'email.required' => 'Email wajib diisi',
                    'email.email' => 'Format email tidak valid',
                    'email.unique' => 'Email sudah digunakan oleh pengguna lain',
                    'nip.unique' => 'NIP sudah digunakan oleh guru lain',
                    'nip.max' => 'NIP maksimal 50 karakter',
                    'nomor_telepon.max' => 'Nomor telepon maksimal 20 karakter',
                    'jenis_kelamin.in' => 'Jenis kelamin harus L atau P',
                    'tempat_lahir.max' => 'Tempat lahir maksimal 100 karakter',
                    'tanggal_lahir.date' => 'Format tanggal lahir tidak valid',
                    'tanggal_lahir.before' => 'Tanggal lahir harus sebelum hari ini',
                    'status_kepegawaian.in' => 'Status kepegawaian harus PNS, Honor, atau Kontrak',
                    'golongan.max' => 'Golongan maksimal 20 karakter',
                    'mata_pelajaran.max' => 'Mata pelajaran maksimal 100 karakter',
                    'kelas_id.exists' => 'Kelas tidak valid',
                ]);

                // Custom validation for HEIC/HEIF files
                if ($request->hasFile('profile_photo')) {
                    $file = $request->file('profile_photo');
                    $extension = strtolower($file->getClientOriginalExtension());
                    $mimeType = $file->getMimeType();

                    // Allowed extensions
                    $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'heic', 'heif'];

                    // Check extension
                    if (!in_array($extension, $allowedExtensions)) {
                        Log::error('Invalid file extension in profile update', [
                            'extension' => $extension,
                            'mime_type' => $mimeType,
                            'allowed' => $allowedExtensions
                        ]);

                        return response()->json([
                            'success' => false,
                            'message' => 'Format file tidak didukung. Gunakan JPG, PNG, WebP, atau HEIC',
                        ], 422);
                    }

                    // Check file size (5MB = 5120 KB = 5242880 bytes)
                    if ($file->getSize() > 5242880) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Ukuran file maksimal 5MB',
                        ], 422);
                    }
                }

                // Handle profile photo upload for teachers
                if ($request->hasFile('profile_photo')) {
                    $photo = $request->file('profile_photo');

                    // Delete old photo if exists in guru profile
                    if ($user->guruProfile && $user->guruProfile->foto_profil) {
                        Storage::disk('public')->delete('profile_photos/' . $user->guruProfile->foto_profil);
                    }

                    // Compress and store the photo as WebP with quality 60%
                    $photoPath = ImageCompressor::compressAndStore(
                        $photo,
                        'profile_photos',
                        time() . '_' . $user->id . '_' . uniqid(),
                        60,  // Quality 60% for better compression
                        1200 // Max width 1200px
                    );

                    // Store photo to guru profile (not user table)
                    $validated['foto_profil'] = basename($photoPath);
                }


                // Update user basic data
                $user->name = $validated['name'];
                $user->email = $validated['email'];
                $user->save();

                // Update guru profile with all fields
                if ($user->role === 'guru' || $user->role === 'kepala_sekolah') {
                    $profileData = [
                        'nama' => $validated['name'],
                        'nip' => $validated['nip'] ?? null,
                        'email' => $validated['email'],
                        'nomor_telepon' => $validated['nomor_telepon'] ?? null,
                        'jenis_kelamin' => $validated['jenis_kelamin'] ?? null,
                        'tempat_lahir' => $validated['tempat_lahir'] ?? null,
                        'tanggal_lahir' => $validated['tanggal_lahir'] ?? null,
                        'status_kepegawaian' => $validated['status_kepegawaian'] ?? null,
                        'golongan' => $validated['golongan'] ?? null,
                        'mata_pelajaran' => $validated['mata_pelajaran'] ?? null,
                        'kelas_id' => $validated['kelas_id'] ?? null,
                    ];

                    // Add foto_profil if uploaded
                    if (isset($validated['foto_profil'])) {
                        $profileData['foto_profil'] = $validated['foto_profil'];
                    }

                    $user->guruProfile()->updateOrCreate(
                        ['user_id' => $user->id],
                        $profileData
                    );
                }
            }

            Log::info('Profile updated successfully', [
                'user_id' => $user->id,
                'role' => $user->role
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Profil berhasil diperbarui',
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Profile validation failed', [
                'errors' => $e->errors(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Profile update failed with exception', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        try {
            Log::info('Password update started', [
                'user_id' => Auth::id(),
                'has_password' => !empty($request->password),
                'has_confirmation' => !empty($request->password_confirmation),
                'all_request_data' => $request->except(['password', 'password_confirmation']),
                'password_length' => strlen($request->password ?? ''),
                'confirmation_length' => strlen($request->password_confirmation ?? '')
            ]);

            // Validate the request
            $validated = $request->validate([
                'password' => [
                    'required',
                    'string',
                    'min:8',
                    'confirmed'
                ],
                'password_confirmation' => 'required|string'
            ], [
                'password.required' => 'Password baru wajib diisi',
                'password.min' => 'Password minimal 8 karakter',
                'password.confirmed' => 'Konfirmasi password tidak sesuai',
                'password_confirmation.required' => 'Konfirmasi password wajib diisi'
            ]);

            Log::info('Password validation passed', [
                'validated_password_length' => strlen($validated['password'])
            ]);

            /** @var User $user */
            $user = Auth::user();

            Log::info('Before password update', [
                'user_id' => $user->id,
                'user_email' => $user->email
            ]);

            // Update password
            $user->password = Hash::make($validated['password']);
            $result = $user->save();

            Log::info('Password updated successfully', [
                'user_id' => $user->id,
                'update_result' => $result
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Password berhasil diubah'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Password validation failed', [
                'errors' => $e->errors(),
                'user_id' => Auth::id(),
                'request_data' => $request->except(['password', 'password_confirmation'])
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Password update failed with exception', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update profile photo only.
     */
    public function updateProfilePhoto(Request $request)
    {
        try {
            Log::info('Profile photo upload started', [
                'has_file' => $request->hasFile('profile_photo'),
                'file_info' => $request->hasFile('profile_photo') ? [
                    'name' => $request->file('profile_photo')->getClientOriginalName(),
                    'size' => $request->file('profile_photo')->getSize(),
                    'mime' => $request->file('profile_photo')->getMimeType(),
                    'extension' => $request->file('profile_photo')->getClientOriginalExtension()
                ] : null
            ]);

            // Custom validation for HEIC/HEIF files
            if ($request->hasFile('profile_photo')) {
                $file = $request->file('profile_photo');
                $extension = strtolower($file->getClientOriginalExtension());
                $mimeType = $file->getMimeType();

                // Allowed extensions
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'heic', 'heif'];
                // Check extension
                if (!in_array($extension, $allowedExtensions)) {
                    Log::error('Invalid file extension', [
                        'extension' => $extension,
                        'mime_type' => $mimeType,
                        'allowed' => $allowedExtensions
                    ]);

                    return response()->json([
                        'success' => false,
                        'message' => 'Format file tidak didukung. Gunakan JPG, PNG, WebP, atau HEIC',
                    ], 422);
                }

                // Check file size (5MB = 5120 KB = 5242880 bytes)
                if ($file->getSize() > 5242880) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Ukuran file maksimal 5MB',
                    ], 422);
                }
            }

            // Validate the request
            $validated = $request->validate([
                'profile_photo' => [
                    'required',
                    'file',
                    'max:5120', // 5MB = 5120 KB
                ]
            ]);

            Log::info('Validation passed');

            /** @var User $user */
            $user = Auth::user();

            // Handle profile photo upload
            if ($request->hasFile('profile_photo')) {
                // Admins are not allowed to upload profile photos per new policy
                if ($user->role === 'admin') {
                    Log::info('Admin attempted to upload profile photo - action skipped', ['user_id' => $user->id]);
                    return response()->json([
                        'success' => false,
                        'message' => 'Admin tidak perlu mengunggah foto profil',
                    ], 403);
                }
                $photo = $request->file('profile_photo');

                // Check if file is valid
                if (!$photo->isValid()) {
                    Log::error('File is not valid', ['error' => $photo->getError()]);
                    return response()->json([
                        'success' => false,
                        'message' => 'File tidak valid atau rusak',
                    ], 422);
                }

                // Delete old photo if exists based on user role
                if ($user->role === 'siswa' && $user->studentProfile && $user->studentProfile->foto_profil) {
                    Storage::disk('public')->delete('profile_photos/' . $user->studentProfile->foto_profil);
                    Log::info('Old student photo deleted', ['old_photo' => $user->studentProfile->foto_profil]);
                } elseif (in_array($user->role, ['guru', 'kepala_sekolah']) && $user->guruProfile && $user->guruProfile->foto_profil) {
                    Storage::disk('public')->delete('profile_photos/' . $user->guruProfile->foto_profil);
                    Log::info('Old guru photo deleted', ['old_photo' => $user->guruProfile->foto_profil]);
                }

                // Compress and store the photo as WebP with maximum compression
                $photoPath = ImageCompressor::compressAndStore(
                    $photo,
                    'profile_photos',
                    time() . '_' . $user->id . '_' . uniqid(),
                    60,  // Quality 60% for better compression while maintaining acceptable quality
                    1200 // Max width 1200px
                );

                Log::info('Photo compressed and stored', [
                    'photo_path' => $photoPath,
                    'original_name' => $photo->getClientOriginalName(),
                    'size' => $photo->getSize(),
                    'mime_type' => $photo->getMimeType()
                ]);

                $photoName = basename($photoPath);

                // Update profile photo based on user role
                if ($user->role === 'siswa') {
                    // For students, save photo in student_profiles.foto_profil
                    $user->studentProfile()->updateOrCreate(
                        ['user_id' => $user->id],
                        ['foto_profil' => $photoName]
                    );
                } elseif (in_array($user->role, ['guru', 'kepala_sekolah'])) {
                    // For teachers, save photo in guru_profiles.foto_profil
                    $user->guruProfile()->updateOrCreate(
                        ['user_id' => $user->id],
                        ['foto_profil' => $photoName]
                    );
                }

                Log::info('Photo stored successfully', [
                    'photo_name' => $photoName,
                    'user_id' => $user->id,
                    'stored_path' => $photoPath
                ]);

                $profilePhotoUrl = asset('storage/profile_photos/' . $photoName);

                return response()->json([
                    'success' => true,
                    'message' => 'Foto profil berhasil diperbarui',
                    'profile_photo_url' => $profilePhotoUrl,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Tidak ada file yang diupload',
            ], 422);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed', [
                'errors' => $e->errors(),
                'file_info' => $request->hasFile('profile_photo') ? [
                    'name' => $request->file('profile_photo')->getClientOriginalName(),
                    'size' => $request->file('profile_photo')->getSize(),
                    'mime' => $request->file('profile_photo')->getMimeType(),
                ] : null
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal: ' . implode(', ', $e->validator->errors()->all()),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Upload failed with exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get user profile data as JSON.
     */
    public function getUserData()
    {
        /** @var User $user */
        $user = Auth::user();

        // Load profile based on role
        if ($user->role === 'guru' || $user->role === 'kepala_sekolah') {
            $user->load('guruProfile');
            $profile = $user->guruProfile;
        } elseif ($user->role === 'siswa') {
            $user->load('studentProfile');
            $profile = $user->studentProfile;
        } else {
            $profile = null;
        }

        return response()->json([
            'name' => $user->name,
            'email' => $user->email,
            'nomor_induk' => $profile ? ($profile->nip ?? $profile->nisn ?? null) : null,
            'nomor_telepon' => $profile ? $profile->nomor_telepon : null,
            'nomor_induk_label' => $user->getNomorIndukLabel(),
            'role_display' => $user->getRoleDisplayName(),
            'profile_photo_url' => $user->getProfilePhotoUrl()
        ]);
    }
}
