@extends('layouts.app')

@section('title', 'Edit Guru')

@section('content')
<div class="guru-management">
    <!-- Page Header -->
    <div class="page-header">
        <div class="header-content">
            <h1><i class="fas fa-user-edit"></i> Edit Data Guru</h1>
            <p class="page-subtitle">Perbarui informasi data guru dan tenaga pendidik</p>
        </div>
        <div class="header-actions">
            <a href="{{ route('admin.guru.show', $teacher->id) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

<!-- Edit Form -->
<div class="form-container">
    <form action="{{ route('admin.guru.update', $teacher->id) }}" method="POST" enctype="multipart/form-data" class="teacher-form">
        @csrf
        @method('PUT')

        <!-- Current Photo Display -->
        <div class="current-photo-section">
            <h3>
                <i class="fas fa-image"></i>
                Foto Profil Saat Ini
            </h3>
            <div class="photo-preview">
                @if($teacher->profile_photo)
                    <img src="{{ asset('storage/' . $teacher->profile_photo) }}"
                         alt="Foto {{ $teacher->name }}"
                         class="current-photo"
                         id="currentPhoto">
                @else
                    <div class="no-photo" id="currentPhoto">
                        <i class="fas fa-user"></i>
                        <span>Belum ada foto</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Personal Information Section -->
        <div class="form-section">
            <h3>
                <i class="fas fa-user"></i>
                Informasi Personal
            </h3>
            <div class="form-grid">
                <div class="form-group">
                    <label for="name" class="required">Nama Lengkap</label>
                    <input type="text"
                           class="form-control @error('name') is-invalid @enderror"
                           id="name"
                           name="name"
                           value="{{ old('name', $teacher->name) }}"
                           required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="nomor_induk" class="required">Nomor Induk Guru</label>
                    <input type="text"
                           class="form-control @error('nomor_induk') is-invalid @enderror"
                           id="nomor_induk"
                           name="nomor_induk"
                           value="{{ old('nomor_induk', $teacher->nomor_induk) }}"
                           required>
                    @error('nomor_induk')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email" class="required">Email</label>
                    <input type="email"
                           class="form-control @error('email') is-invalid @enderror"
                           id="email"
                           name="email"
                           value="{{ old('email', $teacher->email) }}"
                           required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="nomor_telepon">Nomor Telepon</label>
                    <input type="tel"
                           class="form-control @error('nomor_telepon') is-invalid @enderror"
                           id="nomor_telepon"
                           name="nomor_telepon"
                           value="{{ old('nomor_telepon', $teacher->nomor_telepon) }}"
                           placeholder="Contoh: 08123456789">
                    @error('nomor_telepon')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="jenis_kelamin" class="required">Jenis Kelamin</label>
                    <select class="form-control @error('jenis_kelamin') is-invalid @enderror"
                            id="jenis_kelamin"
                            name="jenis_kelamin"
                            required>
                        <option value="">Pilih Jenis Kelamin</option>
                        <option value="laki-laki" {{ old('jenis_kelamin', $teacher->jenis_kelamin) === 'laki-laki' ? 'selected' : '' }}>
                            Laki-laki
                        </option>
                        <option value="perempuan" {{ old('jenis_kelamin', $teacher->jenis_kelamin) === 'perempuan' ? 'selected' : '' }}>
                            Perempuan
                        </option>
                    </select>
                    @error('jenis_kelamin')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="profile_photo">Foto Profil Baru</label>
                    <input type="file"
                           class="form-control @error('profile_photo') is-invalid @enderror"
                           id="profile_photo"
                           name="profile_photo"
                           accept="image/jpeg,image/png,image/jpg"
                           onchange="previewPhoto(this)">
                    <small class="form-text text-muted">Format: JPG, PNG. Maksimal 2MB. Kosongkan jika tidak ingin mengubah foto.</small>
                    @error('profile_photo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Professional Information Section -->
        <div class="form-section">
            <h3>
                <i class="fas fa-briefcase"></i>
                Informasi Kepegawaian
            </h3>
            <div class="form-grid">
                <div class="form-group">
                    <label for="mata_pelajaran" class="required">Mata Pelajaran</label>
                    <select class="form-control @error('mata_pelajaran') is-invalid @enderror"
                            id="mata_pelajaran"
                            name="mata_pelajaran"
                            required>
                        <option value="">Pilih Mata Pelajaran</option>
                        @php
                        $mataPelajaran = [
                            'Matematika', 'Bahasa Indonesia', 'Bahasa Inggris', 'IPA Fisika',
                            'IPA Biologi', 'IPS Sejarah', 'IPS Geografi', 'IPS Ekonomi',
                            'PKN', 'Pendidikan Jasmani', 'Seni Budaya', 'Prakarya',
                            'Bahasa Daerah', 'Teknologi Informasi', 'Bimbingan Konseling'
                        ];
                        @endphp
                        @foreach($mataPelajaran as $mapel)
                        <option value="{{ $mapel }}" {{ old('mata_pelajaran', $teacher->mata_pelajaran) === $mapel ? 'selected' : '' }}>
                            {{ $mapel }}
                        </option>
                        @endforeach
                    </select>
                    @error('mata_pelajaran')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="status_kepegawaian" class="required">Status Kepegawaian</label>
                    <select class="form-control @error('status_kepegawaian') is-invalid @enderror"
                            id="status_kepegawaian"
                            name="status_kepegawaian"
                            required>
                        <option value="">Pilih Status Kepegawaian</option>
                        <option value="pns" {{ old('status_kepegawaian', $teacher->status_kepegawaian) === 'pns' ? 'selected' : '' }}>
                            PNS (Pegawai Negeri Sipil)
                        </option>
                        <option value="honorer" {{ old('status_kepegawaian', $teacher->status_kepegawaian) === 'honorer' ? 'selected' : '' }}>
                            Honorer
                        </option>
                        <option value="kontrak" {{ old('status_kepegawaian', $teacher->status_kepegawaian) === 'kontrak' ? 'selected' : '' }}>
                            Kontrak
                        </option>
                        <option value="tetap_yayasan" {{ old('status_kepegawaian', $teacher->status_kepegawaian) === 'tetap_yayasan' ? 'selected' : '' }}>
                            Tetap Yayasan
                        </option>
                    </select>
                    @error('status_kepegawaian')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="wali_kelas">Wali Kelas</label>
                    <input type="text"
                           class="form-control @error('wali_kelas') is-invalid @enderror"
                           id="wali_kelas"
                           name="wali_kelas"
                           value="{{ old('wali_kelas', $teacher->wali_kelas) }}"
                           placeholder="Contoh: VII-A, VIII-B, IX-C">
                    <small class="form-text text-muted">Kosongkan jika tidak menjadi wali kelas</small>
                    @error('wali_kelas')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Password Section (Optional) -->
        <div class="form-section">
            <h3>
                <i class="fas fa-lock"></i>
                Ubah Password (Opsional)
            </h3>
            <p class="text-muted">Kosongkan jika tidak ingin mengubah password</p>
            <div class="form-grid">
                <div class="form-group">
                    <label for="password">Password Baru</label>
                    <input type="password"
                           class="form-control @error('password') is-invalid @enderror"
                           id="password"
                           name="password"
                           minlength="8">
                    <small class="form-text text-muted">Minimal 8 karakter</small>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Konfirmasi Password Baru</label>
                    <input type="password"
                           class="form-control"
                           id="password_confirmation"
                           name="password_confirmation"
                           minlength="8">
                    <small class="form-text text-muted">Ketik ulang password baru</small>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i>
                Simpan Perubahan
            </button>
            <a href="{{ route('admin.guru.show', $teacher->id) }}" class="btn btn-secondary">
                <i class="fas fa-times"></i>
                Batal
            </a>
            <button type="reset" class="btn btn-outline-secondary">
                <i class="fas fa-undo"></i>
                Reset Form
            </button>
        </div>
    </form>
</div>
</div>

<style>
.current-photo-section {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    padding: 25px;
    margin-bottom: 25px;
    text-align: center;
}

.current-photo-section h3 {
    margin: 0 0 20px 0;
    color: var(--primary-color);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    font-size: 18px;
    font-weight: 600;
}

.photo-preview {
    display: inline-block;
}

.current-photo {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid #e9ecef;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.no-photo {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    background: #f8f9fa;
    border: 4px dashed #dee2e6;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #6c757d;
    font-size: 14px;
}

.no-photo i {
    font-size: 48px;
    margin-bottom: 10px;
    opacity: 0.5;
}

@media (max-width: 768px) {
    .current-photo,
    .no-photo {
        width: 120px;
        height: 120px;
    }

    .no-photo i {
        font-size: 40px;
    }
}
</style>

<script>
function previewPhoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();

        reader.onload = function(e) {
            const currentPhoto = document.getElementById('currentPhoto');

            // If current element is img, just change src
            if (currentPhoto.tagName === 'IMG') {
                currentPhoto.src = e.target.result;
            } else {
                // If it's a div (no photo), replace with img
                const newImg = document.createElement('img');
                newImg.src = e.target.result;
                newImg.alt = 'Preview foto';
                newImg.className = 'current-photo';
                newImg.id = 'currentPhoto';
                currentPhoto.parentNode.replaceChild(newImg, currentPhoto);
            }
        }

        reader.readAsDataURL(input.files[0]);
    }
}

// Form validation
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.teacher-form');
    const password = document.getElementById('password');
    const passwordConfirm = document.getElementById('password_confirmation');

    // Password confirmation validation
    function validatePassword() {
        if (password.value && passwordConfirm.value) {
            if (password.value !== passwordConfirm.value) {
                passwordConfirm.setCustomValidity('Password tidak cocok');
            } else {
                passwordConfirm.setCustomValidity('');
            }
        } else {
            passwordConfirm.setCustomValidity('');
        }
    }

    password.addEventListener('input', validatePassword);
    passwordConfirm.addEventListener('input', validatePassword);
});
</script>
@endsection
