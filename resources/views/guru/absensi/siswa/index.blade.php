@extends('layouts.app')

@section('title', 'Rekap Absensi Siswa')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h1 class="h3 mb-2">
                                <i class="fas fa-clipboard-check text-primary me-2"></i>Rekapan Absensi Siswa
                            </h1>
                            <p class="text-muted mb-0">Monitoring dan rekap kehadiran siswa di kelas yang Anda ajar</p>
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
                    <form action="{{ route('guru.rekap-absensi.siswa') }}" method="GET" class="row g-3" id="filterForm">
                        <div class="col-md-3">
                            <label class="form-label">Periode</label>
                            <select name="filter" class="form-select auto-submit" id="filterPeriode">
                                <option value="hari-ini" {{ $filter == 'hari-ini' ? 'selected' : '' }}>Hari Ini</option>
                                <option value="minggu-ini" {{ $filter == 'minggu-ini' ? 'selected' : '' }}>Minggu Ini</option>
                                <option value="bulan-ini" {{ $filter == 'bulan-ini' ? 'selected' : '' }}>Bulan Ini</option>
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
                            <a href="{{ route('guru.rekap-absensi.siswa') }}" class="btn btn-secondary">
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
                <strong>Periode:</strong> {{ $dateRange['start_formatted'] }} - {{ $dateRange['end_formatted'] }}
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
                    @if($rekap->isEmpty())
                        <div class="text-center py-5">
                            <img src="https://cdn-icons-png.flaticon.com/512/2706/2706962.png" alt="No Data" style="width: 150px; opacity: 0.5;">
                            <h5 class="mt-3 text-muted">Tidak ada data absensi</h5>
                            <p class="text-muted">Belum ada data absensi untuk periode yang dipilih</p>
                        </div>
                    @else
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
                                    @foreach($rekap as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <div>
                                                <strong>{{ $item['siswa']->name }}</strong><br>
                                                <small class="text-muted">NISN: {{ $item['siswa']->studentProfile->nisn ?? '-' }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                {{ $item['siswa']->studentProfile->kelas->tingkat ?? '' }}{{ $item['siswa']->studentProfile->kelas->nama_kelas ?? '' }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-success">{{ $item['summary']['hadir'] }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-warning">{{ $item['summary']['sakit'] }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-info">{{ $item['summary']['izin'] }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-danger">{{ $item['summary']['alpha'] }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary">{{ $item['summary']['terlambat'] }}</span>
                                        </td>
                                        <td class="text-center">
                                            @if($item['persentase_hadir'] >= 80)
                                                <span class="badge bg-success">{{ $item['persentase_hadir'] }}%</span>
                                            @elseif($item['persentase_hadir'] >= 60)
                                                <span class="badge bg-warning">{{ $item['persentase_hadir'] }}%</span>
                                            @else
                                                <span class="badge bg-danger">{{ $item['persentase_hadir'] }}%</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('guru.rekap-absensi.siswa.detail', $item['siswa']->id) }}?filter={{ $filter }}&start_date={{ $startDate }}&end_date={{ $endDate }}"
                                               class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye me-1"></i>Detail
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Auto-submit functionality
    $('.auto-submit').on('change', function() {
        $('#filterForm').submit();
    });

    $('.auto-submit-date').on('change', function() {
        if ($('#filterPeriode').val() === 'custom') {
            $('#filterForm').submit();
        }
    });

    // Show/hide custom date range
    $('#filterPeriode').on('change', function() {
        if ($(this).val() === 'custom') {
            $('#customDateRange, #customDateRange2').show();
        } else {
            $('#customDateRange, #customDateRange2').hide();
        }
    });
</script>
@endpush
@endsection
