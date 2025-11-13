@extends('layouts.app')

@section('title', 'Dashboard Guru')

@section('content')
<div class="container-fluid">
    <!-- Welcome Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="bg-white rounded-3 shadow-sm p-4">
                <h1 class="h3 mb-2 text-primary">
                    <i class="fas fa-chalkboard-teacher me-2"></i>Dashboard Guru
                </h1>
                <p class="text-muted mb-0">Selamat datang, {{ auth()->user()->name }}! Kelola pembelajaran Anda dengan mudah.</p>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row g-4 mb-4">
        <div class="col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm h-100 hover-card">
                <div class="card-body text-center p-4">
                    <div class="text-primary fs-1 mb-3">
                        <i class="fas fa-upload"></i>
                    </div>
                    <h5 class="card-title">Upload Materi</h5>
                    <p class="card-text text-muted">Bagikan materi pembelajaran kepada siswa</p>
                    <a href="#" class="btn btn-outline-primary">
                        <i class="fas fa-plus me-1"></i> Tambah Materi
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm h-100 hover-card">
                <div class="card-body text-center p-4">
                    <div class="text-success fs-1 mb-3">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <h5 class="card-title">Buat Tugas</h5>
                    <p class="card-text text-muted">Buat dan kelola tugas untuk siswa</p>
                    <a href="#" class="btn btn-outline-success">
                        <i class="fas fa-plus me-1"></i> Buat Tugas
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm h-100 hover-card">
                <div class="card-body text-center p-4">
                    <div class="text-info fs-1 mb-3">
                        <i class="fas fa-check-square"></i>
                    </div>
                    <h5 class="card-title">Koreksi Nilai</h5>
                    <p class="card-text text-muted">Koreksi dan beri nilai tugas siswa</p>
                    <a href="#" class="btn btn-outline-info">
                        <i class="fas fa-edit me-1"></i> Koreksi
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Overview -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-primary fs-2 mb-3">
                        <i class="fas fa-users"></i>
                    </div>
                    <h5 class="card-title text-muted">Total Siswa</h5>
                    <h3 class="text-primary">{{ rand(120, 150) }}</h3>
                    <p class="text-muted small mb-0">Siswa Aktif</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-success fs-2 mb-3">
                        <i class="fas fa-book"></i>
                    </div>
                    <h5 class="card-title text-muted">Mata Pelajaran</h5>
                    <h3 class="text-success">{{ rand(2, 5) }}</h3>
                    <p class="text-muted small mb-0">Mapel Diampu</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-warning fs-2 mb-3">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <h5 class="card-title text-muted">Tugas Aktif</h5>
                    <h3 class="text-warning">{{ rand(5, 12) }}</h3>
                    <p class="text-muted small mb-0">Perlu Koreksi</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-info fs-2 mb-3">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <h5 class="card-title text-muted">Kelas Hari Ini</h5>
                    <h3 class="text-info">{{ rand(3, 6) }}</h3>
                    <p class="text-muted small mb-0">Jadwal Mengajar</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Schedule Overview -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-calendar-alt me-2"></i>Jadwal Mengajar Hari Ini
                    </h5>
                    <small class="text-muted">{{ date('l, d F Y') }}</small>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <!-- Sample Schedule Items -->
                        <div class="col-md-6 col-lg-4">
                            <div class="card border-start border-primary border-4">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="card-title mb-1">Matematika</h6>
                                        <span class="badge bg-primary">08:00</span>
                                    </div>
                                    <p class="card-text text-muted small">Kelas 7A • Ruang 101</p>
                                    <p class="card-text small">Materi: Aljabar Dasar</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4">
                            <div class="card border-start border-success border-4">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="card-title mb-1">Matematika</h6>
                                        <span class="badge bg-success">10:00</span>
                                    </div>
                                    <p class="card-text text-muted small">Kelas 7B • Ruang 102</p>
                                    <p class="card-text small">Materi: Aljabar Dasar</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4">
                            <div class="card border-start border-info border-4">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="card-title mb-1">Matematika</h6>
                                        <span class="badge bg-info">13:00</span>
                                    </div>
                                    <p class="card-text text-muted small">Kelas 8A • Ruang 201</p>
                                    <p class="card-text small">Materi: Geometri</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <a href="#" class="btn btn-outline-primary">
                            <i class="fas fa-calendar me-1"></i> Lihat Jadwal Lengkap
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-history me-2"></i>Aktivitas Terbaru
                    </h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex align-items-center border-0 px-0">
                            <div class="text-primary me-3">
                                <i class="fas fa-file-upload"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">Materi baru diupload</h6>
                                <p class="mb-1 text-muted small">Aljabar Dasar untuk Kelas 7A</p>
                                <small class="text-muted">2 jam yang lalu</small>
                            </div>
                        </div>

                        <div class="list-group-item d-flex align-items-center border-0 px-0">
                            <div class="text-success me-3">
                                <i class="fas fa-tasks"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">Tugas baru dibuat</h6>
                                <p class="mb-1 text-muted small">Latihan Soal Geometri - Deadline 25 Nov</p>
                                <small class="text-muted">1 hari yang lalu</small>
                            </div>
                        </div>

                        <div class="list-group-item d-flex align-items-center border-0 px-0">
                            <div class="text-info me-3">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">15 tugas telah dikoreksi</h6>
                                <p class="mb-1 text-muted small">Kelas 8B - Rata-rata nilai: 82</p>
                                <small class="text-muted">2 hari yang lalu</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-exclamation-circle me-2"></i>Tugas Menunggu
                    </h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-start border-0 px-0">
                            <div>
                                <h6 class="mb-1">PR Matematika</h6>
                                <p class="mb-1 text-muted small">Kelas 7A • 25 siswa</p>
                            </div>
                            <span class="badge bg-warning">5 hari</span>
                        </div>

                        <div class="list-group-item d-flex justify-content-between align-items-start border-0 px-0">
                            <div>
                                <h6 class="mb-1">Quiz Aljabar</h6>
                                <p class="mb-1 text-muted small">Kelas 8B • 30 siswa</p>
                            </div>
                            <span class="badge bg-danger">2 hari</span>
                        </div>

                        <div class="list-group-item d-flex justify-content-between align-items-start border-0 px-0">
                            <div>
                                <h6 class="mb-1">Latihan Geometri</h6>
                                <p class="mb-1 text-muted small">Kelas 9A • 28 siswa</p>
                            </div>
                            <span class="badge bg-success">7 hari</span>
                        </div>
                    </div>

                    <div class="text-center mt-3">
                        <a href="#" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-edit me-1"></i> Kelola Semua
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
