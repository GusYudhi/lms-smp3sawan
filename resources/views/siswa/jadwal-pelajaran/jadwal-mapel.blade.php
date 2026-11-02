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
                    @if(isset($todayTimeline) && $todayTimeline->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($todayTimeline as $item)
                                @if($item->type === 'lesson')
                                    <div class="list-group-item border-0 border-start border-4 border-primary mb-2 shadow-sm rounded-2">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1 fw-bold text-primary">
                                                    {{ $item->nama_mapel }}
                                                </h6>
                                                <div class="small text-muted">
                                                    <i class="fas fa-user me-1"></i>
                                                    {{ $item->guru_name }}
                                                </div>
                                                @if($item->waktu_text)
                                                <div class="small text-muted mt-1">
                                                    <i class="fas fa-clock me-1"></i>
                                                    {{ $item->waktu_text }}
                                                </div>
                                                @endif
                                            </div>
                                            <div class="text-end">
                                                <span class="badge bg-primary">
                                                    {{ $item->jam_label }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @elseif($item->type === 'fixed')
                                    @php
                                        $isKokuri = ($item->tipe ?? '') === \App\Models\FixedSchedule::TIPE_KOKURIKULER;
                                        $badgeClass = $isKokuri ? 'bg-warning text-dark' : 'bg-secondary text-white';
                                        $iconClass = $isKokuri ? 'fas fa-star text-warning' : 'fas fa-lock text-secondary';
                                        $borderClass = $isKokuri ? 'border-warning' : 'border-secondary';
                                    @endphp
                                    <div class="list-group-item border-0 border-start border-4 {{ $borderClass }} bg-light mb-2 shadow-sm rounded-2">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <div class="d-flex align-items-center gap-1 mb-1">
                                                    <i class="{{ $iconClass }} me-1"></i>
                                                    <h6 class="mb-0 fw-bold text-dark">
                                                        {{ $item->keterangan }}
                                                    </h6>
                                                </div>
                                                <div class="small text-muted">
                                                    <span class="badge {{ $badgeClass }} me-1" style="font-size: 0.65rem;">
                                                        {{ $isKokuri ? 'Kokurikuler / Pembiasaan' : 'Jadwal Tetap' }}
                                                    </span>
                                                </div>
                                                @if($item->waktu_text)
                                                <div class="small text-muted mt-1">
                                                    <i class="fas fa-clock me-1"></i>
                                                    {{ $item->waktu_text }}
                                                </div>
                                                @endif
                                            </div>
                                            <div class="text-end">
                                                <span class="badge {{ $badgeClass }}">
                                                    {{ $item->jam_label }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-times text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-3">Tidak ada jadwal hari ini</p>
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
                    @if(isset($tomorrowTimeline) && $tomorrowTimeline->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($tomorrowTimeline as $item)
                                @if($item->type === 'lesson')
                                    <div class="list-group-item border-0 border-start border-4 border-success mb-2 shadow-sm rounded-2">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1 fw-bold text-success">
                                                    {{ $item->nama_mapel }}
                                                </h6>
                                                <div class="small text-muted">
                                                    <i class="fas fa-user me-1"></i>
                                                    {{ $item->guru_name }}
                                                </div>
                                                @if($item->waktu_text)
                                                <div class="small text-muted mt-1">
                                                    <i class="fas fa-clock me-1"></i>
                                                    {{ $item->waktu_text }}
                                                </div>
                                                @endif
                                            </div>
                                            <div class="text-end">
                                                <span class="badge bg-success">
                                                    {{ $item->jam_label }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @elseif($item->type === 'fixed')
                                    @php
                                        $isKokuri = ($item->tipe ?? '') === \App\Models\FixedSchedule::TIPE_KOKURIKULER;
                                        $badgeClass = $isKokuri ? 'bg-warning text-dark' : 'bg-secondary text-white';
                                        $iconClass = $isKokuri ? 'fas fa-star text-warning' : 'fas fa-lock text-secondary';
                                        $borderClass = $isKokuri ? 'border-warning' : 'border-secondary';
                                    @endphp
                                    <div class="list-group-item border-0 border-start border-4 {{ $borderClass }} bg-light mb-2 shadow-sm rounded-2">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <div class="d-flex align-items-center gap-1 mb-1">
                                                    <i class="{{ $iconClass }} me-1"></i>
                                                    <h6 class="mb-0 fw-bold text-dark">
                                                        {{ $item->keterangan }}
                                                    </h6>
                                                </div>
                                                <div class="small text-muted">
                                                    <span class="badge {{ $badgeClass }} me-1" style="font-size: 0.65rem;">
                                                        {{ $isKokuri ? 'Kokurikuler / Pembiasaan' : 'Jadwal Tetap' }}
                                                    </span>
                                                </div>
                                                @if($item->waktu_text)
                                                <div class="small text-muted mt-1">
                                                    <i class="fas fa-clock me-1"></i>
                                                    {{ $item->waktu_text }}
                                                </div>
                                                @endif
                                            </div>
                                            <div class="text-end">
                                                <span class="badge {{ $badgeClass }}">
                                                    {{ $item->jam_label }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-times text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-3">Tidak ada jadwal besok</p>
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
                                    <th class="text-center align-middle" style="width: 80px;">Jam Ke</th>
                                    <th class="text-center align-middle" style="width: 100px;">Waktu</th>
                                    <th class="text-center align-middle">Senin</th>
                                    <th class="text-center align-middle">Selasa</th>
                                    <th class="text-center align-middle">Rabu</th>
                                    <th class="text-center align-middle">Kamis</th>
                                    <th class="text-center align-middle">Jumat</th>
                                    <th class="text-center align-middle">Sabtu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($slotNumbers) && $slotNumbers->count() > 0)
                                    @foreach($slotNumbers as $slot)
                                    <tr>
                                        <td class="text-center fw-bold align-middle bg-light">
                                            <span class="badge {{ $slot === 0 ? 'bg-warning text-dark' : 'bg-secondary' }}">
                                                Jam {{ $slot }}
                                            </span>
                                        </td>
                                        <td class="text-center align-middle small bg-light">
                                            @if(isset($jamPelajaranList[$slot]))
                                                <div style="line-height: 1.3;">
                                                    <span class="fw-semibold">{{ \Carbon\Carbon::parse($jamPelajaranList[$slot]->jam_mulai)->format('H:i') }}</span><br>
                                                    <small class="text-muted">s/d</small><br>
                                                    <span class="fw-semibold">{{ \Carbon\Carbon::parse($jamPelajaranList[$slot]->jam_selesai)->format('H:i') }}</span>
                                                </div>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $day)
                                            @php
                                                $item = $weeklyGrid[$day][$slot] ?? null;
                                                $isToday = ($day === $todayName);
                                            @endphp
                                            <td class="align-middle {{ $isToday ? 'table-primary' : '' }}">
                                                @if($item)
                                                    @if($item->type === 'fixed')
                                                        @php
                                                            $isKokuri = ($item->tipe ?? '') === \App\Models\FixedSchedule::TIPE_KOKURIKULER;
                                                        @endphp
                                                        <div class="p-2 text-center rounded-2 bg-light border border-dashed">
                                                            <i class="{{ $isKokuri ? 'fas fa-star text-warning' : 'fas fa-lock text-secondary' }} mb-1"></i>
                                                            <div class="fw-bold text-dark" style="font-size: 0.85rem; line-height: 1.25;">
                                                                {{ $item->keterangan }}
                                                            </div>
                                                            <span class="badge {{ $isKokuri ? 'bg-warning text-dark' : 'bg-secondary text-white' }} mt-1" style="font-size: 0.65rem;">
                                                                {{ $isKokuri ? 'Kokurikuler' : 'Jadwal Tetap' }}
                                                            </span>
                                                        </div>
                                                    @elseif($item->type === 'lesson')
                                                        <div class="card border-0 border-start border-4 border-primary shadow-sm h-100">
                                                            <div class="card-body p-2 text-center">
                                                                <div class="fw-bold text-primary" style="font-size: 0.85rem; line-height: 1.25;">
                                                                    {{ $item->nama_mapel }}
                                                                </div>
                                                                <div class="text-muted mt-1 small" style="font-size: 0.72rem;">
                                                                    <i class="fas fa-user me-1"></i>
                                                                    {{ $item->guru_name }}
                                                                </div>
                                                                @if(!empty($item->kode_mapel) && $item->kode_mapel !== '-')
                                                                    <div class="mt-1">
                                                                        <span class="badge bg-light text-dark border" style="font-size: 0.65rem;">
                                                                            {{ $item->kode_mapel }}
                                                                        </span>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endif
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
                    <div class="row g-2">
                        <div class="col-md-3">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary rounded me-2" style="width: 24px; height: 18px;"></div>
                                <span class="small">Hari ini</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex align-items-center">
                                <div class="bg-success rounded me-2" style="width: 24px; height: 18px;"></div>
                                <span class="small">Hari besok</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex align-items-center">
                                <div class="bg-light border border-dashed rounded me-2 d-flex align-items-center justify-content-center" style="width: 24px; height: 18px;">
                                    <i class="fas fa-lock text-secondary" style="font-size: 0.65rem;"></i>
                                </div>
                                <span class="small">Jadwal Tetap (Umum)</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex align-items-center">
                                <div class="bg-light border border-dashed rounded me-2 d-flex align-items-center justify-content-center" style="width: 24px; height: 18px;">
                                    <i class="fas fa-star text-warning" style="font-size: 0.65rem;"></i>
                                </div>
                                <span class="small">Kokurikuler / Pembiasaan</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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
