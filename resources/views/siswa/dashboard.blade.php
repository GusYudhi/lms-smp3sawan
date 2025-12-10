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

    <!-- Student Overview -->
    <div class="row g-4 mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="card card-stats h-100 hover-card">
                <div class="card-body text-center p-4">
                    <div class="bg-info bg-opacity-10 rounded-circle p-3 d-inline-flex align-items-center justify-content-center mb-3">
                        <i class="fas fa-user-check text-info fs-1"></i>
                    </div>
                    <h5 class="card-title text-high-contrast fw-semibold">Absensi</h5>
                    <p class="text-subtle mb-3">Lapor izin/sakit dari rumah</p>
                    <a href="{{ route('siswa.absensi.index') }}" class="btn btn-outline-info">
                        <i class="fas fa-clipboard-check me-2"></i>Buka Absensi
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card card-stats h-100 hover-card">
                <div class="card-body text-center p-4">
                    <div class="bg-primary bg-opacity-10 rounded-circle p-3 d-inline-flex align-items-center justify-content-center mb-3">
                        <i class="fas fa-book-open text-primary fs-1"></i>
                    </div>
                    <h5 class="card-title text-high-contrast fw-semibold">Materi Terbaru</h5>
                    <p class="text-subtle mb-3">Akses materi pembelajaran terkini</p>
                    <a href="#" class="btn btn-outline-primary">
                        <i class="fas fa-eye me-2"></i>Lihat Materi
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card card-stats h-100 hover-card">
                <div class="card-body text-center p-4">
                    <div class="bg-warning bg-opacity-10 rounded-circle p-3 d-inline-flex align-items-center justify-content-center mb-3">
                        <i class="fas fa-clipboard-list text-warning fs-1"></i>
                    </div>
                    <h5 class="card-title text-high-contrast fw-semibold">Tugas Pending</h5>
                    <p class="text-subtle mb-3">{{ rand(2, 8) }} tugas belum diselesaikan</p>
                    <a href="#" class="btn btn-outline-warning">
                        <i class="fas fa-tasks me-2"></i>Kerjakan
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card card-stats h-100 hover-card">
                <div class="card-body text-center p-4">
                    <div class="bg-success bg-opacity-10 rounded-circle p-3 d-inline-flex align-items-center justify-content-center mb-3">
                        <i class="fas fa-chart-line text-success fs-1"></i>
                    </div>
                    <h5 class="card-title text-high-contrast fw-semibold">Nilai Terbaru</h5>
                    <p class="text-subtle mb-3">Lihat progress dan pencapaian</p>
                    <a href="#" class="btn btn-outline-success">
                        <i class="fas fa-chart-bar me-2"></i>Lihat Nilai
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Overview -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card card-stats hover-card">
                <div class="card-body text-center p-4">
                    <div class="bg-primary bg-opacity-10 rounded-circle p-3 d-inline-flex align-items-center justify-content-center mb-3">
                        <i class="fas fa-graduation-cap text-primary fs-2"></i>
                    </div>
                    <h5 class="text-subtle fw-medium mb-2">Rata-rata Nilai</h5>
                    <h3 class="text-primary fw-bold mb-1">{{ rand(75, 90) }}</h3>
                    <p class="text-subtle small mb-0">Semester Ini</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card card-stats hover-card">
                <div class="card-body text-center p-4">
                    <div class="bg-success bg-opacity-10 rounded-circle p-3 d-inline-flex align-items-center justify-content-center mb-3">
                        <i class="fas fa-check-circle text-success fs-2"></i>
                    </div>
                    <h5 class="text-subtle fw-medium mb-2">Tugas Selesai</h5>
                    <h3 class="text-success fw-bold mb-1">{{ rand(85, 95) }}%</h3>
                    <p class="text-subtle small mb-0">Tingkat Penyelesaian</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card card-stats hover-card">
                <div class="card-body text-center p-4">
                    <div class="bg-info bg-opacity-10 rounded-circle p-3 d-inline-flex align-items-center justify-content-center mb-3">
                        <i class="fas fa-calendar-check text-info fs-2"></i>
                    </div>
                    <h5 class="text-subtle fw-medium mb-2">Kehadiran</h5>
                    <h3 class="text-info fw-bold mb-1">{{ rand(90, 98) }}%</h3>
                    <p class="text-subtle small mb-0">Bulan Ini</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card card-stats hover-card">
                <div class="card-body text-center p-4">
                    <div class="bg-warning bg-opacity-10 rounded-circle p-3 d-inline-flex align-items-center justify-content-center mb-3">
                        <i class="fas fa-book text-warning fs-2"></i>
                    </div>
                    <h5 class="text-subtle fw-medium mb-2">Mata Pelajaran</h5>
                    <h3 class="text-warning fw-bold mb-1">{{ rand(8, 12) }}</h3>
                    <p class="text-subtle small mb-0">Mapel Aktif</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Schedule -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card card-stats">
                <div class="card-header bg-light border-bottom-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0 text-high-contrast fw-semibold">
                                <i class="fas fa-calendar-alt text-primary me-2"></i>Jadwal Pelajaran Hari Ini
                            </h5>
                            <small class="text-subtle">{{ date('l, d F Y') }}</small>
                        </div>
                        <div class="badge bg-primary-subtle text-primary border">
                            <i class="fas fa-clock me-1"></i>{{ date('H:i') }}
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <!-- Sample Schedule Items -->
                        <div class="col-md-6 col-lg-4">
                            <div class="card border-0 border-start border-primary border-4 hover-card">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="card-title mb-1 text-high-contrast fw-semibold">Matematika</h6>
                                        <span class="badge bg-primary">08:00-09:30</span>
                                    </div>
                                    <p class="text-subtle small mb-2">
                                        <i class="fas fa-user me-1"></i>Pak Agus •
                                        <i class="fas fa-map-marker-alt me-1"></i>Ruang 101
                                    </p>
                                    <p class="text-high-contrast small mb-0">Aljabar Dasar</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4">
                            <div class="card border-0 border-start border-success border-4 hover-card">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="card-title mb-1 text-high-contrast fw-semibold">Bahasa Indonesia</h6>
                                        <span class="badge bg-success">09:45-11:15</span>
                                    </div>
                                    <p class="text-subtle small mb-2">
                                        <i class="fas fa-user me-1"></i>Bu Sari •
                                        <i class="fas fa-map-marker-alt me-1"></i>Ruang 102
                                    </p>
                                    <p class="text-high-contrast small mb-0">Puisi dan Pantun</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4">
                            <div class="card border-0 border-start border-info border-4 hover-card">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="card-title mb-1 text-high-contrast fw-semibold">IPA</h6>
                                        <span class="badge bg-info">13:00-14:30</span>
                                    </div>
                                    <p class="text-subtle small mb-2">
                                        <i class="fas fa-user me-1"></i>Pak Budi •
                                        <i class="fas fa-map-marker-alt me-1"></i>Lab IPA
                                    </p>
                                    <p class="text-high-contrast small mb-0">Sistem Tata Surya</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <a href="#" class="btn btn-outline-primary">
                            <i class="fas fa-calendar me-2"></i>Lihat Jadwal Lengkap
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Assignments and Recent Activities -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card card-stats border-0 shadow-sm">
                <div class="card-header bg-light border-bottom-0 py-3">
                    <h5 class="card-title mb-0 text-high-contrast fw-semibold">
                        <i class="fas fa-clipboard-check text-primary me-2"></i>Tugas Yang Harus Dikerjakan
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-start border-0 px-0 py-3">
                            <div class="flex-grow-1">
                                <h6 class="mb-2 text-high-contrast fw-semibold">PR Matematika - Aljabar</h6>
                                <p class="mb-1 text-subtle small">
                                    <i class="fas fa-user me-1"></i>Pak Agus •
                                    <i class="fas fa-list-ol me-1"></i>15 soal pilihan ganda
                                </p>
                                <small class="text-subtle">
                                    <i class="fas fa-calendar-alt me-1"></i>Dikumpulkan: Besok, 14 November 2025
                                </small>
                            </div>
                            <span class="badge bg-danger-subtle text-danger border ms-3">1 hari</span>
                        </div>

                        <div class="list-group-item d-flex justify-content-between align-items-start border-0 px-0 py-3">
                            <div class="flex-grow-1">
                                <h6 class="mb-2 text-high-contrast fw-semibold">Essay Bahasa Indonesia</h6>
                                <p class="mb-1 text-subtle small">
                                    <i class="fas fa-user me-1"></i>Bu Sari •
                                    <i class="fas fa-pen-fancy me-1"></i>Menulis puisi bebas
                                </p>
                                <small class="text-subtle">
                                    <i class="fas fa-calendar-alt me-1"></i>Dikumpulkan: 16 November 2025
                                </small>
                            </div>
                            <span class="badge bg-warning-subtle text-warning border ms-3">3 hari</span>
                        </div>

                        <div class="list-group-item d-flex justify-content-between align-items-start border-0 px-0 py-3">
                            <div class="flex-grow-1">
                                <h6 class="mb-2 text-high-contrast fw-semibold">Laporan Praktikum IPA</h6>
                                <p class="mb-1 text-subtle small">
                                    <i class="fas fa-user me-1"></i>Pak Budi •
                                    <i class="fas fa-flask me-1"></i>Percobaan sistem tata surya
                                </p>
                                <small class="text-subtle">
                                    <i class="fas fa-calendar-alt me-1"></i>Dikumpulkan: 18 November 2025
                                </small>
                            </div>
                            <span class="badge bg-success-subtle text-success border ms-3">5 hari</span>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <a href="#" class="btn btn-outline-primary">
                            <i class="fas fa-tasks me-2"></i>Lihat Semua Tugas
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card card-stats border-0 shadow-sm mb-4">
                <div class="card-header bg-light border-bottom-0 py-3">
                    <h5 class="card-title mb-0 text-high-contrast fw-semibold">
                        <i class="fas fa-star text-primary me-2"></i>Prestasi Terbaru
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex align-items-center border-0 px-0 py-3">
                            <div class="bg-success bg-opacity-10 rounded-circle p-2 me-3 flex-shrink-0">
                                <i class="fas fa-trophy text-success"></i>
                            </div>
                            <div>
                                <h6 class="mb-1 text-high-contrast fw-semibold">Nilai Terbaik</h6>
                                <p class="mb-0 text-subtle small">Matematika: 95</p>
                            </div>
                        </div>

                        <div class="list-group-item d-flex align-items-center border-0 px-0 py-3">
                            <div class="bg-info bg-opacity-10 rounded-circle p-2 me-3 flex-shrink-0">
                                <i class="fas fa-medal text-info"></i>
                            </div>
                            <div>
                                <h6 class="mb-1 text-high-contrast fw-semibold">Kehadiran Sempurna</h6>
                                <p class="mb-0 text-subtle small">Bulan Oktober</p>
                            </div>
                        </div>

                        <div class="list-group-item d-flex align-items-center border-0 px-0 py-3">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3 flex-shrink-0">
                                <i class="fas fa-award text-primary"></i>
                            </div>
                            <div>
                                <h6 class="mb-1 text-high-contrast fw-semibold">Tugas Tepat Waktu</h6>
                                <p class="mb-0 text-subtle small">95% penyelesaian</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-stats border-0 shadow-sm">
                <div class="card-header bg-light border-bottom-0 py-3">
                    <h5 class="card-title mb-0 text-high-contrast fw-semibold">
                        <i class="fas fa-bell text-primary me-2"></i>Pengumuman
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="alert alert-info border border-info border-opacity-25 bg-info bg-opacity-10" role="alert">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-info-circle text-info me-2 mt-1 flex-shrink-0"></i>
                            <div>
                                <h6 class="fw-bold text-info mb-1">Ujian Tengah Semester</h6>
                                <p class="mb-0 text-high-contrast">akan dimulai tanggal 25 November 2025. Persiapkan diri dengan baik!</p>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-success border border-success border-opacity-25 bg-success bg-opacity-10" role="alert">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-calendar-check text-success me-2 mt-1 flex-shrink-0"></i>
                            <div>
                                <h6 class="fw-bold text-success mb-1">Libur Nasional</h6>
                                <p class="mb-0 text-high-contrast">tanggal 17 November 2025 (Hari Guru Nasional)</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
