@extends('layouts.app')

@section('title', 'Upload Materi Pelajaran')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Upload Materi Pelajaran</h1>
        <a href="{{ route('guru.materi.index') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('guru.materi.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="judul" class="form-label">Judul Materi</label>
                    <input type="text" class="form-control @error('judul') is-invalid @enderror" id="judul" name="judul" value="{{ old('judul') }}" required>
                    @error('judul')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="mata_pelajaran_id" class="form-label">Mata Pelajaran</label>
                    <select class="form-control @error('mata_pelajaran_id') is-invalid @enderror" id="mata_pelajaran_id" name="mata_pelajaran_id" required>
                        <option value="">Pilih Mata Pelajaran</option>
                        @foreach($mapels as $mapel)
                            <option value="{{ $mapel->id }}" {{ old('mata_pelajaran_id') == $mapel->id ? 'selected' : '' }}>{{ $mapel->nama_mapel }}</option>
                        @endforeach
                    </select>
                    @error('mata_pelajaran_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="target_kelas" class="form-label">Target Kelas</label>
                    <select class="form-control @error('target_kelas') is-invalid @enderror" id="target_kelas" name="target_kelas">
                        <option value="all_taught" {{ old('target_kelas') == 'all_taught' ? 'selected' : '' }}>Semua Kelas yang Saya Ampu (Default)</option>
                        <option value="all_global" {{ old('target_kelas') == 'all_global' ? 'selected' : '' }}>Semua Kelas (7, 8, 9)</option>
                        <option value="grade_7" {{ old('target_kelas') == 'grade_7' ? 'selected' : '' }}>Semua Kelas 7</option>
                        <option value="grade_8" {{ old('target_kelas') == 'grade_8' ? 'selected' : '' }}>Semua Kelas 8</option>
                        <option value="grade_9" {{ old('target_kelas') == 'grade_9' ? 'selected' : '' }}>Semua Kelas 9</option>
                        <optgroup label="Pilih Kelas Spesifik">
                            @foreach($kelas as $k)
                                <option value="class_{{ $k->id }}" {{ old('target_kelas') == 'class_'.$k->id ? 'selected' : '' }}>{{ $k->full_name }}</option>
                            @endforeach
                        </optgroup>
                    </select>
                    @error('target_kelas')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="tipe" class="form-label">Tipe Materi</label>
                    <select class="form-control @error('tipe') is-invalid @enderror" id="tipe" name="tipe" onchange="toggleType()">
                        <option value="file" {{ old('tipe') == 'file' ? 'selected' : '' }}>File (PDF, DOC, dll)</option>
                        <option value="link" {{ old('tipe') == 'link' ? 'selected' : '' }}>Link (Youtube, Google Form, dll)</option>
                    </select>
                    @error('tipe')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3" id="file_input_group">
                    <label for="file_path" class="form-label">File Materi</label>
                    <input type="file" class="form-control @error('file_path') is-invalid @enderror" id="file_path" name="file_path">
                    <small class="text-muted">Format: PDF, DOC, DOCX, PPT, PPTX. Maksimal 10MB.</small>
                    @error('file_path')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3 d-none" id="link_input_group">
                    <label for="link" class="form-label">Link Materi</label>
                    <input type="url" class="form-control @error('link') is-invalid @enderror" id="link" name="link" value="{{ old('link') }}" placeholder="https://youtube.com/...">
                    @error('link')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="deskripsi" class="form-label">Deskripsi (Opsional)</label>
                    <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="3">{{ old('deskripsi') }}</textarea>
                    @error('deskripsi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">Upload</button>
            </form>
        </div>
    </div>
</div>

<script>
    function toggleType() {
        const type = document.getElementById('tipe').value;
        const fileGroup = document.getElementById('file_input_group');
        const linkGroup = document.getElementById('link_input_group');
        const fileInput = document.getElementById('file_path');
        const linkInput = document.getElementById('link');

        if (type === 'file') {
            fileGroup.classList.remove('d-none');
            linkGroup.classList.add('d-none');
            // Reset link
            linkInput.value = '';
        } else {
            fileGroup.classList.add('d-none');
            linkGroup.classList.remove('d-none');
            // Reset file
            fileInput.value = '';
        }
    }

    // Run on load to set initial state (e.g. after validation error)
    document.addEventListener('DOMContentLoaded', toggleType);
</script>
@endsection
