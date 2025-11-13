@extends('layouts.app')

@section('title', 'Tambah Siswa')

@section('content')
<div class="siswa-management">
    <div class="page-header">
        <div class="header-content">
            <h1><i class="fas fa-user-plus"></i> Tambah Siswa Baru</h1>
            <p class="page-subtitle">Tambah data siswa</p>
        </div>
        <div class="header-actions">
            <a href="{{ route('admin.siswa.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali ke Data Siswa
            </a>
        </div>
    </div>

    <div class="form-container-card">
        <div class="form-header">
            <h2><i class="fas fa-edit"></i> Informasi Siswa</h2>
            <p class="form-subtitle">Lengkapi semua informasi di bawah ini</p>
        </div>

        <form action="{{ route('admin.siswa.store') }}" method="POST" enctype="multipart/form-data" id="studentForm">
            @csrf

            <div class="form-section">
                <h3 class="section-title"><i class="fas fa-user"></i> Informasi Personal</h3>

                <div class="form-row">
                    <div class="form-group">
                        <label for="name">Nama Lengkap <span class="required">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">
                        <label for="nis">NIS</label>
                        <input type="text" class="form-control @error('nis') is-invalid @enderror" id="nis" name="nis" value="{{ old('nis') }}">
                        @error('nis')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="nisn">NISN</label>
                        <input type="text" class="form-control @error('nisn') is-invalid @enderror" id="nisn" name="nisn" value="{{ old('nisn') }}">
                        @error('nisn')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}">
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="nomor_telepon">Nomor Telepon</label>
                        <input type="tel" class="form-control @error('nomor_telepon') is-invalid @enderror" id="nomor_telepon" name="nomor_telepon" value="{{ old('nomor_telepon') }}">
                        @error('nomor_telepon')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="jenis_kelamin">Jenis Kelamin</label>
                        <select class="form-control @error('jenis_kelamin') is-invalid @enderror" id="jenis_kelamin" name="jenis_kelamin">
                            <option value="">Pilih Jenis Kelamin</option>
                            <option value="Laki-laki" {{ old('jenis_kelamin') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="Perempuan" {{ old('jenis_kelamin') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                        @error('jenis_kelamin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">
                        <label for="tempat_lahir">Tempat Lahir</label>
                        <input type="text" class="form-control @error('tempat_lahir') is-invalid @enderror" id="tempat_lahir" name="tempat_lahir" value="{{ old('tempat_lahir') }}">
                        @error('tempat_lahir')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="tanggal_lahir">Tanggal Lahir</label>
                        <input type="date" class="form-control @error('tanggal_lahir') is-invalid @enderror" id="tanggal_lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}">
                        @error('tanggal_lahir')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">
                        <label for="profile_photo">Foto Profil</label>
                        <input type="file" class="form-control @error('profile_photo') is-invalid @enderror" id="profile_photo" name="profile_photo" accept="image/*">
                        <small class="form-text">Format: JPG, PNG, maksimal 2MB</small>
                        @error('profile_photo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h3 class="section-title"><i class="fas fa-briefcase"></i> Informasi Sekolah</h3>

                <div class="form-row">
                    <div class="form-group">
                        <label for="kelas">Kelas</label>
                        <input type="text" class="form-control @error('kelas') is-invalid @enderror" id="kelas" name="kelas" value="{{ old('kelas') }}">
                        @error('kelas')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">
                        <label for="nomor_telepon_orangtua">Nomor Telepon Orang Tua</label>
                        <input type="tel" class="form-control @error('nomor_telepon_orangtua') is-invalid @enderror" id="nomor_telepon_orangtua" name="nomor_telepon_orangtua" value="{{ old('nomor_telepon_orangtua') }}">
                        @error('nomor_telepon_orangtua')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h3 class="section-title"><i class="fas fa-key"></i> Informasi Akun</h3>

                <div class="form-row">
                    <div class="form-group">
                        <label for="password">Password <span class="required">*</span></label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                        <small class="form-text">Minimal 8 karakter</small>
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Konfirmasi Password <span class="required">*</span></label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="window.history.back()"><i class="fas fa-times"></i> Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Data Siswa</button>
            </div>
        </form>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('studentForm');
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
