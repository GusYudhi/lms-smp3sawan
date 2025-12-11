@extends('layouts.app')

@section('title', 'Edit Jurnal Mengajar')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('guru.jurnal-mengajar.index') }}">Jurnal Mengajar</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('guru.jurnal-mengajar.show', $jurnal->id) }}">Detail Jurnal</a></li>
                    <li class="breadcrumb-item active">Edit Jurnal</li>
                </ol>
            </nav>
            <h2 class="mb-1"><i class="fas fa-edit text-warning me-2"></i>Edit Jurnal Mengajar</h2>
            <p class="text-muted mb-0">Perbarui informasi jurnal mengajar Anda</p>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Terjadi kesalahan!</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Jurnal</h5>
        </div>
        <div class="card-body">
            <!-- Informasi Read-only -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label text-muted small">Tanggal</label>
                        <p class="form-control-plaintext">
                            <strong>{{ \Carbon\Carbon::parse($jurnal->tanggal)->format('d F Y') }}</strong>
                        </p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small">Hari</label>
                        <p class="form-control-plaintext">
                            <strong>{{ ucfirst($jurnal->hari) }}</strong>
                        </p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small">Jam Pelajaran</label>
                        <p class="form-control-plaintext">
                            <strong>Jam ke-{{ $jurnal->jam_ke }}</strong>
                            ({{ \Carbon\Carbon::parse($jurnal->jam_mulai)->format('H:i') }} -
                            {{ \Carbon\Carbon::parse($jurnal->jam_selesai)->format('H:i') }})
                        </p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label text-muted small">Kelas</label>
                        <p class="form-control-plaintext">
                            <strong>{{ $jurnal->kelas->full_name }}</strong>
                        </p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small">Mata Pelajaran</label>
                        <p class="form-control-plaintext">
                            <strong>{{ $jurnal->mataPelajaran->nama_mapel }}</strong>
                        </p>
                    </div>
                </div>
            </div>

            <hr>

            <!-- Form Edit -->
            <form action="{{ route('guru.jurnal-mengajar.update', $jurnal->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label for="materi_pembelajaran" class="form-label">
                        <strong>Materi Pembelajaran <span class="text-danger">*</span></strong>
                    </label>
                    <textarea class="form-control"
                              id="materi_pembelajaran"
                              name="materi_pembelajaran"
                              rows="6"
                              required>{{ old('materi_pembelajaran', $jurnal->materi_pembelajaran) }}</textarea>
                    <small class="text-muted">Jelaskan materi yang diajarkan pada pertemuan ini</small>
                </div>

                <div class="mb-4">
                    <label for="keterangan" class="form-label">
                        <strong>Keterangan/Catatan</strong>
                    </label>
                    <textarea class="form-control"
                              id="keterangan"
                              name="keterangan"
                              rows="4">{{ old('keterangan', $jurnal->keterangan) }}</textarea>
                    <small class="text-muted">Catatan tambahan mengenai pembelajaran (opsional)</small>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Simpan Perubahan
                    </button>
                    <a href="{{ route('guru.jurnal-mengajar.show', $jurnal->id) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-2"></i>Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
