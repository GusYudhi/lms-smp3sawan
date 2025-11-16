@extends('layouts.app')

@section('title', 'Detail Guru')

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
                                <i class="fas fa-user-circle me-2"></i>Detail Guru
                            </h1>
                            <p class="text-muted mb-0">Informasi lengkap data guru dan tenaga pendidik</p>
                        </div>
                        <div>
                            <a href="{{ route('admin.guru.edit', $teacher->id) }}" class="btn btn-primary me-2">
                                <i class="fas fa-edit me-2"></i>Edit Data
                            </a>
                            <a href="{{ route('admin.guru.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Teacher Profile Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-lg-3 text-center text-lg-start mb-4 mb-lg-0">
                            @if($teacher->profile_photo)
                                <img src="{{ asset('storage/' . $teacher->profile_photo) }}"
                                     alt="Foto {{ $teacher->name }}"
                                     class="rounded-circle border"
                                     style="width: 150px; height: 150px; object-fit: cover;">
                            @else
                                <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-light border text-muted"
                                     style="width: 150px; height: 150px; font-size: 3rem;">
                                    <i class="fas fa-user"></i>
                                </div>
                            @endif
                        </div>
                        <div class="col-lg-9">
                            <h2 class="mb-2 fw-bold">{{ $teacher->name }}</h2>
                            <p class="text-muted mb-3">NIP: {{ $teacher->nomor_induk }}</p>

                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="d-flex flex-column">
                                        <span class="small text-muted">Mata Pelajaran</span>
                                        <span class="fw-semibold">{{ $teacher->mata_pelajaran ?? '-' }}</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="d-flex flex-column">
                                        <span class="small text-muted">Status</span>
                                        <span class="badge bg-success-subtle text-success border mt-1 align-self-start">
                                            {{ ucfirst(str_replace('_', ' ', $teacher->status_kepegawaian ?? 'Tidak diketahui')) }}
                                        </span>
                                    </div>
                                </div>
                                @if($teacher->wali_kelas)
                                <div class="col-md-4">
                                    <div class="d-flex flex-column">
                                        <span class="small text-muted">Wali Kelas</span>
                                        <span class="badge bg-primary-subtle text-primary border mt-1 align-self-start">{{ $teacher->wali_kelas }}</span>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Information -->
    <div class="row">
        <!-- Personal Information -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light">
                    <h6 class="mb-0 fw-semibold text-primary">
                        <i class="fas fa-user me-2"></i>Informasi Personal
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="d-flex flex-column">
                                <span class="small text-muted">Nama Lengkap</span>
                                <span class="fw-semibold">{{ $teacher->name }}</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex flex-column">
                                <span class="small text-muted">Nomor Induk</span>
                                <span class="fw-semibold">{{ $teacher->nomor_induk }}</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex flex-column">
                                <span class="small text-muted">Email</span>
                                <a href="mailto:{{ $teacher->email }}" class="text-primary text-decoration-none">
                                    {{ $teacher->email }}
                                </a>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex flex-column">
                                <span class="small text-muted">Nomor Telepon</span>
                                @if($teacher->nomor_telepon)
                                    <a href="tel:{{ $teacher->nomor_telepon }}" class="text-primary text-decoration-none">
                                        {{ $teacher->nomor_telepon }}
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex flex-column">
                                <span class="small text-muted">Jenis Kelamin</span>
                                @if($teacher->jenis_kelamin)
                                    <span class="fw-semibold">
                                        <i class="fas fa-{{ $teacher->jenis_kelamin === 'laki-laki' ? 'mars text-info' : 'venus text-warning' }} me-1"></i>
                                        {{ ucfirst($teacher->jenis_kelamin) }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Professional Information -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light">
                    <h6 class="mb-0 fw-semibold text-primary">
                        <i class="fas fa-briefcase me-2"></i>Informasi Kepegawaian
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="d-flex flex-column">
                                <span class="small text-muted">Status Kepegawaian</span>
                                <span class="badge bg-success-subtle text-success border mt-1 align-self-start">
                                    {{ ucfirst(str_replace('_', ' ', $teacher->status_kepegawaian ?? 'Tidak diketahui')) }}
                                </span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex flex-column">
                                <span class="small text-muted">Mata Pelajaran</span>
                                @if($teacher->mata_pelajaran)
                                    <span class="badge bg-primary-subtle text-primary border mt-1 align-self-start">{{ $teacher->mata_pelajaran }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex flex-column">
                                <span class="small text-muted">Wali Kelas</span>
                                @if($teacher->wali_kelas)
                                    <span class="badge bg-warning-subtle text-warning border mt-1 align-self-start">{{ $teacher->wali_kelas }}</span>
                                @else
                                    <span class="text-muted">Tidak ada</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Account Information -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light">
                    <h6 class="mb-0 fw-semibold text-primary">
                        <i class="fas fa-cog me-2"></i>Informasi Akun
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="d-flex flex-column">
                                <span class="small text-muted">Tanggal Bergabung</span>
                                <span class="fw-semibold">
                                    <i class="fas fa-calendar-plus text-success me-1"></i>
                                    {{ $teacher->created_at->format('d F Y') }}
                                </span>
                                <small class="text-muted">({{ $teacher->created_at->diffForHumans() }})</small>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex flex-column">
                                <span class="small text-muted">Terakhir Diperbarui</span>
                                <span class="fw-semibold">
                                    <i class="fas fa-clock text-info me-1"></i>
                                    {{ $teacher->updated_at->format('d F Y, H:i') }}
                                </span>
                                <small class="text-muted">({{ $teacher->updated_at->diffForHumans() }})</small>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex flex-column">
                                <span class="small text-muted">Status Akun</span>
                                <span class="badge bg-success-subtle text-success border mt-1 align-self-start">
                                    <i class="fas fa-check-circle me-1"></i>Aktif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex flex-column flex-md-row justify-content-center gap-3">
                        <a href="{{ route('admin.guru.edit', $teacher->id) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-2"></i>Edit Data Guru
                        </a>
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <i class="fas fa-trash me-2"></i>Hapus Guru
                        </button>
                        <a href="{{ route('admin.guru.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title text-danger" id="deleteModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>Konfirmasi Hapus Guru
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus data guru <strong>{{ $teacher->name }}</strong>?</p>
                <div class="alert alert-warning d-flex align-items-center" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <div>Tindakan ini tidak dapat dibatalkan! Semua data terkait guru ini akan dihapus secara permanen.</div>
                </div>
            </div>
            <div class="modal-footer border-top-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Batal
                </button>
                <form action="{{ route('admin.guru.destroy', $teacher->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>Ya, Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
