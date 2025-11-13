@extends('layouts.app')

@section('content')
<div class="guru-management">
    <!-- Page Header -->
    <div class="page-header">
        <div class="header-content">
            <h1><i class="fas fa-user-plus"></i> Tambah Guru Baru</h1>
            <p class="page-subtitle">Tambah data guru dan tenaga pendidik baru</p>
        </div>
        <div class="header-actions">
            <a href="{{ route('admin.guru.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali ke Data Guru
            </a>
        </div>
    </div>

    <!-- Form Container -->
    <div class="form-container-card">
        <div class="form-header">
            <h2><i class="fas fa-edit"></i> Informasi Guru</h2>
            <p class="form-subtitle">Lengkapi semua informasi di bawah ini</p>
        </div>

        <form action="{{ route('admin.guru.store') }}" method="POST" enctype="multipart/form-data" id="teacherForm">
            @csrf

            <!-- Personal Information Section -->
            <div class="form-section">
                <h3 class="section-title">
                    <i class="fas fa-user"></i> Informasi Personal
                </h3>

                <div class="form-row">
                    <div class="form-group">
                        <label for="name">Nama Lengkap <span class="required">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                               id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="nomor_induk">NIP/NIK <span class="required">*</span></label>
                        <input type="text" class="form-control @error('nomor_induk') is-invalid @enderror"
                               id="nomor_induk" name="nomor_induk" value="{{ old('nomor_induk') }}" required>
                        <small class="form-text">Masukkan NIP untuk PNS/PPPK atau NIK untuk Honorer</small>
                        @error('nomor_induk')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email <span class="required">*</span></label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                               id="email" name="email" value="{{ old('email') }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="nomor_telepon">Nomor Telepon</label>
                        <input type="tel" class="form-control @error('nomor_telepon') is-invalid @enderror"
                               id="nomor_telepon" name="nomor_telepon" value="{{ old('nomor_telepon') }}">
                        <small class="form-text">Format: 081234567890</small>
                        @error('nomor_telepon')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="jenis_kelamin">Jenis Kelamin <span class="required">*</span></label>
                        <select class="form-control @error('jenis_kelamin') is-invalid @enderror"
                                id="jenis_kelamin" name="jenis_kelamin" required>
                            <option value="">Pilih Jenis Kelamin</option>
                            <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                        @error('jenis_kelamin')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="tempat_lahir">Tempat Lahir</label>
                        <input type="text" class="form-control @error('tempat_lahir') is-invalid @enderror"
                               id="tempat_lahir" name="tempat_lahir" value="{{ old('tempat_lahir') }}">
                        @error('tempat_lahir')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="tanggal_lahir">Tanggal Lahir</label>
                        <input type="date" class="form-control @error('tanggal_lahir') is-invalid @enderror"
                               id="tanggal_lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}">
                        @error('tanggal_lahir')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="profile_photo">Foto Profil</label>
                        <input type="file" class="form-control @error('profile_photo') is-invalid @enderror"
                               id="profile_photo" name="profile_photo" accept="image/*">
                        <small class="form-text">Format: JPG, PNG, maksimal 2MB</small>
                        @error('profile_photo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Employment Information Section -->
            <div class="form-section">
                <h3 class="section-title">
                    <i class="fas fa-briefcase"></i> Informasi Kepegawaian
                </h3>

                <div class="form-row">
                    <div class="form-group">
                        <label for="status_kepegawaian">Status Kepegawaian <span class="required">*</span></label>
                        <select class="form-control @error('status_kepegawaian') is-invalid @enderror"
                                id="status_kepegawaian" name="status_kepegawaian" required>
                            <option value="">Pilih Status</option>
                            <option value="PNS" {{ old('status_kepegawaian') == 'PNS' ? 'selected' : '' }}>PNS</option>
                            <option value="PPPK" {{ old('status_kepegawaian') == 'PPPK' ? 'selected' : '' }}>PPPK</option>
                            <option value="Honorer" {{ old('status_kepegawaian') == 'Honorer' ? 'selected' : '' }}>Honorer</option>
                        </select>
                        @error('status_kepegawaian')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="golongan">Golongan</label>
                        <select class="form-control @error('golongan') is-invalid @enderror"
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
                        <small class="form-text">Kosongkan jika Honorer</small>
                        @error('golongan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="mata_pelajaran">Mata Pelajaran <span class="required">*</span></label>
                        <select class="form-control @error('mata_pelajaran') is-invalid @enderror"
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
                            <option value="BK" {{ old('mata_pelajaran') == 'BK' ? 'selected' : '' }}>Bimbingan Konseling</option>
                        </select>
                        @error('mata_pelajaran')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="wali_kelas">Wali Kelas</label>
                        <select class="form-control @error('wali_kelas') is-invalid @enderror"
                                id="wali_kelas" name="wali_kelas">
                            <option value="">Tidak sebagai Wali Kelas</option>
                            <option value="7A" {{ old('wali_kelas') == '7A' ? 'selected' : '' }}>7A</option>
                            <option value="7B" {{ old('wali_kelas') == '7B' ? 'selected' : '' }}>7B</option>
                            <option value="7C" {{ old('wali_kelas') == '7C' ? 'selected' : '' }}>7C</option>
                            <option value="7D" {{ old('wali_kelas') == '7D' ? 'selected' : '' }}>7D</option>
                            <option value="7E" {{ old('wali_kelas') == '7E' ? 'selected' : '' }}>7E</option>
                            <option value="7F" {{ old('wali_kelas') == '7F' ? 'selected' : '' }}>7F</option>
                            <option value="8A" {{ old('wali_kelas') == '8A' ? 'selected' : '' }}>8A</option>
                            <option value="8B" {{ old('wali_kelas') == '8B' ? 'selected' : '' }}>8B</option>
                            <option value="8C" {{ old('wali_kelas') == '8C' ? 'selected' : '' }}>8C</option>
                            <option value="8D" {{ old('wali_kelas') == '8D' ? 'selected' : '' }}>8D</option>
                            <option value="8E" {{ old('wali_kelas') == '8E' ? 'selected' : '' }}>8E</option>
                            <option value="8F" {{ old('wali_kelas') == '8F' ? 'selected' : '' }}>8F</option>
                            <option value="9A" {{ old('wali_kelas') == '9A' ? 'selected' : '' }}>9A</option>
                            <option value="9B" {{ old('wali_kelas') == '9B' ? 'selected' : '' }}>9B</option>
                            <option value="9C" {{ old('wali_kelas') == '9C' ? 'selected' : '' }}>9C</option>
                            <option value="9D" {{ old('wali_kelas') == '9D' ? 'selected' : '' }}>9D</option>
                            <option value="9E" {{ old('wali_kelas') == '9E' ? 'selected' : '' }}>9E</option>
                            <option value="9F" {{ old('wali_kelas') == '9F' ? 'selected' : '' }}>9F</option>
                        </select>
                        @error('wali_kelas')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Account Information Section -->
            <div class="form-section">
                <h3 class="section-title">
                    <i class="fas fa-key"></i> Informasi Akun
                </h3>

                <div class="form-row">
                    <div class="form-group">
                        <label for="password">Password <span class="required">*</span></label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                               id="password" name="password" required>
                        <small class="form-text">Minimal 8 karakter</small>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Konfirmasi Password <span class="required">*</span></label>
                        <input type="password" class="form-control"
                               id="password_confirmation" name="password_confirmation" required>
                        <small class="form-text">Ulangi password di atas</small>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="window.history.back()">
                    <i class="fas fa-times"></i> Batal
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan Data Guru
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Success/Error Messages -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

<script>
document.addEventListener('DOMContentLoaded', function() {
    const statusKepegawaian = document.getElementById('status_kepegawaian');
    const golonganField = document.getElementById('golongan');

    // Handle golongan field based on status kepegawaian
    statusKepegawaian.addEventListener('change', function() {
        if (this.value === 'Honorer') {
            golonganField.value = '';
            golonganField.disabled = true;
            golonganField.parentElement.classList.add('disabled');
        } else {
            golonganField.disabled = false;
            golonganField.parentElement.classList.remove('disabled');
        }
    });

    // Photo preview
    const photoInput = document.getElementById('profile_photo');
    photoInput.addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                // You can add photo preview functionality here
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
