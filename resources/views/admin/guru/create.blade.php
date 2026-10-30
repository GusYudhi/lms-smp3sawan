@extends('layouts.app')

@push('styles')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
@endpush

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
                                <div class="col-md-4">
                                    <label for="name" class="form-label fw-medium">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                           id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label for="nomor_induk" class="form-label fw-medium">NIP/NIK <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('nomor_induk') is-invalid @enderror"
                                           id="nomor_induk" name="nomor_induk" value="{{ old('nomor_induk') }}" required>
                                    @error('nomor_induk')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label for="kode_guru" class="form-label fw-medium">Kode Guru</label>
                                    <input type="text" class="form-control @error('kode_guru') is-invalid @enderror"
                                           id="kode_guru" name="kode_guru" value="{{ old('kode_guru') }}" placeholder="Contoh: AHM">
                                    <div class="form-text">Kode singkat untuk jadwal (Opsional)</div>
                                    @error('kode_guru')
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
                                    <input type="text" class="form-control @error('golongan') is-invalid @enderror"
                                           id="golongan" name="golongan" value="{{ old('golongan') }}" placeholder="Contoh: III/a">
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
                                    @php
                                        $selectedMapelIds = old('mata_pelajaran', []);
                                        if (!is_array($selectedMapelIds)) {
                                            $selectedMapelIds = $selectedMapelIds ? [(int) $selectedMapelIds] : [];
                                        } else {
                                            $selectedMapelIds = array_map('intval', $selectedMapelIds);
                                        }
                                        $selectedMapelIds = array_values(array_unique(array_filter($selectedMapelIds)));

                                        $mapelById = $mataPelajarans->keyBy('id');
                                        $selectedMapels = collect();
                                        foreach ($selectedMapelIds as $sId) {
                                            if ($mapelById->has($sId)) {
                                                $selectedMapels->push($mapelById->get($sId));
                                            }
                                        }
                                        $unselectedMapels = $mataPelajarans->reject(function($item) use ($selectedMapelIds) {
                                            return in_array($item->id, $selectedMapelIds);
                                        });
                                    @endphp
                                    <select class="form-select select2-multiple @error('mata_pelajaran') is-invalid @enderror"
                                            id="mata_pelajaran" name="mata_pelajaran[]" multiple="multiple">
                                        @foreach($selectedMapels as $mapel)
                                            <option value="{{ $mapel->id }}" selected>
                                                {{ $mapel->nama_mapel }}
                                            </option>
                                        @endforeach
                                        @foreach($unselectedMapels as $mapel)
                                            <option value="{{ $mapel->id }}">
                                                {{ $mapel->nama_mapel }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="form-text d-flex align-items-center justify-content-between mt-1">
                                        <span>Bisa memilih lebih dari 1 mata pelajaran (pilihan pertama sebagai <strong>Mapel Utama</strong>). Geser untuk menukar urutan.</span>
                                    </div>
                                    @error('mata_pelajaran')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    @error('mata_pelajaran.*')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row g-3 mt-2">
                                <div class="col-md-6">
                                    <label for="kelas_id" class="form-label fw-medium">Wali Kelas (opsional)</label>
                                    <select class="form-select @error('kelas_id') is-invalid @enderror"
                                            id="kelas_id" name="kelas_id">
                                        <option value="">Pilih Kelas</option>
                                        @foreach($kelasList as $kelas)
                                            <option value="{{ $kelas->id }}" {{ old('kelas_id') == $kelas->id ? 'selected' : '' }}>
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

                        <!-- Content Management Permissions Section -->
                        <div class="mb-5">
                            <h6 class="text-primary mb-3 fw-semibold">
                                <i class="fas fa-user-shield me-2"></i>Hak Akses Pengelolaan Konten (Opsional)
                            </h6>
                            <div class="p-3 border rounded bg-light">
                                <p class="text-muted small mb-3">
                                    Berikan wewenang kepada guru ini untuk mengelola publikasi sekolah langsung dari dashboard guru.
                                </p>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" role="switch" 
                                                   id="can_manage_berita" name="can_manage_berita" value="1"
                                                   {{ old('can_manage_berita') ? 'checked' : '' }}>
                                            <label class="form-check-label fw-medium" for="can_manage_berita">
                                                <i class="fas fa-newspaper text-primary me-1"></i> Kelola Berita Sekolah
                                            </label>
                                            <div class="form-text">Mengizinkan guru menambah, mengedit, dan menghapus berita sekolah.</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" role="switch" 
                                                   id="can_manage_prestasi" name="can_manage_prestasi" value="1"
                                                   {{ old('can_manage_prestasi') ? 'checked' : '' }}>
                                            <label class="form-check-label fw-medium" for="can_manage_prestasi">
                                                <i class="fas fa-trophy text-warning me-1"></i> Kelola Prestasi Sekolah
                                            </label>
                                            <div class="form-text">Mengizinkan guru menambah, mengedit, dan menghapus prestasi sekolah.</div>
                                        </div>
                                    </div>
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
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize Select2 Multi-Select with order preservation and drag-and-drop sortable
    const $mapelSelect = $('#mata_pelajaran');
    $mapelSelect.select2({
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: 'Ketik atau pilih mata pelajaran...',
        allowClear: true,
        closeOnSelect: false
    });

    const $select2Container = $mapelSelect.next('.select2-container');
    const $renderedUl = $select2Container.find('.select2-selection__rendered');

    function updateSubjectBadges() {
        $renderedUl.find('.mapel-utama-badge').remove();
        $renderedUl.find('.select2-selection__choice').removeClass('is-primary-subject');

        const $firstChoice = $renderedUl.find('.select2-selection__choice').first();
        if ($firstChoice.length) {
            $firstChoice.addClass('is-primary-subject');
            $firstChoice.append(' <span class="badge bg-primary text-white ms-1 py-0 px-1 mapel-utama-badge">Utama</span>');
        }
    }

    // Pilihan baru selalu ditaruh di akhir DOM (tidak diacak alfabetis)
    $mapelSelect.on('select2:select', function(e) {
        const id = e.params.data.id;
        const option = $(this).children('option[value="' + id + '"]');
        option.detach();
        $(this).append(option);
        setTimeout(updateSubjectBadges, 20);
    });

    $mapelSelect.on('select2:unselect', function(e) {
        setTimeout(updateSubjectBadges, 20);
    });

    // Native Drag and Drop Reordering pada choice chips Select2
    let draggedChoice = null;
    const $selectionBox = $select2Container.find('.select2-selection--multiple');

    $renderedUl.on('dragstart', '.select2-selection__choice', function(e) {
        if ($(e.target).closest('.select2-selection__choice__remove').length) {
            e.preventDefault();
            return;
        }
        draggedChoice = this;
        e.originalEvent.dataTransfer.effectAllowed = 'move';
        $(this).addClass('dragging');
        $selectionBox.addClass('has-dragging');
    });

    $renderedUl.on('dragover', '.select2-selection__choice', function(e) {
        e.preventDefault();
        e.stopPropagation();
        e.originalEvent.dataTransfer.dropEffect = 'move';

        if (!draggedChoice || draggedChoice === this) return;

        const rect = this.getBoundingClientRect();
        const relX = e.originalEvent.clientX - rect.left;
        const isLeft = relX < (rect.width / 2);

        $renderedUl.find('.select2-selection__choice').not(this).removeClass('drag-over-left drag-over-right');
        if (isLeft) {
            $(this).addClass('drag-over-left').removeClass('drag-over-right');
        } else {
            $(this).addClass('drag-over-right').removeClass('drag-over-left');
        }
    });

    $renderedUl.on('dragleave', '.select2-selection__choice', function(e) {
        const rect = this.getBoundingClientRect();
        const x = e.originalEvent.clientX;
        const y = e.originalEvent.clientY;
        if (x < rect.left || x >= rect.right || y < rect.top || y >= rect.bottom) {
            $(this).removeClass('drag-over-left drag-over-right');
        }
    });

    $renderedUl.on('dragend', function() {
        $renderedUl.find('.select2-selection__choice').removeClass('dragging drag-over-left drag-over-right');
        $selectionBox.removeClass('has-dragging');
        draggedChoice = null;
    });

    $renderedUl.on('drop', '.select2-selection__choice', function(e) {
        e.preventDefault();
        e.stopPropagation();

        const isLeft = $(this).hasClass('drag-over-left');
        $renderedUl.find('.select2-selection__choice').removeClass('drag-over-left drag-over-right');
        $selectionBox.removeClass('has-dragging');

        if (draggedChoice && draggedChoice !== this) {
            if (isLeft) {
                $(draggedChoice).insertBefore($(this));
            } else {
                $(draggedChoice).insertAfter($(this));
            }
            syncDomOptionsOrder();
        }
        draggedChoice = null;
    });

    // Support dragover & drop di area container luar chip (misal di margin paling kiri atau area kosong)
    $renderedUl.on('dragover', function(e) {
        e.preventDefault();
        e.originalEvent.dataTransfer.dropEffect = 'move';

        if (!draggedChoice) return;

        if (!$(e.target).closest('.select2-selection__choice').length) {
            const firstChoice = $renderedUl.find('.select2-selection__choice').not(draggedChoice).first();
            if (firstChoice.length && e.originalEvent.clientX < firstChoice[0].getBoundingClientRect().left) {
                $renderedUl.find('.select2-selection__choice').removeClass('drag-over-left drag-over-right');
                firstChoice.addClass('drag-over-left');
            }
        }
    });

    $renderedUl.on('drop', function(e) {
        if (!draggedChoice) return;

        if (!$(e.target).closest('.select2-selection__choice').length) {
            e.preventDefault();
            const firstChoice = $renderedUl.find('.select2-selection__choice').not(draggedChoice).first();
            if (firstChoice.length && e.originalEvent.clientX < firstChoice[0].getBoundingClientRect().left) {
                $(draggedChoice).insertBefore(firstChoice);
                syncDomOptionsOrder();
            } else {
                const searchFieldLi = $renderedUl.find('.select2-search');
                if (searchFieldLi.length) {
                    $(draggedChoice).insertBefore(searchFieldLi);
                } else {
                    $renderedUl.append(draggedChoice);
                }
                syncDomOptionsOrder();
            }
            $renderedUl.find('.select2-selection__choice').removeClass('drag-over-left drag-over-right');
            $selectionBox.removeClass('has-dragging');
            draggedChoice = null;
        }
    });

    function syncDomOptionsOrder() {
        const orderedTitles = [];
        $renderedUl.find('.select2-selection__choice').each(function() {
            const title = $(this).attr('title');
            if (title) {
                orderedTitles.push(title.trim());
            }
        });

        orderedTitles.forEach(function(title) {
            const $opt = $mapelSelect.children('option').filter(function() {
                return $(this).text().trim() === title;
            });
            if ($opt.length) {
                $opt.detach();
                $mapelSelect.append($opt);
            }
        });

        updateSubjectBadges();
    }

    function applyDraggableToChoices() {
        $renderedUl.find('.select2-selection__choice').each(function() {
            if (!this.getAttribute('draggable')) {
                this.setAttribute('draggable', 'true');
            }
        });
    }

    if ($renderedUl.length) {
        const choiceObserver = new MutationObserver(function() {
            applyDraggableToChoices();
            updateSubjectBadges();
        });
        choiceObserver.observe($renderedUl[0], { childList: true });
        applyDraggableToChoices();
        updateSubjectBadges();
    }

    const statusKepegawaian = document.getElementById('status_kepegawaian');
    const golonganField = document.getElementById('golongan');

    if (statusKepegawaian && golonganField) {
        statusKepegawaian.addEventListener('change', function() {
            if (this.value === 'HONORER') {
                golonganField.value = '';
                golonganField.disabled = true;
            } else {
                golonganField.disabled = false;
            }
        });
        statusKepegawaian.dispatchEvent(new Event('change'));
    }

    const photoInput = document.getElementById('profile_photo');
    if (photoInput) {
        photoInput.addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.readAsDataURL(file);
            }
        });
    }

    const form = document.getElementById('teacherForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            syncDomOptionsOrder();
            const password = document.getElementById('password')?.value;
            const confirmPassword = document.getElementById('password_confirmation')?.value;

            if (password && confirmPassword) {
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
            }
        });
    }
});
</script>
@endpush
