@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card card-modern border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <h4 class="mb-0 text-high-contrast fw-semibold">
                        <i class="fas fa-tasks text-primary me-2"></i>Manajemen Tugas Guru
                    </h4>
                    <a href="{{ route('kepala-sekolah.tugas-guru.create') }}" class="btn btn-primary shadow-sm">
                        <i class="fas fa-plus me-1"></i> Tambah Tugas Baru
                    </a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Mobile Filter Toggle Button -->
                    <div class="d-md-none mb-3">
                        <button class="btn btn-outline-primary w-100 d-flex justify-content-between align-items-center shadow-sm" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapseKepsekTugas" aria-expanded="false">
                            <span><i class="fas fa-filter me-2"></i>Filter Tugas</span>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                    </div>

                    <!-- Filter Form -->
                    <div class="collapse d-md-block mb-4" id="filterCollapseKepsekTugas">
                        <div class="dashboard-filter-card">
                            <form method="GET" action="{{ route('kepala-sekolah.tugas-guru.index') }}" id="filterForm">
                                <div class="row g-3 align-items-end">
                                    <div class="col-md-5">
                                        <label class="form-label text-muted small fw-bold mb-1">Cari Tugas</label>
                                        <input type="text" name="search" class="form-control search-input" placeholder="Ketik kata kunci tugas..." value="{{ request('search') }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label text-muted small fw-bold mb-1">Status</label>
                                        <select name="status" class="form-select auto-submit">
                                            <option value="">Semua Status</option>
                                            <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                            <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                            <option value="dibatalkan" {{ request('status') == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 d-flex align-items-center gap-2">
                                        <a href="{{ route('kepala-sekolah.tugas-guru.index') }}" class="btn btn-secondary w-100">
                                            <i class="fas fa-redo me-1"></i>Reset
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Statistics Cards (MD3 Tokens) -->
                    <div class="row g-3 mb-4">
                        <div class="col-4">
                            <div class="dashboard-stat-card text-center">
                                <span class="stat-label">Total Tugas</span>
                                <div class="stat-value text-primary">{{ $tugasList->total() }}</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="dashboard-stat-card text-center">
                                <span class="stat-label">Tugas Aktif</span>
                                <div class="stat-value text-success">{{ $tugasList->where('status', 'aktif')->count() }}</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="dashboard-stat-card text-center">
                                <span class="stat-label">Total Guru</span>
                                <div class="stat-value text-info">{{ $totalGuru }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Desktop Table View -->
                    <div class="table-responsive d-none d-md-block mb-3">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 5%;">No</th>
                                    <th>Judul Tugas</th>
                                    <th>Deadline</th>
                                    <th>Status</th>
                                    <th>Submissions</th>
                                    <th>Lampiran</th>
                                    <th class="text-center" style="width: 140px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tugasList as $tugas)
                                <tr>
                                    <td>{{ $tugasList->firstItem() + $loop->index }}</td>
                                    <td>
                                        <strong class="text-body-emphasis">{{ $tugas->judul }}</strong>
                                        @if($tugas->deskripsi)
                                            <br><small class="text-muted">{{ Str::limit($tugas->deskripsi, 60) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="fw-medium">{{ $tugas->deadline->format('d M Y H:i') }}</span>
                                        @if($tugas->isExpired())
                                            <br><span class="badge bg-danger">Expired</span>
                                        @else
                                            <br><span class="badge bg-info-subtle text-info border">{{ $tugas->deadline->diffForHumans() }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $tugas->status_badge }}">
                                            {{ ucfirst($tugas->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary-subtle text-primary border">
                                            {{ $tugas->submissions_count }} / {{ $totalGuru }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($tugas->files->count() > 0)
                                            <span class="badge bg-secondary-subtle text-secondary border">
                                                <i class="fas fa-paperclip me-1"></i>{{ $tugas->files->count() }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="d-inline-flex align-items-center gap-1">
                                            <a href="{{ route('kepala-sekolah.tugas-guru.show', $tugas->id) }}" class="btn btn-sm btn-outline-primary" title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('kepala-sekolah.tugas-guru.edit', $tugas->id) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('kepala-sekolah.tugas-guru.destroy', $tugas->id) }}" method="POST" class="d-inline delete-form" data-message="Yakin ingin menghapus tugas ini?">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                                        <p class="text-muted mb-0">Belum ada tugas yang dibuat</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Adaptive Cards View -->
                    <div class="d-md-none mb-3">
                        @forelse($tugasList as $tugas)
                            <div class="tugas-card-mobile">
                                <div class="tugas-top-bar">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-{{ $tugas->status_badge }}">{{ ucfirst($tugas->status) }}</span>
                                        @if($tugas->isExpired())
                                            <span class="badge bg-danger">Expired</span>
                                        @else
                                            <span class="badge bg-info-subtle text-info border">{{ $tugas->deadline->diffForHumans() }}</span>
                                        @endif
                                    </div>
                                    <span class="badge bg-primary-subtle text-primary border">
                                        <i class="fas fa-users me-1"></i>{{ $tugas->submissions_count }} / {{ $totalGuru }}
                                    </span>
                                </div>
                                <h5 class="tugas-title">{{ $tugas->judul }}</h5>
                                @if($tugas->deskripsi)
                                    <p class="tugas-desc">{{ Str::limit($tugas->deskripsi, 90) }}</p>
                                @endif
                                <div class="tugas-meta-item mb-2">
                                    <i class="fas fa-calendar-alt text-muted me-1"></i>
                                    <span>Deadline: <strong>{{ $tugas->deadline->format('d M Y H:i') }}</strong></span>
                                </div>
                                @if($tugas->files->count() > 0)
                                    <div class="tugas-meta-item mb-2">
                                        <i class="fas fa-paperclip text-muted me-1"></i>
                                        <span>{{ $tugas->files->count() }} Lampiran Berkas</span>
                                    </div>
                                @endif
                                <div class="tugas-actions">
                                    <a href="{{ route('kepala-sekolah.tugas-guru.show', $tugas->id) }}" class="btn btn-sm btn-info text-white">
                                        <i class="fas fa-eye me-1"></i>Detail
                                    </a>
                                    <a href="{{ route('kepala-sekolah.tugas-guru.edit', $tugas->id) }}" class="btn btn-sm btn-outline-warning">
                                        <i class="fas fa-edit me-1"></i>Edit
                                    </a>
                                    <form action="{{ route('kepala-sekolah.tugas-guru.destroy', $tugas->id) }}" method="POST" class="d-inline delete-form" data-message="Yakin ingin menghapus tugas ini?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash me-1"></i>Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4 bg-white rounded-3 border">
                                <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                                <p class="text-muted mb-0">Belum ada tugas yang dibuat</p>
                            </div>
                        @endforelse
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $tugasList->links() }}
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
