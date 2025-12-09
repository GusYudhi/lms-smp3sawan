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
                <p class="text-muted mb-0">Selamat datang, {{ auth()->user()->name }}! Kelola dan monitor sekolah dengan efisien dan efektif.</p>
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
                    <h2 class="text-primary mb-2">{{ $totalGuru }}</h2>
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
                    <h2 class="text-success mb-2">{{ $totalSiswa }}</h2>
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
                    <h2 class="text-info mb-2">{{ $siswaHadirHariIni }}</h2>
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
                    <h2 class="text-warning mb-2">{{ $siswaTidakHadirHariIni }}</h2>
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
                    <h2 class="text-secondary mb-2">{{ $totalKelas }}</h2>
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
                        @foreach($attendanceWeekly as $attendance)
                        <div class="col-md-2">
                            <div class="text-center">
                                <h6 class="mb-2">{{ $attendance['day'] }}</h6>
                                <div class="progress mb-2" style="height: 20px;">
                                    <div class="progress-bar
                                        @if($attendance['percentage'] >= 90) bg-success
                                        @elseif($attendance['percentage'] >= 80) bg-warning
                                        @else bg-danger @endif"
                                         role="progressbar"
                                         style="width: {{ $attendance['percentage'] }}%;">
                                        {{ $attendance['percentage'] }}%
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
                            <p class="card-text text-muted">{{ $siswaSeringAbsen }} siswa memerlukan perhatian khusus</p>
                            <small class="text-muted">Perlu tindak lanjut dari wali kelas</small>
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
                            <p class="card-text text-muted">Rata-rata kehadiran bulan ini: {{ $rataRataKehadiranBulan }}%</p>
                            <small class="text-muted">Monitoring kehadiran siswa</small>
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
                            @if($kelasTerbaik)
                            <p class="card-text text-muted">
                                Kelas {{ $kelasTerbaik->tingkat }}{{ $kelasTerbaik->nama_kelas }}
                                dengan tingkat kehadiran {{ number_format($kelasTerbaik->attendance_rate, 1) }}%
                            </p>
                            @else
                            <p class="card-text text-muted">Belum ada data kehadiran</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Portal Grid -->
    <div class="row g-4 mb-4">
        <div class="col-lg-3 col-md-6">
            <a href="{{ route('kepala-sekolah.guru.index') }}" class="text-decoration-none">
                <div class="card card-stats h-100 hover-card">
                    <div class="card-body text-center">
                        <div class="text-primary fs-1 mb-3">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                        <h5 class="card-title">Data Guru</h5>
                        <p class="card-text text-muted">Lihat data guru dan staf pengajar</p>
                        <span class="badge bg-primary">Lihat Saja</span>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-lg-3 col-md-6">
            <a href="{{ route('kepala-sekolah.siswa.index') }}" class="text-decoration-none">
                <div class="card card-stats h-100 hover-card">
                    <div class="card-body text-center">
                        <div class="text-success fs-1 mb-3">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <h5 class="card-title">Data Siswa</h5>
                        <p class="card-text text-muted">Lihat data siswa dan kelas</p>
                        <span class="badge bg-success">Lihat Saja</span>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-lg-3 col-md-6">
            <a href="{{ route('kepala-sekolah.tahun-pelajaran.index') }}" class="text-decoration-none">
                <div class="card card-stats h-100 hover-card">
                    <div class="card-body text-center">
                        <div class="text-info fs-1 mb-3">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <h5 class="card-title">Tahun Pelajaran</h5>
                        <p class="card-text text-muted">Lihat tahun pelajaran dan semester</p>
                        <span class="badge bg-info">Lihat Saja</span>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-lg-3 col-md-6">
            <a href="{{ route('school.profile') }}" class="text-decoration-none">
                <div class="card card-stats h-100 hover-card">
                    <div class="card-body text-center">
                        <div class="text-warning fs-1 mb-3">
                            <i class="fas fa-school"></i>
                        </div>
                        <h5 class="card-title">Data Sekolah</h5>
                        <p class="card-text text-muted">Informasi dan profil sekolah</p>
                        <span class="badge bg-warning">Lihat Saja</span>
                    </div>
                </div>
            </a>
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
                                <strong>{{ $siswaSeringAbsen }} siswa</strong> memiliki tingkat absensi tinggi bulan ini
                                <br><small>Perlu tindak lanjut dari wali kelas</small>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info border-0" role="alert">
                        <div class="d-flex">
                            <i class="fas fa-calendar me-2 mt-1"></i>
                            <div>
                                <strong>Rapat koordinasi</strong> dengan dinas pendidikan
                                <br><small>Besok, 10 Desember 2025 - 09:00</small>
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

<style>
.card-stats {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    transition: all 0.3s ease;
}

.card-stats:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    transform: translateY(-5px);
}

.hover-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.hover-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

.border-start {
    border-left: 4px solid #dee2e6 !important;
}
</style>
@endsection
