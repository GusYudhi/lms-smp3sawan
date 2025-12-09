@extends('layouts.app')

@section('title', 'Data Guru')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card card-modern border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h1 class="h3 mb-2 text-high-contrast">
                                <i class="fas fa-chalkboard-teacher text-primary me-2"></i>Data Guru
                            </h1>
                            <p class="text-subtle mb-0 fw-medium">Lihat informasi data guru dan tenaga pendidik</p>
                            <span class="badge bg-info mt-2">Mode Lihat Saja</span>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ route('kepala-sekolah.dashboard') }}" class="btn btn-outline-secondary shadow-sm">
                                <i class="fas fa-arrow-left me-1"></i>Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistik Guru -->
    <div class="row g-4 mb-4">
        <!-- Total Guru -->
        <div class="col-xl-3 col-md-6">
            <div class="card card-stats text-center h-100">
                <div class="card-body">
                    <div class="text-primary fs-1 mb-3">
                        <i class="fas fa-users"></i>
                    </div>
                    <h6 class="card-title text-muted">Total Guru</h6>
                    <h2 class="text-primary">{{ \App\Models\User::whereIn('role', ['guru', 'kepala_sekolah'])->count() }}</h2>
                    <small class="text-muted">Semua Guru</small>
                </div>
            </div>
        </div>

        <!-- Guru PNS -->
        <div class="col-xl-3 col-md-6">
            <div class="card card-stats text-center h-100">
                <div class="card-body">
                    <div class="text-success fs-1 mb-3">
                        <i class="fas fa-id-badge"></i>
                    </div>
                    <h6 class="card-title text-muted">Guru PNS</h6>
                    <h2 class="text-success">{{ \App\Models\GuruProfile::where('status_kepegawaian', 'PNS')->count() }}</h2>
                    <small class="text-muted">Pegawai Negeri</small>
                </div>
            </div>
        </div>

        <!-- Guru PPPK -->
        <div class="col-xl-3 col-md-6">
            <div class="card card-stats text-center h-100">
                <div class="card-body">
                    <div class="text-info fs-1 mb-3">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <h6 class="card-title text-muted">Guru PPPK</h6>
                    <h2 class="text-info">{{ \App\Models\GuruProfile::where('status_kepegawaian', 'PPPK')->count() }}</h2>
                    <small class="text-muted">P3K</small>
                </div>
            </div>
        </div>

        <!-- Guru Honorer -->
        <div class="col-xl-3 col-md-6">
            <div class="card card-stats text-center h-100">
                <div class="card-body">
                    <div class="text-warning fs-1 mb-3">
                        <i class="fas fa-user-clock"></i>
                    </div>
                    <h6 class="card-title text-muted">Guru Honorer</h6>
                    <h2 class="text-warning">{{ \App\Models\GuruProfile::where('status_kepegawaian', 'Honorer')->count() }}</h2>
                    <small class="text-muted">Non PNS</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form action="{{ route('kepala-sekolah.guru.index') }}" method="GET" class="row g-3">
                        <div class="col-md-10">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text"
                                       name="search"
                                       class="form-control"
                                       placeholder="Cari berdasarkan nama, NIP, atau email..."
                                       value="{{ $search }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search me-1"></i>Cari
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Guru Table -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-list me-2"></i>Daftar Guru
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="25%">Nama Lengkap</th>
                                    <th width="15%">NIP</th>
                                    <th width="15%">Mata Pelajaran</th>
                                    <th width="15%">Status Kepegawaian</th>
                                    <th width="15%">Jabatan</th>
                                    <th width="10%" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $index => $user)
                                <tr>
                                    <td>{{ $users->firstItem() + $index }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle bg-primary text-white me-2">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <strong>{{ $user->name }}</strong>
                                                @if($user->role === 'kepala_sekolah')
                                                <span class="badge bg-danger ms-1">Kepala Sekolah</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $user->guruProfile->nip ?? '-' }}</td>
                                    <td>
                                        @if($user->guruProfile && $user->guruProfile->mata_pelajaran)
                                            @if(is_array($user->guruProfile->mata_pelajaran))
                                                {{ implode(', ', $user->guruProfile->mata_pelajaran) }}
                                            @else
                                                {{ $user->guruProfile->mata_pelajaran }}
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($user->guruProfile)
                                        <span class="badge
                                            @if($user->guruProfile->status_kepegawaian == 'PNS') bg-success
                                            @elseif($user->guruProfile->status_kepegawaian == 'PPPK') bg-info
                                            @else bg-warning @endif">
                                            {{ $user->guruProfile->status_kepegawaian }}
                                        </span>
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $user->guruProfile->jabatan_di_sekolah ?? '-' }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('kepala-sekolah.guru.show', $user->id) }}"
                                           class="btn btn-sm btn-info"
                                           title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <i class="fas fa-inbox text-muted fs-1 mb-3 d-block"></i>
                                        <p class="text-muted mb-0">Tidak ada data guru</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white border-top">
                    <div class="pagination-container">
                        <div class="pagination-info">
                            Menampilkan {{ $users->firstItem() ?? 0 }}-{{ $users->lastItem() ?? 0 }} dari {{ $users->total() }} data
                        </div>
                        <div class="pagination-controls">
                            @if ($users->onFirstPage())
                                <button class="btn btn-pagination" disabled>
                                    <i class="fas fa-chevron-left"></i> Sebelumnya
                                </button>
                            @else
                                <a href="{{ $users->previousPageUrl() }}" class="btn btn-pagination">
                                    <i class="fas fa-chevron-left"></i> Sebelumnya
                                </a>
                            @endif

                            <span class="pagination-current">{{ $users->currentPage() }}</span>

                            @if ($users->hasMorePages())
                                <a href="{{ $users->nextPageUrl() }}" class="btn btn-pagination">
                                    Selanjutnya <i class="fas fa-chevron-right"></i>
                                </a>
                            @else
                                <button class="btn btn-pagination" disabled>
                                    Selanjutnya <i class="fas fa-chevron-right"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.pagination-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.pagination-info {
    color: #6c757d;
    font-size: 0.9rem;
}

.pagination-controls {
    display: flex;
    gap: 0.5rem;
    align-items: center;
}

.btn-pagination {
    padding: 0.5rem 1rem;
    border: 1px solid #dee2e6;
    background-color: #fff;
    color: #495057;
    text-decoration: none;
    border-radius: 0.375rem;
    font-size: 0.9rem;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-pagination:hover:not(:disabled) {
    background-color: #0d6efd;
    color: #fff;
    border-color: #0d6efd;
}

.btn-pagination:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.pagination-current {
    padding: 0.5rem 1rem;
    background-color: #0d6efd;
    color: #fff;
    border-radius: 0.375rem;
    font-weight: 600;
    min-width: 2.5rem;
    text-align: center;
}

@media (max-width: 576px) {
    .pagination-container {
        flex-direction: column;
        text-align: center;
    }

    .pagination-controls {
        width: 100%;
        justify-content: center;
    }
}

<style>
.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 1.2em;
}

.card-stats {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    transition: all 0.3s ease;
}

.card-stats:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    transform: translateY(-5px);
}
</style>
@endsection
