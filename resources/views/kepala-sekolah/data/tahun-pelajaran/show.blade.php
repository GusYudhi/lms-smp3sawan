@extends('layouts.app')

@section('title', 'Detail Tahun Pelajaran')

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
                                <i class="fas fa-calendar-alt me-2"></i>Detail Tahun Pelajaran
                            </h1>
                            <p class="text-muted mb-0">Informasi lengkap tahun pelajaran</p>
                        </div>
                        <div>
                            <a href="{{ route('kepala-sekolah.tahun-pelajaran.index') }}" class="btn btn-secondary">
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
                        <i class="fas fa-info-circle me-2"></i>Informasi Tahun Pelajaran
                    </h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td width="40%" class="text-muted">Nama</td>
                            <td width="5%">:</td>
                            <td class="fw-semibold">{{ $tahunPelajaran->nama }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tahun Mulai</td>
                            <td>:</td>
                            <td class="fw-semibold">{{ $tahunPelajaran->tahun_mulai }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tahun Selesai</td>
                            <td>:</td>
                            <td class="fw-semibold">{{ $tahunPelajaran->tahun_selesai }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tanggal Mulai</td>
                            <td>:</td>
                            <td class="fw-semibold">{{ \Carbon\Carbon::parse($tahunPelajaran->tanggal_mulai)->format('d F Y') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tanggal Selesai</td>
                            <td>:</td>
                            <td class="fw-semibold">{{ \Carbon\Carbon::parse($tahunPelajaran->tanggal_selesai)->format('d F Y') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Status</td>
                            <td>:</td>
                            <td>
                                @if($tahunPelajaran->is_active)
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
                        <i class="fas fa-calendar me-2"></i>Daftar Semester
                    </h6>
                </div>
                <div class="card-body">
                    @if($tahunPelajaran->semester->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($tahunPelajaran->semester as $semester)
                        <div class="list-group-item px-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1">{{ $semester->nama }}</h6>
                                    <small class="text-muted">
                                        {{ \Carbon\Carbon::parse($semester->tanggal_mulai)->format('d M Y') }} -
                                        {{ \Carbon\Carbon::parse($semester->tanggal_selesai)->format('d M Y') }}
                                    </small>
                                </div>
                                <div>
                                    @if($semester->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                    @else
                                    <span class="badge bg-secondary">Tidak Aktif</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p class="text-muted text-center py-4 mb-0">Belum ada semester</p>
                    @endif
                </div>
                <div class="card-footer bg-white">
                    <a href="{{ route('kepala-sekolah.semester.index', $tahunPelajaran->id) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-eye me-1"></i>Lihat Semua Semester
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
