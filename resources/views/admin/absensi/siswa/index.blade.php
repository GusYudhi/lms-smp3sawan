@extends('layouts.app')

@section('title', 'Rekap Absensi Siswa')

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
                                <i class="fas fa-clipboard-check me-2"></i>Rekap Absensi Siswa
                            </h1>
                            <p class="text-muted mb-0">Monitoring dan rekap kehadiran siswa</p>
                        </div>
                        <div class="col-md-4 text-end">
                            <button type="button" class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#exportModal">
                                <i class="fas fa-file-excel me-1"></i>Unduh Rekap Absensi
                            </button>
                            <a href="{{ route('admin.absensi.index') }}" class="btn btn-outline-secondary">
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
                    <form action="{{ route('admin.absensi.siswa.index') }}" method="GET" class="row g-3" id="filterForm">
                        <div class="col-md-3">
                            <label class="form-label">Periode</label>
                            <select name="filter" class="form-select auto-submit" id="filterPeriode">
                                <option value="hari-ini" {{ $filter == 'hari-ini' ? 'selected' : '' }}>Hari Ini</option>
                                <option value="minggu-ini" {{ $filter == 'minggu-ini' ? 'selected' : '' }}>Minggu Ini</option>
                                <option value="bulan-ini" {{ $filter == 'bulan-ini' ? 'selected' : '' }}>Bulan Ini</option>
                                <option value="semester-ini" {{ $filter == 'semester-ini' ? 'selected' : '' }}>Semester Ini</option>
                                <option value="custom" {{ $filter == 'custom' ? 'selected' : '' }}>Custom</option>
                            </select>
                        </div>

                        <div class="col-md-3" id="customDateRange" style="display: {{ $filter == 'custom' ? 'block' : 'none' }};">
                            <label class="form-label">Tanggal Mulai</label>
                            <input type="date" name="start_date" class="form-control auto-submit-date" value="{{ $startDate }}">
                        </div>

                        <div class="col-md-3" id="customDateRange2" style="display: {{ $filter == 'custom' ? 'block' : 'none' }};">
                            <label class="form-label">Tanggal Selesai</label>
                            <input type="date" name="end_date" class="form-control auto-submit-date" value="{{ $endDate }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Cari Nama Siswa</label>
                            <div class="input-group">
                                <input type="text" name="search" class="form-control auto-submit" value="{{ $search ?? '' }}" placeholder="Cari nama...">
                                <button class="btn btn-outline-secondary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Filter Kelas</label>
                            <select name="kelas_id" class="form-select auto-submit">
                                <option value="">Semua Kelas</option>
                                @foreach($kelasList as $kelas)
                                <option value="{{ $kelas->id }}" {{ $kelasId == $kelas->id ? 'selected' : '' }}>
                                    {{ $kelas->tingkat }}{{ $kelas->nama_kelas }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Filter Status</label>
                            <select name="status_filter" class="form-select auto-submit">
                                <option value="">Semua Status</option>
                                <option value="hadir" {{ ($statusFilter ?? '') == 'hadir' ? 'selected' : '' }}>Hadir</option>
                                <option value="sakit" {{ ($statusFilter ?? '') == 'sakit' ? 'selected' : '' }}>Sakit</option>
                                <option value="izin" {{ ($statusFilter ?? '') == 'izin' ? 'selected' : '' }}>Izin</option>
                                <option value="alpha" {{ ($statusFilter ?? '') == 'alpha' ? 'selected' : '' }}>Alpha</option>
                                <option value="terlambat" {{ ($statusFilter ?? '') == 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                            </select>
                        </div>

                        <div class="col-md-12">
                            <a href="{{ route('admin.absensi.siswa.index') }}" class="btn btn-secondary">
                                <i class="fas fa-redo me-1"></i>Reset
                            </a>
                            <small class="text-muted ms-2">
                                <i class="fas fa-info-circle me-1"></i>Filter akan diterapkan otomatis
                            </small>
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
                        <i class="fas fa-list me-2"></i>Rekap Kehadiran Siswa
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="20%">Nama Siswa</th>
                                    <th width="10%">Kelas</th>
                                    <th width="8%" class="text-center">Hadir</th>
                                    <th width="8%" class="text-center">Sakit</th>
                                    <th width="8%" class="text-center">Izin</th>
                                    <th width="8%" class="text-center">Alpha</th>
                                    <th width="8%" class="text-center">Terlambat</th>
                                    <th width="10%" class="text-center">Persentase</th>
                                    <th width="10%" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rekap as $index => $siswa)
                                @php
                                    $totalAbsensi = $siswa->total_hadir + $siswa->total_sakit + $siswa->total_izin + $siswa->total_alpha + $siswa->total_terlambat;
                                    $persentaseHadir = $totalAbsensi > 0 ? round(($siswa->total_hadir / $totalAbsensi) * 100, 1) : 0;
                                @endphp
                                <tr>
                                    <td>{{ $rekap->firstItem() + $index }}</td>
                                    <td>
                                        <strong>{{ $siswa->name }}</strong>
                                        <br><small class="text-muted">NISN: {{ $siswa->studentProfile->nisn ?? '-' }}</small>
                                    </td>
                                    <td>
                                        @if($siswa->studentProfile && $siswa->studentProfile->kelas)
                                        <span class="badge bg-primary">
                                            {{ $siswa->studentProfile->kelas->tingkat }}{{ $siswa->studentProfile->kelas->nama_kelas }}
                                        </span>
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-success">{{ $siswa->total_hadir }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-warning">{{ $siswa->total_sakit }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info">{{ $siswa->total_izin }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-danger">{{ $siswa->total_alpha }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary">{{ $siswa->total_terlambat }}</span>
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
                                        <a href="{{ route('admin.absensi.siswa.detail', $siswa->id) }}?filter={{ $filter }}&start_date={{ $startDate }}&end_date={{ $endDate }}"
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
                                <a href="{{ $rekap->appends(['filter' => $filter, 'kelas_id' => $kelasId, 'start_date' => $startDate, 'end_date' => $endDate])->previousPageUrl() }}" class="btn btn-pagination">
                                    <i class="fas fa-chevron-left"></i> Sebelumnya
                                </a>
                            @endif

                            <span class="pagination-current">{{ $rekap->currentPage() }}</span>

                            @if ($rekap->hasMorePages())
                                <a href="{{ $rekap->appends(['filter' => $filter, 'kelas_id' => $kelasId, 'start_date' => $startDate, 'end_date' => $endDate])->nextPageUrl() }}" class="btn btn-pagination">
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

<!-- Modal Export Rekap Absensi -->
<div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="exportModalLabel">
                    <i class="fas fa-file-excel me-2"></i>Unduh Rekap Absensi Siswa
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.absensi.siswa.export') }}" method="POST" id="exportForm">
                @csrf
                <div class="modal-body">
                    <!-- Periode Selection -->
                    <div class="mb-3">
                        <label for="periode" class="form-label fw-bold">Pilih Periode</label>
                        <select class="form-select" id="periode" name="periode" required>
                            <option value="">-- Pilih Periode --</option>
                            @if($activeSemester && $activeSemester->semester_ke == 1)
                                <!-- Semester Ganjil: Juli - Desember -->
                                <option value="7">Juli {{ date('Y') }}</option>
                                <option value="8">Agustus {{ date('Y') }}</option>
                                <option value="9">September {{ date('Y') }}</option>
                                <option value="10">Oktober {{ date('Y') }}</option>
                                <option value="11">November {{ date('Y') }}</option>
                                <option value="12">Desember {{ date('Y') }}</option>
                            @else
                                <!-- Semester Genap: Januari - Juni -->
                                <option value="1">Januari {{ date('Y') + 1 }}</option>
                                <option value="2">Februari {{ date('Y') + 1 }}</option>
                                <option value="3">Maret {{ date('Y') + 1 }}</option>
                                <option value="4">April {{ date('Y') + 1 }}</option>
                                <option value="5">Mei {{ date('Y') + 1 }}</option>
                                <option value="6">Juni {{ date('Y') + 1 }}</option>
                            @endif
                            <option value="semester">Selama 1 Semester</option>
                        </select>
                    </div>

                    <!-- Tingkat Kelas Selection -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Pilih Tingkat Kelas</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="tingkat_kelas[]" value="7" id="kelas7">
                            <label class="form-check-label" for="kelas7">
                                Kelas 7
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="tingkat_kelas[]" value="8" id="kelas8">
                            <label class="form-check-label" for="kelas8">
                                Kelas 8
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="tingkat_kelas[]" value="9" id="kelas9">
                            <label class="form-check-label" for="kelas9">
                                Kelas 9
                            </label>
                        </div>
                        <small class="text-muted">*Pilih minimal 1 tingkat kelas</small>
                        <div id="kelasError" class="text-danger mt-1" style="display: none;">
                            Silakan pilih minimal 1 tingkat kelas
                        </div>
                    </div>

                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        <small>File akan diunduh dalam format Excel (.xlsx) dengan sheet terpisah untuk setiap kelas.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-download me-1"></i>Unduh
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Auto-submit form when filter changes
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('filterForm');
    const filterPeriode = document.getElementById('filterPeriode');

    // Auto-submit for select dropdowns
    document.querySelectorAll('.auto-submit').forEach(function(element) {
        element.addEventListener('change', function() {
            form.submit();
        });
    });

    // Auto-submit for date inputs (with small delay)
    document.querySelectorAll('.auto-submit-date').forEach(function(element) {
        element.addEventListener('change', function() {
            setTimeout(function() {
                form.submit();
            }, 300);
        });
    });

    // Show/hide custom date range
    filterPeriode.addEventListener('change', function() {
        const customDateRange = document.getElementById('customDateRange');
        const customDateRange2 = document.getElementById('customDateRange2');

        if (this.value === 'custom') {
            customDateRange.style.display = 'block';
            customDateRange2.style.display = 'block';
        } else {
            customDateRange.style.display = 'none';
            customDateRange2.style.display = 'none';
        }
    });
});

// Export modal validation
document.getElementById('exportForm').addEventListener('submit', function(e) {
    // Check if at least one checkbox is selected
    const checkboxes = document.querySelectorAll('input[name="tingkat_kelas[]"]:checked');
    const kelasError = document.getElementById('kelasError');

    if (checkboxes.length === 0) {
        e.preventDefault();
        kelasError.style.display = 'block';
        return false;
    }

    kelasError.style.display = 'none';
    return true;
});

// Hide error when checkbox is clicked
document.querySelectorAll('input[name="tingkat_kelas[]"]').forEach(function(checkbox) {
    checkbox.addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('input[name="tingkat_kelas[]"]:checked');
        if (checkboxes.length > 0) {
            document.getElementById('kelasError').style.display = 'none';
        }
    });
});
</script>
</style>
@endsection
