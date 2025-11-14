<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\StudentProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Display the user's profile page.
     */
    public function show()
    {
        /** @var User $user */
        $user = Auth::user();
        return view('profile.profile-section');
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
                // Validation rules for students (simplified)
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

                // Update student profile (with new fields)
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
                // Validation rules for teachers, admin, etc.
                $validated = $request->validate([
                    'name' => 'required|string|max:255',
                    'email' => 'required|email|max:255|unique:users,email,' . $user->id,
                    'nomor_induk' => 'nullable|string|max:50|unique:users,nomor_induk,' . $user->id,
                    'nomor_telepon' => 'nullable|string|max:20',
                    'jenis_kelamin' => 'nullable|in:L,P',
                    'tempat_lahir' => 'nullable|string|max:100',
                    'tanggal_lahir' => 'nullable|date|before:today',
                    'status_kepegawaian' => 'nullable|string|in:PNS,Honor,Kontrak',
                    'golongan' => 'nullable|string|max:20',
                    'mata_pelajaran' => 'nullable|string|max:100',
                    'wali_kelas' => 'nullable|string|max:10',
                ], [
                    'name.required' => 'Nama lengkap wajib diisi',
                    'name.max' => 'Nama lengkap maksimal 255 karakter',
                    'email.required' => 'Email wajib diisi',
                    'email.email' => 'Format email tidak valid',
                    'email.unique' => 'Email sudah digunakan oleh pengguna lain',
                    'nomor_induk.unique' => 'Nomor induk sudah digunakan oleh pengguna lain',
                    'nomor_induk.max' => 'Nomor induk maksimal 50 karakter',
                    'nomor_telepon.max' => 'Nomor telepon maksimal 20 karakter',
                    'jenis_kelamin.in' => 'Jenis kelamin harus L atau P',
                    'tempat_lahir.max' => 'Tempat lahir maksimal 100 karakter',
                    'tanggal_lahir.date' => 'Format tanggal lahir tidak valid',
                    'tanggal_lahir.before' => 'Tanggal lahir harus sebelum hari ini',
                    'status_kepegawaian.in' => 'Status kepegawaian harus PNS, Honor, atau Kontrak',
                    'golongan.max' => 'Golongan maksimal 20 karakter',
                    'mata_pelajaran.max' => 'Mata pelajaran maksimal 100 karakter',
                    'wali_kelas.max' => 'Wali kelas maksimal 10 karakter',
                ]);

                // Update user data
                $user->fill($validated);
                $user->save();
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

            // Validate the request
            $validated = $request->validate([
                'profile_photo' => [
                    'required',
                    'file',
                    'image',
                    'mimes:jpeg,jpg,png,webp',
                    'max:2048',
                ]
            ]);

            Log::info('Validation passed');

            /** @var User $user */
            $user = Auth::user();

            // Handle profile photo upload
            if ($request->hasFile('profile_photo')) {
                $photo = $request->file('profile_photo');

                // Check if file is valid
                if (!$photo->isValid()) {
                    Log::error('File is not valid', ['error' => $photo->getError()]);
                    return response()->json([
                        'success' => false,
                        'message' => 'File tidak valid atau rusak',
                    ], 422);
                }

                // Delete old photo if exists
                if ($user->role === 'siswa' && $user->studentProfile && $user->studentProfile->foto_profil) {
                    // For students, delete from student profile
                    Storage::disk('public')->delete('profile_photos/' . $user->studentProfile->foto_profil);
                    Log::info('Old student photo deleted', ['old_photo' => $user->studentProfile->foto_profil]);
                } elseif ($user->profile_photo) {
                    // For other users, delete from user profile
                    Storage::disk('public')->delete('profile_photos/' . $user->profile_photo);
                    Log::info('Old photo deleted', ['old_photo' => $user->profile_photo]);
                }

                // Generate unique filename
                $extension = $photo->getClientOriginalExtension();
                $photoName = time() . '_' . $user->id . '_' . uniqid() . '.' . $extension;

                Log::info('Storing photo', [
                    'photo_name' => $photoName,
                    'original_name' => $photo->getClientOriginalName(),
                    'size' => $photo->getSize(),
                    'mime_type' => $photo->getMimeType()
                ]);

                // Store the file
                $stored = $photo->storeAs('profile_photos', $photoName, 'public');

                if (!$stored) {
                    Log::error('Failed to store photo');
                    return response()->json([
                        'success' => false,
                        'message' => 'Gagal menyimpan file foto',
                    ], 500);
                }

                // Update user or student profile record
                if ($user->role === 'siswa') {
                    // For students, save photo in student profile
                    $user->studentProfile()->updateOrCreate(
                        ['user_id' => $user->id],
                        ['foto_profil' => $photoName]
                    );
                } else {
                    // For other users, save in user profile
                    $user->profile_photo = $photoName;
                    $user->save();
                }

                Log::info('Photo stored successfully', [
                    'photo_name' => $photoName,
                    'user_id' => $user->id,
                    'stored_path' => $stored
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

        return response()->json([
            'name' => $user->name,
            'email' => $user->email,
            'nomor_induk' => $user->nomor_induk,
            'nomor_telepon' => $user->nomor_telepon,
            'nomor_induk_label' => $user->getNomorIndukLabel(),
            'role_display' => $user->getRoleDisplayName(),
            'profile_photo_url' => $user->getProfilePhotoUrl()
        ]);
    }
}
