@extends('layouts.semester')

@section('title', 'Dashboard Semester - ' . $semester->full_name)

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
                                        <a href="{{ route('admin.tahun-pelajaran.dashboard', $semester->tahunPelajaran->id) }}">{{ $semester->tahunPelajaran->nama }}</a>
                                    </li>
                                    <li class="breadcrumb-item active">{{ $semester->nama }}</li>
                                </ol>
                            </nav>
                            <h1 class="h3 mb-2">
                                <i class="fas fa-calendar-alt me-2 text-primary"></i>
                                {{ $semester->full_name }}
                                @if($semester->is_active)
                                <span class="badge bg-success ms-2">
                                    <i class="fas fa-check-circle me-1"></i>Aktif
                                </span>
                                @endif
                            </h1>
                            <p class="text-muted mb-0">
                                <i class="fas fa-calendar me-2"></i>
                                {{ $semester->tanggal_mulai->format('d F Y') }} - {{ $semester->tanggal_selesai->format('d F Y') }}
                            </p>
                        </div>
                        <div>
                            <a href="{{ route('admin.tahun-pelajaran.dashboard', $semester->tahunPelajaran->id) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
    <div class="row mb-3">
        <div class="col-12">
            <div class="alert alert-success alert-dismissible alert-permanent show" role="alert" id="successAlert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" onclick="document.getElementById('successAlert').remove()" aria-label="Close"></button>
            </div>
        </div>
    </div>
    @endif

    <!-- Error Message -->
    @if(session('error'))
    <div class="row mb-3">
        <div class="col-12">
            <div class="alert alert-danger alert-dismissible alert-permanent show" role="alert" id="errorAlert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" onclick="document.getElementById('errorAlert').remove()" aria-label="Close"></button>
            </div>
        </div>
    </div>
    @endif

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm border-start border-primary border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">Total Mata Pelajaran</p>
                            <h3 class="mb-0 fw-bold text-primary">{{ $statistics['total_mata_pelajaran'] ?? 0 }}</h3>
                        </div>
                        <div class="text-primary">
                            <i class="fas fa-book fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm border-start border-warning border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">Jam Pelajaran</p>
                            <h3 class="mb-0 fw-bold text-warning">{{ $statistics['total_jam_pelajaran'] ?? 0 }}</h3>
                        </div>
                        <div class="text-warning">
                            <i class="fas fa-clock fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm border-start border-info border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">Jadwal Tetap</p>
                            <h3 class="mb-0 fw-bold text-info">{{ $statistics['total_fixed_schedule'] ?? 0 }}</h3>
                        </div>
                        <div class="text-info">
                            <i class="fas fa-calendar-check fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0 fw-semibold">
                        <i class="fas fa-bolt me-2"></i>Aksi Cepat
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            @if(!$semester->is_active)
                            <form action="{{ route('admin.semester.set-active', $semester->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="fas fa-check-circle me-2"></i>Aktifkan Semester Ini
                                </button>
                            </form>
                            @else
                            <button type="button" class="btn btn-success w-100" disabled>
                                <i class="fas fa-check-circle me-2"></i>Semester Sedang Aktif
                            </button>
                            @endif
                        </div>
                        <div class="col-md-6 mb-2">
                            <a href="{{ route('admin.semester.edit', $semester->id) }}" class="btn btn-warning w-100">
                                <i class="fas fa-edit me-2"></i>Edit Semester
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Import Data Section (Semester 2 Only) -->
    @if($semester->semester_ke == 2)
        @php
            $hasData = \App\Models\MataPelajaran::where('semester_id', $semester->id)->exists() ||
                       \App\Models\JamPelajaran::where('semester_id', $semester->id)->exists() ||
                       \App\Models\FixedSchedule::where('semester_id', $semester->id)->exists();
        @endphp

        @if(!$hasData)
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm border-warning">
                    <div class="card-header bg-warning text-dark">
                        <h6 class="mb-0 fw-semibold">
                            <i class="fas fa-download me-2"></i>Import Data dari Semester Ganjil
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info alert-permanent mb-3">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Semester Genap belum memiliki data.</strong>
                            <p class="mb-0 mt-2">Anda dapat mengimport data dari Semester Ganjil (Semester 1) di tahun pelajaran yang sama. Data yang akan di-copy meliputi:</p>
                            <ul class="mb-0 mt-2">
                                <li>Mata Pelajaran</li>
                                <li>Jam Pelajaran</li>
                                <li>Jadwal Tetap</li>
                            </ul>
                        </div>
                        <form action="{{ route('admin.semester.import-from-semester-1', $semester->id) }}" method="POST"
                              onsubmit="return confirm('Apakah Anda yakin ingin import data dari Semester Ganjil? Data akan di-copy ke Semester Genap ini.')">
                            @csrf
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-download me-2"></i>Import Data dari Semester Ganjil
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endif
    @endif

    <!-- Management Cards -->
    <div class="row">
        <!-- Mata Pelajaran -->
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card h-100 shadow-sm hover-shadow">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <i class="fas fa-book fa-3x text-primary"></i>
                    </div>
                    <h5 class="card-title fw-semibold">Mata Pelajaran</h5>
                    <p class="card-text text-muted small">Kelola mata pelajaran untuk semester ini</p>
                    <a href="{{ route('admin.mapel.index', ['semester_id' => $semester->id]) }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-arrow-right me-2"></i>Kelola
                    </a>
                </div>
            </div>
        </div>

        <!-- Jadwal Pelajaran -->
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card h-100 shadow-sm hover-shadow">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <i class="fas fa-calendar-day fa-3x text-success"></i>
                    </div>
                    <h5 class="card-title fw-semibold">Jadwal Pelajaran</h5>
                    <p class="card-text text-muted small">Atur jadwal mengajar per kelas</p>
                    <a href="{{ route('admin.jadwal.index', ['semester_id' => $semester->id]) }}" class="btn btn-outline-success btn-sm">
                        <i class="fas fa-arrow-right me-2"></i>Kelola
                    </a>
                </div>
            </div>
        </div>

        <!-- Jam Pelajaran -->
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card h-100 shadow-sm hover-shadow">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <i class="fas fa-clock fa-3x text-warning"></i>
                    </div>
                    <h5 class="card-title fw-semibold">Jam Pelajaran</h5>
                    <p class="card-text text-muted small">Tentukan waktu jam pelajaran</p>
                    <a href="{{ route('admin.jam-pelajaran.index', ['semester_id' => $semester->id]) }}" class="btn btn-outline-warning btn-sm">
                        <i class="fas fa-arrow-right me-2"></i>Kelola
                    </a>
                </div>
            </div>
        </div>

        <!-- Jadwal Tetap -->
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card h-100 shadow-sm hover-shadow">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <i class="fas fa-calendar-check fa-3x text-info"></i>
                    </div>
                    <h5 class="card-title fw-semibold">Jadwal Tetap</h5>
                    <p class="card-text text-muted small">Kelola jadwal kegiatan tetap</p>
                    <a href="{{ route('admin.fixed-schedule.index', ['semester_id' => $semester->id]) }}" class="btn btn-outline-info btn-sm">
                        <i class="fas fa-arrow-right me-2"></i>Kelola
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Semester Info -->
    @if($semester->keterangan)
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-start border-primary border-4">
                <div class="card-body">
                    <h6 class="fw-semibold mb-2">
                        <i class="fas fa-info-circle me-2"></i>Keterangan
                    </h6>
                    <p class="mb-0">{{ $semester->keterangan }}</p>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('styles')
<style>
.hover-shadow {
    transition: all 0.3s ease;
}
.hover-shadow:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15) !important;
}
</style>
@endpush

@push('scripts')
<script>
// Override auto-dismiss behavior for alerts on this page
document.addEventListener('DOMContentLoaded', function() {
    // Remove all alert auto-dismiss functionality
    const alerts = document.querySelectorAll('.alert-permanent');
    alerts.forEach(function(alert) {
        // Prevent the alert from being auto-closed
        alert.addEventListener('close.bs.alert', function(e) {
            // Only allow close if triggered by button click
            if (!e.target.querySelector('.btn-close:active')) {
                // This will keep the alert visible
            }
        });
    });
});
</script>
@endpush
