@extends('layouts.app')

@section('title', 'Detail Siswa')

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
                                <i class="fas fa-user-circle me-2"></i>Detail Siswa
                            </h1>
                            <p class="text-muted mb-0">Informasi lengkap data siswa</p>
                        </div>
                        <div>
                            <a href="{{ route('admin.siswa.edit', $student->id) }}" class="btn btn-primary me-2">
                                <i class="fas fa-edit me-2"></i>Edit Data
                            </a>
                            <a href="{{ route('admin.siswa.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Student Profile Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-lg-3 text-center text-lg-start mb-4 mb-lg-0">
                            @if($student->studentProfile && $student->studentProfile->foto_profil)
                                <img src="{{ asset('storage/' . $student->studentProfile->foto_profil) }}"
                                     alt="Foto {{ $student->name }}"
                                     class="rounded-circle border"
                                     style="width: 150px; height: 150px; object-fit: cover;">
                            @else
                                <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-light border text-muted"
                                     style="width: 150px; height: 150px; font-size: 3rem;">
                                    <i class="fas fa-user-graduate"></i>
                                </div>
                            @endif
                        </div>
                        <div class="col-lg-9">
                            <h2 class="mb-2 fw-bold">{{ $student->name }}</h2>
                            <p class="text-muted mb-3">NIS: {{ $student->studentProfile->nis ?? '-' }}</p>

                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="d-flex flex-column">
                                        <span class="small text-muted">Kelas</span>
                                        <span class="fw-semibold">{{ $student->studentProfile->kelas ?? '-' }}</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="d-flex flex-column">
                                        <span class="small text-muted">NISN</span>
                                        <span class="fw-semibold">{{ $student->studentProfile->nisn ?? '-' }}</span>
                                    </div>
                                </div>
                                @if($student->studentProfile && $student->studentProfile->nomor_telepon_orangtua)
                                <div class="col-md-4">
                                    <div class="d-flex flex-column">
                                        <span class="small text-muted">Telp. Orang Tua</span>
                                        <span class="fw-semibold">{{ $student->studentProfile->nomor_telepon_orangtua }}</span>
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
        <div class="col-lg-6 mb-4">
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
                                <span class="fw-semibold">{{ $student->name }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex flex-column">
                                <span class="small text-muted">NIS</span>
                                <span class="fw-semibold">{{ $student->studentProfile->nis ?? '-' }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex flex-column">
                                <span class="small text-muted">NISN</span>
                                <span class="fw-semibold">{{ $student->studentProfile->nisn ?? '-' }}</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex flex-column">
                                <span class="small text-muted">Email</span>
                                @if($student->email)
                                    <a href="mailto:{{ $student->email }}" class="text-primary text-decoration-none">
                                        {{ $student->email }}
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex flex-column">
                                <span class="small text-muted">Jenis Kelamin</span>
                                @if($student->studentProfile && $student->studentProfile->jenis_kelamin)
                                    <span class="fw-semibold">
                                        @if($student->studentProfile->jenis_kelamin === 'L')
                                            <i class="fas fa-mars text-info me-1"></i>Laki-laki
                                        @else
                                            <i class="fas fa-venus text-warning me-1"></i>Perempuan
                                        @endif
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex flex-column">
                                <span class="small text-muted">Tempat Lahir</span>
                                <span class="fw-semibold">{{ $student->studentProfile->tempat_lahir ?? '-' }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex flex-column">
                                <span class="small text-muted">Tanggal Lahir</span>
                                @if($student->studentProfile && $student->studentProfile->tanggal_lahir)
                                    <span class="fw-semibold">
                                        <i class="fas fa-calendar text-success me-1"></i>
                                        {{ \Carbon\Carbon::parse($student->studentProfile->tanggal_lahir)->format('d F Y') }}
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

        <!-- Academic & Account Information -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light">
                    <h6 class="mb-0 fw-semibold text-primary">
                        <i class="fas fa-graduation-cap me-2"></i>Informasi Akademik & Akun
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="d-flex flex-column">
                                <span class="small text-muted">Kelas</span>
                                @if($student->studentProfile && $student->studentProfile->kelas)
                                    <span class="badge bg-primary-subtle text-primary border mt-1 align-self-start">{{ $student->studentProfile->kelas }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex flex-column">
                                <span class="small text-muted">Status Akun</span>
                                <span class="badge bg-success-subtle text-success border mt-1 align-self-start">
                                    <i class="fas fa-check-circle me-1"></i>Aktif
                                </span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex flex-column">
                                <span class="small text-muted">Nomor Telepon Orang Tua</span>
                                @if($student->studentProfile && $student->studentProfile->nomor_telepon_orangtua)
                                    <a href="tel:{{ $student->studentProfile->nomor_telepon_orangtua }}" class="text-primary text-decoration-none">
                                        {{ $student->studentProfile->nomor_telepon_orangtua }}
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex flex-column">
                                <span class="small text-muted">Tanggal Bergabung</span>
                                <span class="fw-semibold">
                                    <i class="fas fa-calendar-plus text-success me-1"></i>
                                    {{ $student->created_at->format('d F Y') }}
                                </span>
                                <small class="text-muted">({{ $student->created_at->diffForHumans() }})</small>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex flex-column">
                                <span class="small text-muted">Terakhir Diperbarui</span>
                                <span class="fw-semibold">
                                    <i class="fas fa-clock text-info me-1"></i>
                                    {{ $student->updated_at->format('d F Y, H:i') }}
                                </span>
                                <small class="text-muted">({{ $student->updated_at->diffForHumans() }})</small>
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
                        <a href="{{ route('admin.siswa.edit', $student->id) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-2"></i>Edit Data Siswa
                        </a>
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <i class="fas fa-trash me-2"></i>Hapus Siswa
                        </button>
                        <a href="{{ route('admin.siswa.index') }}" class="btn btn-outline-secondary">
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
                    <i class="fas fa-exclamation-triangle me-2"></i>Konfirmasi Hapus Siswa
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus data siswa <strong>{{ $student->name }}</strong>?</p>
                <div class="alert alert-warning d-flex align-items-center" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <div>Tindakan ini tidak dapat dibatalkan! Semua data terkait siswa ini akan dihapus secara permanen.</div>
                </div>
            </div>
            <div class="modal-footer border-top-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Batal
                </button>
                <form action="{{ route('admin.siswa.destroy', $student->id) }}" method="POST" class="d-inline">
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
