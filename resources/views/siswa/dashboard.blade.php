@extends('layouts.app')

@section('title', 'Dashboard Siswa')

@section('content')
<div class="container-fluid py-4">
    <!-- Welcome Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card card-modern border-0 shadow-sm">
                <div class="card-body p-4">
                    <h1 class="h3 text-high-contrast fw-bold mb-2">
                        <i class="fas fa-user-graduate text-primary me-2"></i>Dashboard Siswa
                    </h1>
                    <p class="text-subtle mb-0">Selamat datang, <strong>{{ auth()->user()->name }}</strong>! Semangat belajar hari ini.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="card card-stats h-100 hover-card">
                <div class="card-body text-center p-4">
                    <div class="bg-info bg-opacity-10 rounded-circle p-3 d-inline-flex align-items-center justify-content-center mb-3">
                        <i class="fas fa-user-check text-info fs-1"></i>
                    </div>
                    <h5 class="card-title text-high-contrast fw-semibold">Absensi</h5>
                    <p class="text-subtle mb-3">Lapor izin/sakit dari rumah</p>
                    <a href="{{ route('siswa.absensi.index') }}" class="btn btn-info">
                        <i class="fas fa-clipboard-check me-2"></i>Buka Absensi
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card card-stats h-100 hover-card">
                <div class="card-body text-center p-4">
                    <div class="bg-primary bg-opacity-10 rounded-circle p-3 d-inline-flex align-items-center justify-content-center mb-3">
                        <i class="fas fa-calendar-alt text-primary fs-1"></i>
                    </div>
                    <h5 class="card-title text-high-contrast fw-semibold">Jadwal Pelajaran</h5>
                    <p class="text-subtle mb-3">Lihat jadwal lengkap minggu ini</p>
                    <a href="{{ route('siswa.jadwal-pelajaran.index') }}" class="btn btn-primary">
                        <i class="fas fa-table me-2"></i>Lihat Jadwal
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Informasi Penting -->
    <div class="row">
        <div class="col-12">
            <div class="card card-stats border-0 shadow-sm">
                <div class="card-header bg-light border-bottom-0 py-3">
                    <h5 class="card-title mb-0 text-high-contrast fw-semibold">
                        <i class="fas fa-info-circle text-primary me-2"></i>Informasi Penting
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="alert alert-info border border-info border-opacity-25 bg-info bg-opacity-10 mb-0" role="alert">
                                <div class="d-flex align-items-start">
                                    <i class="fas fa-calendar-alt text-info me-3 mt-1 flex-shrink-0 fs-4"></i>
                                    <div>
                                        <h6 class="fw-bold text-info mb-2">Jadwal Pelajaran</h6>
                                        <p class="mb-2 text-high-contrast small">Lihat jadwal lengkap pelajaran Anda untuk minggu ini</p>
                                        <a href="{{ route('siswa.jadwal-pelajaran.index') }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-table me-1"></i>Lihat Jadwal
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="alert alert-primary border border-primary border-opacity-25 bg-primary bg-opacity-10 mb-0" role="alert">
                                <div class="d-flex align-items-start">
                                    <i class="fas fa-clipboard-check text-primary me-3 mt-1 flex-shrink-0 fs-4"></i>
                                    <div>
                                        <h6 class="fw-bold text-primary mb-2">Absensi</h6>
                                        <p class="mb-2 text-high-contrast small">Laporkan jika Anda berhalangan hadir (izin/sakit)</p>
                                        <a href="{{ route('siswa.absensi.index') }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-user-check me-1"></i>Lapor Absensi
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3 p-3 bg-light rounded">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-lightbulb text-warning fs-4 me-3"></i>
                            <div>
                                <h6 class="mb-1 text-high-contrast fw-semibold">Tips Belajar</h6>
                                <p class="mb-0 text-subtle small">Selalu periksa jadwal pelajaran dan jangan lupa untuk selalu rajin belajar. Semangat!</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
