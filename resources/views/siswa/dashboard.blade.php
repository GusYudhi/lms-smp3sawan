@extends('layouts.app')

@section('title', 'Dashboard Siswa')

@section('content')
<div class="container-fluid">
    <!-- Welcome Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="bg-white rounded-3 shadow-sm p-4">
                <h1 class="h3 mb-2 text-primary">
                    <i class="fas fa-user-graduate me-2"></i>Dashboard Siswa
                </h1>
                <p class="text-muted mb-0">Selamat datang, {{ auth()->user()->name }}! Semangat belajar hari ini.</p>
            </div>
        </div>
    </div>

    <!-- Student Overview -->
    <div class="row g-4 mb-4">
        <div class="col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm h-100 hover-card">
                <div class="card-body text-center p-4">
                    <div class="text-primary fs-1 mb-3">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <h5 class="card-title">Materi Terbaru</h5>
                    <p class="card-text text-muted">Akses materi pembelajaran terkini</p>
                    <a href="#" class="btn btn-outline-primary">
                        <i class="fas fa-eye me-1"></i> Lihat Materi
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm h-100 hover-card">
                <div class="card-body text-center p-4">
                    <div class="text-warning fs-1 mb-3">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <h5 class="card-title">Tugas Pending</h5>
                    <p class="card-text text-muted">{{ rand(2, 8) }} tugas belum diselesaikan</p>
                    <a href="#" class="btn btn-outline-warning">
                        <i class="fas fa-tasks me-1"></i> Kerjakan
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm h-100 hover-card">
                <div class="card-body text-center p-4">
                    <div class="text-success fs-1 mb-3">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h5 class="card-title">Nilai Terbaru</h5>
                    <p class="card-text text-muted">Lihat progress dan pencapaian</p>
                    <a href="#" class="btn btn-outline-success">
                        <i class="fas fa-chart-bar me-1"></i> Lihat Nilai
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
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <h5 class="card-title text-muted">Rata-rata Nilai</h5>
                    <h3 class="text-primary">{{ rand(75, 90) }}</h3>
                    <p class="text-muted small mb-0">Semester Ini</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-success fs-2 mb-3">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h5 class="card-title text-muted">Tugas Selesai</h5>
                    <h3 class="text-success">{{ rand(85, 95) }}%</h3>
                    <p class="text-muted small mb-0">Tingkat Penyelesaian</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-info fs-2 mb-3">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <h5 class="card-title text-muted">Kehadiran</h5>
                    <h3 class="text-info">{{ rand(90, 98) }}%</h3>
                    <p class="text-muted small mb-0">Bulan Ini</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-warning fs-2 mb-3">
                        <i class="fas fa-book"></i>
                    </div>
                    <h5 class="card-title text-muted">Mata Pelajaran</h5>
                    <h3 class="text-warning">{{ rand(8, 12) }}</h3>
                    <p class="text-muted small mb-0">Mapel Aktif</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Schedule -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-calendar-alt me-2"></i>Jadwal Pelajaran Hari Ini
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
                                        <span class="badge bg-primary">08:00-09:30</span>
                                    </div>
                                    <p class="card-text text-muted small">Pak Agus • Ruang 101</p>
                                    <p class="card-text small">Aljabar Dasar</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4">
                            <div class="card border-start border-success border-4">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="card-title mb-1">Bahasa Indonesia</h6>
                                        <span class="badge bg-success">09:45-11:15</span>
                                    </div>
                                    <p class="card-text text-muted small">Bu Sari • Ruang 102</p>
                                    <p class="card-text small">Puisi dan Pantun</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4">
                            <div class="card border-start border-info border-4">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="card-title mb-1">IPA</h6>
                                        <span class="badge bg-info">13:00-14:30</span>
                                    </div>
                                    <p class="card-text text-muted small">Pak Budi • Lab IPA</p>
                                    <p class="card-text small">Sistem Tata Surya</p>
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

    <!-- Pending Assignments and Recent Activities -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-clipboard-check me-2"></i>Tugas Yang Harus Dikerjakan
                    </h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-start border-0 px-0">
                            <div class="flex-grow-1">
                                <h6 class="mb-1">PR Matematika - Aljabar</h6>
                                <p class="mb-1 text-muted small">Pak Agus • 15 soal pilihan ganda</p>
                                <small class="text-muted">Dikumpulkan: Besok, 14 November 2025</small>
                            </div>
                            <span class="badge bg-danger">1 hari</span>
                        </div>

                        <div class="list-group-item d-flex justify-content-between align-items-start border-0 px-0">
                            <div class="flex-grow-1">
                                <h6 class="mb-1">Essay Bahasa Indonesia</h6>
                                <p class="mb-1 text-muted small">Bu Sari • Menulis puisi bebas</p>
                                <small class="text-muted">Dikumpulkan: 16 November 2025</small>
                            </div>
                            <span class="badge bg-warning">3 hari</span>
                        </div>

                        <div class="list-group-item d-flex justify-content-between align-items-start border-0 px-0">
                            <div class="flex-grow-1">
                                <h6 class="mb-1">Laporan Praktikum IPA</h6>
                                <p class="mb-1 text-muted small">Pak Budi • Percobaan sistem tata surya</p>
                                <small class="text-muted">Dikumpulkan: 18 November 2025</small>
                            </div>
                            <span class="badge bg-success">5 hari</span>
                        </div>
                    </div>

                    <div class="text-center mt-3">
                        <a href="#" class="btn btn-outline-primary">
                            <i class="fas fa-tasks me-1"></i> Lihat Semua Tugas
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-star me-2"></i>Prestasi Terbaru
                    </h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex align-items-center border-0 px-0">
                            <div class="text-success me-3">
                                <i class="fas fa-trophy"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">Nilai Terbaik</h6>
                                <p class="mb-0 text-muted small">Matematika: 95</p>
                            </div>
                        </div>

                        <div class="list-group-item d-flex align-items-center border-0 px-0">
                            <div class="text-info me-3">
                                <i class="fas fa-medal"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">Kehadiran Sempurna</h6>
                                <p class="mb-0 text-muted small">Bulan Oktober</p>
                            </div>
                        </div>

                        <div class="list-group-item d-flex align-items-center border-0 px-0">
                            <div class="text-primary me-3">
                                <i class="fas fa-award"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">Tugas Tepat Waktu</h6>
                                <p class="mb-0 text-muted small">95% penyelesaian</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-bell me-2"></i>Pengumuman
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info border-0" role="alert">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Ujian Tengah Semester</strong> akan dimulai tanggal 25 November 2025. Persiapkan diri dengan baik!
                    </div>

                    <div class="alert alert-success border-0" role="alert">
                        <i class="fas fa-calendar-check me-2"></i>
                        <strong>Libur Nasional</strong> tanggal 17 November 2025 (Hari Guru Nasional)
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
