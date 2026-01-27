@extends('layouts.app')

@section('title', 'Edit Kegiatan Kokurikuler')

@section('content')
<div class="container-fluid">
    @php
        $routePrefix = auth()->user()->isGuru() ? 'guru' : (auth()->user()->isKepalaSekolah() ? 'kepala-sekolah' : 'admin');
    @endphp
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Kegiatan Kokurikuler</h1>
        <a href="{{ route($routePrefix . '.kegiatan-kokurikuler.index') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route($routePrefix . '.kegiatan-kokurikuler.update', $kegiatan->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="nama" class="form-label">Nama Kegiatan</label>
                    <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama" name="nama" value="{{ old('nama', $kegiatan->nama) }}" required>
                    @error('nama')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="tanggal" class="form-label">Tanggal Kegiatan</label>
                    <input type="date" class="form-control @error('tanggal') is-invalid @enderror" id="tanggal" name="tanggal" value="{{ old('tanggal', $kegiatan->tanggal->format('Y-m-d')) }}" required>
                    @error('tanggal')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="tipe" class="form-label">Tipe Lampiran</label>
                    <select name="tipe" id="tipe" class="form-select @error('tipe') is-invalid @enderror" onchange="toggleInput()">
                        <option value="foto" {{ old('tipe', $kegiatan->tipe) == 'foto' ? 'selected' : '' }}>Foto (JPG, PNG)</option>
                        <option value="pdf" {{ old('tipe', $kegiatan->tipe) == 'pdf' ? 'selected' : '' }}>Dokumen (PDF)</option>
                        <option value="link" {{ old('tipe', $kegiatan->tipe) == 'link' ? 'selected' : '' }}>Link (URL)</option>
                    </select>
                    @error('tipe')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div id="file_input_container" class="mb-3 {{ old('tipe', $kegiatan->tipe) == 'link' ? 'd-none' : '' }}">
                    <label for="foto" id="file_label" class="form-label">File Lampiran</label>
                    @if($kegiatan->tipe == 'foto' && $kegiatan->foto)
                        <div class="mb-2">
                            <img src="{{ asset('storage/' . $kegiatan->foto) }}" alt="Current Photo" width="150" class="img-thumbnail">
                        </div>
                    @elseif($kegiatan->tipe == 'pdf' && $kegiatan->foto)
                        <div class="mb-2">
                            <a href="{{ asset('storage/' . $kegiatan->foto) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                <i class="fas fa-file-pdf me-1"></i> Lihat PDF Saat Ini
                            </a>
                        </div>
                    @endif
                    <input type="file" class="form-control @error('foto') is-invalid @enderror" id="foto" name="foto">
                    <small class="text-muted" id="file_hint">Format: jpg, jpeg, png. Maksimal 10MB. Biarkan kosong jika tidak ingin mengubah file.</small>
                    @error('foto')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div id="link_input_container" class="mb-3 {{ old('tipe', $kegiatan->tipe) == 'link' ? '' : 'd-none' }}">
                    <label for="link" class="form-label">Link Lampiran</label>
                    <input type="url" class="form-control @error('link') is-invalid @enderror" id="link" name="link" value="{{ old('link', $kegiatan->link) }}" placeholder="https://...">
                    @error('link')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="deskripsi" class="form-label">Deskripsi</label>
                    <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="5" required>{{ old('deskripsi', $kegiatan->deskripsi) }}</textarea>
                    @error('deskripsi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </form>
        </div>
    </div>
</div>

<script>
    function toggleInput() {
        const tipe = document.getElementById('tipe').value;
        const fileContainer = document.getElementById('file_input_container');
        const linkContainer = document.getElementById('link_input_container');
        const fileLabel = document.getElementById('file_label');
        const fileHint = document.getElementById('file_hint');
        const fileInput = document.getElementById('foto');

        if (tipe === 'link') {
            fileContainer.classList.add('d-none');
            linkContainer.classList.remove('d-none');
        } else {
            fileContainer.classList.remove('d-none');
            linkContainer.classList.add('d-none');

            if (tipe === 'foto') {
                fileLabel.innerText = 'Foto Kegiatan';
                fileHint.innerText = 'Format: jpg, jpeg, png. Maksimal 10MB. Biarkan kosong jika tidak ingin mengubah file.';
                fileInput.accept = 'image/*';
            } else {
                fileLabel.innerText = 'Dokumen PDF';
                fileHint.innerText = 'Format: PDF. Maksimal 10MB. Biarkan kosong jika tidak ingin mengubah file.';
                fileInput.accept = '.pdf';
            }
        }
    }

    // Initialize on load
    document.addEventListener('DOMContentLoaded', toggleInput);
</script>
@endsection