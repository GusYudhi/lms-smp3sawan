@extends('layouts.app')

@section('title', 'Jadwal Pelajaran')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="bg-white rounded-3 shadow-sm p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-2 text-primary">
                            <i class="fas fa-calendar-alt me-2"></i>Jadwal Pelajaran
                        </h1>
                        @if($studentProfile && $studentProfile->kelas)
                            <p class="text-muted mb-0">
                                <i class="fas fa-school me-1"></i>
                                Kelas: <strong>{{ $studentProfile->kelas->full_name }}</strong>
                            </p>
                        @endif
                    </div>
                    <div class="text-end">
                        <h2 class="mb-0 text-primary" id="current-time">{{ date('H:i:s') }}</h2>
                        <small class="text-muted">{{ date('l, d F Y') }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(isset($message))
    <!-- No Class Message -->
    <div class="row">
        <div class="col-12">
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                {{ $message }}
            </div>
        </div>
    </div>
    @else
    <!-- Today and Tomorrow Schedule -->
    <div class="row g-4 mb-4">
        <!-- Today's Schedule -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-gradient bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-calendar-day me-2"></i>Jadwal Hari Ini ({{ $todayName }})
                    </h5>
                </div>
                <div class="card-body p-3">
                    @if($todaySchedules->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($todaySchedules as $schedule)
                            <div class="list-group-item border-0 border-start border-4 border-primary mb-2 shadow-sm">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 fw-bold text-primary">
                                            {{ $schedule->mataPelajaran ? $schedule->mataPelajaran->nama_mapel : 'Mata Pelajaran tidak tersedia' }}
                                        </h6>
                                        <div class="small text-muted">
                                            <i class="fas fa-user me-1"></i>
                                            {{ $schedule->guru ? $schedule->guru->name : 'Belum ditentukan' }}
                                        </div>
                                        @if(isset($jamPelajaranList[$schedule->jam_ke]))
                                        <div class="small text-muted mt-1">
                                            <i class="fas fa-clock me-1"></i>
                                            {{ \Carbon\Carbon::parse($jamPelajaranList[$schedule->jam_ke]->jam_mulai)->format('H:i') }} -
                                            {{ \Carbon\Carbon::parse($jamPelajaranList[$schedule->jam_ke]->jam_selesai)->format('H:i') }}
                                        </div>
                                        @endif
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-primary">
                                            Jam ke-{{ $schedule->jam_ke }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-times text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-3">Tidak ada jadwal pelajaran hari ini</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Tomorrow's Schedule -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-gradient bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-calendar-plus me-2"></i>Jadwal Besok ({{ $tomorrowName }})
                    </h5>
                </div>
                <div class="card-body p-3">
                    @if($tomorrowSchedules->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($tomorrowSchedules as $schedule)
                            <div class="list-group-item border-0 border-start border-4 border-success mb-2 shadow-sm">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 fw-bold text-success">
                                            {{ $schedule->mataPelajaran ? $schedule->mataPelajaran->nama_mapel : 'Mata Pelajaran tidak tersedia' }}
                                        </h6>
                                        <div class="small text-muted">
                                            <i class="fas fa-user me-1"></i>
                                            {{ $schedule->guru ? $schedule->guru->name : 'Belum ditentukan' }}
                                        </div>
                                        @if(isset($jamPelajaranList[$schedule->jam_ke]))
                                        <div class="small text-muted mt-1">
                                            <i class="fas fa-clock me-1"></i>
                                            {{ \Carbon\Carbon::parse($jamPelajaranList[$schedule->jam_ke]->jam_mulai)->format('H:i') }} -
                                            {{ \Carbon\Carbon::parse($jamPelajaranList[$schedule->jam_ke]->jam_selesai)->format('H:i') }}
                                        </div>
                                        @endif
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-success">
                                            Jam ke-{{ $schedule->jam_ke }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-times text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-3">Tidak ada jadwal pelajaran besok</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Full Weekly Schedule Table -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-gradient bg-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-table me-2"></i>Jadwal Lengkap Minggu Ini
                    </h5>
                </div>
                <div class="card-body p-0">
                    <!-- Mobile Scroll Hint -->
                    <div class="alert alert-info mb-0 rounded-0 border-0 d-md-none" role="alert">
                        <small>
                            <i class="fas fa-hand-point-right me-1"></i>
                            Geser tabel ke kanan untuk melihat semua hari (Senin - Sabtu)
                        </small>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered mb-0 jadwal-table">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center">Jam Ke</th>
                                    <th class="text-center">Waktu</th>
                                    <th class="text-center">Senin</th>
                                    <th class="text-center">Selasa</th>
                                    <th class="text-center">Rabu</th>
                                    <th class="text-center">Kamis</th>
                                    <th class="text-center">Jumat</th>
                                    <th class="text-center">Sabtu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    // Get all unique jam_ke values
                                    $allJamKe = collect();
                                    foreach($weeklySchedules as $daySchedules) {
                                        foreach($daySchedules as $schedule) {
                                            $allJamKe->push($schedule->jam_ke);
                                        }
                                    }
                                    $jamKeList = $allJamKe->unique()->sort()->values();
                                @endphp

                                @if($jamKeList->count() > 0)
                                    @foreach($jamKeList as $jamKe)
                                    <tr>
                                        <td class="text-center fw-bold align-middle">{{ $jamKe }}</td>
                                        <td class="text-center align-middle small">
                                            @if(isset($jamPelajaranList[$jamKe]))
                                                <div style="line-height: 1.3;">
                                                    {{ \Carbon\Carbon::parse($jamPelajaranList[$jamKe]->jam_mulai)->format('H:i') }}<br>
                                                    <small class="text-muted">s/d</small><br>
                                                    {{ \Carbon\Carbon::parse($jamPelajaranList[$jamKe]->jam_selesai)->format('H:i') }}
                                                </div>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $day)
                                            @php
                                                $schedule = $weeklySchedules[$day]->firstWhere('jam_ke', $jamKe);
                                            @endphp
                                            <td class="align-middle {{ $day === $todayName ? 'table-primary' : '' }}">
                                                @if($schedule)
                                                    <div class="p-2">
                                                        <div class="fw-bold text-center" style="font-size: 0.9rem;">
                                                            {{ $schedule->mataPelajaran ? $schedule->mataPelajaran->nama_mapel : '-' }}
                                                        </div>
                                                        <div class="text-center text-muted mt-1" style="font-size: 0.7rem;">
                                                            <i class="fas fa-user" style="font-size: 0.65rem;"></i>
                                                            {{ $schedule->guru ? $schedule->guru->name : '-' }}
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="text-center text-muted">-</div>
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="8" class="text-center py-5">
                                            <i class="fas fa-calendar-times text-muted" style="font-size: 3rem;"></i>
                                            <p class="text-muted mt-3">Belum ada jadwal pelajaran untuk kelas ini</p>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Legend -->
    <div class="row mt-3">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="mb-3">
                        <i class="fas fa-info-circle me-2"></i>Keterangan
                    </h6>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="d-flex align-items-center mb-2">
                                <div class="bg-primary" style="width: 30px; height: 20px; margin-right: 10px;"></div>
                                <span>Hari ini</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center mb-2">
                                <div class="bg-success" style="width: 30px; height: 20px; margin-right: 10px;"></div>
                                <span>Hari besok</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-user text-muted me-2"></i>
                                <span>Nama guru pengampu</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        position: relative;
    }

    /* Shadow hint untuk scroll di mobile */
    @media (max-width: 768px) {
        .table-responsive {
            background:
                /* Shadow di kanan */
                linear-gradient(to right, transparent 30%, rgba(255,255,255,0)),
                linear-gradient(to right, rgba(0,0,0,0.15), transparent 10px) 0 0,
                /* Shadow di kiri */
                linear-gradient(to left, rgba(0,0,0,0.15), transparent 10px) 100% 0;
            background-repeat: no-repeat;
            background-size: 100% 100%, 10px 100%, 10px 100%;
            background-attachment: local, scroll, scroll;
        }
    }

    .table-bordered th,
    .table-bordered td {
        border: 1px solid #dee2e6;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(0, 123, 255, 0.05);
    }

    .list-group-item {
        transition: all 0.3s ease;
    }

    .list-group-item:hover {
        transform: translateX(5px);
    }

    /* Style untuk tabel jadwal lengkap - fokus pada mata pelajaran */
    .table tbody td > div > div.fw-bold {
        color: #0d6efd;
        font-size: 0.9rem !important;
        margin-bottom: 4px;
        line-height: 1.3;
    }

    .table tbody td > div > div.text-muted {
        font-size: 0.7rem !important;
        opacity: 0.7;
    }

    /* Highlight hari aktif */
    .table-primary {
        background-color: rgba(13, 110, 253, 0.1) !important;
    }

    /* Pastikan tabel jadwal memiliki minimum width untuk scroll */
    .jadwal-table {
        table-layout: fixed;
        width: 1000px; /* Fixed width: 70 + 90 + (140 * 6 hari) = 1000px */
        min-width: 1000px;
    }

    /* Kolom Jam Ke */
    .jadwal-table th:nth-child(1),
    .jadwal-table td:nth-child(1) {
        width: 70px;
        min-width: 70px;
    }

    /* Kolom Waktu */
    .jadwal-table th:nth-child(2),
    .jadwal-table td:nth-child(2) {
        width: 90px;
        min-width: 90px;
    }

    /* Kolom Senin */
    .jadwal-table th:nth-child(3),
    .jadwal-table td:nth-child(3) {
        width: 140px;
        min-width: 140px;
    }

    /* Kolom Selasa */
    .jadwal-table th:nth-child(4),
    .jadwal-table td:nth-child(4) {
        width: 140px;
        min-width: 140px;
    }

    /* Kolom Rabu */
    .jadwal-table th:nth-child(5),
    .jadwal-table td:nth-child(5) {
        width: 140px;
        min-width: 140px;
    }

    /* Kolom Kamis */
    .jadwal-table th:nth-child(6),
    .jadwal-table td:nth-child(6) {
        width: 140px;
        min-width: 140px;
    }

    /* Kolom Jumat */
    .jadwal-table th:nth-child(7),
    .jadwal-table td:nth-child(7) {
        width: 140px;
        min-width: 140px;
    }

    /* Kolom Sabtu */
    .jadwal-table th:nth-child(8),
    .jadwal-table td:nth-child(8) {
        width: 140px;
        min-width: 140px;
    }

    @media (max-width: 768px) {
        .jadwal-table {
            font-size: 0.75rem;
            width: 1000px !important;
            min-width: 1000px !important;
        }

        .jadwal-table td,
        .jadwal-table th {
            padding: 0.4rem 0.3rem;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        /* Force semua kolom hari untuk tetap tampil */
        .jadwal-table th:nth-child(n+3),
        .jadwal-table td:nth-child(n+3) {
            display: table-cell !important;
            width: 140px !important;
            min-width: 140px !important;
        }

        .jadwal-table tbody td > div {
            padding: 4px !important;
        }

        .jadwal-table tbody td > div > div.fw-bold {
            font-size: 0.7rem !important;
            word-break: break-word;
        }

        .jadwal-table tbody td > div > div.text-muted {
            font-size: 0.6rem !important;
            word-break: break-word;
        }

        /* Alert hint style */
        .alert-info {
            font-size: 0.85rem;
            padding: 8px 12px;
        }
    }
</style>

@endsection

@push('scripts')
<script>
    // Update time every second
    function updateTime() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        const timeElement = document.getElementById('current-time');
        if (timeElement) {
            timeElement.textContent = `${hours}:${minutes}:${seconds}`;
        }
    }

    // Initial call and set interval
    document.addEventListener('DOMContentLoaded', function() {
        updateTime();
        setInterval(updateTime, 1000);
    });
</script>
@endpush
