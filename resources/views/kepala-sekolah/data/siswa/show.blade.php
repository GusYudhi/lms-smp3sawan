@extends('layouts.app')

@section('title', 'Detail Siswa')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body py-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h3 mb-2 text-success">
                                <i class="fas fa-user-circle me-2"></i>Detail Siswa
                            </h1>
                            <p class="text-muted mb-0">Informasi lengkap data siswa</p>
                        </div>
                        <div>
                            <a href="{{ route('kepala-sekolah.siswa.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Student Profile Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-lg-3 text-center mb-4 mb-lg-0">
                            @if($user->studentProfile && $user->studentProfile->foto_profil)
                                <img src="{{ asset('storage/' . $user->studentProfile->foto_profil) }}"
                                     alt="Foto {{ $user->name }}"
                                     class="rounded-circle border"
                                     style="width: 150px; height: 150px; object-fit: cover;">
                            @else
                                <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-light border text-muted"
                                     style="width: 150px; height: 150px; font-size: 3rem;">
                                    <i class="fas fa-user"></i>
                                </div>
                            @endif
                        </div>
                        <div class="col-lg-9">
                            <h2 class="mb-2 fw-bold">{{ $user->name }}</h2>
                            <p class="text-muted mb-3">NISN: {{ $user->studentProfile->nisn ?? '-' }}</p>

                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="d-flex flex-column">
                                        <span class="small text-muted">NIS</span>
                                        <span class="fw-semibold">{{ $user->studentProfile->nis ?? '-' }}</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="d-flex flex-column">
                                        <span class="small text-muted">Kelas</span>
                                        @if($user->studentProfile && $user->studentProfile->kelas)
                                        <span class="badge bg-primary mt-1 align-self-start">
                                            {{ $user->studentProfile->kelas->tingkat }}{{ $user->studentProfile->kelas->nama_kelas }}
                                        </span>
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="d-flex flex-column">
                                        <span class="small text-muted">Status</span>
                                        @if($user->studentProfile && $user->studentProfile->status)
                                            @if($user->studentProfile->status === 'AKTIF')
                                                <span class="badge bg-success mt-1 align-self-start">Aktif</span>
                                            @elseif($user->studentProfile->status === 'LULUS')
                                                <span class="badge bg-primary mt-1 align-self-start">Lulus</span>
                                            @else
                                                <span class="badge bg-secondary mt-1 align-self-start">Tidak Aktif</span>
                                            @endif
                                        @else
                                            <span class="badge bg-success mt-1 align-self-start">Aktif</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Information -->
    <div class="row">
        <!-- Personal Information -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light">
                    <h6 class="mb-0 fw-semibold text-success">
                        <i class="fas fa-user me-2"></i>Informasi Personal
                    </h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td width="40%" class="text-muted">Nama Lengkap</td>
                            <td width="5%">:</td>
                            <td class="fw-semibold">{{ $user->name }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">NISN</td>
                            <td>:</td>
                            <td class="fw-semibold">{{ $user->studentProfile->nisn ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">NIS</td>
                            <td>:</td>
                            <td class="fw-semibold">{{ $user->studentProfile->nis ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Email</td>
                            <td>:</td>
                            <td class="fw-semibold">{{ $user->email }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Jenis Kelamin</td>
                            <td>:</td>
                            <td class="fw-semibold">
                                @if($user->studentProfile && $user->studentProfile->jenis_kelamin)
                                    {{ $user->studentProfile->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tempat, Tanggal Lahir</td>
                            <td>:</td>
                            <td class="fw-semibold">
                                @if($user->studentProfile)
                                    {{ $user->studentProfile->tempat_lahir ?? '-' }}@if($user->studentProfile->tempat_lahir && $user->studentProfile->tanggal_lahir),@endif
                                    {{ $user->studentProfile->tanggal_lahir ? \Carbon\Carbon::parse($user->studentProfile->tanggal_lahir)->format('d F Y') : ($user->studentProfile->tempat_lahir ? '' : '-') }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Academic Information -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light">
                    <h6 class="mb-0 fw-semibold text-success">
                        <i class="fas fa-graduation-cap me-2"></i>Informasi Akademik
                    </h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td width="40%" class="text-muted">Kelas</td>
                            <td width="5%">:</td>
                            <td class="fw-semibold">
                                @if($user->studentProfile && $user->studentProfile->kelas)
                                <span class="badge bg-primary">
                                    {{ $user->studentProfile->kelas->tingkat }}{{ $user->studentProfile->kelas->nama_kelas }}
                                </span>
                                @else
                                -
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tahun Angkatan</td>
                            <td>:</td>
                            <td class="fw-semibold">{{ $user->studentProfile->tahun_angkatan ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Status</td>
                            <td>:</td>
                            <td>
                                @if($user->studentProfile && $user->studentProfile->status)
                                    @if($user->studentProfile->status === 'AKTIF')
                                        <span class="badge bg-success">Aktif</span>
                                    @elseif($user->studentProfile->status === 'LULUS')
                                        <span class="badge bg-primary">Lulus</span>
                                    @else
                                        <span class="badge bg-secondary">Tidak Aktif</span>
                                    @endif
                                @else
                                    <span class="badge bg-success">Aktif</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">No. Telp Orang Tua</td>
                            <td>:</td>
                            <td class="fw-semibold">{{ $user->studentProfile->nomor_telepon_orangtua ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
