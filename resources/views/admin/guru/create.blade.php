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
                                    <label for="nomor_telepon" class="form-label fw-medium">Nomor Telepon (opsional)</label>
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
                                    <label for="tempat_lahir" class="form-label fw-medium">Tempat Lahir (opsional)</label>
                                    <input type="text" class="form-control @error('tempat_lahir') is-invalid @enderror"
                                           id="tempat_lahir" name="tempat_lahir" value="{{ old('tempat_lahir') }}">
                                    @error('tempat_lahir')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row g-3 mt-2">
                                <div class="col-md-6">
                                    <label for="tanggal_lahir" class="form-label fw-medium">Tanggal Lahir (opsional)</label>
                                    <input type="date" class="form-control @error('tanggal_lahir') is-invalid @enderror"
                                           id="tanggal_lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}">
                                    @error('tanggal_lahir')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="profile_photo" class="form-label fw-medium">Foto Profil (opsional)</label>
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

                                        <option value="HONORER" {{ old('status_kepegawaian') == 'HONORER' ? 'selected' : '' }}>HONORER</option>
                                    </select>
                                    @error('status_kepegawaian')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="golongan" class="form-label fw-medium">Golongan (opsional)</label>
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
                                    <label for="jabatan_di_sekolah" class="form-label fw-medium">Jabatan di Sekolah <span class="text-danger">*</span></label>
                                    <select class="form-select @error('jabatan_di_sekolah') is-invalid @enderror"
                                            id="jabatan_di_sekolah" name="jabatan_di_sekolah">
                                        <option value="">Pilih Jabatan</option>
                                        <option value="Kepala Sekolah" {{ old('jabatan_di_sekolah') == 'Kepala Sekolah' ? 'selected' : '' }}>Kepala Sekolah</option>
                                        <option value="Wakil Kepala Sekolah Kurikulum" {{ old('jabatan_di_sekolah') == 'Wakil Kepala Sekolah Kurikulum' ? 'selected' : '' }}>Wakil Kepala Sekolah Kurikulum</option>
                                        <option value="Wakil Kepala Sekolah Kesiswaan" {{ old('jabatan_di_sekolah') == 'Wakil Kepala Sekolah Kesiswaan' ? 'selected' : '' }}>Wakil Kepala Sekolah Kesiswaan</option>
                                        <option value="Wakil Kepala Sekolah Sarana Prasarana" {{ old('jabatan_di_sekolah') == 'Wakil Kepala Sekolah Sarana Prasarana' ? 'selected' : '' }}>Wakil Kepala Sekolah Sarana Prasarana</option>
                                        <option value="Wakil Kepala Sekolah Humas" {{ old('jabatan_di_sekolah') == 'Wakil Kepala Sekolah Humas' ? 'selected' : '' }}>Wakil Kepala Sekolah Humas</option>
                                        <option value="Koordinator BK" {{ old('jabatan_di_sekolah') == 'Koordinator BK' ? 'selected' : '' }}>Koordinator BK</option>
                                        <option value="Guru" {{ old('jabatan_di_sekolah') == 'Guru' ? 'selected' : '' }}>Guru</option>
                                        <option value="Guru BK" {{ old('jabatan_di_sekolah') == 'Guru BK' ? 'selected' : '' }}>Guru BK (Bimbingan Konseling)</option>
                                        <option value="Kepala Laboratorium" {{ old('jabatan_di_sekolah') == 'Kepala Laboratorium' ? 'selected' : '' }}>Kepala Laboratorium</option>
                                        <option value="Kepala Perpustakaan" {{ old('jabatan_di_sekolah') == 'Kepala Perpustakaan' ? 'selected' : '' }}>Kepala Perpustakaan</option>
                                        <option value="Staff TU" {{ old('jabatan_di_sekolah') == 'Staff TU' ? 'selected' : '' }}>Staff TU (Tata Usaha)</option>
                                        <option value="Operator Sekolah" {{ old('jabatan_di_sekolah') == 'Operator Sekolah' ? 'selected' : '' }}>Operator Sekolah</option>
                                        <option value="Petugas Kebersihan" {{ old('jabatan_di_sekolah') == 'Petugas Kebersihan' ? 'selected' : '' }}>Petugas Kebersihan</option>
                                        <option value="Petugas Keamanan" {{ old('jabatan_di_sekolah') == 'Petugas Keamanan' ? 'selected' : '' }}>Petugas Keamanan</option>
                                    </select>
                                    <div class="form-text">Kosongkan jika hanya sebagai Guru biasa</div>
                                    @error('jabatan_di_sekolah')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="mata_pelajaran" class="form-label fw-medium">Mata Pelajaran (opsional)</label>
                                    <input type="text"
                                           class="form-control @error('mata_pelajaran') is-invalid @enderror"
                                           id="mata_pelajaran"
                                           name="mata_pelajaran_display"
                                           placeholder="Ketik nama mata pelajaran..."
                                           value="{{ old('mata_pelajaran_display') }}"
                                           autocomplete="off">
                                    <input type="hidden" id="mata_pelajaran_hidden" name="mata_pelajaran" value="{{ old('mata_pelajaran') }}">
                                    <div id="mata-pelajaran-dropdown" class="dropdown-menu w-100" style="max-height: 200px; overflow-y: auto;"></div>
                                    <div class="form-text">Kosongkan jika Guru BK atau jabatan non-pengajar</div>
                                    @error('mata_pelajaran')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="kelas_id" class="form-label fw-medium">Wali Kelas (opsional)</label>
                                    <input type="text"
                                           class="form-control @error('kelas_id') is-invalid @enderror"
                                           id="kelas_id"
                                           name="kelas_display"
                                           placeholder="Ketik tingkat/nama kelas (misal: 7 atau 7A)..."
                                           value="{{ old('kelas_display') }}"
                                           autocomplete="off">
                                    <input type="hidden" id="kelas_id_hidden" name="kelas_id" value="{{ old('kelas_id') }}">
                                    <div id="kelas-dropdown" class="dropdown-menu w-100" style="max-height: 200px; overflow-y: auto;"></div>
                                    <div class="form-text">Kosongkan jika tidak menjadi wali kelas</div>
                                    @error('kelas_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
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
        if (this.value === 'HONORER') {
            golonganField.value = '';
            golonganField.disabled = true;
        } else {
            golonganField.disabled = false;
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
