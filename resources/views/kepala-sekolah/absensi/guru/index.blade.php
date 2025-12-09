@extends('layouts.app')

@section('title', 'Rekap Absensi Guru')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h1 class="h3 mb-2 text-success">
                                <i class="fas fa-clipboard-check me-2"></i>Rekap Absensi Guru
                                <span class="badge bg-info ms-2">Mode Lihat Saja</span>
                            </h1>
                            <p class="text-muted mb-0">Monitoring dan rekap kehadiran guru</p>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ route('kepala-sekolah.dashboard') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-filter me-2"></i>Filter Periode
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('kepala-sekolah.absensi.guru.index') }}" method="GET" class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Periode</label>
                            <select name="filter" class="form-select" id="filterPeriode">
                                <option value="hari-ini" {{ $filter == 'hari-ini' ? 'selected' : '' }}>Hari Ini</option>
                                <option value="minggu-ini" {{ $filter == 'minggu-ini' ? 'selected' : '' }}>Minggu Ini</option>
                                <option value="bulan-ini" {{ $filter == 'bulan-ini' ? 'selected' : '' }}>Bulan Ini</option>
                                <option value="semester-ini" {{ $filter == 'semester-ini' ? 'selected' : '' }}>Semester Ini</option>
                                <option value="custom" {{ $filter == 'custom' ? 'selected' : '' }}>Custom</option>
                            </select>
                        </div>

                        <div class="col-md-4" id="customDateRange" style="display: {{ $filter == 'custom' ? 'block' : 'none' }};">
                            <label class="form-label">Tanggal Mulai</label>
                            <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                        </div>

                        <div class="col-md-4" id="customDateRange2" style="display: {{ $filter == 'custom' ? 'block' : 'none' }};">
                            <label class="form-label">Tanggal Selesai</label>
                            <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                        </div>

                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-1"></i>Tampilkan
                            </button>
                            <a href="{{ route('kepala-sekolah.absensi.guru.index') }}" class="btn btn-secondary">
                                <i class="fas fa-redo me-1"></i>Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Periode -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Periode:</strong> {{ $dateRange['label'] }}
                @if($activeSemester)
                | <strong>Semester Aktif:</strong> {{ $activeSemester->nama }}
                @endif
            </div>
        </div>
    </div>

    <!-- Rekap Table -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-list me-2"></i>Rekap Kehadiran Guru
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="22%">Nama Guru</th>
                                    <th width="10%">Mata Pelajaran</th>
                                    <th width="8%" class="text-center">Hadir</th>
                                    <th width="8%" class="text-center">Sakit</th>
                                    <th width="8%" class="text-center">Izin</th>
                                    <th width="8%" class="text-center">Alpha</th>
                                    <th width="8%" class="text-center">Terlambat</th>
                                    <th width="13%" class="text-center">Persentase</th>
                                    <th width="10%" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rekap as $index => $guru)
                                @php
                                    $totalAbsensi = $guru->total_hadir + $guru->total_sakit + $guru->total_izin + $guru->total_alpha + $guru->total_terlambat;
                                    $persentaseHadir = $totalAbsensi > 0 ? round(($guru->total_hadir / $totalAbsensi) * 100, 1) : 0;
                                @endphp
                                <tr>
                                    <td>{{ $rekap->firstItem() + $index }}</td>
                                    <td>
                                        <strong>{{ $guru->name }}</strong>
                                        <br><small class="text-muted">NIP: {{ $guru->guruProfile->nip ?? '-' }}</small>
                                    </td>
                                    <td>
                                        @if($guru->guruProfile && $guru->guruProfile->mataPelajaran)
                                        <span class="badge bg-info">
                                            {{ $guru->guruProfile->mataPelajaran->nama }}
                                        </span>
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-success">{{ $guru->total_hadir }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-warning">{{ $guru->total_sakit }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info">{{ $guru->total_izin }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-danger">{{ $guru->total_alpha }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary">{{ $guru->total_terlambat }}</span>
                                    </td>
                                    <td class="text-center">
                                        <div class="progress" style="height: 25px;">
                                            <div class="progress-bar {{ $persentaseHadir >= 80 ? 'bg-success' : ($persentaseHadir >= 60 ? 'bg-warning' : 'bg-danger') }}"
                                                 style="width: {{ $persentaseHadir }}%">
                                                {{ $persentaseHadir }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('kepala-sekolah.absensi.guru.detail', $guru->id) }}?filter={{ $filter }}&start_date={{ $startDate }}&end_date={{ $endDate }}"
                                           class="btn btn-sm btn-info"
                                           title="Lihat Detail">
                                            <i class="fas fa-eye"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center py-4">
                                        <i class="fas fa-inbox text-muted fs-1 mb-3 d-block"></i>
                                        <p class="text-muted mb-0">Tidak ada data absensi</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-light border-top-0 py-3">
                    <div class="pagination-container">
                        <div class="pagination-info">
                            Menampilkan {{ $rekap->firstItem() ?? 0 }}-{{ $rekap->lastItem() ?? 0 }} dari {{ $rekap->total() }} data
                        </div>
                        <div class="pagination-controls">
                            @if ($rekap->onFirstPage())
                                <button class="btn btn-pagination" disabled>
                                    <i class="fas fa-chevron-left"></i> Sebelumnya
                                </button>
                            @else
                                <a href="{{ $rekap->appends(['filter' => $filter, 'start_date' => $startDate, 'end_date' => $endDate])->previousPageUrl() }}" class="btn btn-pagination">
                                    <i class="fas fa-chevron-left"></i> Sebelumnya
                                </a>
                            @endif

                            <span class="pagination-current">{{ $rekap->currentPage() }}</span>

                            @if ($rekap->hasMorePages())
                                <a href="{{ $rekap->appends(['filter' => $filter, 'start_date' => $startDate, 'end_date' => $endDate])->nextPageUrl() }}" class="btn btn-pagination">
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

<script>
document.getElementById('filterPeriode').addEventListener('change', function() {
    const customFields = document.getElementById('customDateRange');
    const customFields2 = document.getElementById('customDateRange2');
    if (this.value === 'custom') {
        customFields.style.display = 'block';
        customFields2.style.display = 'block';
    } else {
        customFields.style.display = 'none';
        customFields2.style.display = 'none';
    }
});
</script>

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
</style>
@endsection
