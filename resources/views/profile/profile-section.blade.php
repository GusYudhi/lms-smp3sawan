@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card card-modern border-0 shadow-sm">
                <div class="card-body p-4">
                    <h1 class="h3 text-high-contrast fw-bold mb-2">
                        <i class="fas fa-user-cog text-primary me-2"></i>Profil Pengguna & Pengaturan Akun
                    </h1>
                    <p class="text-subtle mb-0">Kelola informasi pribadi dan pengaturan keamanan akun Anda</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-7">
            <!-- Profile Photo Section -->
            <div class="card card-stats border-0 shadow-sm mb-4">
                <div class="card-header bg-light border-bottom-0 py-3">
                    <h5 class="card-title mb-0 text-high-contrast fw-semibold">
                        <i class="fas fa-camera text-primary me-2"></i>Foto Profil
                    </h5>
                </div>
                <div class="card-body text-center p-4">
                    <div class="position-relative d-inline-block mb-3">
                        <img id="profile-photo-preview"
                             src="{{ auth()->user()->profile_photo ? asset('storage/profile_photos/' . auth()->user()->profile_photo) : asset('assets/image/profile-default.svg') }}"
                             alt="Profile Photo"
                             class="rounded-circle border border-3 border-light shadow"
                             style="width: 120px; height: 120px; object-fit: cover;">
                        <button type="button" id="change-photo-btn"
                                class="btn btn-primary rounded-circle position-absolute bottom-0 end-0 p-2"
                                style="width: 40px; height: 40px;"
                                onclick="document.getElementById('profile-photo-input').click();">
                            <i class="fas fa-camera"></i>
                        </button>
                    </div>
                    <input type="file" id="profile-photo-input" accept="image/*" class="d-none" onchange="previewPhoto(this)">
                    <p class="text-subtle small mb-0">Klik ikon kamera untuk mengubah foto profil</p>
                    <small class="text-muted">Format: JPG, PNG, WebP (Maks. 2MB)</small>
                </div>
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
                showAlert('Format file tidak didukung. Gunakan JPG, PNG, atau WebP.', 'danger');
                return;
            }

            if (file.size > maxSize) {
                showAlert('Ukuran file terlalu besar. Maksimal 2MB.', 'danger');
                return;
            }

            // Show loading state
            const changeBtn = document.getElementById('change-photo-btn');
            const originalHtml = changeBtn.innerHTML;
            changeBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            changeBtn.disabled = true;

            // Create FormData
            const formData = new FormData();
            formData.append('profile_photo', file);

            // Get CSRF token
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                         document.querySelector('#profile-form input[name="_token"]')?.value ||
                         document.querySelector('#password-form input[name="_token"]')?.value;

            if (token) {
                formData.append('_token', token);
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
                const responseText = await response.text();
                let data;
                try {
                    data = JSON.parse(responseText);
                } catch (e) {
                    throw new Error(`Server returned non-JSON response: ${responseText}`);
                }

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${data.message || 'Unknown error'}`);
                }

                return data;
            })
            .then(data => {
                if(data.success) {
                    showAlert('Foto profil berhasil diperbarui!', 'success');
                    if(data.profile_photo_url) {
                        document.getElementById('profile-photo-preview').src = data.profile_photo_url;
                    }
                } else {
                    showAlert('Terjadi kesalahan: ' + data.message, 'danger');
                }
            })
            .catch(error => {
                console.error('Upload error:', error);
                showAlert('Terjadi kesalahan saat menyimpan foto: ' + error.message, 'danger');
                // Restore original image would need to be implemented
            })
            .finally(() => {
                // Restore button state
                changeBtn.innerHTML = originalHtml;
                changeBtn.disabled = false;
            });
        }

        // Password visibility toggle
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = document.getElementById(fieldId + '-toggle-icon');

            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Alert helper function
        function showAlert(message, type = 'info') {
            // Remove existing alerts
            const existingAlerts = document.querySelectorAll('.alert-floating');
            existingAlerts.forEach(alert => alert.remove());

            // Create new alert
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show alert-floating position-fixed`;
            alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            alertDiv.innerHTML = `
                <div class="d-flex align-items-center">
                    <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'danger' ? 'exclamation-triangle' : 'info-circle'} me-2"></i>
                    <div class="flex-grow-1">${message}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            document.body.appendChild(alertDiv);

            // Auto remove after 5 seconds
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 5000);
        }

        // Password strength checker
        function checkPasswordStrength(password) {
            const isValidLength = password.length >= 8;
            const lengthCheck = document.getElementById('length-check');

            if (lengthCheck) {
                if (isValidLength) {
                    lengthCheck.className = 'text-success';
                    lengthCheck.innerHTML = '<i class="fas fa-check me-1"></i>Minimal 8 karakter';
                } else {
                    lengthCheck.className = 'text-danger';
                    lengthCheck.innerHTML = '<i class="fas fa-times me-1"></i>Minimal 8 karakter';
                }
            }

            updatePasswordButton();
            return isValidLength;
        }

        // Password match checker
        function checkPasswordMatch() {
            const password = document.getElementById('password').value;
            const confirmation = document.getElementById('password_confirmation').value;
            const matchCheck = document.getElementById('password-match-check');

            if (confirmation.length > 0) {
                if (password === confirmation) {
                    matchCheck.className = 'text-success';
                    matchCheck.innerHTML = '<i class="fas fa-check me-1"></i>Password sesuai';
                    matchCheck.classList.remove('d-none');
                } else {
                    matchCheck.className = 'text-danger';
                    matchCheck.innerHTML = '<i class="fas fa-times me-1"></i>Password tidak sesuai';
                    matchCheck.classList.remove('d-none');
                }
            } else {
                matchCheck.classList.add('d-none');
            }

            updatePasswordButton();
        }

        // Update password button status
        function updatePasswordButton() {
            const password = document.getElementById('password').value;
            const confirmation = document.getElementById('password_confirmation').value;
            const submitBtn = document.getElementById('save-password-btn');

            const isValidLength = password.length >= 8;
            const isMatch = password === confirmation && confirmation.length > 0;

            submitBtn.disabled = !(isValidLength && isMatch);
            submitBtn.style.opacity = submitBtn.disabled ? '0.6' : '1';
        }
        </script>

            <!-- Personal Information Form -->
            <div class="card card-stats border-0 shadow-sm mb-4">
                <div class="card-header bg-light border-bottom-0 py-3">
                    <h5 class="card-title mb-0 text-high-contrast fw-semibold">
                        <i class="fas fa-user text-primary me-2"></i>Informasi Pribadi
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form id="profile-form" enctype="multipart/form-data">
                        @csrf
                        <div class="row g-4">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="name" class="form-label fw-medium text-high-contrast">
                                        Nama Lengkap <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                           id="name"
                                           name="name"
                                           class="form-control"
                                           placeholder="Masukkan nama lengkap"
                                           value="{{ old('name', auth()->user()->name ?? '') }}"
                                           required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label fw-medium text-high-contrast">
                                        Alamat Email <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-envelope text-muted"></i>
                                        </span>
                                        <input type="email"
                                               id="email"
                                               name="email"
                                               class="form-control"
                                               placeholder="Masukkan alamat email"
                                               value="{{ old('email', auth()->user()->email ?? '') }}"
                                               required>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nomor_induk" class="form-label fw-medium text-high-contrast">
                                        @if(auth()->user()->role === 'siswa')
                                            NISN
                                        @elseif(in_array(auth()->user()->role, ['guru', 'kepala_sekolah']))
                                            NIP
                                        @endif
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-id-card text-muted"></i>
                                        </span>
                                        <input type="text"
                                               id="nomor_induk"
                                               name="nomor_induk"
                                               class="form-control"
                                               placeholder="Masukkan nomor induk"
                                               value="{{ old('nomor_induk', auth()->user()->nomor_induk ?? '') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="mb-4">
                                    <label for="nomor_telepon" class="form-label fw-medium text-high-contrast">
                                        @if(auth()->user()->role === 'siswa')
                                            Nomor Telepon Orang Tua
                                        @elseif(in_array(auth()->user()->role, ['guru', 'kepala_sekolah']))
                                            Nomor Telepon
                                        @endif
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-phone text-muted"></i>
                                        </span>
                                        <input type="text"
                                               id="nomor_telepon"
                                               name="nomor_telepon"
                                               class="form-control"
                                               placeholder="Masukkan nomor telepon"
                                               value="{{ old('nomor_telepon', auth()->user()->nomor_telepon ?? '') }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save me-2"></i>Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Password Settings Form -->
            <div class="card card-stats border-0 shadow-sm">
                <div class="card-header bg-light border-bottom-0 py-3">
                    <h5 class="card-title mb-0 text-high-contrast fw-semibold">
                        <i class="fas fa-lock text-primary me-2"></i>Pengaturan Keamanan
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form id="password-form">
                        @csrf
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password" class="form-label fw-medium text-high-contrast">
                                        Kata Sandi Baru <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-key text-muted"></i>
                                        </span>
                                        <input type="password"
                                               id="password"
                                               name="password"
                                               class="form-control"
                                               placeholder="Masukkan kata sandi baru"
                                               onkeyup="checkPasswordStrength(this.value)">
                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password')">
                                            <i class="fas fa-eye" id="password-toggle-icon"></i>
                                        </button>
                                    </div>
                                    <div class="mt-2">
                                        <small class="text-muted d-block">
                                            <span id="length-check" class="text-danger">
                                                <i class="fas fa-times me-1"></i>Minimal 8 karakter
                                            </span>
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label for="password_confirmation" class="form-label fw-medium text-high-contrast">
                                        Konfirmasi Kata Sandi <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-key text-muted"></i>
                                        </span>
                                        <input type="password"
                                               id="password_confirmation"
                                               name="password_confirmation"
                                               class="form-control"
                                               placeholder="Konfirmasi kata sandi baru"
                                               onkeyup="checkPasswordMatch()">
                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_confirmation')">
                                            <i class="fas fa-eye" id="password_confirmation-toggle-icon"></i>
                                        </button>
                                    </div>
                                    <div class="mt-2">
                                        <small id="password-match-check" class="text-danger d-none">
                                            <i class="fas fa-times me-1"></i>Password tidak sesuai
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-success btn-lg" id="save-password-btn" disabled>
                                <i class="fas fa-shield-alt me-2"></i>Perbarui Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    // Handle profile form submission
    document.getElementById('profile-form').addEventListener('submit', function(e) {
        e.preventDefault();

        const submitBtn = this.querySelector('button[type="submit"]');
        const originalHtml = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';
        submitBtn.disabled = true;

        const formData = new FormData(this);
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                     document.querySelector('#profile-form input[name="_token"]')?.value;

        // Debug: Log form data
        console.log('Profile form data:');
        for (let [key, value] of formData.entries()) {
            console.log(`${key}:`, value);
        }
        console.log('CSRF Token:', token ? 'Found' : 'Not found');

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
            console.log('Profile update response headers:', Object.fromEntries(response.headers));

            const responseText = await response.text();
            console.log('Profile update response text:', responseText);

            let data;
            try {
                data = JSON.parse(responseText);
            } catch (e) {
                console.error('Failed to parse JSON response:', e);
                throw new Error(`Server returned non-JSON response: ${responseText}`);
            }

            console.log('Profile update parsed data:', data);

            // Handle validation errors (422) differently from other errors
            if (response.status === 422) {
                console.log('Validation error detected (422)');
                if (data.errors) {
                    console.log('Validation errors:', data.errors);
                    let errorMessages = [];
                    for (let field in data.errors) {
                        errorMessages.push(...data.errors[field]);
                    }
                    showAlert('Error validasi: ' + errorMessages.join(', '), 'danger');
                    return null; // Don't throw, just return null to skip the success handler
                } else {
                    throw new Error(data.message || 'Validasi gagal');
                }
            } else if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${data.message || 'Unknown error'}`);
            }

            return data;
        })
        .then(data => {
            // Only process success if data is not null (validation didn't fail)
            if (data && data.success) {
                showAlert('Profil berhasil diperbarui!', 'success');
                setTimeout(() => location.reload(), 2000);
            } else if (data) {
                // Handle other error cases
                showAlert('Terjadi kesalahan: ' + (data.message || 'Unknown error'), 'danger');
            }
        })
        .catch(error => {
            console.error('Profile update error:', error);
            showAlert('Terjadi kesalahan saat menyimpan data: ' + error.message, 'danger');
        })
        .finally(() => {
            submitBtn.innerHTML = originalHtml;
            submitBtn.disabled = false;
        });
    });

    // Handle password form submission
    document.getElementById('password-form').addEventListener('submit', function(e) {
        e.preventDefault();

        const submitBtn = this.querySelector('button[type="submit"]');
        const originalHtml = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Memperbarui...';
        submitBtn.disabled = true;

        const password = document.getElementById('password').value;
        const passwordConfirmation = document.getElementById('password_confirmation').value;

        // Client-side validation
        if(!password || !passwordConfirmation) {
            showAlert('Harap isi kedua field password', 'warning');
            submitBtn.innerHTML = originalHtml;
            submitBtn.disabled = false;
            return;
        }

        if(password.length < 8) {
            showAlert('Password minimal 8 karakter', 'warning');
            submitBtn.innerHTML = originalHtml;
            submitBtn.disabled = false;
            return;
        }

        if(password !== passwordConfirmation) {
            showAlert('Konfirmasi password tidak cocok', 'warning');
            submitBtn.innerHTML = originalHtml;
            submitBtn.disabled = false;
            return;
        }

        const formData = new FormData(this);
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                     document.querySelector('#password-form input[name="_token"]')?.value;

        fetch('{{ route("profile.update-password") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            }
        })
        .then(async response => {
            const responseText = await response.text();
            let data;
            try {
                data = JSON.parse(responseText);
            } catch (e) {
                throw new Error(`Server returned non-JSON response: ${responseText}`);
            }

            // Handle validation errors (422) differently from other errors
            if (response.status === 422) {
                if (data.errors) {
                    let errorMessages = [];
                    for (let field in data.errors) {
                        errorMessages.push(...data.errors[field]);
                    }
                    showAlert('Error validasi: ' + errorMessages.join(', '), 'danger');
                    return null; // Don't throw, just return null to skip the success handler
                } else {
                    throw new Error(data.message || 'Validasi gagal');
                }
            } else if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${data.message || 'Unknown error'}`);
            }

            return data;
        })
        .then(data => {
            // Only process success if data is not null (validation didn't fail)
            if (data && data.success) {
                showAlert('Password berhasil diperbarui!', 'success');
                // Clear password fields
                document.getElementById('password').value = '';
                document.getElementById('password_confirmation').value = '';
                document.getElementById('password-match-check').classList.add('d-none');
                updatePasswordButton();
            } else if (data) {
                // Handle other error cases
                showAlert('Terjadi kesalahan: ' + (data.message || 'Unknown error'), 'danger');
            }
        })
        .catch(error => {
            console.error('Password update error:', error);
            showAlert('Terjadi kesalahan saat menyimpan password: ' + error.message, 'danger');
        })
        .finally(() => {
            submitBtn.innerHTML = originalHtml;
        });
    });
</script>
@endsection
