@extends('layouts.app')

@section('title', 'Jadwal Mengajar Hari Ini')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-between gap-2 mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Jadwal Mengajar Hari Ini</h1>
            <p class="text-muted mb-0">
                <small><i class="fas fa-calendar-day me-1"></i>{{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</small>
            </p>
        </div>
        <a href="{{ route('guru.jadwal-mengajar') }}" class="btn btn-outline-primary">
            <i class="fas fa-calendar-alt me-1"></i>Lihat Semua Jadwal
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show mb-4" role="alert">
            <i class="fas fa-info-circle me-2"></i>{{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Metric Stat Cards (Ringkasan Sesi Mengajar) -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="dashboard-stat-card stat-primary">
                <div class="stat-icon">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value">
                        {{ $totalSesi ?? $schedules->count() }} <small>Sesi ({{ $totalJp ?? $schedules->count() }} JP)</small>
                    </div>
                    <p class="stat-label">Total Sesi Hari Ini</p>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="dashboard-stat-card stat-success">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value">
                        {{ $sesiTerisi ?? 0 }} <small>Sesi Selesai</small>
                    </div>
                    <p class="stat-label">Jurnal Terisi</p>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-12 col-lg-4">
            <div class="dashboard-stat-card stat-warning">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value">
                        {{ $sesiBelumTerisi ?? 0 }} <small>Perlu Diisi</small>
                    </div>
                    <p class="stat-label">Belum Mengisi Jurnal</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Schedule -->
    @if($schedules->isEmpty())
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-center py-5">
                    <img src="https://cdn-icons-png.flaticon.com/512/2921/2921222.png" alt="No Schedule" style="width: 150px; opacity: 0.5;">
                    <h5 class="mt-3 text-muted">Tidak ada jadwal mengajar hari ini</h5>
                    <p class="text-muted">Anda tidak memiliki jadwal mengajar pada hari {{ $today }}</p>
                </div>
            </div>
        </div>
    @else
        <!-- DESKTOP / TABLET VIEW (>= 768px): Structured Table -->
        <div class="card border-0 shadow-sm d-none d-md-block mb-4">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="min-width: 820px;">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center text-nowrap" style="width: 120px; min-width: 120px;">Jam Ke</th>
                                <th class="text-center text-nowrap" style="width: 150px; min-width: 150px;">Waktu</th>
                                <th style="min-width: 180px;">Mata Pelajaran</th>
                                <th class="text-nowrap" style="width: 90px; min-width: 90px;">Kelas</th>
                                <th class="text-center text-nowrap" style="width: 140px; min-width: 140px;">Status</th>
                                <th class="text-center text-nowrap" style="width: 150px; min-width: 150px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($schedules as $schedule)
                            @php
                                $jamMulai = $jamPelajarans[$schedule->jam_ke_mulai] ?? null;
                                $jamSelesai = $jamPelajarans[$schedule->jam_ke_selesai] ?? null;
                                $waktuMulai = $jamMulai ? \Carbon\Carbon::parse($jamMulai->jam_mulai)->format('H:i') : '-';
                                $waktuSelesai = $jamSelesai ? \Carbon\Carbon::parse($jamSelesai->jam_selesai)->format('H:i') : '-';
                                $waktu = ($waktuMulai !== '-' && $waktuSelesai !== '-') ? "{$waktuMulai} - {$waktuSelesai}" : '-';

                                $jamKeText = ($schedule->jam_ke_mulai == $schedule->jam_ke_selesai)
                                    ? $schedule->jam_ke_mulai
                                    : ($schedule->jam_ke_mulai . ' - ' . $schedule->jam_ke_selesai);

                                $hasJurnal = $schedule->has_jurnal;
                                $tanggalHariIni = \Carbon\Carbon::today()->format('Y-m-d');

                                // Real-time active session check
                                $now = \Carbon\Carbon::now();
                                $isCurrentSession = false;
                                if ($jamMulai && $jamSelesai) {
                                    $sessionStart = \Carbon\Carbon::createFromTimeString($jamMulai->jam_mulai);
                                    $sessionEnd = \Carbon\Carbon::createFromTimeString($jamSelesai->jam_selesai);
                                    $isCurrentSession = $now->between($sessionStart, $sessionEnd);
                                }
                            @endphp
                            <tr class="{{ $isCurrentSession ? 'table-active-session' : '' }}">
                                <td class="text-center text-nowrap">
                                    <span class="badge bg-secondary">{{ $jamKeText }}</span>
                                    @if(($schedule->jumlah_jp ?? 1) > 1)
                                        <span class="badge bg-light text-dark border ms-1">{{ $schedule->jumlah_jp }} JP</span>
                                    @endif
                                    @if($isCurrentSession)
                                        <div class="mt-1">
                                            <span class="badge bg-primary"><span class="pulse-dot me-1"></span>Aktif</span>
                                        </div>
                                    @endif
                                </td>
                                <td class="text-center text-nowrap">
                                    <small class="text-dark fw-bold">{{ $waktu }}</small>
                                </td>
                                <td>
                                    <div>
                                        <h6 class="mb-0 fw-bold">{{ $schedule->mataPelajaran->nama_mapel ?? '-' }}</h6>
                                        <small class="text-muted">{{ $schedule->mataPelajaran->kode_mapel ?? '-' }}</small>
                                    </div>
                                </td>
                                <td class="text-nowrap">
                                    <span class="badge bg-info">{{ $schedule->kelas_full }}</span>
                                </td>
                                <td class="text-center text-nowrap">
                                    @if($hasJurnal)
                                        <span class="badge bg-success">
                                            <i class="fas fa-check-circle me-1"></i>Sudah Mengisi
                                        </span>
                                    @else
                                        <span class="badge bg-warning text-dark">
                                            <i class="fas fa-clock me-1"></i>Belum Mengisi
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center text-nowrap">
                                    @if(!$hasJurnal)
                                        <a href="{{ route('guru.jurnal-mengajar.wizard') }}?tanggal={{ $tanggalHariIni }}&kelas_id={{ $schedule->kelas_id }}&mata_pelajaran_id={{ $schedule->mata_pelajaran_id }}&jam_ke_mulai={{ $schedule->jam_ke_mulai }}&jam_ke_selesai={{ $schedule->jam_ke_selesai }}&return_to=today"
                                           class="btn btn-sm btn-primary text-nowrap px-3 shadow-sm">
                                            <i class="fas fa-plus-circle me-1"></i><span>Isi Jurnal</span>
                                        </a>
                                    @else
                                        @if($schedule->jurnal)
                                            <a href="{{ route('guru.jurnal-mengajar.show', $schedule->jurnal->id) }}" class="btn btn-sm btn-outline-success text-nowrap px-3">
                                                <i class="fas fa-eye me-1"></i><span>Lihat Jurnal</span>
                                            </a>
                                        @else
                                            <button class="btn btn-sm btn-success text-nowrap px-3" disabled>
                                                <i class="fas fa-check me-1"></i><span>Selesai</span>
                                            </button>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- MOBILE VIEW (< 768px): Thumb-Friendly Adaptive Cards -->
        <div class="d-md-none mb-4">
            @foreach($schedules as $schedule)
            @php
                $jamMulai = $jamPelajarans[$schedule->jam_ke_mulai] ?? null;
                $jamSelesai = $jamPelajarans[$schedule->jam_ke_selesai] ?? null;
                $waktuMulai = $jamMulai ? \Carbon\Carbon::parse($jamMulai->jam_mulai)->format('H:i') : '-';
                $waktuSelesai = $jamSelesai ? \Carbon\Carbon::parse($jamSelesai->jam_selesai)->format('H:i') : '-';
                $waktu = ($waktuMulai !== '-' && $waktuSelesai !== '-') ? "{$waktuMulai} - {$waktuSelesai}" : '-';

                $jamKeText = ($schedule->jam_ke_mulai == $schedule->jam_ke_selesai)
                    ? $schedule->jam_ke_mulai
                    : ($schedule->jam_ke_mulai . ' - ' . $schedule->jam_ke_selesai);

                $hasJurnal = $schedule->has_jurnal;
                $tanggalHariIni = \Carbon\Carbon::today()->format('Y-m-d');

                // Real-time active session check
                $now = \Carbon\Carbon::now();
                $isCurrentSession = false;
                if ($jamMulai && $jamSelesai) {
                    $sessionStart = \Carbon\Carbon::createFromTimeString($jamMulai->jam_mulai);
                    $sessionEnd = \Carbon\Carbon::createFromTimeString($jamSelesai->jam_selesai);
                    $isCurrentSession = $now->between($sessionStart, $sessionEnd);
                }
            @endphp
            <div class="schedule-card-mobile {{ $isCurrentSession ? 'is-current-session' : '' }}">
                <div class="card-top-bar">
                    <div class="d-flex align-items-center gap-1 flex-wrap">
                        <span class="badge bg-secondary">Jam {{ $jamKeText }}</span>
                        @if(($schedule->jumlah_jp ?? 1) > 1)
                            <span class="badge bg-light text-dark border">{{ $schedule->jumlah_jp }} JP</span>
                        @endif
                        @if($isCurrentSession)
                            <span class="badge bg-primary">
                                <span class="pulse-dot me-1"></span>Sedang Berlangsung
                            </span>
                        @endif
                    </div>
                    <div class="schedule-time">
                        <i class="far fa-clock text-muted"></i>
                        <span>{{ $waktu }}</span>
                    </div>
                </div>

                <h5 class="schedule-title">{{ $schedule->mataPelajaran->nama_mapel ?? '-' }}</h5>

                <div class="schedule-meta">
                    <span class="badge bg-info">
                        <i class="fas fa-users me-1"></i>Kelas {{ $schedule->kelas_full }}
                    </span>
                    @if(!empty($schedule->mataPelajaran->kode_mapel))
                        <span class="badge bg-light text-dark border">
                            {{ $schedule->mataPelajaran->kode_mapel }}
                        </span>
                    @endif
                    @if($hasJurnal)
                        <span class="badge bg-success">
                            <i class="fas fa-check-circle me-1"></i>Sudah Mengisi
                        </span>
                    @else
                        <span class="badge bg-warning text-dark">
                            <i class="fas fa-clock me-1"></i>Belum Mengisi
                        </span>
                    @endif
                </div>

                <div class="schedule-actions">
                    @if(!$hasJurnal)
                        <a href="{{ route('guru.jurnal-mengajar.wizard') }}?tanggal={{ $tanggalHariIni }}&kelas_id={{ $schedule->kelas_id }}&mata_pelajaran_id={{ $schedule->mata_pelajaran_id }}&jam_ke_mulai={{ $schedule->jam_ke_mulai }}&jam_ke_selesai={{ $schedule->jam_ke_selesai }}&return_to=today"
                           class="btn btn-primary schedule-action-btn">
                            <i class="fas fa-plus-circle me-2"></i>Isi Jurnal Mengajar
                        </a>
                    @else
                        @if($schedule->jurnal)
                            <a href="{{ route('guru.jurnal-mengajar.show', $schedule->jurnal->id) }}" class="btn btn-outline-success schedule-action-btn">
                                <i class="fas fa-eye me-2"></i>Lihat Jurnal Pembelajaran
                            </a>
                        @else
                            <button class="btn btn-success schedule-action-btn" disabled>
                                <i class="fas fa-check me-2"></i>Jurnal Selesai
                            </button>
                        @endif
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>
@endsection

