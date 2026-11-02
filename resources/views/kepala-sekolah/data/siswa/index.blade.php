@extends('layouts.app')

@section('title', 'Data Siswa')

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
                                <i class="fas fa-user-graduate text-success me-2"></i>Data Siswa
                            </h1>
                            <p class="text-subtle mb-0 fw-medium">Lihat direktori data siswa terdaftar di seluruh kelas</p>
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

    <!-- Statistik Siswa (MD3 Tokens) -->
    <div class="row g-3 mb-4">
        <div class="col-6">
            <div class="dashboard-stat-card text-center">
                <span class="stat-label">Total Siswa</span>
                <div class="stat-value text-success">{{ \App\Models\User::where('role', 'siswa')->count() }}</div>
            </div>
        </div>
        <div class="col-6">
            <div class="dashboard-stat-card text-center">
                <span class="stat-label">Total Kelas</span>
                <div class="stat-value text-primary">{{ \App\Models\Kelas::count() }}</div>
            </div>
        </div>
    </div>

    <!-- Mobile Filter Toggle Button -->
    <div class="d-md-none mb-3">
        <button class="btn btn-outline-primary w-100 d-flex justify-content-between align-items-center shadow-sm" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapseDataSiswa" aria-expanded="false">
            <span><i class="fas fa-filter me-2"></i>Filter Data Siswa</span>
            <i class="fas fa-chevron-down"></i>
        </button>
    </div>

    <!-- Search and Filter -->
    <div class="collapse d-md-block mb-4" id="filterCollapseDataSiswa">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form action="{{ route('kepala-sekolah.siswa.index') }}" method="GET" class="row g-3" id="filterForm">
                    <div class="col-md-7">
                        <label class="form-label text-muted small fw-bold mb-1">Pencarian</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text"
                                   name="search"
                                   class="form-control border-start-0 search-input"
                                   placeholder="Cari berdasarkan nama, NISN, NIS, atau email..."
                                   value="{{ $search }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-muted small fw-bold mb-1">Kelas</label>
                        <select name="kelas" class="form-select auto-submit">
                            <option value="">Semua Kelas</option>
                            @foreach($kelasList as $kelas)
                            <option value="{{ $kelas->id }}" {{ $kelasFilter == $kelas->id ? 'selected' : '' }}>
                                {{ $kelas->tingkat }}{{ $kelas->nama_kelas }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <a href="{{ route('kepala-sekolah.siswa.index') }}" class="btn btn-secondary w-100">
                            <i class="fas fa-redo me-1"></i>Reset
                        </a>
                    </div>
                    <div class="col-12">
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>Filter kelas diterapkan otomatis. Tekan Enter untuk pencarian teks.
                        </small>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Data Siswa Table -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="card-title mb-0 fw-semibold text-high-contrast">
                        <i class="fas fa-list text-primary me-2"></i>Daftar Siswa
                    </h5>
                </div>
                <div class="card-body p-0">
                    <!-- Desktop Table View -->
                    <div class="table-responsive d-none d-md-block">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 5%;">No</th>
                                    <th style="width: 25%;">Nama Lengkap</th>
                                    <th style="width: 12%;">NISN</th>
                                    <th style="width: 12%;">NIS</th>
                                    <th style="width: 15%;">Kelas</th>
                                    <th style="width: 20%;">Email</th>
                                    <th style="width: 11%;" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $index => $user)
                                <tr>
                                    <td>{{ $users->firstItem() + $index }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="me-2">
                                                <x-profile-photo
                                                    :src="$user->studentProfile && $user->studentProfile->foto_profil
                                                        ? asset('storage/profile_photos/' . $user->studentProfile->foto_profil)
                                                        : null"
                                                    :name="$user->name"
                                                    size="sm"
                                                    :clickable="true"
                                                />
                                            </div>
                                            <strong class="text-body-emphasis">{{ $user->name }}</strong>
                                        </div>
                                    </td>
                                    <td>{{ $user->studentProfile->nisn ?? '-' }}</td>
                                    <td>{{ $user->studentProfile->nis ?? '-' }}</td>
                                    <td>
                                        @if($user->studentProfile && $user->studentProfile->kelas)
                                        <span class="badge bg-primary-subtle text-primary border">
                                            {{ $user->studentProfile->kelas->tingkat }}{{ $user->studentProfile->kelas->nama_kelas }}
                                        </span>
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td><small class="text-muted">{{ $user->email }}</small></td>
                                    <td class="text-center">
                                        <a href="{{ route('kepala-sekolah.siswa.show', $user->id) }}"
                                           class="btn btn-sm btn-info text-white"
                                           title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <i class="fas fa-inbox text-muted fs-1 mb-3 d-block"></i>
                                        <p class="text-muted mb-0">Tidak ada data siswa</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Adaptive Cards View -->
                    <div class="d-md-none p-3">
                        @forelse($users as $index => $user)
                            <div class="direktori-card-mobile">
                                <div class="direktori-header">
                                    <x-profile-photo
                                        :src="$user->studentProfile && $user->studentProfile->foto_profil
                                            ? asset('storage/profile_photos/' . $user->studentProfile->foto_profil)
                                            : null"
                                        :name="$user->name"
                                        size="md"
                                        :clickable="true"
                                    />
                                    <div>
                                        <h6 class="mb-0 fw-bold text-body-emphasis">{{ $user->name }}</h6>
                                        <small class="text-muted">NISN: {{ $user->studentProfile->nisn ?? '-' }}</small>
                                        @if($user->studentProfile && $user->studentProfile->nis)
                                            <small class="text-muted"> | NIS: {{ $user->studentProfile->nis }}</small>
                                        @endif
                                    </div>
                                </div>
                                <div class="direktori-meta">
                                    @if($user->studentProfile && $user->studentProfile->kelas)
                                        <span class="badge bg-primary-subtle text-primary border">
                                            Kelas {{ $user->studentProfile->kelas->tingkat }}{{ $user->studentProfile->kelas->nama_kelas }}
                                        </span>
                                    @endif
                                    <span class="badge bg-light text-dark border">
                                        {{ $user->email }}
                                    </span>
                                </div>
                                <div class="direktori-footer">
                                    <a href="{{ route('kepala-sekolah.siswa.show', $user->id) }}" class="btn btn-sm btn-info text-white">
                                        <i class="fas fa-eye me-1"></i>Lihat Profil Siswa
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4 bg-white rounded-3 border">
                                <i class="fas fa-inbox text-muted fs-1 mb-3 d-block opacity-50"></i>
                                <p class="text-muted mb-0">Tidak ada data siswa</p>
                            </div>
                        @endforelse
                    </div>
                </div>
                <div class="card-footer bg-white border-top py-3">
                    <div class="pagination-container">
                        <div class="pagination-info">
                            Menampilkan {{ $users->firstItem() ?? 0 }} - {{ $users->lastItem() ?? 0 }} dari {{ $users->total() }} data
                        </div>
                        <div class="pagination-controls">
                            @if ($users->onFirstPage())
                                <span class="btn btn-pagination disabled">
                                    <i class="fas fa-chevron-left me-1"></i> Sebelumnya
                                </span>
                            @else
                                <a href="{{ $users->appends(['search' => $search, 'kelas' => $kelasFilter])->previousPageUrl() }}"
                                   class="btn btn-pagination">
                                    <i class="fas fa-chevron-left me-1"></i> Sebelumnya
                                </a>
                            @endif

                            <span class="pagination-current">
                                Halaman {{ $users->currentPage() }} dari {{ $users->lastPage() }}
                            </span>

                            @if ($users->hasMorePages())
                                <a href="{{ $users->appends(['search' => $search, 'kelas' => $kelasFilter])->nextPageUrl() }}"
                                   class="btn btn-pagination">
                                    Selanjutnya <i class="fas fa-chevron-right ms-1"></i>
                                </a>
                            @else
                                <span class="btn btn-pagination disabled">
                                    Selanjutnya <i class="fas fa-chevron-right ms-1"></i>
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.getElementById('filterForm');
    const autoSubmitElements = document.querySelectorAll('.auto-submit');
    const searchInput = document.querySelector('.search-input');

    // Auto-submit untuk dropdown
    autoSubmitElements.forEach(element => {
        element.addEventListener('change', function() {
            filterForm.submit();
        });
    });

    // Submit saat tekan Enter di search input
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                filterForm.submit();
            }
        });
    }
});
</script>
@endsection
