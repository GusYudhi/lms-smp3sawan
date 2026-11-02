@extends('layouts.app')

@section('title', 'Rekap Absensi Siswa')

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
                                <i class="fas fa-user-check text-info me-2"></i>Rekap Absensi Siswa
                            </h1>
                            <p class="text-subtle mb-0 fw-medium">Monitoring dan rekapitulasi kehadiran siswa per kelas</p>
                        </div>
                        <div class="col-md-4 text-end">
                            <button type="button" class="btn btn-success me-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#exportModal">
                                <i class="fas fa-file-excel me-1"></i>Unduh Rekap
                            </button>
                            <a href="{{ route('kepala-sekolah.absensi.index') }}" class="btn btn-outline-secondary shadow-sm">
                                <i class="fas fa-arrow-left me-1"></i>Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Filter Toggle Button -->
    <div class="d-md-none mb-3">
        <button class="btn btn-outline-primary w-100 d-flex justify-content-between align-items-center shadow-sm" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapseAbsensiSiswa" aria-expanded="false">
            <span><i class="fas fa-filter me-2"></i>Filter Siswa & Periode</span>
            <i class="fas fa-chevron-down"></i>
        </button>
    </div>

    <!-- Filter Card -->
    <div class="collapse d-md-block mb-4" id="filterCollapseAbsensiSiswa">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom">
                <h5 class="card-title mb-0">
                    <i class="fas fa-filter me-2"></i>Filter Periode & Kelas
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('kepala-sekolah.absensi.siswa.index') }}" method="GET" class="row g-3" id="filterForm">
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
                        <a href="{{ route('kepala-sekolah.absensi.siswa.index') }}" class="btn btn-secondary">
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

    <!-- Info Periode -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-info shadow-sm mb-0">
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
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="card-title mb-0 fw-semibold text-high-contrast">
                        <i class="fas fa-list text-primary me-2"></i>Rekap Kehadiran Siswa
                    </h5>
                </div>
                <div class="card-body p-0">
                    <!-- Desktop Table View -->
                    <div class="table-responsive d-none d-md-block">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 5%;">No</th>
                                    <th style="width: 20%;">Nama Siswa</th>
                                    <th style="width: 10%;">Kelas</th>
                                    <th style="width: 8%;" class="text-center">Hadir</th>
                                    <th style="width: 8%;" class="text-center">Sakit</th>
                                    <th style="width: 8%;" class="text-center">Izin</th>
                                    <th style="width: 8%;" class="text-center">Alpha</th>
                                    <th style="width: 8%;" class="text-center">Terlambat</th>
                                    <th style="width: 15%;" class="text-center">Persentase</th>
                                    <th style="width: 10%;" class="text-center">Aksi</th>
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
                                        <strong class="text-body-emphasis">{{ $siswa->name }}</strong>
                                        <br><small class="text-muted">NISN: {{ $siswa->studentProfile->nisn ?? '-' }}</small>
                                    </td>
                                    <td>
                                        @if($siswa->studentProfile && $siswa->studentProfile->kelas)
                                        <span class="badge bg-primary-subtle text-primary border">
                                            {{ $siswa->studentProfile->kelas->tingkat }}{{ $siswa->studentProfile->kelas->nama_kelas }}
                                        </span>
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-success-subtle text-success border">{{ $siswa->total_hadir }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-warning-subtle text-warning border">{{ $siswa->total_sakit }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info-subtle text-info border">{{ $siswa->total_izin }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-danger-subtle text-danger border">{{ $siswa->total_alpha }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary-subtle text-secondary border">{{ $siswa->total_terlambat }}</span>
                                    </td>
                                    <td class="text-center">
                                        <div class="progress" style="height: 22px;">
                                            <div class="progress-bar {{ $persentaseHadir >= 80 ? 'bg-success' : ($persentaseHadir >= 60 ? 'bg-warning' : 'bg-danger') }}"
                                                 style="width: {{ $persentaseHadir }}%">
                                                {{ $persentaseHadir }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('kepala-sekolah.absensi.siswa.detail', $siswa->id) }}?filter={{ $filter }}&start_date={{ $startDate }}&end_date={{ $endDate }}"
                                           class="btn btn-sm btn-info text-white"
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

                    <!-- Mobile Adaptive Cards View -->
                    <div class="d-md-none p-3">
                        @forelse($rekap as $index => $siswa)
                            @php
                                $totalAbsensi = $siswa->total_hadir + $siswa->total_sakit + $siswa->total_izin + $siswa->total_alpha + $siswa->total_terlambat;
                                $persentaseHadir = $totalAbsensi > 0 ? round(($siswa->total_hadir / $totalAbsensi) * 100, 1) : 0;
                            @endphp
                            <div class="rekap-absensi-card-mobile">
                                <div class="rekap-person-header">
                                    <div>
                                        <h6 class="mb-0 fw-bold text-body-emphasis">{{ $siswa->name }}</h6>
                                        <small class="text-muted">NISN: {{ $siswa->studentProfile->nisn ?? '-' }}</small>
                                        @if($siswa->studentProfile && $siswa->studentProfile->kelas)
                                            <div class="mt-1">
                                                <span class="badge bg-primary-subtle text-primary border">
                                                    Kelas {{ $siswa->studentProfile->kelas->tingkat }}{{ $siswa->studentProfile->kelas->nama_kelas }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                    <span class="badge fs-6 {{ $persentaseHadir >= 80 ? 'bg-success' : ($persentaseHadir >= 60 ? 'bg-warning' : 'bg-danger') }}">
                                        {{ $persentaseHadir }}%
                                    </span>
                                </div>
                                <div class="progress mb-3" style="height: 8px;">
                                    <div class="progress-bar {{ $persentaseHadir >= 80 ? 'bg-success' : ($persentaseHadir >= 60 ? 'bg-warning' : 'bg-danger') }}"
                                         style="width: {{ $persentaseHadir }}%"></div>
                                </div>
                                <div class="rekap-attendance-grid">
                                    <div class="rekap-stat-box">
                                        <span class="rekap-label">Hadir</span>
                                        <span class="rekap-val text-success">{{ $siswa->total_hadir }}</span>
                                    </div>
                                    <div class="rekap-stat-box">
                                        <span class="rekap-label">Sakit</span>
                                        <span class="rekap-val text-warning">{{ $siswa->total_sakit }}</span>
                                    </div>
                                    <div class="rekap-stat-box">
                                        <span class="rekap-label">Izin</span>
                                        <span class="rekap-val text-info">{{ $siswa->total_izin }}</span>
                                    </div>
                                    <div class="rekap-stat-box">
                                        <span class="rekap-label">Alpha</span>
                                        <span class="rekap-val text-danger">{{ $siswa->total_alpha }}</span>
                                    </div>
                                    <div class="rekap-stat-box">
                                        <span class="rekap-label">Telat</span>
                                        <span class="rekap-val text-secondary">{{ $siswa->total_terlambat }}</span>
                                    </div>
                                </div>
                                <div class="rekap-footer">
                                    <small class="text-muted">Total: {{ $totalAbsensi }} Sesi</small>
                                    <a href="{{ route('kepala-sekolah.absensi.siswa.detail', $siswa->id) }}?filter={{ $filter }}&start_date={{ $startDate }}&end_date={{ $endDate }}"
                                       class="btn btn-sm btn-info text-white">
                                        <i class="fas fa-eye me-1"></i>Lihat Detail
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4 bg-white rounded-3 border">
                                <i class="fas fa-inbox text-muted fs-1 mb-3 d-block opacity-50"></i>
                                <p class="text-muted mb-0">Tidak ada data absensi</p>
                            </div>
                        @endforelse
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
            <form action="{{ route('kepala-sekolah.absensi.siswa.export') }}" method="POST" id="exportForm">
                @csrf
                <div class="modal-body">
                    <!-- Periode Selection -->
                    <div class="mb-3">
                        <label for="periode" class="form-label fw-bold">Pilih Periode</label>
                        <select class="form-select" id="periode" name="periode" required>
                            <option value="">-- Pilih Periode --</option>
                            @if($activeSemester && $activeSemester->semester_ke == 1)
                                <option value="7">Juli {{ date('Y') }}</option>
                                <option value="8">Agustus {{ date('Y') }}</option>
                                <option value="9">September {{ date('Y') }}</option>
                                <option value="10">Oktober {{ date('Y') }}</option>
                                <option value="11">November {{ date('Y') }}</option>
                                <option value="12">Desember {{ date('Y') }}</option>
                            @else
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
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('filterForm');
    const filterPeriode = document.getElementById('filterPeriode');

    document.querySelectorAll('.auto-submit').forEach(function(element) {
        element.addEventListener('change', function() {
            form.submit();
        });
    });

    document.querySelectorAll('.auto-submit-date').forEach(function(element) {
        element.addEventListener('change', function() {
            setTimeout(function() {
                form.submit();
            }, 300);
        });
    });

    if (filterPeriode) {
        filterPeriode.addEventListener('change', function() {
            const customDateRange = document.getElementById('customDateRange');
            const customDateRange2 = document.getElementById('customDateRange2');

            if (this.value === 'custom') {
                if (customDateRange) customDateRange.style.display = 'block';
                if (customDateRange2) customDateRange2.style.display = 'block';
            } else {
                if (customDateRange) customDateRange.style.display = 'none';
                if (customDateRange2) customDateRange2.style.display = 'none';
            }
        });
    }
});

const exportForm = document.getElementById('exportForm');
if (exportForm) {
    exportForm.addEventListener('submit', function(e) {
        const checkboxes = document.querySelectorAll('input[name="tingkat_kelas[]"]:checked');
        const kelasError = document.getElementById('kelasError');

        if (checkboxes.length === 0) {
            e.preventDefault();
            if (kelasError) kelasError.style.display = 'block';
            return false;
        }

        if (kelasError) kelasError.style.display = 'none';
        return true;
    });
}

document.querySelectorAll('input[name="tingkat_kelas[]"]').forEach(function(checkbox) {
    checkbox.addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('input[name="tingkat_kelas[]"]:checked');
        const kelasError = document.getElementById('kelasError');
        if (checkboxes.length > 0 && kelasError) {
            kelasError.style.display = 'none';
        }
    });
});
</script>
@endsection
