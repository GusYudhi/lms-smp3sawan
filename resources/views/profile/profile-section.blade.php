@extends('layouts.app')

@section('content')
<div class="" id="profile-page">
    <h1 class="page-title">Profil Pengguna & Pengaturan Akun</h1>

    <div class="form-container-card" style="width: 700px;">
        <!-- Profile Photo Section -->
        <div class="profile-photo-section" style="text-align: center; margin-bottom: 30px;">
            <div class="profile-photo-container" style="position: relative; display: inline-block;">
            <img id="profile-photo-preview"
                 src="{{ auth()->user()->profile_photo ? asset('storage/profile_photos/' . auth()->user()->profile_photo) : asset('assets/image/profile-default.svg') }}"
                 alt="Profile Photo"
                 style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 4px solid #ddd;">
            <button type="button" id="change-photo-btn"
                style="position: absolute; bottom: 0; right: 0; background: #007bff; color: white; border: none; border-radius: 50%; width: 35px; height: 35px; cursor: pointer;"
                onclick="document.getElementById('profile-photo-input').click();">
                <i class="fas fa-camera"></i>
            </button>
            </div>
            <input type="file" id="profile-photo-input" accept="image/*" style="display: none;" onchange="previewPhoto(this)">
            <p style="margin-top: 10px; color: #666; font-size: 14px;">Klik ikon kamera untuk mengubah foto profil</p>
        </div>

        <script>
        function previewPhoto(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('profile-photo-preview').src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);

                // Auto-submit the photo when selected
                uploadProfilePhoto(input.files[0]);
            }
        }

        function uploadProfilePhoto(file) {
            // Validate file on client side
            console.log('Starting photo upload...');
            console.log('File details:', {
                name: file.name,
                size: file.size,
                type: file.type,
                extension: file.name.split('.').pop().toLowerCase()
            });

            // Client side validation
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
            const allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
            const maxSize = 2048 * 1024; // 2MB in bytes

            const extension = file.name.split('.').pop().toLowerCase();

            if (!allowedTypes.includes(file.type) && !allowedExtensions.includes(extension)) {
                alert('Format file tidak didukung. Gunakan JPG, PNG, atau WebP.');
                return;
            }

            if (file.size > maxSize) {
                alert('Ukuran file terlalu besar. Maksimal 2MB.');
                return;
            }

            // Show loading indicator
            const preview = document.getElementById('profile-photo-preview');
            const originalSrc = preview.src;

            // Create fresh FormData for each upload
            const formData = new FormData();
            formData.append('profile_photo', file);

            // Get CSRF token
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                         document.querySelector('#profile-form input[name="_token"]')?.value ||
                         document.querySelector('#password-form input[name="_token"]')?.value;

            if (token) {
                formData.append('_token', token);
            }

            console.log('FormData entries:');
            for (let [key, value] of formData.entries()) {
                console.log(key, value instanceof File ? `File: ${value.name} (${value.size} bytes)` : value);
            }

            fetch('{{ route("profile.update-photo") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                }
            })
            .then(async response => {
                console.log('Response status:', response.status);
                console.log('Response headers:', Object.fromEntries(response.headers));

                // Get response text first
                const responseText = await response.text();
                console.log('Response text:', responseText);

                // Try to parse as JSON
                let data;
                try {
                    data = JSON.parse(responseText);
                } catch (e) {
                    console.error('Failed to parse JSON:', e);
                    throw new Error(`Server returned non-JSON response: ${responseText}`);
                }

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${data.message || 'Unknown error'}`);
                }

                return data;
            })
            .then(data => {
                console.log('Response data:', data);
                if(data.success) {
                    alert('Foto profil berhasil diperbarui!');
                    if(data.profile_photo_url) {
                        preview.src = data.profile_photo_url;
                    }
                } else {
                    alert('Terjadi kesalahan: ' + data.message);
                    preview.src = originalSrc; // Restore original image
                    console.error('Upload error:', data);
                }
            })
            .catch(error => {
                console.error('Upload error:', error);
                alert('Terjadi kesalahan saat menyimpan foto: ' + error.message);
                preview.src = originalSrc; // Restore original image
            });
        }
        </script>

        <script>
        // Password strength checker
        function checkPasswordStrength(password) {
            const isValidLength = password.length >= 8;

            // Update UI indicator
            const lengthCheck = document.getElementById('length-check');
            if (lengthCheck) {
                lengthCheck.style.color = isValidLength ? 'green' : 'red';
                lengthCheck.innerHTML = (isValidLength ? '✓' : '✗') + ' Minimal 8 karakter';
            }

            // Update button status
            updatePasswordButton();

            return isValidLength;
        }

        // Update password button status
        function updatePasswordButton() {
            const password = document.getElementById('password').value;
            const confirmation = document.getElementById('password_confirmation').value;
            const submitBtn = document.getElementById('save-password-btn');

            const isValidLength = password.length >= 8;
            const isMatch = password === confirmation && confirmation.length > 0;

            submitBtn.disabled = !(isValidLength && isMatch);
            submitBtn.style.opacity = submitBtn.disabled ? '0.5' : '1';

            console.log('Button status updated:', {
                password_length: password.length,
                is_valid_length: isValidLength,
                is_match: isMatch,
                button_disabled: submitBtn.disabled
            });
        }        // Password match checker
        function checkPasswordMatch() {
            const password = document.getElementById('password').value;
            const confirmation = document.getElementById('password_confirmation').value;
            const matchCheck = document.getElementById('password-match-check');

            if (confirmation.length > 0) {
                if (password === confirmation) {
                    matchCheck.style.color = 'green';
                    matchCheck.innerHTML = '✓ Password sesuai';
                    matchCheck.style.display = 'block';
                } else {
                    matchCheck.style.color = 'red';
                    matchCheck.innerHTML = '✗ Password tidak sesuai';
                    matchCheck.style.display = 'block';
                }
            } else {
                matchCheck.style.display = 'none';
            }

            // Update button status
            updatePasswordButton();
        }
        </script>

        <form id="profile-form" enctype="multipart/form-data">
            @csrf

            <h2>Informasi Pribadi</h2>
            <div class="form-group">
                <label for="name">Nama Lengkap</label>
                <input type="text" id="name" name="name" class="form-control" placeholder="Masukkan nama lengkap" value="{{ old('name', auth()->user()->name ?? '') }}" required>
            </div>
            <div class="form-group">
                <label for="email">Alamat Email</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="Masukkan alamat email" value="{{ old('email', auth()->user()->email ?? '') }}" required>
            </div>
            <div class="form-group">
                @if(auth()->user()->role === 'siswa')
                    <label for="nomor_induk">NISN</label>
                @elseif(in_array(auth()->user()->role, ['guru', 'kepala_sekolah']))
                    <label for="nomor_induk">NIP</label>
                @endif
                <input type="text" id="nomor_induk" name="nomor_induk" class="form-control" placeholder="Masukkan nomor induk" value="{{ old('nomor_induk', auth()->user()->nomor_induk ?? '') }}">
            </div>
            <div class="form-group">
                @if(auth()->user()->role === 'siswa')
                    <label for="nomor_telepon">Nomor Telepon Orang Tua</label>
                @elseif(in_array(auth()->user()->role, ['guru', 'kepala_sekolah']))
                    <label for="nomor_telepon">Nomor Telepon</label>
                @endif
                <input type="text" id="nomor_telepon" name="nomor_telepon" class="form-control" placeholder="Masukkan nomor telepon" value="{{ old('nomor_telepon', auth()->user()->nomor_telepon ?? '') }}">
            </div>
            <button type="submit" class="btn btn-primary">Simpan data</button>
        </form>

        <form id="password-form" style="margin-top: 30px;">
            @csrf
            <h2>Pengaturan Akun</h2>
            <div class="form-group">
                <label for="password">Kata Sandi Baru</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Masukkan kata sandi baru" onkeyup="checkPasswordStrength(this.value)">
                <small style="color: #666; font-size: 12px; margin-top: 5px; display: block;">
                    <span id="length-check" style="color: red;">✗ Minimal 8 karakter</span>
                </small>
            </div>
            <div class="form-group">
                <label for="password_confirmation">Konfirmasi Kata Sandi</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Konfirmasi kata sandi baru" onkeyup="checkPasswordMatch()">
                <small id="password-match-check" style="color: red; font-size: 12px; margin-top: 5px; display: none;">
                    Password tidak sesuai
                </small>
            </div>

            <button type="submit" class="btn btn-primary" id="save-password-btn">Simpan Password</button>
        </form>
    </div>
</div>
<script>
    // Handle profile form submission
    document.getElementById('profile-form').addEventListener('submit', function(e) {
        e.preventDefault();

        console.log('Profile form submission started');

        const formData = new FormData(this);

        // Get CSRF token
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                     document.querySelector('#profile-form input[name="_token"]')?.value;

        fetch('{{ route("profile.update") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            }
        })
        .then(async response => {
            console.log('Profile update response status:', response.status);

            // Get response text first
            const responseText = await response.text();
            console.log('Profile update response text:', responseText);

            // Try to parse as JSON
            let data;
            try {
                data = JSON.parse(responseText);
            } catch (e) {
                console.error('Failed to parse JSON:', e);
                throw new Error(`Server returned non-JSON response: ${responseText}`);
            }

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${data.message || 'Unknown error'}`);
            }

            return data;
        })
        .then(data => {
            console.log('Profile update response data:', data);
            if(data.success) {
                alert('Profil berhasil diperbarui!');
                location.reload();
            } else {
                // Handle validation errors
                if(data.errors) {
                    let errorMessages = [];
                    for(let field in data.errors) {
                        errorMessages.push(...data.errors[field]);
                    }
                    alert('Error validasi:\n' + errorMessages.join('\n'));
                } else {
                    alert('Terjadi kesalahan: ' + data.message);
                }
                console.error('Profile update error:', data);
            }
        })
        .catch(error => {
            console.error('Profile update error:', error);
            alert('Terjadi kesalahan saat menyimpan data: ' + error.message);
        });
    });

    // Handle password form submission
    document.getElementById('password-form').addEventListener('submit', function(e) {
        e.preventDefault();

        const password = document.getElementById('password').value;
        const passwordConfirmation = document.getElementById('password_confirmation').value;

        console.log('Password form submission started');
        console.log('Password length:', password.length);
        console.log('Confirmation length:', passwordConfirmation.length);

        // Client-side validation
        if(!password || !passwordConfirmation) {
            alert('Harap isi kedua field password');
            return;
        }

        if(password.length < 8) {
            alert('Password minimal 8 karakter');
            return;
        }

        if(password !== passwordConfirmation) {
            alert('Konfirmasi password tidak cocok');
            return;
        }

        console.log('Client validation passed');

        const formData = new FormData(this);

        // Log FormData contents
        console.log('FormData contents:');
        for (let [key, value] of formData.entries()) {
            console.log(key, value instanceof File ? `File: ${value.name}` : value);
        }

        // Get CSRF token
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                     document.querySelector('#password-form input[name="_token"]')?.value;

        console.log('CSRF Token:', token ? 'Found' : 'Not found');

        fetch('{{ route("profile.update-password") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            }
        })
        .then(async response => {
            console.log('Password update response status:', response.status);
            console.log('Response headers:', Object.fromEntries(response.headers));

            // Get response text first
            const responseText = await response.text();
            console.log('Password update response text:', responseText);

            // Try to parse as JSON
            let data;
            try {
                data = JSON.parse(responseText);
            } catch (e) {
                console.error('Failed to parse JSON:', e);
                throw new Error(`Server returned non-JSON response: ${responseText}`);
            }

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${data.message || 'Unknown error'}`);
            }

            return data;
        })
        .then(data => {
            console.log('Password update response data:', data);
            if(data.success) {
                alert('Password berhasil diperbarui!');
                // Clear password fields
                document.getElementById('password').value = '';
                document.getElementById('password_confirmation').value = '';
            } else {
                // Handle validation errors
                if(data.errors) {
                    let errorMessages = [];
                    for(let field in data.errors) {
                        errorMessages.push(...data.errors[field]);
                    }
                    alert('Error validasi:\n' + errorMessages.join('\n'));
                } else {
                    alert('Terjadi kesalahan: ' + data.message);
                }
                console.error('Password update error:', data);
            }
        })
        .catch(error => {
            console.error('Password update error:', error);
            alert('Terjadi kesalahan saat menyimpan password: ' + error.message);
        });
    });
</script>
@endsection
