@extends('layouts.app')

@section('title', 'Tambah Semester')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body py-4">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                        <div class="mb-3 mb-md-0">
                            <nav aria-label="breadcrumb" class="mb-2">
                                <ol class="breadcrumb mb-0">
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('admin.tahun-pelajaran.index') }}">Tahun Pelajaran</a>
                                    </li>
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('admin.tahun-pelajaran.dashboard', $tahunPelajaran->id) }}">{{ $tahunPelajaran->nama }}</a>
                                    </li>
                                    <li class="breadcrumb-item active">Tambah Semester</li>
                                </ol>
                            </nav>
                            <h1 class="h3 mb-2 text-primary">
                                <i class="fas fa-plus-circle me-2"></i>Tambah Semester
                            </h1>
                            <p class="text-muted mb-0">Tambahkan semester baru untuk tahun pelajaran {{ $tahunPelajaran->nama }}</p>
                        </div>
                        <div>
                            <a href="{{ route('admin.semester.index', ['tahun_pelajaran_id' => $tahunPelajaran->id]) }}" class="btn btn-secondary">
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
                        <i class="fas fa-calendar-plus me-2"></i>Form Tambah Semester
                    </h6>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.semester.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="tahun_pelajaran_id" value="{{ $tahunPelajaran->id }}">

                        <!-- Nama Semester -->
                        <div class="mb-4">
                            <label for="nama" class="form-label fw-medium">
                                Nama Semester <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   class="form-control @error('nama') is-invalid @enderror"
                                   id="nama"
                                   name="nama"
                                   value="{{ old('nama') }}"
                                   placeholder="Contoh: Semester Ganjil, Semester Genap"
                                   required>
                            @error('nama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Nama yang mudah dikenali untuk semester ini</small>
                        </div>

                        <!-- Semester Ke -->
                        <div class="mb-4">
                            <label for="semester_ke" class="form-label fw-medium">
                                Semester Ke <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('semester_ke') is-invalid @enderror"
                                    id="semester_ke"
                                    name="semester_ke"
                                    required>
                                <option value="">Pilih Semester</option>
                                <option value="1" {{ old('semester_ke') == 1 ? 'selected' : '' }}>Semester 1 (Ganjil)</option>
                                <option value="2" {{ old('semester_ke') == 2 ? 'selected' : '' }}>Semester 2 (Genap)</option>
                            </select>
                            @error('semester_ke')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Tanggal Mulai & Selesai -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="tanggal_mulai" class="form-label fw-medium">
                                    Tanggal Mulai <span class="text-danger">*</span>
                                </label>
                                <input type="date"
                                       class="form-control @error('tanggal_mulai') is-invalid @enderror"
                                       id="tanggal_mulai"
                                       name="tanggal_mulai"
                                       value="{{ old('tanggal_mulai') }}"
                                       required>
                                @error('tanggal_mulai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="tanggal_selesai" class="form-label fw-medium">
                                    Tanggal Selesai <span class="text-danger">*</span>
                                </label>
                                <input type="date"
                                       class="form-control @error('tanggal_selesai') is-invalid @enderror"
                                       id="tanggal_selesai"
                                       name="tanggal_selesai"
                                       value="{{ old('tanggal_selesai') }}"
                                       required>
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
                                      rows="3"
                                      placeholder="Catatan atau informasi tambahan tentang semester ini">{{ old('keterangan') }}</textarea>
                            @error('keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Status Aktif -->
                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input"
                                       type="checkbox"
                                       id="is_active"
                                       name="is_active"
                                       value="1"
                                       {{ old('is_active') ? 'checked' : '' }}>
                                <label class="form-check-label fw-medium" for="is_active">
                                    Aktifkan Semester Ini
                                </label>
                            </div>
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Hanya satu semester yang dapat aktif dalam satu waktu. Mengaktifkan semester ini akan menonaktifkan semester lain.
                            </small>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-between pt-3 border-top">
                            <a href="{{ route('admin.semester.index', ['tahun_pelajaran_id' => $tahunPelajaran->id]) }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Simpan Semester
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
