@extends('layouts.app')

@section('title', 'Edit Siswa')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body py-4">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                        <div class="mb-3 mb-md-0">
                            <h1 class="h3 mb-2 text-primary">
                                <i class="fas fa-user-edit me-2"></i>Edit Data Siswa
                            </h1>
                            <p class="text-muted mb-0">Perbarui informasi siswa</p>
                        </div>
                        <div>
                            <a href="{{ route('admin.siswa.show', $student->id) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Form -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <form action="{{ route('admin.siswa.update', $student->id) }}" method="POST" enctype="multipart/form-data" id="studentForm">
                        @csrf
                        @method('PUT')

                        <!-- Personal Information Section -->
                        <div class="mb-5">
                            <h6 class="text-primary mb-3 fw-semibold">
                                <i class="fas fa-user me-2"></i>Informasi Personal
                            </h6>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label fw-medium">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text"
                                           class="form-control @error('name') is-invalid @enderror"
                                           id="name"
                                           name="name"
                                           value="{{ old('name', $student->name) }}"
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="nis" class="form-label fw-medium">NIS</label>
                                    <input type="text"
                                           class="form-control @error('nis') is-invalid @enderror"
                                           id="nis"
                                           name="nis"
                                           value="{{ old('nis', $student->studentProfile->nis ?? '') }}">
                                    @error('nis')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row g-3 mt-2">
                                <div class="col-md-6">
                                    <label for="nisn" class="form-label fw-medium">NISN</label>
                                    <input type="text"
                                           class="form-control @error('nisn') is-invalid @enderror"
                                           id="nisn"
                                           name="nisn"
                                           value="{{ old('nisn', $student->studentProfile->nisn ?? '') }}">
                                    @error('nisn')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="email" class="form-label fw-medium">Email</label>
                                    <input type="email"
                                           class="form-control @error('email') is-invalid @enderror"
                                           id="email"
                                           name="email"
                                           value="{{ old('email', $student->email) }}">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row g-3 mt-2">
                                <div class="col-md-6">
                                    <label for="jenis_kelamin" class="form-label fw-medium">Jenis Kelamin</label>
                                    <select class="form-select @error('jenis_kelamin') is-invalid @enderror"
                                            id="jenis_kelamin"
                                            name="jenis_kelamin">
                                        <option value="">Pilih Jenis Kelamin</option>
                                        <option value="L" {{ old('jenis_kelamin', $student->studentProfile->jenis_kelamin ?? '') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                        <option value="P" {{ old('jenis_kelamin', $student->studentProfile->jenis_kelamin ?? '') == 'P' ? 'selected' : '' }}>Perempuan</option>
                                    </select>
                                    @error('jenis_kelamin')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row g-3 mt-2">
                                <div class="col-md-6">
                                    <label for="tempat_lahir" class="form-label fw-medium">Tempat Lahir</label>
                                    <input type="text"
                                           class="form-control @error('tempat_lahir') is-invalid @enderror"
                                           id="tempat_lahir"
                                           name="tempat_lahir"
                                           value="{{ old('tempat_lahir', $student->studentProfile->tempat_lahir ?? '') }}">
                                    @error('tempat_lahir')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="tanggal_lahir" class="form-label fw-medium">Tanggal Lahir</label>
                                    <input type="date"
                                           class="form-control @error('tanggal_lahir') is-invalid @enderror"
                                           id="tanggal_lahir"
                                           name="tanggal_lahir"
                                           value="{{ old('tanggal_lahir', optional($student->studentProfile->tanggal_lahir ?? null)->format('Y-m-d')) }}">
                                    @error('tanggal_lahir')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row g-3 mt-2">
                                <div class="col-md-6">
                                    <label for="profile_photo" class="form-label fw-medium">Foto Profil</label>
                                    <input type="file"
                                           class="form-control @error('profile_photo') is-invalid @enderror"
                                           id="profile_photo"
                                           name="profile_photo"
                                           accept="image/*"
                                           onchange="previewPhoto(this)">
                                    <div class="form-text">Biarkan kosong jika tidak ingin mengganti foto</div>
                                    @error('profile_photo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- School Information Section -->
                        <div class="mb-5">
                            <h6 class="text-primary mb-3 fw-semibold">
                                <i class="fas fa-school me-2"></i>Informasi Sekolah
                            </h6>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="kelas" class="form-label fw-medium">Kelas</label>
                                    <input type="text"
                                           class="form-control @error('kelas') is-invalid @enderror"
                                           id="kelas"
                                           name="kelas"
                                           value="{{ old('kelas', $student->studentProfile->kelas ?? '') }}">
                                    @error('kelas')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="nomor_telepon_orangtua" class="form-label fw-medium">Nomor Telepon Orang Tua</label>
                                    <input type="tel"
                                           class="form-control @error('nomor_telepon_orangtua') is-invalid @enderror"
                                           id="nomor_telepon_orangtua"
                                           name="nomor_telepon_orangtua"
                                           value="{{ old('nomor_telepon_orangtua', $student->studentProfile->nomor_telepon_orangtua ?? '') }}">
                                    @error('nomor_telepon_orangtua')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Password Section (Optional) -->
                        <div class="mb-5">
                            <h6 class="text-primary mb-3 fw-semibold">
                                <i class="fas fa-key me-2"></i>Ubah Password (Opsional)
                            </h6>
                            <p class="text-muted small mb-3">Kosongkan jika tidak ingin mengubah password</p>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="password" class="form-label fw-medium">Password Baru</label>
                                    <input type="password"
                                           class="form-control @error('password') is-invalid @enderror"
                                           id="password"
                                           name="password"
                                           minlength="8">
                                    <div class="form-text">Minimal 8 karakter</div>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="password_confirmation" class="form-label fw-medium">Konfirmasi Password</label>
                                    <input type="password"
                                           class="form-control"
                                           id="password_confirmation"
                                           name="password_confirmation"
                                           minlength="8">
                                    <div class="form-text">Ketik ulang password baru</div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex flex-column flex-sm-row justify-content-end gap-3">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Simpan Perubahan
                                    </button>
                                    <a href="{{ route('admin.siswa.show', $student->id) }}" class="btn btn-secondary">
                                        <i class="fas fa-times me-2"></i>Batal
                                    </a>
                                    <button type="reset" class="btn btn-outline-secondary">
                                        <i class="fas fa-undo me-2"></i>Reset Form
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function previewPhoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            let img = document.getElementById('currentPhoto');
            if (img) img.src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('studentForm');
    const password = document.getElementById('password');
    const passwordConfirm = document.getElementById('password_confirmation');

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
