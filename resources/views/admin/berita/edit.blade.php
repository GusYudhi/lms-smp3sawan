@extends('layouts.app')

@section('title', 'Edit Berita')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Berita</h1>
        <a href="{{ route('admin.berita.index') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form id="beritaEditForm" action="{{ route('admin.berita.update', $berita->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Real Form File Inputs (populated on submit) -->
                <input type="file" id="real_foto" name="foto" class="d-none" accept="image/*">
                <input type="file" id="real_foto_tambahan" name="foto_tambahan[]" class="d-none" accept="image/*" multiple>

                <div class="mb-3">
                    <label for="judul" class="form-label fw-semibold">Judul Berita <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('judul') is-invalid @enderror" id="judul" name="judul" value="{{ old('judul', $berita->judul) }}" required>
                    @error('judul')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="tanggal" class="form-label fw-semibold">Tanggal <span class="text-danger">*</span></label>
                    <input type="date" class="form-control @error('tanggal') is-invalid @enderror" id="tanggal" name="tanggal" value="{{ old('tanggal', $berita->tanggal ? $berita->tanggal->format('Y-m-d') : '') }}" required>
                    @error('tanggal')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Unified Image Gallery & Uploader -->
                <div class="mb-4">
                    <label class="form-label fw-semibold d-flex justify-content-between align-items-center">
                        <span>Galeri Foto Berita</span>
                        <span class="badge bg-secondary" id="totalImageCountBadge">Memuat foto...</span>
                    </label>

                    <input type="file" id="unified_file_input" class="d-none" accept="image/jpeg,image/png,image/jpg,image/webp,image/heic,image/heif" multiple>

                    <!-- Dropzone for adding new photos -->
                    <div id="dropzoneBox" class="border border-2 border-dashed rounded p-3 text-center bg-light mb-3" role="button" tabindex="0">
                        <i class="fas fa-cloud-upload-alt fa-2x text-primary mb-1"></i>
                        <h6 class="fw-bold mb-0">Klik atau seret foto ke area ini untuk menambah foto baru</h6>
                        <p class="text-muted small mb-0">Mendukung format JPG, PNG, WEBP, HEIC (Maksimal 10MB per foto).</p>
                    </div>

                    @error('foto')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                    @error('foto_tambahan.*')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror

                    <!-- Unified Photo Grid -->
                    <div id="unifiedGallery" class="row g-2">
                        <!-- 1. Existing Main Photo -->
                        @if($berita->foto)
                            <div class="col-6 col-sm-4 col-md-3 col-lg-2" id="existing-main-box">
                                <div class="card h-100 shadow-sm border-primary border-2 position-relative">
                                    <div class="ratio ratio-4x3 bg-light">
                                        <img src="{{ asset('storage/' . $berita->foto) }}" class="rounded-top object-fit-cover w-100 h-100" alt="Sampul Utama">
                                    </div>
                                    <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 rounded-circle d-flex align-items-center justify-content-center p-0 delete-main-btn"
                                            style="width: 24px; height: 24px;"
                                            data-url="{{ route('admin.berita.delete-main-image', $berita->id) }}"
                                            title="Hapus foto sampul ini">
                                        &times;
                                    </button>
                                    <div class="card-body p-2 d-flex flex-column justify-content-between">
                                        <div>
                                            <span class="badge bg-primary w-100 mb-1" id="mainPhotoBadge">
                                                <i class="fas fa-star me-1"></i>Sampul Utama
                                            </span>
                                            <small class="text-muted d-block text-truncate">Tersimpan di server</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- 2. Existing Extra Photos -->
                        @if($berita->images && $berita->images->count() > 0)
                            @foreach($berita->images as $img)
                                <div class="col-6 col-sm-4 col-md-3 col-lg-2 existing-extra-col" id="extra-box-{{ $img->id }}">
                                    <div class="card h-100 shadow-sm border position-relative">
                                        <div class="ratio ratio-4x3 bg-light">
                                            <img src="{{ asset('storage/' . $img->foto) }}" class="rounded-top object-fit-cover w-100 h-100" alt="Dokumentasi">
                                        </div>
                                        <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 rounded-circle d-flex align-items-center justify-content-center p-0 delete-extra-btn"
                                                style="width: 24px; height: 24px;"
                                                data-url="{{ route('admin.berita.delete-image', ['id' => $berita->id, 'imageId' => $img->id]) }}"
                                                data-target="#extra-box-{{ $img->id }}"
                                                title="Hapus foto ini">
                                            &times;
                                        </button>
                                        <div class="card-body p-2 d-flex flex-column justify-content-between">
                                            <div>
                                                <span class="badge bg-secondary w-100 mb-1">Foto Galeri</span>
                                                <small class="text-muted d-block text-truncate">Tersimpan di server</small>
                                            </div>
                                            <button type="button" class="btn btn-outline-primary btn-sm w-100 py-0 px-1 mt-2 make-existing-main-btn"
                                                    data-url="{{ route('admin.berita.set-main-image', ['id' => $berita->id, 'imageId' => $img->id]) }}">
                                                <i class="fas fa-star me-1"></i>Jadikan Sampul
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif

                        <!-- 3. Dynamic container for newly added files -->
                        <div id="newFilesContainer" class="contents d-flex flex-wrap p-0 m-0 w-100"></div>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="konten" class="form-label fw-semibold">Konten Berita <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('konten') is-invalid @enderror" id="konten" name="konten" rows="10" required>{{ old('konten', $berita->konten) }}</textarea>
                    @error('konten')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.berita.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const fileInput = document.getElementById('unified_file_input');
    const dropzoneBox = document.getElementById('dropzoneBox');
    const newFilesContainer = document.getElementById('newFilesContainer');
    const totalCountBadge = document.getElementById('totalImageCountBadge');
    const form = document.getElementById('beritaEditForm');
    const realFoto = document.getElementById('real_foto');
    const realFotoTambahan = document.getElementById('real_foto_tambahan');

    let newSelectedFiles = []; // Array of new File objects
    let newMainIndex = -1; // If a new file is chosen as main cover (-1 means none, 0..N)

    updateTotalCount();

    function hasExistingMain() {
        return !!document.getElementById('existing-main-box');
    }

    function updateTotalCount() {
        const existingCount = (hasExistingMain() ? 1 : 0) + document.querySelectorAll('.existing-extra-col').length;
        const total = existingCount + newSelectedFiles.length;
        totalCountBadge.textContent = `${total} Foto Total (${existingCount} Tersimpan, ${newSelectedFiles.length} Baru)`;
    }

    dropzoneBox.addEventListener('click', () => fileInput.click());
    dropzoneBox.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            fileInput.click();
        }
    });

    ['dragenter', 'dragover'].forEach(eventName => {
        dropzoneBox.addEventListener(eventName, (e) => {
            e.preventDefault();
            e.stopPropagation();
            dropzoneBox.classList.add('border-primary', 'bg-primary', 'bg-opacity-10');
        });
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropzoneBox.addEventListener(eventName, (e) => {
            e.preventDefault();
            e.stopPropagation();
            dropzoneBox.classList.remove('border-primary', 'bg-primary', 'bg-opacity-10');
        });
    });

    dropzoneBox.addEventListener('drop', (e) => {
        const dt = e.dataTransfer;
        if (dt && dt.files.length) {
            handleNewFiles(dt.files);
        }
    });

    fileInput.addEventListener('change', () => {
        if (fileInput.files.length) {
            handleNewFiles(fileInput.files);
            fileInput.value = '';
        }
    });

    function handleNewFiles(files) {
        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            if (file.type.startsWith('image/') || /\.(heic|heif)$/i.test(file.name)) {
                newSelectedFiles.push(file);
            }
        }
        // If there's no existing main photo and no new main photo chosen yet, first new photo becomes main
        if (!hasExistingMain() && newMainIndex === -1 && newSelectedFiles.length > 0) {
            newMainIndex = 0;
        }
        renderNewFilesGallery();
        updateTotalCount();
    }

    function renderNewFilesGallery() {
        newFilesContainer.innerHTML = '';

        newSelectedFiles.forEach((file, index) => {
            const isMain = (!hasExistingMain() && newMainIndex === index) || (newMainIndex === index);
            const col = document.createElement('div');
            col.className = 'col-6 col-sm-4 col-md-3 col-lg-2';

            const card = document.createElement('div');
            card.className = `card h-100 shadow-sm position-relative ${isMain ? 'border-primary border-2' : 'border'}`;

            const objectUrl = URL.createObjectURL(file);

            card.innerHTML = `
                <div class="ratio ratio-4x3 bg-light">
                    <img src="${objectUrl}" class="rounded-top object-fit-cover w-100 h-100" alt="${file.name}">
                </div>
                <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 rounded-circle d-flex align-items-center justify-content-center p-0 remove-new-file-btn"
                        style="width: 24px; height: 24px;" data-index="${index}" title="Hapus foto ini">
                    &times;
                </button>
                <div class="card-body p-2 d-flex flex-column justify-content-between">
                    <div>
                        <span class="badge ${isMain ? 'bg-primary' : 'bg-success'} w-100 mb-1">
                            ${isMain ? '<i class="fas fa-star me-1"></i>Sampul Utama (Baru)' : 'Foto Baru'}
                        </span>
                        <small class="text-muted d-block text-truncate" title="${file.name}">${file.name}</small>
                    </div>
                    ${!isMain ? `
                        <button type="button" class="btn btn-outline-primary btn-sm w-100 py-0 px-1 mt-2 make-new-cover-btn" data-index="${index}">
                            <i class="fas fa-star me-1"></i>Jadikan Sampul
                        </button>
                    ` : ''}
                </div>
            `;

            col.appendChild(card);
            newFilesContainer.appendChild(col);
        });

        // Event listeners for new files
        newFilesContainer.querySelectorAll('.remove-new-file-btn').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                const idx = parseInt(this.dataset.index, 10);
                newSelectedFiles.splice(idx, 1);
                if (newMainIndex === idx) {
                    newMainIndex = (!hasExistingMain() && newSelectedFiles.length > 0) ? 0 : -1;
                } else if (newMainIndex > idx) {
                    newMainIndex--;
                }
                renderNewFilesGallery();
                updateTotalCount();
            });
        });

        newFilesContainer.querySelectorAll('.make-new-cover-btn').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                const idx = parseInt(this.dataset.index, 10);
                newMainIndex = idx;
                renderNewFilesGallery();
            });
        });
    }

    // AJAX: Delete Existing Extra Image
    document.querySelectorAll('.delete-extra-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const url = this.dataset.url;
            const targetSelector = this.dataset.target;
            Swal.fire({
                title: 'Hapus foto galeri ini?',
                text: 'Foto ini akan dihapus permanen dari server.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((res) => {
                if (res.isConfirmed) {
                    fetch(url, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            const elem = document.querySelector(targetSelector);
                            if (elem) elem.remove();
                            updateTotalCount();
                            Swal.fire({
                                icon: 'success',
                                title: 'Terhapus',
                                text: data.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                        }
                    })
                    .catch(() => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Terjadi kesalahan saat menghapus foto.'
                        });
                    });
                }
            });
        });
    });

    // AJAX: Delete Existing Main Photo
    const deleteMainBtn = document.querySelector('.delete-main-btn');
    if (deleteMainBtn) {
        deleteMainBtn.addEventListener('click', function () {
            const url = this.dataset.url;
            Swal.fire({
                title: 'Hapus foto sampul utama?',
                text: 'Foto sampul ini akan dihapus permanen dari server. Jika ada foto galeri lain, foto galeri pertama otomatis menjadi sampul baru.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus Sampul',
                cancelButtonText: 'Batal'
            }).then((res) => {
                if (res.isConfirmed) {
                    fetch(url, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: data.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.reload();
                            });
                        }
                    })
                    .catch(() => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Terjadi kesalahan saat menghapus foto sampul.'
                        });
                    });
                }
            });
        });
    }

    // AJAX: Set Existing Extra Image as Main Photo
    document.querySelectorAll('.make-existing-main-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const url = this.dataset.url;
            Swal.fire({
                title: 'Jadikan Sampul Utama?',
                text: 'Foto galeri ini akan dijadikan sebagai Sampul Utama berita.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0d6efd',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Jadikan Sampul',
                cancelButtonText: 'Batal'
            }).then((res) => {
                if (res.isConfirmed) {
                    fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: data.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.reload();
                            });
                        }
                    })
                    .catch(() => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Terjadi kesalahan saat mengubah foto sampul.'
                        });
                    });
                }
            });
        });
    });

    // Form submit: sync DataTransfer
    form.addEventListener('submit', function (e) {
        if (newSelectedFiles.length > 0) {
            // Check if user set a new photo as main, or if there is no existing main photo
            if (newMainIndex >= 0 && newMainIndex < newSelectedFiles.length) {
                const dtMain = new DataTransfer();
                dtMain.items.add(newSelectedFiles[newMainIndex]);
                realFoto.files = dtMain.files;

                const dtExtra = new DataTransfer();
                newSelectedFiles.forEach((f, idx) => {
                    if (idx !== newMainIndex) {
                        dtExtra.items.add(f);
                    }
                });
                if (dtExtra.files.length > 0) {
                    realFotoTambahan.files = dtExtra.files;
                } else {
                    realFotoTambahan.value = '';
                }
            } else {
                // Keep existing main photo, put all new files into foto_tambahan[]
                realFoto.value = '';
                const dtExtra = new DataTransfer();
                newSelectedFiles.forEach(f => dtExtra.items.add(f));
                realFotoTambahan.files = dtExtra.files;
            }
        } else {
            realFoto.value = '';
            realFotoTambahan.value = '';
        }
    });
});
</script>
@endpush
@endsection
