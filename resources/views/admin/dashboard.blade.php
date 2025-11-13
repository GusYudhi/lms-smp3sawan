@extends('layouts.app')

@section('title', 'Dashboard Administrator')

@section('content')
<div class="container-fluid">
    <!-- Welcome Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="bg-white rounded-3 shadow-sm p-4">
                <h1 class="h3 mb-2 text-primary">
                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard Administrator
                </h1>
                <p class="text-muted mb-0">Selamat datang di panel administrasi SMPN 3 SAWAN</p>
            </div>
        </div>
    </div>

    <!-- Statistik Utama -->
    <div class="row g-4 mb-4">
        <!-- Total Guru -->
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="card card-stats h-100">
                <div class="card-body text-center">
                    <div class="text-primary fs-1 mb-3">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <h5 class="card-title text-muted">Total Guru</h5>
                    <h2 class="text-primary mb-2">{{ \App\Models\User::where('role', 'guru')->count() }}</h2>
                    <p class="text-muted small mb-0">Guru Aktif</p>
                </div>
            </div>
        </div>

        <!-- Total Siswa -->
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="card card-stats h-100">
                <div class="card-body text-center">
                    <div class="text-success fs-1 mb-3">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <h5 class="card-title text-muted">Total Siswa</h5>
                    <h2 class="text-success mb-2">{{ \App\Models\User::where('role', 'siswa')->count() }}</h2>
                    <p class="text-muted small mb-0">Siswa Terdaftar</p>
                </div>
            </div>
        </div>

        <!-- Siswa Hadir Hari Ini -->
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="card card-stats h-100">
                <div class="card-body text-center">
                    <div class="text-info fs-1 mb-3">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <h5 class="card-title text-muted">Siswa Hadir</h5>
                    <h2 class="text-info mb-2">{{ rand(150, 180) }}</h2>
                    <p class="text-muted small mb-0">Hari Ini</p>
                </div>
            </div>
        </div>

        <!-- Siswa Tidak Hadir Hari Ini -->
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="card card-stats h-100">
                <div class="card-body text-center">
                    <div class="text-warning fs-1 mb-3">
                        <i class="fas fa-user-times"></i>
                    </div>
                    <h5 class="card-title text-muted">Siswa Absen</h5>
                    <h2 class="text-warning mb-2">{{ rand(5, 25) }}</h2>
                    <p class="text-muted small mb-0">Hari Ini</p>
                </div>
            </div>
        </div>

        <!-- Total Kelas -->
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="card card-stats h-100">
                <div class="card-body text-center">
                    <div class="text-secondary fs-1 mb-3">
                        <i class="fas fa-door-open"></i>
                    </div>
                    <h5 class="card-title text-muted">Total Kelas</h5>
                    <h2 class="text-secondary mb-2">18</h2>
                    <p class="text-muted small mb-0">Kelas Aktif</p>
                </div>
            </div>
        </div>

        <!-- Persentase Kehadiran -->
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="card card-stats h-100">
                <div class="card-body text-center">
                    <div class="text-dark fs-1 mb-3">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                    <h5 class="card-title text-muted">Tingkat Kehadiran</h5>
                    @php
                        $totalSiswa = \App\Models\User::where('role', 'siswa')->count();
                        $siswaHadir = rand(150, 180);
                        $persentaseKehadiran = $totalSiswa > 0 ? round(($siswaHadir / $totalSiswa) * 100, 1) : 0;
                    @endphp
                    <h2 class="text-dark mb-2">{{ $persentaseKehadiran }}%</h2>
                    <p class="text-muted small mb-0">Hari Ini</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Ringkasan Kehadiran Mingguan -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card card-stats">
                <div class="card-header bg-white border-bottom">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-calendar-week me-2"></i>Ringkasan Kehadiran 7 Hari Terakhir
                    </h5>
                    <small class="text-muted">Persentase kehadiran siswa dalam seminggu terakhir</small>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @php
                            $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                            $attendancePercentages = [92, 88, 95, 90, 87, 85];
                        @endphp

                        @foreach($days as $index => $day)
                        <div class="col-md-2">
                            <div class="text-center">
                                <h6 class="mb-2">{{ $day }}</h6>
                                <div class="progress mb-2" style="height: 20px;">
                                    <div class="progress-bar
                                        @if($attendancePercentages[$index] >= 90) bg-success
                                        @elseif($attendancePercentages[$index] >= 80) bg-warning
                                        @else bg-danger @endif"
                                         role="progressbar"
                                         style="width: {{ $attendancePercentages[$index] }}%;">
                                        {{ $attendancePercentages[$index] }}%
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card card-stats h-100 border-start">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="text-danger fs-2 me-3">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div>
                            <h6 class="card-title mb-1">Siswa Sering Absen</h6>
                            <p class="card-text text-muted">{{ rand(3, 8) }} siswa memerlukan perhatian khusus</p>
                            <a href="#" class="btn btn-outline-danger btn-sm">Lihat Detail</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-stats h-100 border-start">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="text-info fs-2 me-3">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div>
                            <h6 class="card-title mb-1">Kehadiran Bulanan</h6>
                            <p class="card-text text-muted">Rata-rata kehadiran bulan ini: 89.5%</p>
                            <a href="#" class="btn btn-outline-info btn-sm">Lihat Laporan</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-stats h-100 border-start">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="text-success fs-2 me-3">
                            <i class="fas fa-trophy"></i>
                        </div>
                        <div>
                            <h6 class="card-title mb-1">Kelas Terbaik</h6>
                            <p class="card-text text-muted">Kelas 9A dengan tingkat kehadiran 97%</p>
                            <a href="#" class="btn btn-outline-success btn-sm">Lihat Ranking</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Portal Grid -->
    <div class="row g-4 mb-4">
        <div class="col-lg-3 col-md-6">
            <a href="{{ route('admin.guru.index') }}" class="text-decoration-none">
                <div class="card card-stats h-100 hover-card">
                    <div class="card-body text-center">
                        <div class="text-primary fs-1 mb-3">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                        <h5 class="card-title">Manajemen Guru</h5>
                        <p class="card-text text-muted">Kelola data guru dan staf pengajar</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-lg-3 col-md-6">
            <a href="{{ route('admin.siswa.index') }}" class="text-decoration-none">
                <div class="card card-stats h-100 hover-card">
                    <div class="card-body text-center">
                        <div class="text-primary fs-1 mb-3">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <h5 class="card-title">Manajemen Siswa</h5>
                        <p class="card-text text-muted">Kelola data siswa dan kelas</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-lg-3 col-md-6">
            <a href="{{ route('school.profile') }}" class="text-decoration-none">
                <div class="card card-stats h-100 hover-card">
                    <div class="card-body text-center">
                        <div class="text-primary fs-1 mb-3">
                            <i class="fas fa-school"></i>
                        </div>
                        <h5 class="card-title">Data Sekolah</h5>
                        <p class="card-text text-muted">Informasi dan profil sekolah</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card card-stats h-100 hover-card">
                <div class="card-body text-center">
                    <div class="text-primary fs-1 mb-3">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <h5 class="card-title">Laporan System</h5>
                    <p class="card-text text-muted">Analisis dan laporan kinerja sekolah</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-history me-2"></i>Aktivitas Terbaru
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Kelola sistem dan monitor aktivitas sekolah</p>
                    <div class="text-center py-4">
                        <i class="fas fa-chart-line text-muted fs-1 mb-3"></i>
                        <p class="text-muted">Fitur aktivitas terbaru akan segera tersedia</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.hover-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.hover-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}
</style>
@endsection
