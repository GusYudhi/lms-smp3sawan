@extends('layouts.app')

@section('title', 'Edit Tahun Pelajaran')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body py-4">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                        <div class="mb-3 mb-md-0">
                            <h1 class="h3 mb-2 text-primary">
                                <i class="fas fa-edit me-2"></i>Edit Tahun Pelajaran
                            </h1>
                            <p class="text-muted mb-0">Ubah data tahun pelajaran {{ $tahunPelajaran->nama }}</p>
                        </div>
                        <div>
                            <a href="{{ route('admin.tahun-pelajaran.dashboard', $tahunPelajaran->id) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Card -->
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0 fw-semibold">
                        <i class="fas fa-edit me-2"></i>Form Edit
                    </h6>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.tahun-pelajaran.update', $tahunPelajaran->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Nama Tahun Pelajaran -->
                        <div class="mb-4">
                            <label for="nama" class="form-label fw-medium">
                                Nama Tahun Pelajaran <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   class="form-control @error('nama') is-invalid @enderror"
                                   id="nama"
                                   name="nama"
                                   value="{{ old('nama', $tahunPelajaran->nama) }}"
                                   required>
                            @error('nama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Tahun Mulai & Selesai -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="tahun_mulai" class="form-label fw-medium">
                                    Tahun Mulai <span class="text-danger">*</span>
                                </label>
                                <input type="number"
                                       class="form-control @error('tahun_mulai') is-invalid @enderror"
                                       id="tahun_mulai"
                                       name="tahun_mulai"
                                       value="{{ old('tahun_mulai', $tahunPelajaran->tahun_mulai) }}"
                                       min="2000"
                                       max="2100"
                                       required>
                                @error('tahun_mulai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="tahun_selesai" class="form-label fw-medium">
                                    Tahun Selesai <span class="text-danger">*</span>
                                </label>
                                <input type="number"
                                       class="form-control @error('tahun_selesai') is-invalid @enderror"
                                       id="tahun_selesai"
                                       name="tahun_selesai"
                                       value="{{ old('tahun_selesai', $tahunPelajaran->tahun_selesai) }}"
                                       min="2000"
                                       max="2100"
                                       required>
                                @error('tahun_selesai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Tanggal Mulai & Selesai -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="tanggal_mulai" class="form-label fw-medium">Tanggal Mulai</label>
                                <input type="date"
                                       class="form-control @error('tanggal_mulai') is-invalid @enderror"
                                       id="tanggal_mulai"
                                       name="tanggal_mulai"
                                       value="{{ old('tanggal_mulai', $tahunPelajaran->tanggal_mulai?->format('Y-m-d')) }}">
                                @error('tanggal_mulai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="tanggal_selesai" class="form-label fw-medium">Tanggal Selesai</label>
                                <input type="date"
                                       class="form-control @error('tanggal_selesai') is-invalid @enderror"
                                       id="tanggal_selesai"
                                       name="tanggal_selesai"
                                       value="{{ old('tanggal_selesai', $tahunPelajaran->tanggal_selesai?->format('Y-m-d')) }}">
                                @error('tanggal_selesai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Keterangan -->
                        <div class="mb-4">
                            <label for="keterangan" class="form-label fw-medium">Keterangan</label>
                            <textarea class="form-control @error('keterangan') is-invalid @enderror"
                                      id="keterangan"
                                      name="keterangan"
                                      rows="3">{{ old('keterangan', $tahunPelajaran->keterangan) }}</textarea>
                            @error('keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-between pt-3 border-top">
                            <a href="{{ route('admin.tahun-pelajaran.dashboard', $tahunPelajaran->id) }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
