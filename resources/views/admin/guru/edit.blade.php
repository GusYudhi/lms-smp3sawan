@extends('layouts.app')

@section('title', 'Edit Guru')

@section('content')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body py-4">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                        <div class="mb-3 mb-md-0">
                            <h1 class="h3 mb-2 text-primary">
                                <i class="fas fa-user-edit me-2"></i>Edit Data Guru
                            </h1>
                            <p class="text-muted mb-0">Perbarui informasi data guru dan tenaga pendidik</p>
                        </div>
                        <div>
                            <a href="{{ route('admin.guru.show', $teacher->id) }}" class="btn btn-secondary">
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
                    <form action="{{ route('admin.guru.update', $teacher->id) }}" method="POST" enctype="multipart/form-data" id="teacherForm">
                        @csrf
                        @method('PUT')

                        <!-- Current Photo Display -->
                        <div class="mb-5">
                            <h6 class="text-primary mb-3 fw-semibold">
                                <i class="fas fa-image me-2"></i>Foto Profil Saat Ini
                            </h6>
                            <div class="text-center">
                                @if($teacher->guruProfile && $teacher->guruProfile->foto_profil)
                                    <img src="{{ asset('storage/' . $teacher->guruProfile->foto_profil) }}"
                                         alt="Foto {{ $teacher->name }}"
                                         class="rounded-circle border"
                                         id="currentPhoto"
                                         style="width: 150px; height: 150px; object-fit: cover;">
                                @else
                                    <div class="d-inline-flex flex-column align-items-center justify-content-center rounded-circle bg-light border border-2 border-dashed text-muted"
                                         id="currentPhoto"
                                         style="width: 150px; height: 150px;">
                                        <i class="fas fa-user fs-1 opacity-50"></i>
                                        <span class="small">Belum ada foto</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Personal Information Section -->
                        <div class="mb-5">
                            <h6 class="text-primary mb-3 fw-semibold">
                                <i class="fas fa-user me-2"></i>Informasi Personal
                            </h6>

                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="name" class="form-label fw-medium">Nama Lengkap <span class="text-danger">*</span></label>
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

                                <div class="col-md-4">
                                    <label for="nomor_induk" class="form-label fw-medium">Nomor Induk Guru <span class="text-danger">*</span></label>
                                    <input type="text"
                                           class="form-control @error('nomor_induk') is-invalid @enderror"
                                           id="nomor_induk"
                                           name="nomor_induk"
                                           value="{{ old('nomor_induk', $teacher->guruProfile->nip ?? '') }}"
                                           required>
                                    @error('nomor_induk')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label for="kode_guru" class="form-label fw-medium">Kode Guru</label>
                                    <input type="text"
                                           class="form-control @error('kode_guru') is-invalid @enderror"
                                           id="kode_guru"
                                           name="kode_guru"
                                           value="{{ old('kode_guru', $teacher->guruProfile->kode_guru ?? '') }}"
                                           placeholder="Contoh: AHM">
                                    @error('kode_guru')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row g-3 mt-2">
                                <div class="col-md-6">
                                    <label for="email" class="form-label fw-medium">Email <span class="text-danger">*</span></label>
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

                                <div class="col-md-6">
                                    <label for="nomor_telepon" class="form-label fw-medium">Nomor Telepon</label>
                                    <input type="tel"
                                           class="form-control @error('nomor_telepon') is-invalid @enderror"
                                           id="nomor_telepon"
                                           name="nomor_telepon"
                                           value="{{ old('nomor_telepon', $teacher->guruProfile->nomor_telepon ?? '') }}"
                                           placeholder="Contoh: 08123456789">
                                    @error('nomor_telepon')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row g-3 mt-2">
                                <div class="col-md-6">
                                    <label for="jenis_kelamin" class="form-label fw-medium">Jenis Kelamin <span class="text-danger">*</span></label>
                                    <select class="form-select @error('jenis_kelamin') is-invalid @enderror"
                                            id="jenis_kelamin"
                                            name="jenis_kelamin"
                                            required>
                                        <option value="">Pilih Jenis Kelamin</option>
                                        <option value="L" {{ old('jenis_kelamin', $teacher->guruProfile->jenis_kelamin ?? '') === 'L' ? 'selected' : '' }}>
                                            Laki-laki
                                        </option>
                                        <option value="P" {{ old('jenis_kelamin', $teacher->guruProfile->jenis_kelamin ?? '') === 'P' ? 'selected' : '' }}>
                                            Perempuan
                                        </option>
                                    </select>
                                    @error('jenis_kelamin')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="profile_photo" class="form-label fw-medium">Foto Profil Baru</label>
                                    <input type="file"
                                           class="form-control @error('profile_photo') is-invalid @enderror"
                                           id="profile_photo"
                                           name="profile_photo"
                                           accept="image/jpeg,image/png,image/jpg"
                                           onchange="previewPhoto(this)">
                                    <div class="form-text">Format: JPG, PNG. Maksimal 2MB. Kosongkan jika tidak ingin mengubah foto.</div>
                                    @error('profile_photo')
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
                                           value="{{ old('tempat_lahir', $teacher->guruProfile->tempat_lahir ?? '') }}"
                                           placeholder="Contoh: Jakarta">
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
                                           value="{{ old('tanggal_lahir', $teacher->guruProfile->tanggal_lahir ? $teacher->guruProfile->tanggal_lahir->format('Y-m-d') : '') }}">
                                    @error('tanggal_lahir')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Professional Information Section -->
                        <div class="mb-5">
                            <h6 class="text-primary mb-3 fw-semibold">
                                <i class="fas fa-briefcase me-2"></i>Informasi Kepegawaian
                            </h6>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="jabatan_di_sekolah" class="form-label fw-medium">Jabatan di Sekolah</label>
                                    <select class="form-select @error('jabatan_di_sekolah') is-invalid @enderror"
                                            id="jabatan_di_sekolah" name="jabatan_di_sekolah">
                                        <option value="">Pilih Jabatan (Opsional)</option>
                                        @php
                                        $jabatanList = [
                                            'Kepala Sekolah',
                                            'Wakil Kepala Sekolah Kurikulum',
                                            'Wakil Kepala Sekolah Kesiswaan',
                                            'Wakil Kepala Sekolah Sarana Prasarana',
                                            'Wakil Kepala Sekolah Humas',
                                            'Koordinator BK',
                                            'Guru',
                                            'Guru BK',
                                            'Kepala Laboratorium',
                                            'Kepala Perpustakaan',
                                            'Staff TU',
                                            'Operator Sekolah',
                                            'Petugas Kebersihan',
                                            'Petugas Keamanan'
                                        ];
                                        $currentJabatan = old('jabatan_di_sekolah', $teacher->guruProfile->jabatan_di_sekolah ?? '');
                                        @endphp
                                        @foreach($jabatanList as $jabatan)
                                        <option value="{{ $jabatan }}" {{ $currentJabatan === $jabatan ? 'selected' : '' }}>
                                            {{ $jabatan }}
                                        </option>
                                        @endforeach
                                    </select>
                                    <div class="form-text">Kosongkan jika hanya sebagai Guru biasa</div>
                                    @error('jabatan_di_sekolah')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="mata_pelajaran" class="form-label fw-medium">Mata Pelajaran (opsional)</label>
                                    <select class="form-select select2 @error('mata_pelajaran') is-invalid @enderror"
                                            id="mata_pelajaran"
                                            name="mata_pelajaran">
                                        <option value="">Pilih Mata Pelajaran</option>
                                        @foreach($mataPelajarans as $mapel)
                                            <option value="{{ $mapel->id }}" {{ old('mata_pelajaran', $teacher->guruProfile->mata_pelajaran_id) == $mapel->id ? 'selected' : '' }}>
                                                {{ $mapel->nama_mapel }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="form-text">Pilih satu mata pelajaran utama</div>
                                    @error('mata_pelajaran')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="status_kepegawaian" class="form-label fw-medium">Status Kepegawaian</label>
                                    <select class="form-select @error('status_kepegawaian') is-invalid @enderror"
                                            id="status_kepegawaian"
                                            name="status_kepegawaian">
                                        <option value="">Pilih Status Kepegawaian (Opsional)</option>
                                        <option value="PNS" {{ old('status_kepegawaian', $teacher->guruProfile->status_kepegawaian ?? '') === 'PNS' ? 'selected' : '' }}>
                                            PNS
                                        </option>
                                        <option value="PPPK" {{ old('status_kepegawaian', $teacher->guruProfile->status_kepegawaian ?? '') === 'PPPK' ? 'selected' : '' }}>
                                            PPPK
                                        </option>
                                        <option value="HONORER" {{ old('status_kepegawaian', $teacher->guruProfile->status_kepegawaian ?? '') === 'HONORER' ? 'selected' : '' }}>
                                            HONORER
                                        </option>
                                    </select>
                                    @error('status_kepegawaian')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="golongan" class="form-label fw-medium">Golongan</label>
                                    <input type="text" class="form-control @error('golongan') is-invalid @enderror"
                                           id="golongan" name="golongan" value="{{ old('golongan', $teacher->guruProfile->golongan ?? '') }}" placeholder="Contoh: III/a">
                                    <div class="form-text">Kosongkan jika Honorer</div>
                                    @error('golongan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row g-3 mt-2">
                                <div class="col-md-6">
                                    <label for="kelas_id" class="form-label fw-medium">Wali Kelas</label>
                                    <select class="form-select @error('kelas_id') is-invalid @enderror"
                                            id="kelas_id"
                                            name="kelas_id">
                                        <option value="">Pilih Kelas (Opsional)</option>
                                        @php
                                            $currentKelasId = old('kelas_id', $teacher->guruProfile->kelas_id ?? '');
                                        @endphp
                                        @foreach($kelasList as $kelas)
                                            <option value="{{ $kelas->id }}" {{ $currentKelasId == $kelas->id ? 'selected' : '' }}>
                                                {{ $kelas->tingkat }} {{ $kelas->nama_kelas }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="form-text">Kosongkan jika tidak menjadi wali kelas</div>
                                    @error('kelas_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Password Section (Optional) -->
                        <div class="mb-5">
                            <h6 class="text-primary mb-3 fw-semibold">
                                <i class="fas fa-lock me-2"></i>Ubah Password (Opsional)
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
                                    <label for="password_confirmation" class="form-label fw-medium">Konfirmasi Password Baru</label>
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
                                    <a href="{{ route('admin.guru.show', $teacher->id) }}" class="btn btn-secondary">
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

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
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
                newImg.className = 'rounded-circle border';
                newImg.id = 'currentPhoto';
                newImg.style.cssText = 'width: 150px; height: 150px; object-fit: cover;';
                currentPhoto.parentNode.replaceChild(newImg, currentPhoto);
            }
        }

        reader.readAsDataURL(input.files[0]);
    }
}

// Form validation and autocomplete
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Select2
    $('.select2').select2({
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: 'Pilih Mata Pelajaran'
    });

    const form = document.getElementById('teacherForm');
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

    const statusKepegawaian = document.getElementById('status_kepegawaian');
    const golonganField = document.getElementById('golongan');

    // Handle golongan field based on status kepegawaian
    statusKepegawaian.addEventListener('change', function() {
        if (this.value === 'HONORER') {
            golonganField.value = '';
            golonganField.disabled = true;
        } else {
            golonganField.disabled = false;
        }
    });

    // Trigger change on load
    statusKepegawaian.dispatchEvent(new Event('change'));
});
</script>
@endsection
