@extends('layouts.app')

@section('title', 'Agenda Guru')

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
                                <i class="fas fa-calendar-check text-success me-2"></i>Agenda Guru
                            </h1>
                            <p class="text-subtle mb-0 fw-medium">Lihat agenda dari semua guru</p>
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


    <!-- Filter Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form action="{{ route('kepala-sekolah.agenda-guru.index') }}" method="GET" class="row g-3" id="filterForm">
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
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select auto-submit">
                                <option value="">Semua Status</option>
                                <option value="pending" {{ $statusFilter == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="selesai" {{ $statusFilter == 'selesai' ? 'selected' : '' }}>Selesai</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Urutkan Menurut</label>
                            <select name="sort_by" class="form-select auto-submit">
                                <option value="tanggal_desc" {{ request('sort_by', 'tanggal_desc') == 'tanggal_desc' ? 'selected' : '' }}>Tanggal Terbaru</option>
                                <option value="tanggal_asc" {{ request('sort_by') == 'tanggal_asc' ? 'selected' : '' }}>Tanggal Terlama</option>
                                <option value="guru_asc" {{ request('sort_by') == 'guru_asc' ? 'selected' : '' }}>Guru (A-Z)</option>
                                <option value="guru_desc" {{ request('sort_by') == 'guru_desc' ? 'selected' : '' }}>Guru (Z-A)</option>
                            </select>
                        </div>

                        <div class="col-md-12">
                            <a href="{{ route('kepala-sekolah.agenda-guru.index') }}" class="btn btn-secondary">
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

    <!-- Agenda List -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    @if($agendaList->isEmpty())
                        <div class="text-center py-5">
                            <img src="https://cdn-icons-png.flaticon.com/512/2706/2706962.png" alt="No Data" style="width: 150px; opacity: 0.5;">
                            <h5 class="mt-3 text-muted">Tidak ada agenda</h5>
                            <p class="text-muted">Belum ada agenda yang dibuat untuk periode ini</p>
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
                                        <th>Waktu</th>
                                        <th>Materi</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($agendaList as $agenda)
                                    <tr>
                                        <td>{{ $agendaList->firstItem() + $loop->index }}</td>
                                        <td>
                                            <strong>{{ $agenda->tanggal->format('d M Y') }}</strong><br>
                                            <small class="text-muted">{{ $agenda->tanggal->format('l') }}</small>
                                        </td>
                                        <td>
                                            <strong>{{ $agenda->user->name }}</strong><br>
                                            <small class="text-muted">{{ $agenda->user->guruProfile->nip ?? '-' }}</small>
                                        </td>
                                        <td><span class="badge bg-info">{{ $agenda->kelas }}</span></td>
                                        <td>
                                            @if($agenda->jamMulai && $agenda->jamSelesai)
                                                <small>{{ \Carbon\Carbon::parse($agenda->jamMulai->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($agenda->jamSelesai->jam_selesai)->format('H:i') }}</small>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ \Str::limit($agenda->materi, 50) }}</td>
                                        <td>
                                            @if($agenda->status_jurnal == 'selesai')
                                                <span class="badge bg-success">Selesai</span>
                                            @else
                                                <span class="badge bg-warning">Pending</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('kepala-sekolah.agenda-guru.show', $agenda->id) }}" class="btn btn-sm btn-info">
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
                            {{ $agendaList->links() }}
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
