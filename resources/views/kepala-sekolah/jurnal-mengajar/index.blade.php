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

    <!-- Statistik -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card card-stats text-center h-100">
                <div class="card-body">
                    <div class="text-info fs-1 mb-3">
                        <i class="fas fa-book"></i>
                    </div>
                    <h6 class="card-title text-muted">Total Jurnal</h6>
                    <h2 class="text-info">{{ $jurnalList->total() }}</h2>
                    <small class="text-muted">Semua Periode</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="row mb-4">
        <div class="col-12">
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
                                    {{ $mapel->nama_mata_pelajaran }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-9">
                            <label class="form-label">&nbsp;</label><br>
                            <a href="{{ route('kepala-sekolah.jurnal-mengajar.index') }}" class="btn btn-secondary">
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

    <!-- Jurnal List -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    @if($jurnalList->isEmpty())
                        <div class="text-center py-5">
                            <img src="https://cdn-icons-png.flaticon.com/512/2748/2748558.png" alt="No Data" style="width: 150px; opacity: 0.5;">
                            <h5 class="mt-3 text-muted">Tidak ada jurnal mengajar</h5>
                            <p class="text-muted">Belum ada jurnal yang dibuat untuk periode ini</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Guru</th>
                                        <th>Kelas</th>
                                        <th>Mata Pelajaran</th>
                                        <th>Jam Ke</th>
                                        <th>Materi</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($jurnalList as $jurnal)
                                    <tr>
                                        <td>{{ $jurnalList->firstItem() + $loop->index }}</td>
                                        <td>
                                            <span class="badge bg-primary">{{ $jurnal->hari }}</span><br>
                                            <small>{{ $jurnal->tanggal->format('d M Y') }}</small>
                                        </td>
                                        <td>
                                            <strong>{{ $jurnal->guru->name }}</strong><br>
                                            <small class="text-muted">{{ $jurnal->guru->guruProfile->nip ?? '-' }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $jurnal->kelas->tingkat }}{{ $jurnal->kelas->nama_kelas }}</span>
                                        </td>
                                        <td>{{ $jurnal->mataPelajaran->nama_mata_pelajaran }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary">{{ $jurnal->jam_ke_mulai }}</span>
                                            @if($jurnal->jam_ke_selesai != $jurnal->jam_ke_mulai)
                                            - <span class="badge bg-secondary">{{ $jurnal->jam_ke_selesai }}</span>
                                            @endif
                                        </td>
                                        <td>{{ \Str::limit($jurnal->materi_pembelajaran, 50) }}</td>
                                        <td>
                                            <a href="{{ route('kepala-sekolah.jurnal-mengajar.show', $jurnal->id) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
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

<style>
.card-stats {
    transition: all 0.3s ease;
}

.card-stats:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    transform: translateY(-5px);
}
</style>
@endsection
