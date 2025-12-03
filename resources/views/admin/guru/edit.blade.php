@extends('layouts.app')

@section('title', 'Edit Guru')

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
                                <div class="col-md-6">
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

                                <div class="col-md-6">
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
                                    <label for="mata_pelajaran" class="form-label fw-medium">Mata Pelajaran</label>
                                    @php
                                        $currentMapel = old('mata_pelajaran', $teacher->guruProfile->mata_pelajaran[0] ?? '');
                                    @endphp
                                    <input type="text"
                                           class="form-control @error('mata_pelajaran') is-invalid @enderror"
                                           id="mata_pelajaran"
                                           name="mata_pelajaran_display"
                                           placeholder="Ketik nama mata pelajaran..."
                                           value="{{ old('mata_pelajaran_display', $currentMapel) }}"
                                           autocomplete="off">
                                    <input type="hidden" id="mata_pelajaran_hidden" name="mata_pelajaran" value="{{ old('mata_pelajaran', $currentMapel) }}">
                                    <div id="mata-pelajaran-dropdown" class="dropdown-menu w-100" style="max-height: 200px; overflow-y: auto;"></div>
                                    <div class="form-text">Kosongkan jika Guru BK atau jabatan non-pengajar</div>
                                    @error('mata_pelajaran')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
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
                                        <option value="GTT" {{ old('status_kepegawaian', $teacher->guruProfile->status_kepegawaian ?? '') === 'GTT' ? 'selected' : '' }}>
                                            GTT (Guru Tidak Tetap)
                                        </option>
                                        <option value="GTY" {{ old('status_kepegawaian', $teacher->guruProfile->status_kepegawaian ?? '') === 'GTY' ? 'selected' : '' }}>
                                            GTY (Guru Tetap Yayasan)
                                        </option>
                                        <option value="GTK" {{ old('status_kepegawaian', $teacher->guruProfile->status_kepegawaian ?? '') === 'GTK' ? 'selected' : '' }}>
                                            GTK (Guru Tenaga Kependidikan)
                                        </option>
                                    </select>
                                    @error('status_kepegawaian')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row g-3 mt-2">
                                <div class="col-md-6">
                                    <label for="kelas_id" class="form-label fw-medium">Wali Kelas</label>
                                    @php
                                        $currentKelas = $teacher->guruProfile->kelas ?? null;
                                        $currentKelasDisplay = $currentKelas ? "{$currentKelas->tingkat} {$currentKelas->nama_kelas}" : '';
                                    @endphp
                                    <input type="text"
                                           class="form-control @error('kelas_id') is-invalid @enderror"
                                           id="kelas_id"
                                           name="kelas_display"
                                           placeholder="Ketik tingkat/nama kelas (misal: 7 atau 7A)..."
                                           value="{{ old('kelas_display', $currentKelasDisplay) }}"
                                           autocomplete="off">
                                    <input type="hidden" id="kelas_id_hidden" name="kelas_id" value="{{ old('kelas_id', $teacher->guruProfile->kelas_id ?? '') }}">
                                    <div id="kelas-dropdown" class="dropdown-menu w-100" style="max-height: 200px; overflow-y: auto;"></div>
                                    <div class="form-text">Kosongkan jika tidak menjadi wali kelas</div>
                                    @error('kelas_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
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

    // Autocomplete for Mata Pelajaran
    const mataPelajaranInput = document.getElementById('mata_pelajaran');
    const mataPelajaranHidden = document.getElementById('mata_pelajaran_hidden');
    const mataPelajaranDropdown = document.getElementById('mata-pelajaran-dropdown');
    let mataPelajaranTimeout;

    mataPelajaranInput.addEventListener('input', function() {
        clearTimeout(mataPelajaranTimeout);
        const query = this.value.trim();

        if (query.length < 1) {
            mataPelajaranDropdown.classList.remove('show');
            return;
        }

        mataPelajaranTimeout = setTimeout(() => {
            fetch(`{{ route('admin.api.mata-pelajaran') }}?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    mataPelajaranDropdown.innerHTML = '';

                    if (data.length === 0) {
                        mataPelajaranDropdown.innerHTML = '<div class="dropdown-item text-muted">Tidak ada mata pelajaran ditemukan</div>';
                    } else {
                        data.forEach(mapel => {
                            const item = document.createElement('a');
                            item.className = 'dropdown-item';
                            item.href = '#';
                            item.textContent = `${mapel.nama_mapel} (${mapel.kode_mapel})`;
                            item.addEventListener('click', function(e) {
                                e.preventDefault();
                                mataPelajaranInput.value = mapel.nama_mapel;
                                mataPelajaranHidden.value = mapel.nama_mapel;
                                mataPelajaranDropdown.classList.remove('show');
                            });
                            mataPelajaranDropdown.appendChild(item);
                        });
                    }

                    mataPelajaranDropdown.classList.add('show');
                })
                .catch(error => {
                    console.error('Error fetching mata pelajaran:', error);
                });
        }, 300);
    });

    // Autocomplete for Kelas
    const kelasInput = document.getElementById('kelas_id');
    const kelasHidden = document.getElementById('kelas_id_hidden');
    const kelasDropdown = document.getElementById('kelas-dropdown');
    let kelasTimeout;

    kelasInput.addEventListener('input', function() {
        clearTimeout(kelasTimeout);
        const query = this.value.trim();

        if (query.length < 1) {
            kelasDropdown.classList.remove('show');
            return;
        }

        kelasTimeout = setTimeout(() => {
            fetch(`{{ route('admin.api.kelas') }}?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    kelasDropdown.innerHTML = '';

                    if (data.length === 0) {
                        kelasDropdown.innerHTML = '<div class="dropdown-item text-muted">Tidak ada kelas ditemukan</div>';
                    } else {
                        data.forEach(kelas => {
                            const item = document.createElement('a');
                            item.className = 'dropdown-item';
                            item.href = '#';
                            item.textContent = `Kelas ${kelas.tingkat} ${kelas.nama_kelas}`;
                            item.addEventListener('click', function(e) {
                                e.preventDefault();
                                kelasInput.value = `${kelas.tingkat} ${kelas.nama_kelas}`;
                                kelasHidden.value = kelas.id;
                                kelasDropdown.classList.remove('show');
                            });
                            kelasDropdown.appendChild(item);
                        });
                    }

                    kelasDropdown.classList.add('show');
                })
                .catch(error => {
                    console.error('Error fetching kelas:', error);
                });
        }, 300);
    });

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (!mataPelajaranInput.contains(e.target) && !mataPelajaranDropdown.contains(e.target)) {
            mataPelajaranDropdown.classList.remove('show');
        }
        if (!kelasInput.contains(e.target) && !kelasDropdown.contains(e.target)) {
            kelasDropdown.classList.remove('show');
        }
    });
});
</script>
@endsection
