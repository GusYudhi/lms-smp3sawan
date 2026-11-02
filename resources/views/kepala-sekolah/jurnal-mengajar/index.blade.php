@extends('layouts.app')

@section('title', 'Jurnal Mengajar')

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
                                <i class="fas fa-book-open text-info me-2"></i>Jurnal Mengajar
                            </h1>
                            <p class="text-subtle mb-0 fw-medium">Lihat jurnal mengajar dari semua guru</p>
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

    <!-- Mobile Filter Toggle Button -->
    <div class="d-md-none mb-3">
        <button class="btn btn-outline-primary w-100 d-flex justify-content-between align-items-center shadow-sm" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapseJurnalGuru" aria-expanded="false">
            <span><i class="fas fa-filter me-2"></i>Filter Jurnal Mengajar</span>
            <i class="fas fa-chevron-down"></i>
        </button>
    </div>

    <!-- Filter Section -->
    <div class="collapse d-md-block mb-4" id="filterCollapseJurnalGuru">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form action="{{ route('kepala-sekolah.jurnal-mengajar.index') }}" method="GET" class="row g-3" id="filterForm">
                    <div class="col-md-3">
                        <label class="form-label">Periode</label>
                        <select name="filter" class="form-select auto-submit" id="filterPeriode">
                            <option value="hari-ini" {{ $filterPeriode == 'hari-ini' ? 'selected' : '' }}>Hari Ini</option>
                            <option value="minggu-ini" {{ $filterPeriode == 'minggu-ini' ? 'selected' : '' }}>Minggu Ini</option>
                            <option value="bulan-ini" {{ $filterPeriode == 'bulan-ini' ? 'selected' : '' }}>Bulan Ini</option>
                            <option value="custom" {{ $filterPeriode == 'custom' ? 'selected' : '' }}>Custom</option>
                        </select>
                    </div>

                    <div class="col-md-2" id="customDateRange" style="display: {{ $filterPeriode == 'custom' ? 'block' : 'none' }};">
                        <label class="form-label">Tanggal Mulai</label>
                        <input type="date" name="start_date" class="form-control auto-submit-date" value="{{ $startDate }}">
                    </div>

                    <div class="col-md-2" id="customDateRange2" style="display: {{ $filterPeriode == 'custom' ? 'block' : 'none' }};">
                        <label class="form-label">Tanggal Selesai</label>
                        <input type="date" name="end_date" class="form-control auto-submit-date" value="{{ $endDate }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Guru</label>
                        <select name="guru_id" class="form-select auto-submit">
                            <option value="">Semua Guru</option>
                            @foreach($guruList as $guru)
                            <option value="{{ $guru->id }}" {{ $guruFilter == $guru->id ? 'selected' : '' }}>
                                {{ $guru->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Kelas</label>
                        <select name="kelas_id" class="form-select auto-submit">
                            <option value="">Semua Kelas</option>
                            @foreach($kelasList as $kelas)
                            <option value="{{ $kelas->id }}" {{ $kelasFilter == $kelas->id ? 'selected' : '' }}>
                                {{ $kelas->tingkat }}{{ $kelas->nama_kelas }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Mata Pelajaran</label>
                        <select name="mata_pelajaran_id" class="form-select auto-submit">
                            <option value="">Semua Mata Pelajaran</option>
                            @foreach($mapelList as $mapel)
                            <option value="{{ $mapel->id }}" {{ $mapelFilter == $mapel->id ? 'selected' : '' }}>
                                {{ $mapel->nama_mapel }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-9 d-flex align-items-end">
                        <a href="{{ route('kepala-sekolah.jurnal-mengajar.index') }}" class="btn btn-secondary">
                            <i class="fas fa-redo me-1"></i>Reset
                        </a>
                        <small class="text-muted ms-3">
                            <i class="fas fa-info-circle me-1"></i>Filter akan diterapkan otomatis
                        </small>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Jurnal List -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    @if($jurnalList->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-book fa-3x text-muted mb-3 d-block opacity-50"></i>
                            <h5 class="mt-3 text-muted">Tidak ada jurnal mengajar</h5>
                            <p class="text-muted">Belum ada jurnal yang dibuat untuk periode ini</p>
                        </div>
                    @else
                        <!-- Desktop Table View -->
                        <div class="table-responsive d-none d-md-block">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 5%;">No</th>
                                        <th>Tanggal</th>
                                        <th>Guru</th>
                                        <th>Kelas</th>
                                        <th>Mata Pelajaran</th>
                                        <th class="text-center">Jam Ke</th>
                                        <th>Materi</th>
                                        <th class="text-center" style="width: 80px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($jurnalList as $jurnal)
                                    <tr>
                                        <td>{{ $jurnalList->firstItem() + $loop->index }}</td>
                                        <td>
                                            <span class="badge bg-primary-subtle text-primary border">{{ $jurnal->hari }}</span><br>
                                            <small class="text-muted">{{ $jurnal->tanggal->format('d M Y') }}</small>
                                        </td>
                                        <td>
                                            <strong class="text-body-emphasis">{{ $jurnal->guru->name }}</strong><br>
                                            <small class="text-muted">{{ $jurnal->guru->guruProfile->nip ?? '-' }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-info-subtle text-info border">{{ $jurnal->kelas->tingkat }}{{ $jurnal->kelas->nama_kelas }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary-subtle text-secondary border">{{ $jurnal->mataPelajaran->nama_mapel }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-light text-dark border">
                                                {{ $jurnal->jam_ke_mulai }}@if($jurnal->jam_ke_selesai != $jurnal->jam_ke_mulai) - {{ $jurnal->jam_ke_selesai }}@endif
                                            </span>
                                        </td>
                                        <td>{{ \Str::limit($jurnal->materi_pembelajaran, 50) }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('kepala-sekolah.jurnal-mengajar.show', $jurnal->id) }}" class="btn btn-sm btn-info text-white" title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Mobile Adaptive Cards View -->
                        <div class="d-md-none mb-3">
                            @foreach($jurnalList as $jurnal)
                                <div class="kepsek-supervisi-card status-selesai">
                                    <div class="supervisi-header">
                                        <div>
                                            <h6 class="mb-0 fw-bold text-body-emphasis">{{ $jurnal->guru->name }}</h6>
                                            <small class="text-muted">NIP: {{ $jurnal->guru->guruProfile->nip ?? '-' }}</small>
                                        </div>
                                        <span class="badge bg-primary-subtle text-primary border">
                                            {{ $jurnal->mataPelajaran->nama_mapel }}
                                        </span>
                                    </div>
                                    <div class="supervisi-meta-badges">
                                        <span class="badge bg-light text-dark border">
                                            <i class="fas fa-calendar-day me-1"></i>{{ $jurnal->hari }}, {{ $jurnal->tanggal->format('d M Y') }}
                                        </span>
                                        <span class="badge bg-info-subtle text-info border">
                                            <i class="fas fa-door-open me-1"></i>Kelas {{ $jurnal->kelas->tingkat }}{{ $jurnal->kelas->nama_kelas }}
                                        </span>
                                        <span class="badge bg-secondary-subtle text-secondary border">
                                            <i class="fas fa-clock me-1"></i>Jam Ke: {{ $jurnal->jam_ke_mulai }}@if($jurnal->jam_ke_selesai != $jurnal->jam_ke_mulai)-{{ $jurnal->jam_ke_selesai }}@endif
                                        </span>
                                    </div>
                                    <div class="supervisi-content">
                                        <div class="small fw-semibold text-muted mb-1">Materi Pembelajaran:</div>
                                        {{ $jurnal->materi_pembelajaran }}
                                    </div>
                                    <div class="supervisi-footer">
                                        <span class="badge bg-success-subtle text-success border">
                                            <i class="fas fa-check-circle me-1"></i>Tersimpan
                                        </span>
                                        <a href="{{ route('kepala-sekolah.jurnal-mengajar.show', $jurnal->id) }}" class="btn btn-sm btn-info text-white">
                                            <i class="fas fa-eye me-1"></i>Detail Jurnal
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $jurnalList->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.getElementById('filterForm');
    const filterPeriode = document.getElementById('filterPeriode');
    const customDateRange = document.getElementById('customDateRange');
    const customDateRange2 = document.getElementById('customDateRange2');
    const autoSubmitElements = document.querySelectorAll('.auto-submit');
    const autoSubmitDateElements = document.querySelectorAll('.auto-submit-date');

    // Toggle custom date range
    filterPeriode.addEventListener('change', function() {
        if (this.value === 'custom') {
            customDateRange.style.display = 'block';
            customDateRange2.style.display = 'block';
        } else {
            customDateRange.style.display = 'none';
            customDateRange2.style.display = 'none';
            filterForm.submit();
        }
    });

    // Auto-submit untuk dropdown
    autoSubmitElements.forEach(element => {
        element.addEventListener('change', function() {
            if (element.id !== 'filterPeriode') {
                filterForm.submit();
            }
        });
    });

    // Auto-submit untuk date input dengan debounce
    let dateTimeout;
    autoSubmitDateElements.forEach(element => {
        element.addEventListener('change', function() {
            clearTimeout(dateTimeout);
            dateTimeout = setTimeout(() => {
                filterForm.submit();
            }, 300);
        });
    });
});
</script>
@endsection
