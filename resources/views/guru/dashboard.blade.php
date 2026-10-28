@extends('layouts.app')

@section('title', 'Dashboard Guru')

@section('content')
<div class="container-fluid">
    <!-- Welcome Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="bg-white rounded-3 shadow-sm p-4">
                <h1 class="h3 mb-2">
                    <i class="fas fa-chalkboard-teacher text-primary me-2"></i>Dashboard Guru
                </h1>
                <p class="text-muted mb-0">Selamat datang, {{ auth()->user()->name }}! Kelola tugas dan kehadiran Anda dengan mudah.</p>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row g-4 mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 hover-card">
                <div class="card-body text-center p-4">
                    <div class="text-warning fs-1 mb-3">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <h5 class="card-title">Jadwal Hari Ini</h5>
                    <p class="card-text text-muted">Lihat jadwal mengajar hari ini</p>
                    <a href="{{ route('guru.jadwal-mengajar.today') }}" class="btn btn-outline-warning">
                        <i class="fas fa-eye me-1"></i> Lihat Jadwal
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 hover-card">
                <div class="card-body text-center p-4">
                    <div class="text-primary fs-1 mb-3">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <h5 class="card-title">Tugas Guru</h5>
                    <p class="card-text text-muted">Lihat dan kerjakan tugas dari kepala sekolah</p>
                    <a href="{{ route('guru.tugas-guru.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-list me-1"></i> Lihat Tugas
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 hover-card">
                <div class="card-body text-center p-4">
                    <div class="text-success fs-1 mb-3">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <h5 class="card-title">Absensi</h5>
                    <p class="card-text text-muted">Kelola absensi kehadiran Anda</p>
                    <a href="{{ route('guru.absensi-guru') }}" class="btn btn-outline-success">
                        <i class="fas fa-clock me-1"></i> Lihat Absensi
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 hover-card">
                <div class="card-body text-center p-4">
                    <div class="text-info fs-1 mb-3">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <h5 class="card-title">Profil Saya</h5>
                    <p class="card-text text-muted">Kelola informasi profil Anda</p>
                    <a href="{{ route('profile.show') }}" class="btn btn-outline-info">
                        <i class="fas fa-edit me-1"></i> Edit Profil
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if(auth()->user()->canManageBerita() || auth()->user()->canManagePrestasi())
    <!-- Publication Access Quick Cards -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <h5 class="fw-bold text-dark mb-0">
                <i class="fas fa-bullhorn text-primary me-2"></i>Pengelolaan Publikasi Sekolah
            </h5>
        </div>
        @if(auth()->user()->canManageBerita())
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100 bg-primary bg-opacity-10 border-start border-primary border-4">
                <div class="card-body p-4 d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="card-title text-primary fw-bold mb-1">Berita Sekolah</h5>
                        <p class="card-text text-muted mb-0 small">Anda memiliki akses untuk mengelola publikasi berita dan pengumuman sekolah.</p>
                    </div>
                    <a href="{{ route('guru.berita.index') }}" class="btn btn-primary btn-sm ms-3 text-nowrap">
                        <i class="fas fa-newspaper me-1"></i> Buka Berita
                    </a>
                </div>
            </div>
        </div>
        @endif
        @if(auth()->user()->canManagePrestasi())
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100 bg-warning bg-opacity-10 border-start border-warning border-4">
                <div class="card-body p-4 d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="card-title text-warning-emphasis fw-bold mb-1">Prestasi Sekolah</h5>
                        <p class="card-text text-muted mb-0 small">Anda memiliki akses untuk mencatat dan memperbarui capaian prestasi sekolah.</p>
                    </div>
                    <a href="{{ route('guru.prestasi.index') }}" class="btn btn-warning btn-sm ms-3 text-nowrap">
                        <i class="fas fa-trophy me-1"></i> Buka Prestasi
                    </a>
                </div>
            </div>
        </div>
        @endif
    </div>
    @endif

    <!-- Statistics Overview -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-primary fs-2 mb-3">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <h5 class="card-title text-muted">Tugas Aktif</h5>
                    <h3 class="text-primary">{{ $totalTugasAktif }}</h3>
                    <p class="text-muted small mb-0">Dari Kepala Sekolah</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-success fs-2 mb-3">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h5 class="card-title text-muted">Sudah Dikerjakan</h5>
                    <h3 class="text-success">{{ $tugasSaya }}</h3>
                    <p class="text-muted small mb-0">Tugas Selesai</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-warning fs-2 mb-3">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <h5 class="card-title text-muted">Belum Dikerjakan</h5>
                    <h3 class="text-warning">{{ $tugasBelumDikumpulkan }}</h3>
                    <p class="text-muted small mb-0">Perlu Diselesaikan</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-info fs-2 mb-3">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <h5 class="card-title text-muted">Kehadiran Bulan Ini</h5>
                    <h3 class="text-info">{{ $hadirCount }}/{{ $absensiCount }}</h3>
                    <p class="text-muted small mb-0">Hari Hadir</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row">
        <!-- Tugas Menunggu -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-exclamation-circle me-2"></i>Tugas Belum Dikerjakan
                    </h5>
                </div>
                <div class="card-body">
                    @forelse($tugasMenunggu as $tugas)
                    <div class="list-group-item d-flex justify-content-between align-items-start border-0 px-0 {{ !$loop->last ? 'border-bottom pb-3 mb-3' : '' }}">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">{{ $tugas->judul }}</h6>
                            <p class="mb-1 text-muted small">{{ Str::limit($tugas->deskripsi, 80) }}</p>
                            <small class="text-muted">
                                <i class="fas fa-calendar me-1"></i>
                                Deadline: {{ \Carbon\Carbon::parse($tugas->deadline)->format('d M Y, H:i') }}
                            </small>
                        </div>
                        <div class="ms-3">
                            @php
                                $deadline = \Carbon\Carbon::parse($tugas->deadline);
                                $now = \Carbon\Carbon::now();
                                $diffDays = $now->diffInDays($deadline, false);
                            @endphp
                            @if($diffDays < 0)
                                <span class="badge bg-danger">Terlambat</span>
                            @elseif($diffDays <= 2)
                                <span class="badge bg-warning">{{ abs($diffDays) }} hari</span>
                            @else
                                <span class="badge bg-success">{{ abs($diffDays) }} hari</span>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4">
                        <i class="fas fa-check-circle text-success fs-1 mb-3"></i>
                        <p class="text-muted mb-0">Semua tugas sudah dikerjakan!</p>
                    </div>
                    @endforelse

                    @if($tugasMenunggu->count() > 0)
                    <div class="text-center mt-3">
                        <a href="{{ route('guru.tugas-guru.index') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-list me-1"></i> Lihat Semua Tugas
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Aktivitas Terbaru -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-history me-2"></i>Tugas Yang Sudah Dikerjakan
                    </h5>
                </div>
                <div class="card-body">
                    @forelse($submissionTerbaru as $tugas)
                        @php
                            $submission = $tugas->submissions->first();
                        @endphp
                        <div class="list-group-item d-flex align-items-start border-0 px-0 {{ !$loop->last ? 'border-bottom pb-3 mb-3' : '' }}">
                            <div class="me-3">
                                @if($submission->status_pengumpulan === 'dikumpulkan')
                                    <div class="text-success">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                @elseif($submission->status_pengumpulan === 'terlambat')
                                    <div class="text-warning">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                @else
                                    <div class="text-secondary">
                                        <i class="fas fa-file-alt"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">{{ $tugas->judul }}</h6>
                                <p class="mb-1 text-muted small">
                                    Status:
                                    @if($submission->status_pengumpulan === 'dikumpulkan')
                                        <span class="badge bg-success">Dikumpulkan</span>
                                    @elseif($submission->status_pengumpulan === 'terlambat')
                                        <span class="badge bg-warning">Terlambat</span>
                                    @else
                                        <span class="badge bg-secondary">Draft</span>
                                    @endif

                                    @if($submission->nilai)
                                        • Nilai: <strong>{{ $submission->nilai }}</strong>
                                    @endif
                                </p>
                                <small class="text-muted">
                                    {{ \Carbon\Carbon::parse($submission->tanggal_submit)->diffForHumans() }}
                                </small>
                            </div>
                        </div>
                    @empty
                    <div class="text-center py-4">
                        <i class="fas fa-inbox text-muted fs-1 mb-3"></i>
                        <p class="text-muted mb-0">Belum ada tugas yang dikerjakan</p>
                    </div>
                    @endforelse

                    @if($submissionTerbaru->count() > 0)
                    <div class="text-center mt-3">
                        <a href="{{ route('guru.tugas-guru.index') }}" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-history me-1"></i> Lihat Riwayat
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.hover-card {
    transition: all 0.3s ease;
}

.hover-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}
</style>
@endsection
