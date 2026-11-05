@extends('layouts.app')

@section('title', 'Edit Prestasi')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Prestasi</h1>
        <a href="{{ route('admin.prestasi.index') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('admin.prestasi.update', $prestasi->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="judul" class="form-label">Judul Prestasi</label>
                    <input type="text" class="form-control @error('judul') is-invalid @enderror" id="judul" name="judul" value="{{ old('judul', $prestasi->judul) }}" required>
                    @error('judul')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="tanggal" class="form-label">Tanggal</label>
                    <input type="date" class="form-control @error('tanggal') is-invalid @enderror" id="tanggal" name="tanggal" value="{{ old('tanggal', $prestasi->tanggal->format('Y-m-d')) }}" required>
                    @error('tanggal')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="foto" class="form-label fw-semibold">Foto Sampul Utama</label>
                    @if($prestasi->foto)
                        <div class="mb-2">
                            <img src="{{ asset('storage/' . $prestasi->foto) }}" alt="Current Photo" width="160" class="img-thumbnail rounded">
                        </div>
                    @endif
                    <input type="file" class="form-control @error('foto') is-invalid @enderror" id="foto" name="foto" accept="image/*">
                    <small class="text-muted">Biarkan kosong jika tidak ingin mengubah foto sampul utama.</small>
                    @error('foto')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                @if($prestasi->images && $prestasi->images->count() > 0)
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Foto Dokumentasi Tambahan Tersimpan</label>
                        <div class="row g-2" id="existingPrestasiImages">
                            @foreach($prestasi->images as $img)
                                <div class="col-4 col-md-3 col-lg-2" id="prestasi-img-box-{{ $img->id }}">
                                    <div class="position-relative border rounded p-1 bg-light">
                                        <img src="{{ asset('storage/' . $img->foto) }}" class="img-fluid rounded object-fit-cover w-100" style="height: 100px;" alt="Dokumentasi">
                                        <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 p-0 rounded-circle delete-prestasi-image"
                                                style="width: 24px; height: 24px;"
                                                data-url="{{ route('admin.prestasi.delete-image', ['id' => $prestasi->id, 'imageId' => $img->id]) }}"
                                                data-target="#prestasi-img-box-{{ $img->id }}"
                                                title="Hapus foto ini">
                                            &times;
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="mb-3">
                    <label for="foto_tambahan" class="form-label fw-semibold">Tambah Foto Dokumentasi Baru (Bisa Lebih Dari 1)</label>
                    <input type="file" class="form-control @error('foto_tambahan.*') is-invalid @enderror" id="foto_tambahan" name="foto_tambahan[]" accept="image/*" multiple>
                    <small class="text-muted">Pilih satu atau beberapa foto untuk ditambahkan ke galeri prestasi ini.</small>
                    @error('foto_tambahan.*')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="deskripsi" class="form-label">Deskripsi</label>
                    <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="5" required>{{ old('deskripsi', $prestasi->deskripsi) }}</textarea>
                    @error('deskripsi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </form>
        </div>
    </div>
</div>
@push('scripts')
<script>
    document.querySelectorAll('.delete-prestasi-image').forEach(btn => {
        btn.addEventListener('click', function() {
            const url = this.dataset.url;
            const targetId = this.dataset.target;
            Swal.fire({
                title: 'Hapus foto ini?',
                text: 'Foto dokumentasi ini akan dihapus permanen.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus'
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
                            const elem = document.querySelector(targetId);
                            if (elem) elem.remove();
                        }
                    });
                }
            });
        });
    });
</script>
@endpush
@endsection
