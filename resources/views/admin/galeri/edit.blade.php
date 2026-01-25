@extends('layouts.app')

@section('title', 'Edit Galeri')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Galeri</h1>
        <a href="{{ route('admin.galeri.index') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('admin.galeri.update', $galeri->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="judul" class="form-label">Judul (Opsional)</label>
                    <input type="text" class="form-control @error('judul') is-invalid @enderror" id="judul" name="judul" value="{{ old('judul', $galeri->judul) }}">
                    @error('judul')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="tipe" class="form-label">Tipe Media</label>
                    <select class="form-control @error('tipe') is-invalid @enderror" id="tipe" name="tipe" required>
                        <option value="foto" {{ old('tipe', $galeri->tipe) == 'foto' ? 'selected' : '' }}>Foto</option>
                        <option value="video" {{ old('tipe', $galeri->tipe) == 'video' ? 'selected' : '' }}>Video</option>
                    </select>
                    @error('tipe')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="file" class="form-label">File</label>
                    <div class="mb-2">
                        @if($galeri->tipe == 'foto')
                            <img src="{{ asset('storage/' . $galeri->file_path) }}" alt="Current Media" width="150" class="img-thumbnail">
                        @else
                            <video width="150" controls>
                                <source src="{{ asset('storage/' . $galeri->file_path) }}" type="video/mp4">
                            </video>
                        @endif
                    </div>
                    <input type="file" class="form-control @error('file') is-invalid @enderror" id="file" name="file">
                    <small class="text-muted">Biarkan kosong jika tidak ingin mengubah file.</small>
                    @error('file')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="deskripsi" class="form-label">Deskripsi (Opsional)</label>
                    <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="3">{{ old('deskripsi', $galeri->deskripsi) }}</textarea>
                    @error('deskripsi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </form>
        </div>
    </div>
</div>
@endsection
