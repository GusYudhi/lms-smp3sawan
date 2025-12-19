@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card card-modern border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
                        <div class="flex-grow-1">
                            <h1 class="h3 text-high-contrast fw-bold mb-2">
                                <i class="fas fa-user-cog text-primary me-2"></i>Profil Pengguna & Pengaturan Akun
                            </h1>
                            <p class="text-subtle mb-0">Kelola informasi pribadi dan pengaturan keamanan akun Anda</p>
                        </div>
                        @if(auth()->user()->role === 'siswa')
                        <div class="text-lg-end">
                            <button type="button" class="btn btn-success btn-lg w-100 w-lg-auto d-lg-inline-block" data-bs-toggle="modal" data-bs-target="#kartuIdentitasModal">
                                <i class="fas fa-id-card me-2"></i>
                                <span class="d-inline d-sm-none">Download Kartu</span>
                                <span class="d-none d-sm-inline">Download Kartu Identitas</span>
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
                    <small class="text-muted">Format: JPG, PNG, WebP, HEIC (Maks. 5MB)<br>
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
                                                   value="{{ $profile->kelas->full_name ?? 'Belum diisi' }}"
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
                                        <label for="nip" class="form-label fw-medium text-high-contrast">
                                            {{ $user->getNomorIndukLabel() }}
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-id-badge text-muted"></i>
                                            </span>
                                            <input type="text"
                                                   id="nip"
                                                   name="nip"
                                                   class="form-control bg-light"
                                                   placeholder="Masukkan {{ strtolower($user->getNomorIndukLabel()) }}"
                                                   value="{{ old('nip', $profile->nip ?? '') }}"
                                                   readonly>
                                        </div>
                                        <small class="text-muted">Data ini tidak dapat diubah</small>
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
                                                   value="{{ old('nomor_telepon', $profile->nomor_telepon ?? '') }}">
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
                                            <option value="L" {{ old('jenis_kelamin', $profile->jenis_kelamin ?? '') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                            <option value="P" {{ old('jenis_kelamin', $profile->jenis_kelamin ?? '') == 'P' ? 'selected' : '' }}>Perempuan</option>
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
                                                   value="{{ old('tanggal_lahir', $profile && $profile->tanggal_lahir ? $profile->tanggal_lahir->format('Y-m-d') : '') }}">
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
                                                <option value="PNS" {{ old('status_kepegawaian', $profile->status_kepegawaian ?? '') == 'PNS' ? 'selected' : '' }}>PNS</option>
                                                <option value="PPPK" {{ old('status_kepegawaian', $profile->status_kepegawaian ?? '') == 'PPPK' ? 'selected' : '' }}>PPPK</option>
                                                <option value="HONORER" {{ old('status_kepegawaian', $profile->status_kepegawaian ?? '') == 'HONORER' ? 'selected' : '' }}>HONORER</option>
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
                                                <select class="form-select" id="golongan" name="golongan">
                                                    <option value="">Pilih Golongan</option>
                                                    @php
                                                        $golongans = ['I/a', 'I/b', 'I/c', 'I/d', 'II/a', 'II/b', 'II/c', 'II/d', 'III/a', 'III/b', 'III/c', 'III/d', 'IV/a', 'IV/b', 'IV/c', 'IV/d', 'IV/e'];
                                                        $currentGolongan = old('golongan', $profile->golongan ?? '');
                                                    @endphp
                                                    @foreach($golongans as $gol)
                                                        <option value="{{ $gol }}" {{ $currentGolongan == $gol ? 'selected' : '' }}>{{ $gol }}</option>
                                                    @endforeach
                                                </select>
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
                                                <select id="mata_pelajaran"
                                                       name="mata_pelajaran"
                                                       class="form-select">
                                                    <option value="">Tidak Ada</option>
                                                    @foreach($mataPelajarans as $mapel)
                                                        <option value="{{ $mapel->nama_mapel }}"
                                                            {{ (old('mata_pelajaran') == $mapel->nama_mapel || (isset($profile->mata_pelajaran) && (is_array($profile->mata_pelajaran) ? in_array($mapel->nama_mapel, $profile->mata_pelajaran) : $profile->mata_pelajaran == $mapel->nama_mapel))) ? 'selected' : '' }}>
                                                            {{ $mapel->nama_mapel }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Wali Kelas -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="kelas_id" class="form-label fw-medium text-high-contrast">
                                                Wali Kelas
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="fas fa-chalkboard-teacher text-muted"></i>
                                                </span>
                                                <select id="kelas_id" name="kelas_id" class="form-select">
                                                    <option value="">Tidak menjadi wali kelas</option>
                                                    @if(isset($kelasList))
                                                        @foreach($kelasList as $kelas)
                                                            <option value="{{ $kelas->id }}"
                                                                {{ old('kelas_id', $profile->kelas_id ?? '') == $kelas->id ? 'selected' : '' }}>
                                                                {{ $kelas->full_name }}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                            <small class="text-muted">Pilih kelas jika Anda menjadi wali kelas</small>
                                        </div>
                                    </div>
                                @endif
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
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="kartuIdentitasModalLabel">
                    <i class="fas fa-id-card me-2"></i>Kartu Identitas Murid
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
                    <div style="width:100%; max-width:1205px;">
                        <!-- Keep canvas internal resolution for high-quality download but scale it responsively via CSS -->
                        <canvas id="kartu-identitas-canvas" width="1205" height="768" style="width:100%; height:auto; display:block; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.3);"></canvas>
                    </div>
                </div>

                <!-- Hidden elements for data retrieval -->
                <div style="display: none;">
                    @php
                        $profile = auth()->user()->getProfile();
                        $ttl = '';
                        if ($profile) {
                            $tempat = $profile->tempat_lahir ?? '-';
                            $tanggal = $profile->tanggal_lahir ? $profile->tanggal_lahir->format('d/m/Y') : '-';
                            $ttl = $tempat . ', ' . $tanggal;
                        } else {
                            $ttl = '-, -';
                        }
                    @endphp
                    <span id="kartu-nama">{{ auth()->user()->name }}</span>
                    <span id="kartu-ttl">{{ $ttl }}</span>
                    <span id="kartu-nisn">{{ $profile->nisn ?? '-' }}</span>
                    <span id="kartu-nis">{{ $profile->nis ?? '-' }}</span>
                    <span id="kartu-foto-url">{{ auth()->user()->getProfilePhotoUrl() }}</span>
                    <span id="kartu-logo-url">{{ asset('assets/image/logo-sekolah-smpn3sawan.webp') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<script>
        // Generate QR Code when modal is shown
        document.getElementById('kartuIdentitasModal')?.addEventListener('shown.bs.modal', function () {
            // Draw the card immediately when modal is shown
            drawKartuIdentitas();
        });

        function drawKartuIdentitas() {
            const canvas = document.getElementById('kartu-identitas-canvas');
            const ctx = canvas.getContext('2d');

            // Get data from hidden elements
            const nama = document.getElementById('kartu-nama').textContent.trim();
            const ttl = document.getElementById('kartu-ttl').textContent.trim();
            const nisn = document.getElementById('kartu-nisn').textContent.trim();
            const nis = document.getElementById('kartu-nis').textContent.trim();
            const fotoUrl = document.getElementById('kartu-foto-url').textContent.trim();
            const logoUrl = document.getElementById('kartu-logo-url').textContent.trim();

            // Clear canvas
            ctx.clearRect(0, 0, canvas.width, canvas.height);

            // Create background gradient
            const gradient = ctx.createLinearGradient(0, 0, canvas.width, canvas.height);
            gradient.addColorStop(0, '#1e3c72');
            gradient.addColorStop(1, '#2a5298');

            // Fill background
            ctx.fillStyle = gradient;
            ctx.fillRect(0, 0, canvas.width, canvas.height);

            // Add background patterns (decorative circles)
            drawBackgroundPattern(ctx, canvas.width, canvas.height);

            // Draw header section
            drawHeader(ctx, logoUrl);

            // Draw content section
            drawContent(ctx, nama, ttl, nisn, nis, fotoUrl);

            // Draw QR code
            drawQRCode(ctx, nisn);

            // Draw footer
            drawFooter(ctx);
        }

        function drawBackgroundPattern(ctx, width, height) {
            // Save current state
            ctx.save();

            // Circle 1 (top-right)
            const gradient1 = ctx.createRadialGradient(width * 0.85, height * 0.15, 0, width * 0.85, height * 0.15, 300);
            gradient1.addColorStop(0, 'rgba(255,255,255,0.1)');
            gradient1.addColorStop(0.5, 'rgba(255,165,0,0.1)');
            gradient1.addColorStop(1, 'rgba(255,255,255,0)');

            ctx.fillStyle = gradient1;
            ctx.beginPath();
            ctx.arc(width * 0.85, height * 0.15, 300, 0, 2 * Math.PI);
            ctx.fill();

            // Circle 2 (bottom-left)
            const gradient2 = ctx.createRadialGradient(width * 0.15, height * 0.85, 0, width * 0.15, height * 0.85, 200);
            gradient2.addColorStop(0, 'rgba(255,255,255,0.08)');
            gradient2.addColorStop(0.5, 'rgba(255,165,0,0.08)');
            gradient2.addColorStop(1, 'rgba(255,255,255,0)');

            ctx.fillStyle = gradient2;
            ctx.beginPath();
            ctx.arc(width * 0.15, height * 0.85, 200, 0, 2 * Math.PI);
            ctx.fill();

            // Restore state
            ctx.restore();
        }

        function drawHeader(ctx, logoUrl) {
            // Load and draw logo
            const logo = new Image();
            logo.crossOrigin = 'anonymous';
            logo.onload = function() {
                // Draw logo circle background (centered)
                const centerX = ctx.canvas.width / 2 - 350;
                const centerY = 90
                const r = 60;

                // White circle background
                ctx.save();
                ctx.fillStyle = 'white';
                ctx.beginPath();
                ctx.arc(centerX, centerY, r, 0, Math.PI * 2);
                ctx.fill();
                ctx.restore();

                // Shadow + clip to circle then draw logo centered inside the arc
                ctx.save();
                ctx.shadowColor = 'rgba(0,0,0,0.3)';
                ctx.shadowBlur = 10;
                ctx.shadowOffsetX = 2;
                ctx.shadowOffsetY = 2;

                ctx.beginPath();
                ctx.arc(centerX, centerY, r, 0, Math.PI * 2);
                ctx.closePath();
                ctx.clip();

                // Draw logo centered and slightly inset so it fits nicely
                const imgSize = r * 2 - 6; // small padding inside circle
                ctx.drawImage(logo, centerX - imgSize / 2, centerY - imgSize / 2, imgSize, imgSize);

                ctx.restore();
            };
            logo.src = logoUrl;

            const offsetY = 20;

            // Draw header text (centered)
            ctx.fillStyle = 'white';
            ctx.font = 'bold 33px Arial, sans-serif';
            ctx.textAlign = 'center';
            ctx.fillText('KARTU IDENTITAS MURID', ctx.canvas.width / 2, 55 + offsetY);

            ctx.font = 'bold 41px Arial, sans-serif';
            ctx.fillText('SMP NEGERI 3 SAWAN', ctx.canvas.width / 2, 95 + offsetY);

            ctx.font = 'italic 27px Arial, sans-serif';
            ctx.fillStyle = 'rgba(255,255,255,0.9)';
            ctx.fillText('Student Identity Card', ctx.canvas.width / 2, 125 + offsetY);
            ctx.textAlign = 'left';
        }

        function drawContent(ctx, nama, ttl, nisn, nis, fotoUrl) {
            // Load and draw student photo
            const foto = new Image();
            foto.crossOrigin = 'anonymous';
            foto.onload = function() {
                // Draw photo background with rounded corners and blue border
                ctx.save();

                // White inner background
                ctx.fillStyle = 'white';
                roundRect(ctx, 66, 198, 228, 340, 20);
                ctx.fill();

                // Add shadow
                ctx.shadowColor = 'rgba(0,0,0,0.4)';
                ctx.shadowBlur = 20;
                ctx.shadowOffsetX = 4;
                ctx.shadowOffsetY = 4;

                // Draw photo with rounded corners
                roundRect(ctx, 75, 210, 210, 315, 15);
                ctx.clip();
                ctx.drawImage(foto, 75, 210, 210, 315);
                ctx.restore();
            };
            foto.src = fotoUrl;

            // Draw student data
            ctx.fillStyle = 'white';
            ctx.shadowColor = 'rgba(0,0,0,0.7)';
            ctx.shadowBlur = 0;
            ctx.shadowOffsetX = 0;
            ctx.shadowOffsetY = 0;

            // Student name (larger text)
            ctx.font = 'bold 51px Arial, sans-serif';
            ctx.textAlign = 'left';
            ctx.fillText(nama.toUpperCase().trim().substring(0, 24), 360, 238);

            // Data fields with larger text
            const dataY = 300;
            const lineHeight = 90;

            // TTL
            drawDataField(ctx, 'Tempat, Tanggal Lahir', ttl, 360, dataY, '#4ecdc4');

            // NISN
            drawDataField(ctx, 'NISN', nisn, 360, dataY + lineHeight, '#ffd93d');

            // NIS
            drawDataField(ctx, 'NIS', nis, 360, dataY + (lineHeight * 2), '#4ecdc4');
        }

        function drawDataField(ctx, label, value, x, y, borderColor) {
            // Draw border line
            ctx.fillStyle = borderColor;
            ctx.fillRect(x, y - 25, 6, 50);

            // Draw label (larger text)
            ctx.font = '23px Arial, sans-serif';
            ctx.fillStyle = 'rgba(255,255,255,0.8)';
            ctx.fillText(label, x + 20, y - 5);

            // Draw value (larger text)
            ctx.font = 'bold 31px Arial, sans-serif';
            ctx.fillStyle = 'white';
            ctx.fillText(value, x + 20, y + 30);
        }

        function drawQRCode(ctx, nisn) {
            if (typeof QRCode === 'undefined') {
                // Draw placeholder if QRCode library not available
                ctx.fillStyle = 'white';
                roundRect(ctx, 750, 280, 300, 320, 24);
                ctx.fill();

                ctx.fillStyle = '#666';
                ctx.font = '25px Arial, sans-serif';
                ctx.textAlign = 'center';
                ctx.fillText('QR Code', 900, 430);
                ctx.fillText('Loading...', 900, 460);
                return;
            }

            // Create QR code canvas
            const qrCanvas = document.createElement('canvas');
            QRCode.toCanvas(qrCanvas, nisn, {
                width: 250,
                height: 250,
                margin: 1,
                color: {
                    dark: '#000000',
                    light: '#FFFFFF'
                },
                errorCorrectionLevel: 'M'
            })
            .then(() => {
                // Draw QR code background (larger)
                ctx.fillStyle = 'white';
                roundRect(ctx, 750, 280, 372, 400, 24);
                ctx.fill();

                ctx.shadowBlur = 0;
                ctx.shadowOffsetX = 0;
                ctx.shadowOffsetY = 0;

                // Draw QR code (much larger size)
                ctx.drawImage(qrCanvas, 775, 305, 320, 320);

                // Draw "SCAN ME" text
                ctx.fillStyle = '#666';
                ctx.font = 'bold 21px Arial, sans-serif';
                ctx.textAlign = 'center';
                ctx.fillText('SCAN ME', 950, 655);
            })
            .catch((error) => {
                console.error('QR Code generation failed:', error);
                // Draw error placeholder
                ctx.fillStyle = 'white';
                roundRect(ctx, 750, 280, 300, 320, 24);
                ctx.fill();

                ctx.fillStyle = '#c62828';
                ctx.font = '21px Arial, sans-serif';
                ctx.textAlign = 'center';
                ctx.fillText('QR Error', 900, 430);
                ctx.fillText(error.message, 900, 460);
            });
        }

        function drawFooter(ctx) {
            ctx.fillStyle = 'rgba(255,255,255,0.7)';
            ctx.font = '17px Arial, sans-serif';
            ctx.textAlign = 'left';
            ctx.fillText('SMP Negeri 3 Sawan - Suwug, Kec. Sawan, Kabupaten Buleleng, Bali 81171', 40, 720);

            const currentDate = new Date().toLocaleString('id-ID', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
            ctx.fillText('Generated: ' + currentDate, 40, 745);
        }

        // Helper function to draw rounded rectangles
        function roundRect(ctx, x, y, width, height, radius) {
            ctx.beginPath();
            ctx.moveTo(x + radius, y);
            ctx.lineTo(x + width - radius, y);
            ctx.quadraticCurveTo(x + width, y, x + width, y + radius);
            ctx.lineTo(x + width, y + height - radius);
            ctx.quadraticCurveTo(x + width, y + height, x + width - radius, y + height);
            ctx.lineTo(x + radius, y + height);
            ctx.quadraticCurveTo(x, y + height, x, y + height - radius);
            ctx.lineTo(x, y + radius);
            ctx.quadraticCurveTo(x, y, x + radius, y);
            ctx.closePath();
        }

        function generateQRCode() {
            const nisn = document.getElementById('kartu-nisn').textContent.trim();
            const qrContainer = document.getElementById('qrcode');

            console.log('generateQRCode called with NISN:', nisn);

            // Clear previous QR code
            qrContainer.innerHTML = `
                <div style="width: 180px; height: 180px; background: #f0f0f0; display: flex; align-items: center; justify-content: center; font-size: 14px; text-align: center; color: #666;">
                    Generating<br>QR...
                </div>
            `;

            if (nisn && nisn !== '-' && nisn !== '') {
                // Check if QRCode library is available
                if (typeof QRCode === 'undefined') {
                    console.error('QRCode.js library not loaded');
                    qrContainer.innerHTML = `
                        <div style="width: 180px; height: 180px; background: #ffebee; display: flex; align-items: center; justify-content: center; font-size: 14px; text-align: center; color: #c62828;">
                            QR Library<br>Not Loaded
                        </div>
                    `;
                    return;
                }

                // Create canvas for QR code
                const canvas = document.createElement('canvas');

                // Generate QR code using qrcode.js with proper options
                QRCode.toCanvas(canvas, nisn, {
                    width: 180,
                    height: 180,
                    margin: 2,
                    color: {
                        dark: '#000000',    // Black modules
                        light: '#FFFFFF'   // White background
                    },
                    errorCorrectionLevel: 'M',  // Medium error correction
                    type: 'image/png',
                    quality: 0.92,
                    scale: 4
                })
                .then(() => {
                    console.log('QR Code generated successfully for NISN:', nisn);
                    // Clear container and add the canvas
                    qrContainer.innerHTML = '';
                    canvas.style.width = '180px';
                    canvas.style.height = '180px';
                    canvas.style.display = 'block';
                    qrContainer.appendChild(canvas);
                })
                .catch((error) => {
                    console.error('QR Code generation failed:', error);
                    qrContainer.innerHTML = `
                        <div style="width: 180px; height: 180px; background: #ffebee; display: flex; align-items: center; justify-content: center; font-size: 14px; text-align: center; color: #c62828;">
                            QR Generation<br>Failed
                        </div>
                    `;
                });
            } else {
                console.log('No NISN available for QR code generation');
                qrContainer.innerHTML = `
                    <div style="width: 180px; height: 180px; background: #f5f5f5; display: flex; align-items: center; justify-content: center; font-size: 14px; text-align: center; color: #757575;">
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

                // Wait a moment for canvas to be fully drawn
                await new Promise(resolve => setTimeout(resolve, 1000));

                // Get the canvas element
                const canvas = document.getElementById('kartu-identitas-canvas');

                // Get student name for filename
                const studentName = document.getElementById('kartu-nama').textContent
                    .replace(/\s+/g, '-')
                    .toLowerCase()
                    .replace(/[^a-z0-9\-]/g, '');

                // Convert canvas to blob and download
                canvas.toBlob(function(blob) {
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

        // Helper function to compress image for preview
        function compressImageForPreview(file, callback, maxWidth = 1200, quality = 0.6) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = new Image();
                img.onload = function() {
                    const canvas = document.createElement('canvas');
                    let width = img.width;
                    let height = img.height;

                    // Calculate new dimensions
                    if (width > maxWidth) {
                        height = Math.round((height * maxWidth) / width);
                        width = maxWidth;
                    }

                    canvas.width = width;
                    canvas.height = height;
                    const context = canvas.getContext('2d');
                    context.drawImage(img, 0, 0, width, height);

                    // Use WebP for preview if supported
                    let compressedData = canvas.toDataURL('image/webp', quality);
                    if (!compressedData.startsWith('data:image/webp')) {
                        compressedData = canvas.toDataURL('image/jpeg', quality);
                    }

                    callback(compressedData);
                };
                img.onerror = function() {
                    console.error('Failed to load image');
                    callback(null);
                };
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }

        // Helper functions for loading overlay
        function showLoadingOverlay(title, message) {
            let overlay = document.getElementById('profile-loading-overlay');
            if (!overlay) {
                overlay = document.createElement('div');
                overlay.id = 'profile-loading-overlay';
                overlay.style.cssText = 'position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.7); z-index: 9999; display: flex; align-items: center; justify-content: center;';
                overlay.innerHTML = `
                    <div style="background: white; padding: 30px; border-radius: 10px; text-align: center; max-width: 400px;">
                        <div class="spinner-border text-primary mb-3" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <h5 id="profile-loading-title">${title}</h5>
                        <p id="profile-loading-message">${message}</p>
                    </div>
                `;
                document.body.appendChild(overlay);
            } else {
                overlay.style.display = 'flex';
                document.getElementById('profile-loading-title').textContent = title;
                document.getElementById('profile-loading-message').textContent = message;
            }
        }

        function hideLoadingOverlay() {
            const overlay = document.getElementById('profile-loading-overlay');
            if (overlay) {
                overlay.style.display = 'none';
            }
        }

        // Convert HEIC to JPG via server for profile photo
        function convertHeicForProfile(file, callback) {
            showLoadingOverlay('Memproses foto HEIC...', 'Mohon tunggu, foto sedang dikonversi');

            const formData = new FormData();
            formData.append('file', file);
            formData.append('_token', '{{ csrf_token() }}');

            fetch('{{ route("convert-heic") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                hideLoadingOverlay();
                if (data.success && data.image) {
                    // Convert base64 to Blob
                    fetch(data.image)
                        .then(res => res.blob())
                        .then(blob => {
                            // Create new File object from blob
                            const convertedFile = new File([blob], file.name.replace(/\.(heic|heif)$/i, '.jpg'), {
                                type: 'image/jpeg'
                            });
                            callback(convertedFile, data.image); // Pass both file and base64 for preview
                        });
                } else {
                    showAlert(data.message || 'Gagal mengkonversi foto HEIC.', 'danger');
                }
            })
            .catch(error => {
                hideLoadingOverlay();
                console.error('Error converting HEIC:', error);
                showAlert('Terjadi kesalahan saat mengkonversi foto HEIC.', 'danger');
            });
        }

        // Helper function to compress and convert to blob for upload
        function compressImageForUpload(file, callback, maxWidth = 1200, quality = 0.6) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = new Image();
                img.onload = function() {
                    const canvas = document.createElement('canvas');
                    let width = img.width;
                    let height = img.height;

                    // Calculate new dimensions
                    if (width > maxWidth) {
                        height = Math.round((height * maxWidth) / width);
                        width = maxWidth;
                    }

                    canvas.width = width;
                    canvas.height = height;
                    const context = canvas.getContext('2d');
                    context.drawImage(img, 0, 0, width, height);

                    // Convert to blob with WebP format for best compression
                    canvas.toBlob(function(blob) {
                        if (blob) {
                            // Create a new File object from blob
                            const compressedFile = new File([blob], file.name.replace(/\.(jpg|jpeg|png|heic|heif)$/i, '.webp'), {
                                type: 'image/webp',
                                lastModified: Date.now()
                            });
                            callback(compressedFile);
                        } else {
                            // Fallback to JPEG if WebP not supported
                            canvas.toBlob(function(jpegBlob) {
                                const compressedFile = new File([jpegBlob], file.name.replace(/\.(heic|heif)$/i, '.jpg'), {
                                    type: 'image/jpeg',
                                    lastModified: Date.now()
                                });
                                callback(compressedFile);
                            }, 'image/jpeg', quality);
                        }
                    }, 'image/webp', quality);
                };
                img.onerror = function() {
                    console.error('Failed to load image for compression');
                    callback(file); // Return original if compression fails
                };
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }

        function previewPhotoQuick(input) {
            if (input.files && input.files[0]) {
                const file = input.files[0];
                const fileExtension = file.name.split('.').pop().toLowerCase();
                const isHEIC = fileExtension === 'heic' || fileExtension === 'heif';

                if (isHEIC) {
                    // Convert HEIC first, then compress and upload
                    convertHeicForProfile(file, function(convertedFile, base64Preview) {
                        // Show preview from base64
                        document.getElementById('profile-photo-preview').src = base64Preview;
                        // Compress converted file then upload
                        compressImageForUpload(convertedFile, function(compressedFile) {
                            uploadProfilePhoto(compressedFile);
                        });
                    });
                } else {
                    // Compress and upload for JPG/PNG/WebP
                    compressImageForUpload(file, function(compressedFile) {
                        // Show preview
                        compressImageForPreview(compressedFile, function(compressedData) {
                            if (compressedData) {
                                document.getElementById('profile-photo-preview').src = compressedData;
                            }
                        });
                        // Upload compressed file
                        uploadProfilePhoto(compressedFile);
                    });
                }
            }
        }

        function previewPhoto(input) {
            if (input.files && input.files[0]) {
                const file = input.files[0];
                const fileExtension = file.name.split('.').pop().toLowerCase();
                const isHEIC = fileExtension === 'heic' || fileExtension === 'heif';

                if (isHEIC) {
                    // Convert HEIC first, then compress and upload
                    convertHeicForProfile(file, function(convertedFile, base64Preview) {
                        // Show preview from base64
                        document.getElementById('profile-photo-preview').src = base64Preview;
                        // Compress converted file then upload
                        compressImageForUpload(convertedFile, function(compressedFile) {
                            uploadProfilePhoto(compressedFile);
                        });
                    });
                } else {
                    // Compress and upload for JPG/PNG/WebP
                    compressImageForUpload(file, function(compressedFile) {
                        // Show preview
                        compressImageForPreview(compressedFile, function(compressedData) {
                            if (compressedData) {
                                document.getElementById('profile-photo-preview').src = compressedData;
                            }
                        });
                        // Upload compressed file
                        uploadProfilePhoto(compressedFile);
                    });
                }
            }
        }

        function previewPhotoInForm(input) {
            if (input.files && input.files[0]) {
                const file = input.files[0];
                const previewContainer = document.getElementById('photo-preview-container');
                const previewImg = document.getElementById('photo-preview');
                const fileExtension = file.name.split('.').pop().toLowerCase();
                const isHEIC = fileExtension === 'heic' || fileExtension === 'heif';

                if (isHEIC) {
                    // Convert HEIC first, then compress and show preview
                    convertHeicForProfile(file, function(convertedFile, base64Preview) {
                        previewImg.src = base64Preview;
                        previewContainer.classList.remove('d-none');
                        // Note: The compressed file will be uploaded on form submit
                        // For now we just show the preview
                    });
                } else {
                    // Compress and show preview
                    compressImageForUpload(file, function(compressedFile) {
                        compressImageForPreview(compressedFile, function(compressedData) {
                            if (compressedData) {
                                previewImg.src = compressedData;
                                previewContainer.classList.remove('d-none');
                            }
                        });
                    });
                }
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
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp', 'image/heic', 'image/heif', 'application/octet-stream'];
            const allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'heic', 'heif'];
            const maxSize = 5242880; // maksimal 5MB

            const extension = file.name.split('.').pop().toLowerCase();

            // Check extension first (more reliable for HEIC)
            if (!allowedExtensions.includes(extension)) {
                showAlert('Format file tidak didukung. Gunakan JPG, PNG, WebP, atau HEIC.', 'danger');
                return;
            }

            if (file.size > maxSize) {
                showAlert('Ukuran file terlalu besar. Maksimal 5MB.', 'danger');
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
