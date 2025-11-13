@extends('layouts.app')

@section('title', 'Dashboard Kepala Sekolah')

@section('content')
<div class="container-fluid">
    <!-- Welcome Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="bg-white rounded-3 shadow-sm p-4">
                <h1 class="h3 mb-2 text-primary">
                    <i class="fas fa-school me-2"></i>Dashboard Kepala Sekolah
                </h1>
                <p class="text-muted mb-0">Selamat datang, {{ auth()->user()->name }}! Kelola sekolah dengan efisien dan efektif.</p>
            </div>
        </div>
    </div>

    <!-- Key Performance Indicators -->
    <div class="row g-4 mb-4">
        <!-- Total Siswa -->
        <div class="col-xl-2 col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm text-center h-100">
                <div class="card-body">
                    <div class="text-primary fs-1 mb-3">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <h5 class="card-title text-muted">Total Siswa</h5>
                    <h3 class="text-primary">{{ \App\Models\User::where('role', 'siswa')->count() }}</h3>
                    <small class="text-muted">Siswa Aktif</small>
                </div>
            </div>
        </div>

        <!-- Total Guru -->
        <div class="col-xl-2 col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm text-center h-100">
                <div class="card-body">
                    <div class="text-success fs-1 mb-3">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <h5 class="card-title text-muted">Total Guru</h5>
                    <h3 class="text-success">{{ \App\Models\User::where('role', 'guru')->count() }}</h3>
                    <small class="text-muted">Tenaga Pengajar</small>
                </div>
            </div>
        </div>

        <!-- Total Kelas -->
        <div class="col-xl-2 col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm text-center h-100">
                <div class="card-body">
                    <div class="text-info fs-1 mb-3">
                        <i class="fas fa-door-open"></i>
                    </div>
                    <h5 class="card-title text-muted">Total Kelas</h5>
                    <h3 class="text-info">18</h3>
                    <small class="text-muted">Ruang Kelas</small>
                </div>
            </div>
        </div>

        <!-- Tingkat Kehadiran -->
        <div class="col-xl-2 col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm text-center h-100">
                <div class="card-body">
                    <div class="text-warning fs-1 mb-3">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                    <h5 class="card-title text-muted">Kehadiran</h5>
                    <h3 class="text-warning">{{ rand(85, 95) }}%</h3>
                    <small class="text-muted">Rata-rata Bulan Ini</small>
                </div>
            </div>
        </div>

        <!-- Prestasi Akademik -->
        <div class="col-xl-2 col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm text-center h-100">
                <div class="card-body">
                    <div class="text-purple fs-1 mb-3">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <h5 class="card-title text-muted">Rata-rata Nilai</h5>
                    <h3 class="text-secondary">{{ rand(75, 85) }}</h3>
                    <small class="text-muted">Semester Ini</small>
                </div>
            </div>
        </div>

        <!-- Program Aktif -->
        <div class="col-xl-2 col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm text-center h-100">
                <div class="card-body">
                    <div class="text-dark fs-1 mb-3">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <h5 class="card-title text-muted">Program Aktif</h5>
                    <h3 class="text-dark">{{ rand(8, 15) }}</h3>
                    <small class="text-muted">Kegiatan Sekolah</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Management Overview -->
    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-line me-2"></i>Trend Kehadiran Siswa
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @php
                            $months = ['Jul', 'Aug', 'Sep', 'Okt', 'Nov'];
                            $attendanceData = [88, 92, 85, 90, 87];
                        @endphp

                        @foreach($months as $index => $month)
                        <div class="col">
                            <div class="text-center">
                                <small class="text-muted">{{ $month }}</small>
                                <div class="progress mt-2" style="height: 80px;" data-bs-toggle="tooltip" title="{{ $attendanceData[$index] }}%">
                                    <div class="progress-bar
                                        @if($attendanceData[$index] >= 90) bg-success
                                        @elseif($attendanceData[$index] >= 80) bg-warning
                                        @else bg-danger @endif"
                                         style="height: {{ $attendanceData[$index] }}%;"
                                         data-bs-toggle="tooltip"
                                         title="{{ $attendanceData[$index] }}%">
                                    </div>
                                </div>
                                <small class="text-muted mt-1">{{ $attendanceData[$index] }}%</small>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-users me-2"></i>Distribusi Guru by Status
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="card border border-success">
                                <div class="card-body p-3">
                                    <h4 class="text-success">{{ rand(15, 25) }}</h4>
                                    <small class="text-muted">Guru PNS</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="card border border-info">
                                <div class="card-body p-3">
                                    <h4 class="text-info">{{ rand(8, 15) }}</h4>
                                    <small class="text-muted">Guru PPPK</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="card border border-warning">
                                <div class="card-body p-3">
                                    <h4 class="text-warning">{{ rand(5, 12) }}</h4>
                                    <small class="text-muted">Honorer</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3 text-center">
                        <a href="{{ route('admin.guru.index') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-eye me-1"></i> Lihat Detail
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row g-4 mb-4">
        <div class="col-lg-3 col-md-6">
            <a href="{{ route('admin.guru.index') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100 hover-card">
                    <div class="card-body text-center p-4">
                        <div class="text-primary fs-1 mb-3">
                            <i class="fas fa-users-cog"></i>
                        </div>
                        <h5 class="card-title">Kelola Guru</h5>
                        <p class="card-text text-muted">Manajemen data guru dan tenaga pengajar</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-lg-3 col-md-6">
            <a href="{{ route('admin.siswa.index') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100 hover-card">
                    <div class="card-body text-center p-4">
                        <div class="text-success fs-1 mb-3">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <h5 class="card-title">Kelola Siswa</h5>
                        <p class="card-text text-muted">Manajemen data siswa dan kelas</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-lg-3 col-md-6">
            <a href="{{ route('school.profile') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100 hover-card">
                    <div class="card-body text-center p-4">
                        <div class="text-info fs-1 mb-3">
                            <i class="fas fa-school"></i>
                        </div>
                        <h5 class="card-title">Profil Sekolah</h5>
                        <p class="card-text text-muted">Kelola informasi dan profil sekolah</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 hover-card">
                <div class="card-body text-center p-4">
                    <div class="text-warning fs-1 mb-3">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <h5 class="card-title">Laporan</h5>
                    <p class="card-text text-muted">Analisis dan laporan akademik</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities and Notifications -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-history me-2"></i>Aktivitas Terbaru Sekolah
                    </h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex align-items-center border-0 px-0">
                            <div class="text-success me-3">
                                <i class="fas fa-user-plus"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">Guru baru bergabung</h6>
                                <p class="mb-1 text-muted small">Bu Dewi Sartika - Guru Bahasa Inggris</p>
                                <small class="text-muted">1 hari yang lalu</small>
                            </div>
                        </div>

                        <div class="list-group-item d-flex align-items-center border-0 px-0">
                            <div class="text-info me-3">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">Ujian Tengah Semester dijadwalkan</h6>
                                <p class="mb-1 text-muted small">25 November - 2 Desember 2025</p>
                                <small class="text-muted">2 hari yang lalu</small>
                            </div>
                        </div>

                        <div class="list-group-item d-flex align-items-center border-0 px-0">
                            <div class="text-warning me-3">
                                <i class="fas fa-award"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">Prestasi siswa</h6>
                                <p class="mb-1 text-muted small">Kelas 9A meraih juara 1 Olimpiade Matematika</p>
                                <small class="text-muted">3 hari yang lalu</small>
                            </div>
                        </div>

                        <div class="list-group-item d-flex align-items-center border-0 px-0">
                            <div class="text-primary me-3">
                                <i class="fas fa-tools"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">Perbaikan fasilitas</h6>
                                <p class="mb-1 text-muted small">Renovasi Lab Komputer selesai 90%</p>
                                <small class="text-muted">5 hari yang lalu</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-exclamation-circle me-2"></i>Perhatian Khusus
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning border-0" role="alert">
                        <div class="d-flex">
                            <i class="fas fa-user-times me-2 mt-1"></i>
                            <div>
                                <strong>{{ rand(3, 8) }} siswa</strong> memiliki tingkat absensi tinggi bulan ini
                                <br><small>Perlu tindak lanjut dari wali kelas</small>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info border-0" role="alert">
                        <div class="d-flex">
                            <i class="fas fa-calendar me-2 mt-1"></i>
                            <div>
                                <strong>Rapat koordinasi</strong> dengan dinas pendidikan
                                <br><small>Besok, 14 November 2025 - 09:00</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-bullhorn me-2"></i>Pengumuman
                    </h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item border-0 px-0">
                            <h6 class="mb-1">Hari Guru Nasional</h6>
                            <p class="mb-1 text-muted small">Libur sekolah tanggal 17 November 2025</p>
                            <small class="text-muted">Berlaku untuk semua</small>
                        </div>

                        <div class="list-group-item border-0 px-0">
                            <h6 class="mb-1">Pendaftaran Ekstrakurikuler</h6>
                            <p class="mb-1 text-muted small">Buka hingga 30 November 2025</p>
                            <small class="text-muted">Untuk semua kelas</small>
                        </div>

                        <div class="list-group-item border-0 px-0">
                            <h6 class="mb-1">Evaluasi Kinerja Guru</h6>
                            <p class="mb-1 text-muted small">Periode evaluasi semester I</p>
                            <small class="text-muted">Deadline: 20 Desember 2025</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
