@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card card-modern border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h3 text-high-contrast fw-bold mb-2">
                                <i class="fas fa-user-cog text-primary me-2"></i>Profil Pengguna & Pengaturan Akun
                            </h1>
                            <p class="text-subtle mb-0">Kelola informasi pribadi dan pengaturan keamanan akun Anda</p>
                        </div>
                        @if(auth()->user()->role === 'siswa')
                        <div>
                            <button type="button" class="btn btn-success btn-lg" data-bs-toggle="modal" data-bs-target="#kartuIdentitasModal">
                                <i class="fas fa-id-card me-2"></i>Download Kartu Identitas
                            </button>
                        </div>
                        @endif
                    </div>
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
                             src="{{ auth()->user()->getProfilePhotoUrl() }}"
                             alt="Profile Photo"
                             class="rounded-circle border border-3 border-light shadow-sm"
                             style="width: 120px; height: 120px; object-fit: cover;">
                        <button type="button" id="change-photo-btn"
                                class="btn btn-primary btn-sm rounded-circle position-absolute bottom-0 end-0 shadow"
                                style="width: 36px; height: 36px; right: 10px;"
                                onclick="document.getElementById('profile-photo-input').click()">
                            <i class="fas fa-camera"></i>
                        </button>
                    </div>
                    <input type="file" id="profile-photo-input" accept="image/*" class="d-none" onchange="previewPhotoQuick(this)">
                    <p class="text-subtle small mb-0">Klik ikon kamera untuk mengubah foto</p>
                    <small class="text-muted">Format: JPG, PNG, WebP (Maks. 2MB)<br>
                    <strong>Catatan:</strong> Foto akan langsung tersimpan setelah dipilih</small>
                </div>
            </div>

            @php
                $profile = auth()->user()->getProfile();
                $user = auth()->user();
            @endphp

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
                            @if($user->role === 'siswa')
                                <!-- Data yang TIDAK BISA DIEDIT untuk Siswa -->
                                <div class="col-12">
                                    <h6 class="text-muted mb-3"><i class="fas fa-lock me-2"></i>Informasi Tetap</h6>
                                </div>

                                <!-- NISN (Read-only) -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-medium text-high-contrast">
                                            NISN (Nomor Induk Siswa Nasional)
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-id-card text-muted"></i>
                                            </span>
                                            <input type="text"
                                                   class="form-control bg-light"
                                                   value="{{ $profile->nisn ?? 'Belum diisi' }}"
                                                   readonly>
                                        </div>
                                        <small class="text-muted">Data ini tidak dapat diubah</small>
                                    </div>
                                </div>

                                <!-- NIS (Read-only) -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-medium text-high-contrast">
                                            NIS (Nomor Induk Siswa)
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-id-badge text-muted"></i>
                                            </span>
                                            <input type="text"
                                                   class="form-control bg-light"
                                                   value="{{ $profile->nis ?? 'Belum diisi' }}"
                                                   readonly>
                                        </div>
                                        <small class="text-muted">Data ini tidak dapat diubah</small>
                                    </div>
                                </div>

                                <!-- Email (Read-only) -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-medium text-high-contrast">
                                            Email
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-envelope text-muted"></i>
                                            </span>
                                            <input type="text"
                                                   class="form-control bg-light"
                                                   value="{{ $user->email }}"
                                                   readonly>
                                        </div>
                                        <small class="text-muted">Data ini tidak dapat diubah</small>
                                    </div>
                                </div>

                                <!-- Kelas (Read-only) -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-medium text-high-contrast">
                                            Kelas
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-school text-muted"></i>
                                            </span>
                                            <input type="text"
                                                   class="form-control bg-light"
                                                   value="{{ $profile->kelas ?? 'Belum diisi' }}"
                                                   readonly>
                                        </div>
                                        <small class="text-muted">Data ini tidak dapat diubah</small>
                                    </div>
                                </div>

                                <!-- Jenis Kelamin (Now Editable for Students) -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="jenis_kelamin" class="form-label fw-medium text-high-contrast">
                                            Jenis Kelamin
                                        </label>
                                        <select id="jenis_kelamin" name="jenis_kelamin" class="form-select">
                                            <option value="">Pilih Jenis Kelamin</option>
                                            <option value="L" {{ old('jenis_kelamin', $profile->jenis_kelamin ?? '') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                            <option value="P" {{ old('jenis_kelamin', $profile->jenis_kelamin ?? '') == 'P' ? 'selected' : '' }}>Perempuan</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Data yang BISA DIEDIT untuk Siswa -->
                                <div class="col-12 mt-4">
                                    <h6 class="text-primary mb-3"><i class="fas fa-edit me-2"></i>Informasi yang Dapat Diubah</h6>
                                </div>

                                <!-- Nama Lengkap -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label fw-medium text-high-contrast">
                                            Nama Lengkap <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-user text-muted"></i>
                                            </span>
                                            <input type="text"
                                                   id="name"
                                                   name="name"
                                                   class="form-control"
                                                   placeholder="Masukkan nama lengkap"
                                                   value="{{ old('name', $user->name) }}"
                                                   required>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tempat Lahir -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="tempat_lahir" class="form-label fw-medium text-high-contrast">
                                            Tempat Lahir
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-map-marker-alt text-muted"></i>
                                            </span>
                                            <input type="text"
                                                   id="tempat_lahir"
                                                   name="tempat_lahir"
                                                   class="form-control"
                                                   placeholder="Masukkan tempat lahir"
                                                   value="{{ old('tempat_lahir', $profile->tempat_lahir ?? '') }}">
                                        </div>
                                    </div>
                                </div>

                                <!-- Tanggal Lahir -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="tanggal_lahir" class="form-label fw-medium text-high-contrast">
                                            Tanggal Lahir
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-birthday-cake text-muted"></i>
                                            </span>
                                            <input type="date"
                                                   id="tanggal_lahir"
                                                   name="tanggal_lahir"
                                                   class="form-control"
                                                   value="{{ old('tanggal_lahir', $profile->tanggal_lahir ? $profile->tanggal_lahir->format('Y-m-d') : '') }}">
                                        </div>
                                    </div>
                                </div>

                                <!-- Nomor Telepon Orang Tua -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="nomor_telepon_orangtua" class="form-label fw-medium text-high-contrast">
                                            Nomor Telepon Orang Tua
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-phone-alt text-muted"></i>
                                            </span>
                                            <input type="tel"
                                                   id="nomor_telepon_orangtua"
                                                   name="nomor_telepon_orangtua"
                                                   class="form-control"
                                                   placeholder="Contoh: 081234567890"
                                                   value="{{ old('nomor_telepon_orangtua', $profile->nomor_telepon_orangtua ?? '') }}">
                                        </div>
                                    </div>
                                </div>

                            @else
                                <!-- Untuk role guru, kepala_sekolah, admin -->
                                <div class="col-12">
                                    <h6 class="text-primary mb-3"><i class="fas fa-edit me-2"></i>Informasi Pribadi</h6>
                                </div>

                                <!-- Nama Lengkap -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label fw-medium text-high-contrast">
                                            Nama Lengkap <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-user text-muted"></i>
                                            </span>
                                            <input type="text"
                                                   id="name"
                                                   name="name"
                                                   class="form-control"
                                                   placeholder="Masukkan nama lengkap"
                                                   value="{{ old('name', $user->name) }}"
                                                   required>
                                        </div>
                                    </div>
                                </div>

                                <!-- Email -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label fw-medium text-high-contrast">
                                            Email <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-envelope text-muted"></i>
                                            </span>
                                            <input type="email"
                                                   id="email"
                                                   name="email"
                                                   class="form-control"
                                                   placeholder="Masukkan email"
                                                   value="{{ old('email', $user->email) }}"
                                                   required>
                                        </div>
                                    </div>
                                </div>

                                <!-- Nomor Induk/NIP -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="nomor_induk" class="form-label fw-medium text-high-contrast">
                                            {{ $user->getNomorIndukLabel() }}
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-id-badge text-muted"></i>
                                            </span>
                                            <input type="text"
                                                   id="nomor_induk"
                                                   name="nomor_induk"
                                                   class="form-control"
                                                   placeholder="Masukkan {{ strtolower($user->getNomorIndukLabel()) }}"
                                                   value="{{ old('nomor_induk', $user->nomor_induk) }}">
                                        </div>
                                    </div>
                                </div>

                                <!-- Nomor Telepon -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="nomor_telepon" class="form-label fw-medium text-high-contrast">
                                            Nomor Telepon
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-phone text-muted"></i>
                                            </span>
                                            <input type="tel"
                                                   id="nomor_telepon"
                                                   name="nomor_telepon"
                                                   class="form-control"
                                                   placeholder="Contoh: 081234567890"
                                                   value="{{ old('nomor_telepon', $user->nomor_telepon) }}">
                                        </div>
                                    </div>
                                </div>

                                <!-- Jenis Kelamin -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="jenis_kelamin" class="form-label fw-medium text-high-contrast">
                                            Jenis Kelamin
                                        </label>
                                        <select id="jenis_kelamin" name="jenis_kelamin" class="form-select">
                                            <option value="">Pilih Jenis Kelamin</option>
                                            <option value="L" {{ old('jenis_kelamin', $user->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                            <option value="P" {{ old('jenis_kelamin', $user->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Tempat Lahir -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="tempat_lahir" class="form-label fw-medium text-high-contrast">
                                            Tempat Lahir
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-map-marker-alt text-muted"></i>
                                            </span>
                                            <input type="text"
                                                   id="tempat_lahir"
                                                   name="tempat_lahir"
                                                   class="form-control"
                                                   placeholder="Masukkan tempat lahir"
                                                   value="{{ old('tempat_lahir', $user->tempat_lahir) }}">
                                        </div>
                                    </div>
                                </div>

                                <!-- Tanggal Lahir -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="tanggal_lahir" class="form-label fw-medium text-high-contrast">
                                            Tanggal Lahir
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-birthday-cake text-muted"></i>
                                            </span>
                                            <input type="date"
                                                   id="tanggal_lahir"
                                                   name="tanggal_lahir"
                                                   class="form-control"
                                                   value="{{ old('tanggal_lahir', $user->tanggal_lahir) }}">
                                        </div>
                                    </div>
                                </div>

                                @if($user->role === 'guru' || $user->role === 'kepala_sekolah')
                                    <!-- Status Kepegawaian -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="status_kepegawaian" class="form-label fw-medium text-high-contrast">
                                                Status Kepegawaian
                                            </label>
                                            <select id="status_kepegawaian" name="status_kepegawaian" class="form-select">
                                                <option value="">Pilih Status Kepegawaian</option>
                                                <option value="PNS" {{ old('status_kepegawaian', $user->status_kepegawaian) == 'PNS' ? 'selected' : '' }}>PNS</option>
                                                <option value="Honor" {{ old('status_kepegawaian', $user->status_kepegawaian) == 'Honor' ? 'selected' : '' }}>Honor</option>
                                                <option value="Kontrak" {{ old('status_kepegawaian', $user->status_kepegawaian) == 'Kontrak' ? 'selected' : '' }}>Kontrak</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Golongan -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="golongan" class="form-label fw-medium text-high-contrast">
                                                Golongan
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="fas fa-award text-muted"></i>
                                                </span>
                                                <input type="text"
                                                       id="golongan"
                                                       name="golongan"
                                                       class="form-control"
                                                       placeholder="Contoh: III/a"
                                                       value="{{ old('golongan', $user->golongan) }}">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Mata Pelajaran -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="mata_pelajaran" class="form-label fw-medium text-high-contrast">
                                                Mata Pelajaran
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="fas fa-book text-muted"></i>
                                                </span>
                                                <input type="text"
                                                       id="mata_pelajaran"
                                                       name="mata_pelajaran"
                                                       class="form-control"
                                                       placeholder="Contoh: Matematika"
                                                       value="{{ old('mata_pelajaran', $user->mata_pelajaran) }}">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Wali Kelas -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="wali_kelas" class="form-label fw-medium text-high-contrast">
                                                Wali Kelas
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="fas fa-chalkboard-teacher text-muted"></i>
                                                </span>
                                                <input type="text"
                                                       id="wali_kelas"
                                                       name="wali_kelas"
                                                       class="form-control"
                                                       placeholder="Contoh: 7-A"
                                                       value="{{ old('wali_kelas', $user->wali_kelas) }}">
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Foto Profil untuk Guru/Admin -->
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="profile_photo" class="form-label fw-medium text-high-contrast">
                                            Ganti Foto Profil
                                        </label>
                                        <input type="file"
                                               id="profile_photo"
                                               name="profile_photo"
                                               class="form-control"
                                               accept="image/*"
                                               onchange="previewPhotoInForm(this)">
                                        <small class="text-muted">Format: JPG, PNG, WebP (Maks. 2MB). Kosongkan jika tidak ingin mengubah foto.</small>
                                        <div id="photo-preview-container" class="mt-2 d-none">
                                            <img id="photo-preview" src="" alt="Preview" class="rounded" style="max-width: 150px; max-height: 150px; object-fit: cover;">
                                        </div>
                                    </div>
                                </div>
                            @endif
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
                                        Password Baru <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-key text-muted"></i>
                                        </span>
                                        <input type="password"
                                               id="password"
                                               name="password"
                                               class="form-control"
                                               placeholder="Masukkan password baru"
                                               oninput="checkPasswordStrength(this.value)">
                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password')">
                                            <i class="fas fa-eye" id="password-toggle-icon"></i>
                                        </button>
                                    </div>
                                    <div id="length-check" class="small text-muted mt-1">
                                        <i class="fas fa-times me-1"></i>Minimal 8 karakter
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password_confirmation" class="form-label fw-medium text-high-contrast">
                                        Konfirmasi Password <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-key text-muted"></i>
                                        </span>
                                        <input type="password"
                                               id="password_confirmation"
                                               name="password_confirmation"
                                               class="form-control"
                                               placeholder="Konfirmasi password baru"
                                               oninput="checkPasswordMatch()">
                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_confirmation')">
                                            <i class="fas fa-eye" id="password_confirmation-toggle-icon"></i>
                                        </button>
                                    </div>
                                    <div id="password-match-check" class="small d-none mt-1"></div>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" id="save-password-btn" class="btn btn-warning btn-lg" disabled>
                                <i class="fas fa-shield-alt me-2"></i>Perbarui Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Kartu Identitas -->
@if(auth()->user()->role === 'siswa')
<div class="modal fade" id="kartuIdentitasModal" tabindex="-1" aria-labelledby="kartuIdentitasModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="kartuIdentitasModalLabel">
                    <i class="fas fa-id-card me-2"></i>Kartu Identitas Siswa
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-3">
                    <button type="button" class="btn btn-primary btn-lg shadow-lg" onclick="downloadKartuIdentitas()" style="background: linear-gradient(45deg, #007bff, #0056b3); border: none; padding: 12px 24px;">
                        <i class="fas fa-download me-2"></i>Download Kartu Identitas (PNG)
                    </button>
                    <div class="mt-2">
                        <small class="text-muted">Resolusi: 1205 x 768 pixels | Format: PNG</small>
                    </div>
                </div>

                <!-- Kartu Identitas Canvas -->
                <div class="d-flex justify-content-center">
                    <div id="kartu-identitas-container" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); width: 603px; height: 384px; position: relative; border-radius: 15px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.3);">
                        <!-- Background Pattern -->
                        <div style="position: absolute; top: 0; right: 0; width: 300px; height: 300px; background: linear-gradient(45deg, rgba(255,255,255,0.1) 0%, rgba(255,165,0,0.1) 100%); border-radius: 50%; transform: translate(50%, -50%);"></div>
                        <div style="position: absolute; bottom: 0; left: 0; width: 200px; height: 200px; background: linear-gradient(225deg, rgba(255,255,255,0.05) 0%, rgba(255,165,0,0.05) 100%); border-radius: 50%; transform: translate(-50%, 50%);"></div>

                        <!-- Header -->
                        <div class="text-center pt-3 px-4" style="position: relative; z-index: 2;">
                            <div class="d-flex align-items-center justify-content-center mb-2">
                                <!-- Logo Sekolah -->
                                <div style="width: 60px; height: 60px; background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.2);">
                                    <!-- Garuda/Emblem Indonesia style -->
                                    <img src="{{ asset('assets/image/LogoSMP3SAWAN.webp') }}" alt="Logo SMPN 3 Sawan" style="width:100%; height:100%; object-fit:cover; border-radius:50%;">
                                </div>
                                <div class="text-white">
                                    <h6 class="mb-0 fw-bold" style="font-size: 14px; letter-spacing: 1px;">KARTU TANDA SISWA</h6>
                                    <h5 class="mb-0 fw-bold" style="font-size: 18px; text-shadow: 2px 2px 4px rgba(0,0,0,0.3);">SMP NEGERI 3 SAWAN</h5>
                                    <p class="mb-0" style="font-size: 12px; opacity: 0.9; font-style: italic;">Student Identity Card</p>
                                </div>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="row g-0" style="position: relative; z-index: 2; padding: 20px 20px 0;">
                            <!-- Photo -->
                            <div class="col-3">
                                <div class="text-center">
                                    <div style="width: 120px; height: 150px; background: white; border-radius: 12px; overflow: hidden; margin: 0 auto; border: 4px solid rgba(255,255,255,0.4); box-shadow: 0 8px 20px rgba(0,0,0,0.3);">
                                        <img id="kartu-foto"
                                             src="{{ auth()->user()->getProfilePhotoUrl() }}"
                                             alt="Foto Siswa"
                                             style="width: 100%; height: 100%; object-fit: cover;">
                                    </div>
                                </div>
                            </div>

                            <!-- Data -->
                            <div class="col-8">
                                <div class="ps-3">
                                    <div class="text-white" style="text-shadow: 1px 1px 2px rgba(0,0,0,0.5);">
                                        <h4 class="fw-bold mb-3" style="font-size: 22px; text-transform: uppercase; letter-spacing: 0.5px;" id="kartu-nama">{{ auth()->user()->name }}</h4>
                                        <!-- TTL (stacked) -->
                                        <div class="mt-1">
                                            <div style="background: rgba(255,255,255,0.0); padding: 5px 6px; border-radius: 8px; border-left: 2px solid #4ecdc4;">
                                                <span style="font-size: 11px; opacity: 0.9; letter-spacing: 1px;">Tempat, Tanggal Lahir</span>
                                                <div style="font-size: 15px; font-weight: 600; line-height: 1.3;" id="kartu-ttl">
                                                    @php
                                                        $profile = auth()->user()->getProfile();
                                                        $ttl = '';
                                                        if ($profile) {
                                                            if ($profile->tempat_lahir) {
                                                                $ttl .= $profile->tempat_lahir;
                                                            }
                                                            if ($profile->tanggal_lahir) {
                                                                $ttl .= ($ttl ? ', ' : '') . $profile->tanggal_lahir->format('d/m/Y');
                                                            }
                                                        }
                                                    @endphp
                                                    {{ $ttl ?: '-' }}
                                                </div>
                                            </div>
                                        </div>

                                        <!-- NISN (stacked) -->
                                        <div class="mt-1">
                                            <div style="background: rgba(255,255,255,0.0); padding: 5px; border-radius: 8px; border-left: 2px solid #ffd93d;">
                                                <span style="font-size: 11px; opacity: 0.9; text-transform: uppercase; letter-spacing: 1px;">NISN</span>
                                                <div style="font-size: 15px; font-weight: 600;" id="kartu-nisn">{{ auth()->user()->getProfile()->nisn ?? '-' }}</div>
                                            </div>
                                        </div>

                                        <!-- NIS (stacked) -->
                                        <div class="mt-1">
                                            <div style="background: rgba(255,255,255,0.0); padding: 5px; border-radius: 8px; border-left: 2px solid #4ecdc4;">
                                                <span style="font-size: 11px; opacity: 0.9; text-transform: uppercase; letter-spacing: 1px;">NIS</span>
                                                <div style="font-size: 15px; font-weight: 600;" id="kartu-nis">{{ auth()->user()->getProfile()->nis ?? '-' }}</div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- QR Code -->
                        <div style="position: absolute; bottom: 15px; right: 15px; background: white; padding: 10px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.2);">
                            <div id="qrcode" style="width: 180px; height: 180px;"></div>
                            <div style="text-align: center; font-size: 8px; color: #666; margin-top: 4px; font-weight: 500;">
                                SCAN ME
                            </div>
                        </div>

                        <!-- Footer -->
                        <div style="position: absolute; bottom: 8px; left: 20px;">
                            <div style="color: white; font-size: 8px; opacity: 0.7;">
                                SMP Negeri 3 Sawan - Suwug, Kec. Sawan, Kabupaten Buleleng, Bali 81171
                            </div>
                            <div style="color: white; font-size: 8px; opacity: 0.7; margin-top: 2px;">
                                Generated: {{ date('d/m/Y H:i') }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Hidden canvas for image generation -->
                <canvas id="kartu-canvas" width="1205" height="768" style="display: none;"></canvas>
            </div>
        </div>
    </div>
</div>
@endif

<script>
        // Generate QR Code when modal is shown
        document.getElementById('kartuIdentitasModal')?.addEventListener('shown.bs.modal', function () {
            generateQRCode();
        });

        function generateQRCode() {
            const nisn = document.getElementById('kartu-nisn').textContent.trim();
            const qrContainer = document.getElementById('qrcode');

            // Clear previous QR code
            qrContainer.innerHTML = '';

            if (nisn && nisn !== '-' && nisn !== '') {
                // Create a canvas element for QR code
                const qrCanvas = document.createElement('canvas');
                qrContainer.appendChild(qrCanvas);

                // Generate QR Code with better error correction
                QRCode.toCanvas(qrCanvas, nisn, {
                    width: 80,
                    height: 80,
                    colorDark: '#000000',
                    colorLight: '#ffffff',
                    margin: 1,
                    errorCorrectionLevel: 'M'
                }, function (error) {
                    if (error) {
                        console.error('QR Code generation error:', error);
                        // Show fallback text if QR generation fails
                        qrContainer.innerHTML = `
                            <div style="width: 80px; height: 80px; background: #f0f0f0; display: flex; align-items: center; justify-content: center; font-size: 10px; text-align: center; color: #666;">
                                QR Error
                            </div>
                        `;
                    }
                });
            } else {
                // Show placeholder if no NISN
                qrContainer.innerHTML = `
                    <div style="width: 80px; height: 80px; background: #f0f0f0; display: flex; align-items: center; justify-content: center; font-size: 10px; text-align: center; color: #666;">
                        No NISN<br>Available
                    </div>
                `;
            }
        }

        async function downloadKartuIdentitas() {
            try {
                // Show loading
                const downloadBtn = document.querySelector('button[onclick="downloadKartuIdentitas()"]');
                const originalText = downloadBtn.innerHTML;
                downloadBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Generating Image...';
                downloadBtn.disabled = true;

                // Wait a bit for QR code to fully render
                await new Promise(resolve => setTimeout(resolve, 500));

                // Get kartu container
                const kartuElement = document.getElementById('kartu-identitas-container');

                // Create high resolution canvas with better quality settings
                const canvas = await html2canvas(kartuElement, {
                    scale: 2, // 2x resolution for crisp quality
                    useCORS: true,
                    allowTaint: true,
                    backgroundColor: null,
                    width: 603,
                    height: 384,
                    scrollX: 0,
                    scrollY: 0,
                    logging: false,
                    imageTimeout: 0,
                    removeContainer: false,
                    foreignObjectRendering: false
                });

                // Create final canvas with exact target dimensions
                const finalCanvas = document.createElement('canvas');
                finalCanvas.width = 1205;
                finalCanvas.height = 768;
                const ctx = finalCanvas.getContext('2d');

                // Set high quality image smoothing
                ctx.imageSmoothingEnabled = true;
                ctx.imageSmoothingQuality = 'high';

                // Draw the captured image to final canvas with exact dimensions
                ctx.drawImage(canvas, 0, 0, 1205, 768);

                // Get student name for filename
                const studentName = document.getElementById('kartu-nama').textContent
                    .replace(/\s+/g, '-')
                    .toLowerCase()
                    .replace(/[^a-z0-9\-]/g, '');

                // Convert to blob and download with high quality
                finalCanvas.toBlob(function(blob) {
                    const url = URL.createObjectURL(blob);
                    const link = document.createElement('a');
                    link.download = `kartu-identitas-${studentName}-${new Date().toISOString().slice(0,10)}.png`;
                    link.href = url;
                    link.click();
                    URL.revokeObjectURL(url);

                    // Restore button
                    downloadBtn.innerHTML = originalText;
                    downloadBtn.disabled = false;

                    showAlert('Kartu identitas berhasil didownload!', 'success');
                }, 'image/png', 1.0); // Maximum quality

            } catch (error) {
                console.error('Download error:', error);
                showAlert('Terjadi kesalahan saat mendownload kartu identitas: ' + error.message, 'danger');

                // Restore button
                const downloadBtn = document.querySelector('button[onclick="downloadKartuIdentitas()"]');
                downloadBtn.innerHTML = '<i class="fas fa-download me-2"></i>Download Kartu Identitas (PNG)';
                downloadBtn.disabled = false;
            }
        }

        function previewPhotoQuick(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('profile-photo-preview').src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);

                // Auto-upload the photo when selected
                uploadProfilePhoto(input.files[0]);
            }
        }

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

        function previewPhotoInForm(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                const previewContainer = document.getElementById('photo-preview-container');
                const previewImg = document.getElementById('photo-preview');

                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    previewContainer.classList.remove('d-none');
                }
                reader.readAsDataURL(input.files[0]);
            } else {
                document.getElementById('photo-preview-container').classList.add('d-none');
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

            // Submit form
            fetch('{{ route("profile.update") }}', {
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
                        const errorMessages = Object.values(data.errors).flat();
                        showAlert('Validasi gagal: ' + errorMessages.join(', '), 'danger');
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
                        const errorMessages = Object.values(data.errors).flat();
                        showAlert('Validasi gagal: ' + errorMessages.join(', '), 'danger');
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
                submitBtn.disabled = false;
            });
        });
</script>
@endsection
