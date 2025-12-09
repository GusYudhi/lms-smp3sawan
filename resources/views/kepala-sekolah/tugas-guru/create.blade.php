@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Tambah Tugas Guru Baru</h4>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('kepala-sekolah.tugas-guru.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label for="judul" class="form-label">Judul Tugas <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('judul') is-invalid @enderror"
                                   id="judul" name="judul" value="{{ old('judul') }}" required>
                            @error('judul')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">Deskripsi / Keterangan</label>
                            <textarea class="form-control @error('deskripsi') is-invalid @enderror"
                                      id="deskripsi" name="deskripsi" rows="5">{{ old('deskripsi') }}</textarea>
                            @error('deskripsi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Jelaskan detail tugas yang harus dikerjakan oleh guru</small>
                        </div>

                        <div class="mb-3">
                            <label for="deadline" class="form-label">Deadline / Tenggat Pengumpulan <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control @error('deadline') is-invalid @enderror"
                                   id="deadline" name="deadline" value="{{ old('deadline') }}" required>
                            @error('deadline')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="files" class="form-label">Lampiran File (Opsional)</label>
                            <input type="file" class="form-control @error('files.*') is-invalid @enderror"
                                   id="files" name="files[]" multiple>
                            @error('files.*')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Anda dapat melampirkan lebih dari 1 file. Maksimal 10MB per file.</small>
                            <div id="file-list" class="mt-2"></div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('kepala-sekolah.tugas-guru.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Tugas
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('files').addEventListener('change', function(e) {
    const fileList = document.getElementById('file-list');
    fileList.innerHTML = '';

    if (this.files.length > 0) {
        const ul = document.createElement('ul');
        ul.className = 'list-group';

        Array.from(this.files).forEach((file, index) => {
            const li = document.createElement('li');
            li.className = 'list-group-item d-flex justify-content-between align-items-center';

            const fileSize = file.size < 1024 ? file.size + ' B' :
                            file.size < 1048576 ? (file.size / 1024).toFixed(2) + ' KB' :
                            (file.size / 1048576).toFixed(2) + ' MB';

            li.innerHTML = `
                <span><i class="fas fa-file"></i> ${file.name}</span>
                <span class="badge bg-secondary">${fileSize}</span>
            `;
            ul.appendChild(li);
        });

        fileList.appendChild(ul);
    }
});
</script>
@endpush
@endsection
