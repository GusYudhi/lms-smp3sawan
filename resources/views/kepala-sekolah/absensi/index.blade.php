@extends('layouts.app')

@section('title', 'Data Absensi')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h1 class="h3 mb-2 text-success">
                                <i class="fas fa-clipboard-check me-2"></i>Data Absensi
                            </h1>
                            <p class="text-muted mb-0">Monitoring kehadiran guru dan siswa</p>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ route('kepala-sekolah.dashboard') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Selection Cards -->
    <div class="row g-4">
        <!-- Absensi Siswa -->
        <div class="col-md-6">
            <a href="{{ route('kepala-sekolah.absensi.siswa.index') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100 hover-card" style="transition: all 0.3s ease;">
                    <div class="card-body text-center p-5">
                        <div class="mb-4">
                            <div class="rounded-circle bg-primary bg-opacity-10 d-inline-flex align-items-center justify-content-center"
                                 style="width: 100px; height: 100px;">
                                <i class="fas fa-user-graduate text-primary" style="font-size: 48px;"></i>
                            </div>
                        </div>
                        <h3 class="h4 mb-3 text-dark">Rekap Absensi Siswa</h3>
                        <p class="text-muted mb-4">
                            Lihat rekap kehadiran siswa dengan filter periode (hari ini, minggu ini, bulan ini, semester ini)
                        </p>
                        <div class="d-flex justify-content-around mt-4 text-start">
                            <div>
                                <small class="text-muted d-block">Filter</small>
                                <strong class="text-primary">Per Kelas</strong>
                            </div>
                            <div>
                                <small class="text-muted d-block">Detail</small>
                                <strong class="text-primary">Per Siswa</strong>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-primary text-white text-center py-3">
                        <i class="fas fa-arrow-right me-2"></i>Lihat Rekap Siswa
                    </div>
                </div>
            </a>
        </div>

        <!-- Absensi Guru -->
        <div class="col-md-6">
            <a href="{{ route('kepala-sekolah.absensi.guru.index') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100 hover-card" style="transition: all 0.3s ease;">
                    <div class="card-body text-center p-5">
                        <div class="mb-4">
                            <div class="rounded-circle bg-success bg-opacity-10 d-inline-flex align-items-center justify-content-center"
                                 style="width: 100px; height: 100px;">
                                <i class="fas fa-chalkboard-teacher text-success" style="font-size: 48px;"></i>
                            </div>
                        </div>
                        <h3 class="h4 mb-3 text-dark">Rekap Absensi Guru</h3>
                        <p class="text-muted mb-4">
                            Lihat rekap kehadiran guru dengan filter periode (hari ini, minggu ini, bulan ini, semester ini)
                        </p>
                        <div class="d-flex justify-content-around mt-4 text-start">
                            <div>
                                <small class="text-muted d-block">Persentase</small>
                                <strong class="text-success">Kehadiran</strong>
                            </div>
                            <div>
                                <small class="text-muted d-block">Detail</small>
                                <strong class="text-success">Per Guru</strong>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-success text-white text-center py-3">
                        <i class="fas fa-arrow-right me-2"></i>Lihat Rekap Guru
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Info Section -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card border-0 bg-light">
                <div class="card-body p-4">
                    <h5 class="card-title mb-3">
                        <i class="fas fa-info-circle text-info me-2"></i>Informasi Fitur
                    </h5>
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary"><i class="fas fa-filter me-2"></i>Filter Periode</h6>
                            <ul class="list-unstyled ps-3">
                                <li><i class="fas fa-check text-success me-2"></i>Hari Ini</li>
                                <li><i class="fas fa-check text-success me-2"></i>Minggu Ini</li>
                                <li><i class="fas fa-check text-success me-2"></i>Bulan Ini</li>
                                <li><i class="fas fa-check text-success me-2"></i>Semester Ini</li>
                                <li><i class="fas fa-check text-success me-2"></i>Custom (Pilih Tanggal)</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-primary"><i class="fas fa-chart-bar me-2"></i>Data yang Ditampilkan</h6>
                            <ul class="list-unstyled ps-3">
                                <li><i class="fas fa-check text-success me-2"></i>Total Hadir</li>
                                <li><i class="fas fa-check text-success me-2"></i>Total Sakit</li>
                                <li><i class="fas fa-check text-success me-2"></i>Total Izin</li>
                                <li><i class="fas fa-check text-success me-2"></i>Total Alpha</li>
                                <li><i class="fas fa-check text-success me-2"></i>Total Terlambat</li>
                                <li><i class="fas fa-check text-success me-2"></i>Persentase Kehadiran</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.hover-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.15) !important;
}
</style>
@endsection
