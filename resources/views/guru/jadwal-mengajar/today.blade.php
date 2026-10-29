@extends('layouts.app')

@section('title', 'Jadwal Mengajar Hari Ini')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
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

    <!-- Today's Schedule -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    @if($schedules->isEmpty())
                        <div class="text-center py-5">
                            <img src="https://cdn-icons-png.flaticon.com/512/2921/2921222.png" alt="No Schedule" style="width: 150px; opacity: 0.5;">
                            <h5 class="mt-3 text-muted">Tidak ada jadwal mengajar hari ini</h5>
                            <p class="text-muted">Anda tidak memiliki jadwal mengajar pada hari {{ $today }}</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center" style="width: 100px;">Jam Ke</th>
                                        <th class="text-center" style="width: 150px;">Waktu</th>
                                        <th>Mata Pelajaran</th>
                                        <th>Kelas</th>
                                        <th class="text-center" style="width: 120px;">Status</th>
                                        <th class="text-center" style="width: 150px;">Aksi</th>
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
                                    @endphp
                                    <tr>
                                        <td class="text-center">
                                            <span class="badge bg-secondary">{{ $jamKeText }}</span>
                                            @if(($schedule->jumlah_jp ?? 1) > 1)
                                                <span class="badge bg-light text-dark border ms-1">{{ $schedule->jumlah_jp }} JP</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <small class="text-dark fw-bold">{{ $waktu }}</small>
                                        </td>
                                        <td>
                                            <div>
                                                <h6 class="mb-0">{{ $schedule->mataPelajaran->nama_mapel ?? '-' }}</h6>
                                                <small class="text-muted">{{ $schedule->mataPelajaran->kode_mapel ?? '-' }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $schedule->kelas_full }}</span>
                                        </td>
                                        <td class="text-center">
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
                                        <td class="text-center">
                                            @if(!$hasJurnal)
                                                <a href="{{ route('guru.jurnal-mengajar.wizard') }}?tanggal={{ $tanggalHariIni }}&kelas_id={{ $schedule->kelas_id }}&mata_pelajaran_id={{ $schedule->mata_pelajaran_id }}&jam_ke_mulai={{ $schedule->jam_ke_mulai }}&jam_ke_selesai={{ $schedule->jam_ke_selesai }}&return_to=today"
                                                   class="btn btn-sm btn-primary">
                                                    <i class="fas fa-plus-circle me-1"></i>Isi Jurnal
                                                </a>
                                            @else
                                                @if($schedule->jurnal)
                                                    <a href="{{ route('guru.jurnal-mengajar.show', $schedule->jurnal->id) }}" class="btn btn-sm btn-outline-success">
                                                        <i class="fas fa-eye me-1"></i>Lihat Jurnal
                                                    </a>
                                                @else
                                                    <button class="btn btn-sm btn-success" disabled>
                                                        <i class="fas fa-check me-1"></i>Selesai
                                                    </button>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Summary -->
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="alert alert-info mb-0">
                                    <div class="row text-center">
                                        <div class="col-md-4">
                                            <h4 class="mb-0">{{ $totalSesi ?? $schedules->count() }} <small class="fs-6 text-muted">Sesi ({{ $totalJp ?? $schedules->count() }} JP)</small></h4>
                                            <small class="text-muted">Total Sesi Mengajar</small>
                                        </div>
                                        <div class="col-md-4">
                                            <h4 class="mb-0">{{ $sesiTerisi ?? 0 }} <small class="fs-6 text-muted">Sesi</small></h4>
                                            <small class="text-muted">Jurnal Terisi</small>
                                        </div>
                                        <div class="col-md-4">
                                            <h4 class="mb-0">{{ $sesiBelumTerisi ?? 0 }} <small class="fs-6 text-muted">Sesi</small></h4>
                                            <small class="text-muted">Belum Terisi</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
