@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h3 text-dark fw-bold mb-2">
                                <i class="fas fa-user-plus text-primary me-2"></i>Tambah Guru Baru
                            </h1>
                            <p class="text-muted mb-0">Tambah data guru dan tenaga pendidik baru</p>
                        </div>
                        <div>
                            <a href="{{ route('admin.guru.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Kembali ke Data Guru
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Container -->
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light border-bottom-0 py-3">
                    <h5 class="card-title mb-0 text-dark fw-semibold">
                        <i class="fas fa-edit text-primary me-2"></i>Informasi Guru
                    </h5>
                    <p class="text-muted small mb-0 mt-1">Lengkapi semua informasi di bawah ini</p>
                </div>
                <div class="card-body p-4">

                    <form action="{{ route('admin.guru.store') }}" method="POST" enctype="multipart/form-data" id="teacherForm">
                        @csrf

                        <!-- Personal Information Section -->
                        <div class="mb-5">
                            <h6 class="text-primary mb-3 fw-semibold">
                                <i class="fas fa-user me-2"></i>Informasi Personal
                            </h6>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label fw-medium">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                           id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="nomor_induk" class="form-label fw-medium">NIP/NIK <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('nomor_induk') is-invalid @enderror"
                                           id="nomor_induk" name="nomor_induk" value="{{ old('nomor_induk') }}" required>
                                    <div class="form-text">Masukkan NIP untuk PNS/PPPK atau NIK untuk Honorer</div>
                                    @error('nomor_induk')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row g-3 mt-2">
                                <div class="col-md-6">
                                    <label for="email" class="form-label fw-medium">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                           id="email" name="email" value="{{ old('email') }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="nomor_telepon" class="form-label fw-medium">Nomor Telepon</label>
                                    <input type="tel" class="form-control @error('nomor_telepon') is-invalid @enderror"
                                           id="nomor_telepon" name="nomor_telepon" value="{{ old('nomor_telepon') }}">
                                    <div class="form-text">Format: 081234567890</div>
                                    @error('nomor_telepon')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row g-3 mt-2">
                                <div class="col-md-6">
                                    <label for="jenis_kelamin" class="form-label fw-medium">Jenis Kelamin <span class="text-danger">*</span></label>
                                    <select class="form-select @error('jenis_kelamin') is-invalid @enderror"
                                            id="jenis_kelamin" name="jenis_kelamin" required>
                                        <option value="">Pilih Jenis Kelamin</option>
                                        <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                        <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                                    </select>
                                    @error('jenis_kelamin')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="tempat_lahir" class="form-label fw-medium">Tempat Lahir</label>
                                    <input type="text" class="form-control @error('tempat_lahir') is-invalid @enderror"
                                           id="tempat_lahir" name="tempat_lahir" value="{{ old('tempat_lahir') }}">
                                    @error('tempat_lahir')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row g-3 mt-2">
                                <div class="col-md-6">
                                    <label for="tanggal_lahir" class="form-label fw-medium">Tanggal Lahir</label>
                                    <input type="date" class="form-control @error('tanggal_lahir') is-invalid @enderror"
                                           id="tanggal_lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}">
                                    @error('tanggal_lahir')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="profile_photo" class="form-label fw-medium">Foto Profil</label>
                                    <input type="file" class="form-control @error('profile_photo') is-invalid @enderror"
                                           id="profile_photo" name="profile_photo" accept="image/*">
                                    <div class="form-text">Format: JPG, PNG, maksimal 2MB</div>
                                    @error('profile_photo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Employment Information Section -->
                        <div class="mb-5">
                            <h6 class="text-primary mb-3 fw-semibold">
                                <i class="fas fa-briefcase me-2"></i>Informasi Kepegawaian
                            </h6>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="status_kepegawaian" class="form-label fw-medium">Status Kepegawaian <span class="text-danger">*</span></label>
                                    <select class="form-select @error('status_kepegawaian') is-invalid @enderror"
                                            id="status_kepegawaian" name="status_kepegawaian" required>
                                        <option value="">Pilih Status</option>
                                        <option value="PNS" {{ old('status_kepegawaian') == 'PNS' ? 'selected' : '' }}>PNS</option>
                                        <option value="PPPK" {{ old('status_kepegawaian') == 'PPPK' ? 'selected' : '' }}>PPPK</option>
                                        <option value="GTT" {{ old('status_kepegawaian') == 'GTT' ? 'selected' : '' }}>GTT (Guru Tidak Tetap)</option>
                                        <option value="GTY" {{ old('status_kepegawaian') == 'GTY' ? 'selected' : '' }}>GTY (Guru Tetap Yayasan)</option>
                                        <option value="GTK" {{ old('status_kepegawaian') == 'GTK' ? 'selected' : '' }}>GTK (Guru Tenaga Kependidikan)</option>
                                    </select>
                                    @error('status_kepegawaian')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="golongan" class="form-label fw-medium">Golongan</label>
                                    <select class="form-select @error('golongan') is-invalid @enderror"
                                            id="golongan" name="golongan">
                                        <option value="">Pilih Golongan</option>
                                        <option value="II/a" {{ old('golongan') == 'II/a' ? 'selected' : '' }}>II/a</option>
                                        <option value="II/b" {{ old('golongan') == 'II/b' ? 'selected' : '' }}>II/b</option>
                                        <option value="II/c" {{ old('golongan') == 'II/c' ? 'selected' : '' }}>II/c</option>
                                        <option value="II/d" {{ old('golongan') == 'II/d' ? 'selected' : '' }}>II/d</option>
                                        <option value="III/a" {{ old('golongan') == 'III/a' ? 'selected' : '' }}>III/a</option>
                                        <option value="III/b" {{ old('golongan') == 'III/b' ? 'selected' : '' }}>III/b</option>
                                        <option value="III/c" {{ old('golongan') == 'III/c' ? 'selected' : '' }}>III/c</option>
                                        <option value="III/d" {{ old('golongan') == 'III/d' ? 'selected' : '' }}>III/d</option>
                                        <option value="IV/a" {{ old('golongan') == 'IV/a' ? 'selected' : '' }}>IV/a</option>
                                        <option value="IV/b" {{ old('golongan') == 'IV/b' ? 'selected' : '' }}>IV/b</option>
                                        <option value="IV/c" {{ old('golongan') == 'IV/c' ? 'selected' : '' }}>IV/c</option>
                                        <option value="IV/d" {{ old('golongan') == 'IV/d' ? 'selected' : '' }}>IV/d</option>
                                        <option value="IV/e" {{ old('golongan') == 'IV/e' ? 'selected' : '' }}>IV/e</option>
                                    </select>
                                    <div class="form-text">Kosongkan jika Honorer</div>
                                    @error('golongan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row g-3 mt-2">
                                <div class="col-md-6">
                                    <label for="mata_pelajaran" class="form-label fw-medium">Mata Pelajaran <span class="text-danger">*</span></label>
                                    <select class="form-select @error('mata_pelajaran') is-invalid @enderror"
                                            id="mata_pelajaran" name="mata_pelajaran" required>
                                        <option value="">Pilih Mata Pelajaran</option>
                                        <option value="Matematika" {{ old('mata_pelajaran') == 'Matematika' ? 'selected' : '' }}>Matematika</option>
                                        <option value="Bahasa Indonesia" {{ old('mata_pelajaran') == 'Bahasa Indonesia' ? 'selected' : '' }}>Bahasa Indonesia</option>
                                        <option value="Bahasa Inggris" {{ old('mata_pelajaran') == 'Bahasa Inggris' ? 'selected' : '' }}>Bahasa Inggris</option>
                                        <option value="IPA Fisika" {{ old('mata_pelajaran') == 'IPA Fisika' ? 'selected' : '' }}>IPA Fisika</option>
                                        <option value="IPA Biologi" {{ old('mata_pelajaran') == 'IPA Biologi' ? 'selected' : '' }}>IPA Biologi</option>
                                        <option value="IPA Kimia" {{ old('mata_pelajaran') == 'IPA Kimia' ? 'selected' : '' }}>IPA Kimia</option>
                                        <option value="IPS Sejarah" {{ old('mata_pelajaran') == 'IPS Sejarah' ? 'selected' : '' }}>IPS Sejarah</option>
                                        <option value="IPS Geografi" {{ old('mata_pelajaran') == 'IPS Geografi' ? 'selected' : '' }}>IPS Geografi</option>
                                        <option value="IPS Ekonomi" {{ old('mata_pelajaran') == 'IPS Ekonomi' ? 'selected' : '' }}>IPS Ekonomi</option>
                                        <option value="PKN" {{ old('mata_pelajaran') == 'PKN' ? 'selected' : '' }}>PKN</option>
                                        <option value="Pendidikan Jasmani" {{ old('mata_pelajaran') == 'Pendidikan Jasmani' ? 'selected' : '' }}>Pendidikan Jasmani</option>
                                        <option value="Seni Budaya" {{ old('mata_pelajaran') == 'Seni Budaya' ? 'selected' : '' }}>Seni Budaya</option>
                                        <option value="Prakarya" {{ old('mata_pelajaran') == 'Prakarya' ? 'selected' : '' }}>Prakarya</option>
                                        <option value="TIK" {{ old('mata_pelajaran') == 'TIK' ? 'selected' : '' }}>TIK</option>
                                        <option value="Agama Islam" {{ old('mata_pelajaran') == 'Agama Islam' ? 'selected' : '' }}>Agama Islam</option>
                                        <option value="Agama Kristen" {{ old('mata_pelajaran') == 'Agama Kristen' ? 'selected' : '' }}>Agama Kristen</option>
                                        <option value="Agama Katolik" {{ old('mata_pelajaran') == 'Agama Katolik' ? 'selected' : '' }}>Agama Katolik</option>
                                        <option value="Agama Hindu" {{ old('mata_pelajaran') == 'Agama Hindu' ? 'selected' : '' }}>Agama Hindu</option>
                                        <option value="Agama Buddha" {{ old('mata_pelajaran') == 'Agama Buddha' ? 'selected' : '' }}>Agama Buddha</option>
                                        <option value="Bahasa Daerah" {{ old('mata_pelajaran') == 'Bahasa Daerah' ? 'selected' : '' }}>Bahasa Daerah</option>
                                    </select>
                                    @error('mata_pelajaran')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="wali_kelas" class="form-label fw-medium">Wali Kelas</label>
                                    <select class="form-select @error('wali_kelas') is-invalid @enderror"
                                            id="wali_kelas" name="wali_kelas">
                                        <option value="">Pilih Kelas (Opsional)</option>
                                        <option value="VII-A" {{ old('wali_kelas') == 'VII-A' ? 'selected' : '' }}>VII-A</option>
                                        <option value="VII-B" {{ old('wali_kelas') == 'VII-B' ? 'selected' : '' }}>VII-B</option>
                                        <option value="VII-C" {{ old('wali_kelas') == 'VII-C' ? 'selected' : '' }}>VII-C</option>
                                        <option value="VII-D" {{ old('wali_kelas') == 'VII-D' ? 'selected' : '' }}>VII-D</option>
                                        <option value="VIII-A" {{ old('wali_kelas') == 'VIII-A' ? 'selected' : '' }}>VIII-A</option>
                                        <option value="VIII-B" {{ old('wali_kelas') == 'VIII-B' ? 'selected' : '' }}>VIII-B</option>
                                        <option value="VIII-C" {{ old('wali_kelas') == 'VIII-C' ? 'selected' : '' }}>VIII-C</option>
                                        <option value="VIII-D" {{ old('wali_kelas') == 'VIII-D' ? 'selected' : '' }}>VIII-D</option>
                                        <option value="IX-A" {{ old('wali_kelas') == 'IX-A' ? 'selected' : '' }}>IX-A</option>
                                        <option value="IX-B" {{ old('wali_kelas') == 'IX-B' ? 'selected' : '' }}>IX-B</option>
                                        <option value="IX-C" {{ old('wali_kelas') == 'IX-C' ? 'selected' : '' }}>IX-C</option>
                                        <option value="IX-D" {{ old('wali_kelas') == 'IX-D' ? 'selected' : '' }}>IX-D</option>
                                    </select>
                                    @error('wali_kelas')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Account Information Section -->
                        <div class="mb-5">
                            <h6 class="text-primary mb-3 fw-semibold">
                                <i class="fas fa-key me-2"></i>Informasi Akun
                            </h6>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="password" class="form-label fw-medium">Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                           id="password" name="password" required>
                                    <div class="form-text">Minimal 8 karakter</div>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="password_confirmation" class="form-label fw-medium">Konfirmasi Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control"
                                           id="password_confirmation" name="password_confirmation" required>
                                    <div class="form-text">Ulangi password di atas</div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-3">
                                    <button type="button" class="btn btn-secondary" onclick="window.history.back()">
                                        <i class="fas fa-times me-2"></i>Batal
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Simpan Data Guru
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

<!-- Success/Error Messages -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show position-fixed top-0 end-0 m-3" role="alert" style="z-index: 9999;">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show position-fixed top-0 end-0 m-3" role="alert" style="z-index: 9999;">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<script>
document.addEventListener('DOMContentLoaded', function() {
    const statusKepegawaian = document.getElementById('status_kepegawaian');
    const golonganField = document.getElementById('golongan');

    // Handle golongan field based on status kepegawaian
    statusKepegawaian.addEventListener('change', function() {
        if (this.value === 'Honorer' || this.value === 'GTT' || this.value === 'GTY' || this.value === 'GTK') {
            golonganField.value = '';
            golonganField.disabled = true;
            // golonganField.classList.add('disabled'); // Bootstrap doesn't use disabled class for inputs usually, attribute is enough
        } else {
            golonganField.disabled = false;
            // golonganField.classList.remove('disabled');
        }
    });

    // Trigger change on load
    statusKepegawaian.dispatchEvent(new Event('change'));

    // Photo preview
    const photoInput = document.getElementById('profile_photo');
    photoInput.addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                console.log('Photo selected:', file.name);
            };
            reader.readAsDataURL(file);
        }
    });

    // Form validation
    const form = document.getElementById('teacherForm');
    form.addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('password_confirmation').value;

        if (password !== confirmPassword) {
            e.preventDefault();
            alert('Password dan konfirmasi password tidak sama!');
            return false;
        }

        if (password.length < 8) {
            e.preventDefault();
            alert('Password minimal 8 karakter!');
            return false;
        }
    });
});
</script>
@endsection
