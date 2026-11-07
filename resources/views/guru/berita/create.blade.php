@extends('layouts.app')

@section('title', 'Tambah Berita')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800">Tambah Berita Sekolah</h1>
            <p class="text-muted small mb-0">Publikasikan informasi kegiatan dan pengumuman sekolah</p>
        </div>
        <a href="{{ route('guru.berita.index') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm me-1"></i> Kembali
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form id="beritaForm" action="{{ route('guru.berita.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Real Form File Inputs (populated on submit) -->
                <input type="file" id="real_foto" name="foto" class="d-none" accept="image/*">
                <input type="file" id="real_foto_tambahan" name="foto_tambahan[]" class="d-none" accept="image/*" multiple>

                <div class="mb-3">
                    <label for="judul" class="form-label fw-semibold">Judul Berita <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('judul') is-invalid @enderror" id="judul" name="judul" value="{{ old('judul') }}" placeholder="Masukkan judul berita..." required>
                    @error('judul')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="tanggal" class="form-label fw-semibold">Tanggal <span class="text-danger">*</span></label>
                    <input type="date" class="form-control @error('tanggal') is-invalid @enderror" id="tanggal" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" required>
                    @error('tanggal')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Unified Image Uploader -->
                <div class="mb-4">
                    <label class="form-label fw-semibold d-flex justify-content-between align-items-center">
                        <span>Foto Berita & Dokumentasi Galeri</span>
                        <span class="badge bg-info text-dark" id="fileCountBadge">0 Foto Terpilih</span>
                    </label>

                    <input type="file" id="unified_file_input" class="d-none" accept="image/jpeg,image/png,image/jpg,image/webp,image/heic,image/heif" multiple>

                    <div id="dropzoneBox" class="border border-2 border-dashed rounded p-4 text-center bg-light" role="button" tabindex="0">
                        <i class="fas fa-cloud-upload-alt fa-3x text-primary mb-2"></i>
                        <h6 class="fw-bold mb-1">Klik atau seret foto ke area ini</h6>
                        <p class="text-muted small mb-0">
                            Bisa pilih banyak foto sekaligus atau bertahap. Format: JPG, PNG, WEBP, HEIC (Maksimal 10MB per foto).<br>
                            Foto pertama otomatis menjadi <strong>⭐ Sampul Utama</strong>.
                        </p>
                    </div>

                    @error('foto')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                    @error('foto_tambahan.*')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror

                    <!-- Preview Gallery Grid -->
                    <div id="previewGallery" class="row g-2 mt-3 d-none"></div>
                </div>

                <div class="mb-4">
                    <label for="konten" class="form-label fw-semibold">Konten Berita <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('konten') is-invalid @enderror" id="konten" name="konten" rows="10" placeholder="Tuliskan isi berita secara lengkap di sini..." required>{{ old('konten') }}</textarea>
                    @error('konten')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('guru.berita.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary" id="btnSubmit">
                        <i class="fas fa-save me-1"></i> Simpan Berita
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
    const previewGallery = document.getElementById('previewGallery');
    const fileCountBadge = document.getElementById('fileCountBadge');
    const form = document.getElementById('beritaForm');
    const realFoto = document.getElementById('real_foto');
    const realFotoTambahan = document.getElementById('real_foto_tambahan');

    let selectedFiles = [];

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
                selectedFiles.push(file);
            }
        }
        renderGallery();
    }

    function renderGallery() {
        previewGallery.innerHTML = '';

        if (selectedFiles.length === 0) {
            previewGallery.classList.add('d-none');
            fileCountBadge.textContent = '0 Foto Terpilih';
            return;
        }

        previewGallery.classList.remove('d-none');
        fileCountBadge.textContent = `${selectedFiles.length} Foto Terpilih`;

        selectedFiles.forEach((file, index) => {
            const isMain = (index === 0);
            const col = document.createElement('div');
            col.className = 'col-6 col-sm-4 col-md-3 col-lg-2';

            const card = document.createElement('div');
            card.className = `card h-100 shadow-sm position-relative ${isMain ? 'border-primary border-2' : 'border'}`;

            const objectUrl = URL.createObjectURL(file);

            card.innerHTML = `
                <div class="ratio ratio-4x3 bg-light">
                    <img src="${objectUrl}" class="rounded-top object-fit-cover w-100 h-100" alt="${file.name}">
                </div>
                <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 rounded-circle d-flex align-items-center justify-content-center p-0 remove-file-btn"
                        style="width: 24px; height: 24px;" data-index="${index}" title="Hapus foto ini">
                    &times;
                </button>
                <div class="card-body p-2 d-flex flex-column justify-content-between">
                    <div>
                        <span class="badge ${isMain ? 'bg-primary' : 'bg-secondary'} w-100 mb-1">
                            ${isMain ? '<i class="fas fa-star me-1"></i>Sampul Utama' : 'Galeri #' + index}
                        </span>
                        <small class="text-muted d-block text-truncate" title="${file.name}">${file.name}</small>
                    </div>
                    ${!isMain ? `
                        <button type="button" class="btn btn-outline-primary btn-sm w-100 py-0 px-1 mt-2 make-cover-btn" data-index="${index}">
                            <i class="fas fa-star me-1"></i>Jadikan Sampul
                        </button>
                    ` : ''}
                </div>
            `;

            col.appendChild(card);
            previewGallery.appendChild(col);
        });

        previewGallery.querySelectorAll('.remove-file-btn').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                const idx = parseInt(this.dataset.index, 10);
                selectedFiles.splice(idx, 1);
                renderGallery();
            });
        });

        previewGallery.querySelectorAll('.make-cover-btn').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                const idx = parseInt(this.dataset.index, 10);
                const [targetFile] = selectedFiles.splice(idx, 1);
                selectedFiles.unshift(targetFile);
                renderGallery();
            });
        });
    }

    form.addEventListener('submit', function (e) {
        if (selectedFiles.length > 0) {
            const dtMain = new DataTransfer();
            dtMain.items.add(selectedFiles[0]);
            realFoto.files = dtMain.files;

            if (selectedFiles.length > 1) {
                const dtExtra = new DataTransfer();
                for (let i = 1; i < selectedFiles.length; i++) {
                    dtExtra.items.add(selectedFiles[i]);
                }
                realFotoTambahan.files = dtExtra.files;
            } else {
                realFotoTambahan.value = '';
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
