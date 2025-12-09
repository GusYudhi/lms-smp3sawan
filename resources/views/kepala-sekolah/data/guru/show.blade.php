@extends('layouts.app')

@section('title', 'Detail Guru')

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
                                <i class="fas fa-user-circle me-2"></i>Detail Guru
                            </h1>
                            <p class="text-muted mb-0">Informasi lengkap data guru</p>
                            <span class="badge bg-info mt-2">Mode Lihat Saja</span>
                        </div>
                        <div>
                            <a href="{{ route('kepala-sekolah.guru.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Teacher Profile Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-lg-3 text-center mb-4 mb-lg-0">
                            @if($user->guruProfile && $user->guruProfile->foto_profil)
                                <img src="{{ asset('storage/' . $user->guruProfile->foto_profil) }}"
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
                            @if($user->role === 'kepala_sekolah')
                            <span class="badge bg-danger mb-2">Kepala Sekolah</span>
                            @endif
                            <p class="text-muted mb-3">NIP: {{ $user->guruProfile->nip ?? '-' }}</p>

                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="d-flex flex-column">
                                        <span class="small text-muted">Mata Pelajaran</span>
                                        <span class="fw-semibold">
                                            @if($user->guruProfile && $user->guruProfile->mata_pelajaran)
                                                @if(is_array($user->guruProfile->mata_pelajaran))
                                                    {{ implode(', ', $user->guruProfile->mata_pelajaran) }}
                                                @else
                                                    {{ $user->guruProfile->mata_pelajaran }}
                                                @endif
                                            @else
                                                -
                                            @endif
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="d-flex flex-column">
                                        <span class="small text-muted">Status Kepegawaian</span>
                                        @if($user->guruProfile)
                                        <span class="badge bg-success mt-1 align-self-start">
                                            {{ $user->guruProfile->status_kepegawaian }}
                                        </span>
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="d-flex flex-column">
                                        <span class="small text-muted">Jabatan</span>
                                        <span class="fw-semibold">{{ $user->guruProfile->jabatan_di_sekolah ?? '-' }}</span>
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
                    <h6 class="mb-0 fw-semibold text-primary">
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
                            <td class="text-muted">NIP</td>
                            <td>:</td>
                            <td class="fw-semibold">{{ $user->guruProfile->nip ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Email</td>
                            <td>:</td>
                            <td class="fw-semibold">{{ $user->email }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Nomor Telepon</td>
                            <td>:</td>
                            <td class="fw-semibold">{{ $user->guruProfile->nomor_telepon ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Jenis Kelamin</td>
                            <td>:</td>
                            <td class="fw-semibold">
                                @if($user->guruProfile && $user->guruProfile->jenis_kelamin)
                                    {{ $user->guruProfile->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tempat, Tanggal Lahir</td>
                            <td>:</td>
                            <td class="fw-semibold">
                                @if($user->guruProfile)
                                    {{ $user->guruProfile->tempat_lahir ?? '-' }},
                                    {{ $user->guruProfile->tanggal_lahir ? \Carbon\Carbon::parse($user->guruProfile->tanggal_lahir)->format('d F Y') : '-' }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Professional Information -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light">
                    <h6 class="mb-0 fw-semibold text-primary">
                        <i class="fas fa-briefcase me-2"></i>Informasi Kepegawaian
                    </h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td width="40%" class="text-muted">Status Kepegawaian</td>
                            <td width="5%">:</td>
                            <td class="fw-semibold">
                                @if($user->guruProfile)
                                <span class="badge bg-success">{{ $user->guruProfile->status_kepegawaian }}</span>
                                @else
                                -
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Golongan</td>
                            <td>:</td>
                            <td class="fw-semibold">{{ $user->guruProfile->golongan ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Jabatan di Sekolah</td>
                            <td>:</td>
                            <td class="fw-semibold">{{ $user->guruProfile->jabatan_di_sekolah ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Mata Pelajaran</td>
                            <td>:</td>
                            <td class="fw-semibold">
                                @if($user->guruProfile && $user->guruProfile->mata_pelajaran)
                                    @if(is_array($user->guruProfile->mata_pelajaran))
                                        {{ implode(', ', $user->guruProfile->mata_pelajaran) }}
                                    @else
                                        {{ $user->guruProfile->mata_pelajaran }}
                                    @endif
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Wali Kelas</td>
                            <td>:</td>
                            <td class="fw-semibold">
                                @if($user->guruProfile && $user->guruProfile->kelas)
                                    {{ $user->guruProfile->kelas->tingkat }}{{ $user->guruProfile->kelas->nama_kelas }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
