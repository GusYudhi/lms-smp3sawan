<?php

namespace App\Http\Controllers;

use App\Models\User;
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
        $user = Auth::user();
        return view('profile.profile-section');
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        try {
            $userAuth = Auth::user();
            $user = User::find($userAuth->id);

            Log::info('Profile update started', [
                'user_id' => $user->id,
                'data' => $request->only(['name', 'email', 'nomor_induk', 'nomor_telepon'])
            ]);

            // Validate the request
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:users,email,' . $user->id,
                'nomor_induk' => 'nullable|string|max:50|unique:users,nomor_induk,' . $user->id,
                'nomor_telepon' => 'nullable|string|max:20',
            ], [
                'name.required' => 'Nama lengkap wajib diisi',
                'name.max' => 'Nama lengkap maksimal 255 karakter',
                'email.required' => 'Email wajib diisi',
                'email.email' => 'Format email tidak valid',
                'email.unique' => 'Email sudah digunakan oleh pengguna lain',
                'nomor_induk.unique' => 'Nomor induk sudah digunakan oleh pengguna lain',
                'nomor_induk.max' => 'Nomor induk maksimal 50 karakter',
                'nomor_telepon.max' => 'Nomor telepon maksimal 20 karakter',
            ]);

            Log::info('Profile validation passed');

            $user->name = $validated['name'];
            $user->email = $validated['email'];
            $user->nomor_induk = $validated['nomor_induk'];
            $user->nomor_telepon = $validated['nomor_telepon'];

            $user->save();

            Log::info('Profile updated successfully', [
                'user_id' => $user->id
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

            $user = Auth::user();

            Log::info('Before password update', [
                'user_id' => $user->id,
                'user_email' => $user->email
            ]);

            // Update password
            $result = $user->update([
                'password' => Hash::make($validated['password'])
            ]);

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
                if ($user->profile_photo) {
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

                // Update user record
                $user->profile_photo = $photoName;
                $user->save();

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
