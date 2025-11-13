@extends('layouts.app')

@section('title', 'Edit Siswa')

@section('content')
<div class="siswa-management">
    <div class="page-header">
        <div class="header-content">
            <h1><i class="fas fa-user-edit"></i> Edit Data Siswa</h1>
            <p class="page-subtitle">Perbarui informasi siswa</p>
        </div>
        <div class="header-actions">
            <a href="{{ route('admin.siswa.show', $student->id) }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
        </div>
    </div>

    <div class="form-container-card">
        <form action="{{ route('admin.siswa.update', $student->id) }}" method="POST" enctype="multipart/form-data" class="student-form">
            @csrf
            @method('PUT')

            <div class="form-section">
                <h3 class="section-title"><i class="fas fa-user"></i> Informasi Personal</h3>

                <div class="form-row">
                    <div class="form-group">
                        <label for="name">Nama Lengkap</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $student->name) }}" required>
                    </div>

                    <div class="form-group">
                        <label for="nis">NIS</label>
                        <input type="text" class="form-control" id="nis" name="nis" value="{{ old('nis', $student->nis) }}">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="nisn">NISN</label>
                        <input type="text" class="form-control" id="nisn" name="nisn" value="{{ old('nisn', $student->nisn) }}">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $student->email) }}">
                    </div>

                    <div class="form-group">
                        <label for="nomor_telepon">Nomor Telepon</label>
                        <input type="tel" class="form-control" id="nomor_telepon" name="nomor_telepon" value="{{ old('nomor_telepon', $student->nomor_telepon) }}">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="jenis_kelamin">Jenis Kelamin</label>
                        <select class="form-control" id="jenis_kelamin" name="jenis_kelamin">
                            <option value="">Pilih Jenis Kelamin</option>
                            <option value="Laki-laki" {{ old('jenis_kelamin', $student->jenis_kelamin) == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="Perempuan" {{ old('jenis_kelamin', $student->jenis_kelamin) == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="tempat_lahir">Tempat Lahir</label>
                        <input type="text" class="form-control" id="tempat_lahir" name="tempat_lahir" value="{{ old('tempat_lahir', $student->tempat_lahir) }}">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="tanggal_lahir">Tanggal Lahir</label>
                        <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir', $student->tanggal_lahir) }}">
                    </div>

                    <div class="form-group">
                        <label for="profile_photo">Foto Profil</label>
                        <input type="file" class="form-control" id="profile_photo" name="profile_photo" accept="image/*" onchange="previewPhoto(this)">
                        <small class="form-text">Biarkan kosong jika tidak ingin mengganti foto</small>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h3 class="section-title"><i class="fas fa-briefcase"></i> Informasi Sekolah</h3>

                <div class="form-row">
                    <div class="form-group">
                        <label for="kelas">Kelas</label>
                        <input type="text" class="form-control" id="kelas" name="kelas" value="{{ old('kelas', $student->kelas) }}">
                    </div>

                    <div class="form-group">
                        <label for="nomor_telepon_orangtua">Nomor Telepon Orang Tua</label>
                        <input type="tel" class="form-control" id="nomor_telepon_orangtua" name="nomor_telepon_orangtua" value="{{ old('nomor_telepon_orangtua', $student->nomor_telepon_orangtua) }}">
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h3 class="section-title"><i class="fas fa-key"></i> Ubah Password (Opsional)</h3>
                <p class="text-muted">Kosongkan jika tidak ingin mengubah password</p>
                <div class="form-row">
                    <div class="form-group">
                        <label for="password">Password Baru</label>
                        <input type="password" class="form-control" id="password" name="password">
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Konfirmasi Password</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Perubahan</button>
                <a href="{{ route('admin.siswa.show', $student->id) }}" class="btn btn-secondary"><i class="fas fa-times"></i> Batal</a>
                <button type="reset" class="btn btn-outline-secondary"><i class="fas fa-undo"></i> Reset Form</button>
            </div>
        </form>
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
    const form = document.querySelector('.student-form');
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
