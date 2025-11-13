@extends('layouts.app')

@section('content')
<div class="admin-dashboard">
    <div class="page-header">
        <h1>Dashboard Administrator</h1>
        <div class="welcome-message">
            <h3>Selamat datang di panel administrasi SMPN 3 SAWAN</h3>
        </div>
    </div>

    <!-- Statistik Utama -->
    <div class="stats-overview">
        <div class="stats-grid">
            <!-- Total Guru -->
            <div class="stat-card guru-card">
                <div class="stat-icon">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <div class="stat-content">
                    <h3>Total Guru</h3>
                    <div class="stat-number">{{ \App\Models\User::where('role', 'guru')->count() }}</div>
                    <p class="stat-description">Guru Aktif</p>
                </div>
            </div>

            <!-- Total Siswa -->
            <div class="stat-card siswa-card">
                <div class="stat-icon">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <div class="stat-content">
                    <h3>Total Siswa</h3>
                    <div class="stat-number">{{ \App\Models\User::where('role', 'siswa')->count() }}</div>
                    <p class="stat-description">Siswa Terdaftar</p>
                </div>
            </div>

            <!-- Siswa Hadir Hari Ini -->
            <div class="stat-card hadir-card">
                <div class="stat-icon">
                    <i class="fas fa-user-check"></i>
                </div>
                <div class="stat-content">
                    <h3>Siswa Hadir</h3>
                    <div class="stat-number">
                        {{-- Simulasi data hadir hari ini --}}
                        {{ rand(150, 180) }}
                    </div>
                    <p class="stat-description">Hari Ini</p>
                </div>
            </div>

            <!-- Siswa Tidak Hadir Hari Ini -->
            <div class="stat-card absen-card">
                <div class="stat-icon">
                    <i class="fas fa-user-times"></i>
                </div>
                <div class="stat-content">
                    <h3>Siswa Absen</h3>
                    <div class="stat-number">
                        {{-- Simulasi data absen hari ini --}}
                        {{ rand(5, 25) }}
                    </div>
                    <p class="stat-description">Hari Ini</p>
                </div>
            </div>

            <!-- Total Kelas -->
            <div class="stat-card kelas-card">
                <div class="stat-icon">
                    <i class="fas fa-door-open"></i>
                </div>
                <div class="stat-content">
                    <h3>Total Kelas</h3>
                    <div class="stat-number">18</div>
                    <p class="stat-description">Kelas Aktif</p>
                </div>
            </div>

            <!-- Persentase Kehadiran -->
            <div class="stat-card kehadiran-card">
                <div class="stat-icon">
                    <i class="fas fa-chart-pie"></i>
                </div>
                <div class="stat-content">
                    <h3>Tingkat Kehadiran</h3>
                    <div class="stat-number">
                        @php
                            $totalSiswa = \App\Models\User::where('role', 'siswa')->count();
                            $siswaHadir = rand(150, 180);
                            $persentaseKehadiran = $totalSiswa > 0 ? round(($siswaHadir / $totalSiswa) * 100, 1) : 0;
                        @endphp
                        {{ $persentaseKehadiran }}%
                    </div>
                    <p class="stat-description">Hari Ini</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Ringkasan Kehadiran Mingguan -->
    <div class="attendance-summary">
        <div class="summary-header">
            <h2>Ringkasan Kehadiran 7 Hari Terakhir</h2>
            <p class="text-light">Persentase kehadiran siswa dalam seminggu terakhir</p>
        </div>

        <div class="weekly-attendance-grid">
            @php
                $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                $attendancePercentages = [92, 88, 95, 90, 87, 85]; // Minggu libur
            @endphp

            @foreach($days as $index => $day)
            <div class="attendance-day-card {{ $attendancePercentages[$index] == 0 ? 'holiday' : '' }}">
                <div class="day-name">{{ $day }}</div>
                <div class="attendance-bar">
                    <div class="attendance-fill"
                         style="width: {{ $attendancePercentages[$index] }}%;
                                background: {{ $attendancePercentages[$index] >= 90 ? '#4caf50' : ($attendancePercentages[$index] >= 80 ? '#ff9800' : '#f44336') }};">
                    </div>
                </div>
                <div class="attendance-percentage">
                    {{ $attendancePercentages[$index] == 0 ? 'Libur' : $attendancePercentages[$index] . '%' }}
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Statistik Quick Actions -->
    <div class="quick-actions-grid">
        <div class="action-card urgent">
            <div class="action-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="action-content">
                <h3>Siswa Sering Absen</h3>
                <p>{{ rand(3, 8) }} siswa memerlukan perhatian khusus</p>
                <a href="#" class="action-link">Lihat Detail</a>
            </div>
        </div>

        <div class="action-card info">
            <div class="action-icon">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="action-content">
                <h3>Kehadiran Bulanan</h3>
                <p>Rata-rata kehadiran bulan ini: 89.5%</p>
                <a href="#" class="action-link">Lihat Laporan</a>
            </div>
        </div>

        <div class="action-card success">
            <div class="action-icon">
                <i class="fas fa-trophy"></i>
            </div>
            <div class="action-content">
                <h3>Kelas Terbaik</h3>
                <p>Kelas 9A dengan tingkat kehadiran 97%</p>
                <a href="#" class="action-link">Lihat Ranking</a>
            </div>
        </div>
    </div>
    <div class="portal-grid">
        <a href="{{ route('admin.guru.index') }}" class="portal-card">
            <i class="fas fa-chalkboard-teacher icon-primary fa-3x"></i>
            <h3>Manajemen Guru</h3>
            <p>Kelola data guru dan staf pengajar</p>
        </a>

        <a href="{{ route('admin.siswa.index') }}" class="portal-card">
            <i class="fas fa-user-graduate icon-primary fa-3x"></i>
            <h3>Manajemen Siswa</h3>
            <p>Kelola data siswa dan kelas</p>
        </a>

        <a href="{{ route('school.profile') }}" class="portal-card">
            <i class="fas fa-school icon-primary fa-3x"></i>
            <h3>Data Sekolah</h3>
            <p>Informasi dan profil sekolah</p>
        </a>

        <div class="portal-card">
            <i class="fas fa-chart-bar icon-primary fa-3x"></i>
            <h3>Laporan System</h3>
            <p>Analisis dan laporan kinerja sekolah</p>
        </div>

        <div class="portal-card">
            <i class="fas fa-cogs icon-primary fa-3x"></i>
            <h3>Pengaturan</h3>
            <p>Konfigurasi sistem dan preferensi</p>
        </div>
    </div>


    <div class="recent-activities">
        <h2>Aktivitas Terbaru</h2>
        <p>Kelola sistem dan monitor aktivitas sekolah</p>
    </div>
</div>
@endsection
