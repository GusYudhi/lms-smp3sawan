@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Edit Tugas Guru</h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('kepala-sekolah.tugas-guru.update', $tugas->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="judul" class="form-label">Judul Tugas <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('judul') is-invalid @enderror"
                                   id="judul" name="judul" value="{{ old('judul', $tugas->judul) }}" required>
                            @error('judul')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">Deskripsi / Keterangan</label>
                            <textarea class="form-control @error('deskripsi') is-invalid @enderror"
                                      id="deskripsi" name="deskripsi" rows="5">{{ old('deskripsi', $tugas->deskripsi) }}</textarea>
                            @error('deskripsi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="deadline" class="form-label">Deadline / Tenggat Pengumpulan <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control @error('deadline') is-invalid @enderror"
                                   id="deadline" name="deadline"
                                   value="{{ old('deadline', $tugas->deadline->format('Y-m-d\TH:i')) }}" required>
                            @error('deadline')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="aktif" {{ old('status', $tugas->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="selesai" {{ old('status', $tugas->status) == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                <option value="dibatalkan" {{ old('status', $tugas->status) == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Existing Files -->
                        @if($tugas->files->count() > 0)
                        <div class="mb-3">
                            <label class="form-label">Lampiran Saat Ini:</label>
                            <ul class="list-group">
                                @foreach($tugas->files as $file)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="fas fa-file"></i> {{ $file->nama_file }}
                                        <br><small class="text-muted">{{ $file->formatted_size }}</small>
                                    </div>
                                    <div>
                                        <a href="{{ asset('storage/' . $file->file_path) }}" class="btn btn-sm btn-primary" download>
                                            <i class="fas fa-download"></i>
                                        </a>
                                                <form action="{{ route('kepala-sekolah.tugas-guru.delete-file', $file->id) }}" method="POST" class="d-inline delete-form" data-message="Yakin ingin menghapus file ini?">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                    </div>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <div class="mb-3">
                            <label for="files" class="form-label">Tambah Lampiran Baru (Opsional)</label>
                            <input type="file" class="form-control @error('files.*') is-invalid @enderror"
                                   id="files" name="files[]" multiple>
                            @error('files.*')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Maksimal 10MB per file.</small>
                            <div id="file-list" class="mt-2"></div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('kepala-sekolah.tugas-guru.show', $tugas->id) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Tugas
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
