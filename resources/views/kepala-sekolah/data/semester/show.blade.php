@extends('layouts.app')

@section('title', 'Detail Semester')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body py-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h3 mb-2 text-primary">
                                <i class="fas fa-calendar me-2"></i>Detail Semester
                            </h1>
                            <p class="text-muted mb-0">Informasi lengkap semester</p>
                        </div>
                        <div>
                            <a href="{{ route('kepala-sekolah.semester.index', $semester->tahun_pelajaran_id) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Information -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light">
                    <h6 class="mb-0 fw-semibold text-primary">
                        <i class="fas fa-info-circle me-2"></i>Informasi Semester
                    </h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td width="40%" class="text-muted">Nama Semester</td>
                            <td width="5%">:</td>
                            <td class="fw-semibold">{{ $semester->nama }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tahun Pelajaran</td>
                            <td>:</td>
                            <td class="fw-semibold">{{ $semester->tahunPelajaran->nama }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Semester</td>
                            <td>:</td>
                            <td>
                                <span class="badge {{ $semester->semester == 1 ? 'bg-info' : 'bg-warning' }}">
                                    Semester {{ $semester->semester }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tanggal Mulai</td>
                            <td>:</td>
                            <td class="fw-semibold">{{ \Carbon\Carbon::parse($semester->tanggal_mulai)->format('d F Y') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tanggal Selesai</td>
                            <td>:</td>
                            <td class="fw-semibold">{{ \Carbon\Carbon::parse($semester->tanggal_selesai)->format('d F Y') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Durasi</td>
                            <td>:</td>
                            <td class="fw-semibold">
                                {{ \Carbon\Carbon::parse($semester->tanggal_mulai)->diffInDays(\Carbon\Carbon::parse($semester->tanggal_selesai)) }} hari
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Status</td>
                            <td>:</td>
                            <td>
                                @if($semester->is_active)
                                <span class="badge bg-success">Aktif</span>
                                @else
                                <span class="badge bg-secondary">Tidak Aktif</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light">
                    <h6 class="mb-0 fw-semibold text-primary">
                        <i class="fas fa-clock me-2"></i>Timeline
                    </h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item mb-4">
                            <div class="d-flex">
                                <div class="timeline-marker bg-success"></div>
                                <div class="ms-3">
                                    <h6 class="mb-1">Tanggal Mulai</h6>
                                    <p class="text-muted mb-0">{{ \Carbon\Carbon::parse($semester->tanggal_mulai)->format('d F Y') }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="timeline-item mb-4">
                            <div class="d-flex">
                                <div class="timeline-marker bg-info"></div>
                                <div class="ms-3">
                                    <h6 class="mb-1">Hari Ini</h6>
                                    <p class="text-muted mb-0">{{ \Carbon\Carbon::now()->format('d F Y') }}</p>
                                    @php
                                        $now = \Carbon\Carbon::now();
                                        $start = \Carbon\Carbon::parse($semester->tanggal_mulai);
                                        $end = \Carbon\Carbon::parse($semester->tanggal_selesai);
                                    @endphp
                                    @if($now->between($start, $end))
                                        <small class="text-success">Semester sedang berlangsung</small>
                                    @elseif($now->lt($start))
                                        <small class="text-warning">Semester belum dimulai</small>
                                    @else
                                        <small class="text-muted">Semester telah selesai</small>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="timeline-item">
                            <div class="d-flex">
                                <div class="timeline-marker bg-danger"></div>
                                <div class="ms-3">
                                    <h6 class="mb-1">Tanggal Selesai</h6>
                                    <p class="text-muted mb-0">{{ \Carbon\Carbon::parse($semester->tanggal_selesai)->format('d F Y') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline-marker {
    width: 15px;
    height: 15px;
    border-radius: 50%;
    flex-shrink: 0;
    margin-top: 5px;
}
</style>
@endsection
