@extends('layouts.app')

@section('title', 'Detail Absensi Siswa')

@section('content')
<div class="container-fluid">
    <!-- Back Button -->
    <div class="row mb-4">
        <div class="col-12">
            <a href="{{ route('guru.rekap-absensi.siswa') }}?filter={{ $filter }}&start_date={{ $startDate }}&end_date={{ $endDate }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Kembali ke Rekap
            </a>
        </div>
    </div>

    <!-- Student Info Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h4 class="mb-3"><i class="fas fa-user me-2"></i>{{ $siswa->name }}</h4>
                            <table class="table table-borderless">
                                <tr>
                                    <td width="150">NISN</td>
                                    <td>: {{ $siswa->studentProfile->nisn ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td>Kelas</td>
                                    <td>: {{ $siswa->studentProfile->kelas->tingkat ?? '' }}{{ $siswa->studentProfile->kelas->nama_kelas ?? '' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5 class="mb-3">Ringkasan Kehadiran</h5>
                            <div class="row text-center">
                                <div class="col">
                                    <h3 class="text-success">{{ $summary['hadir'] }}</h3>
                                    <small>Hadir</small>
                                </div>
                                <div class="col">
                                    <h3 class="text-warning">{{ $summary['sakit'] }}</h3>
                                    <small>Sakit</small>
                                </div>
                                <div class="col">
                                    <h3 class="text-info">{{ $summary['izin'] }}</h3>
                                    <small>Izin</small>
                                </div>
                                <div class="col">
                                    <h3 class="text-danger">{{ $summary['alpha'] }}</h3>
                                    <small>Alpha</small>
                                </div>
                                <div class="col">
                                    <h3 class="text-secondary">{{ $summary['terlambat'] }}</h3>
                                    <small>Terlambat</small>
                                </div>
                            </div>
                            <div class="mt-3 text-center">
                                <h4>Persentase Kehadiran:
                                    @if($persentaseHadir >= 80)
                                        <span class="text-success">{{ $persentaseHadir }}%</span>
                                    @elseif($persentaseHadir >= 60)
                                        <span class="text-warning">{{ $persentaseHadir }}%</span>
                                    @else
                                        <span class="text-danger">{{ $persentaseHadir }}%</span>
                                    @endif
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance History -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-history me-2"></i>Riwayat Absensi</h5>
                    <small class="text-muted">Periode: {{ $dateRange['start_formatted'] }} - {{ $dateRange['end_formatted'] }}</small>
                </div>
                <div class="card-body">
                    @if($absensi->isEmpty())
                        <div class="text-center py-5">
                            <img src="https://cdn-icons-png.flaticon.com/512/2706/2706962.png" alt="No Data" style="width: 150px; opacity: 0.5;">
                            <h5 class="mt-3 text-muted">Tidak ada data absensi</h5>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Hari</th>
                                        <th>Waktu</th>
                                        <th>Status</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($absensi as $item)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($item->date)->locale('id')->isoFormat('D MMMM YYYY') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($item->date)->locale('id')->dayName }}</td>
                                        <td>{{ $item->time ?? '-' }}</td>
                                        <td>
                                            @if($item->status == 'hadir')
                                                <span class="badge bg-success">Hadir</span>
                                            @elseif($item->status == 'sakit')
                                                <span class="badge bg-warning">Sakit</span>
                                            @elseif($item->status == 'izin')
                                                <span class="badge bg-info">Izin</span>
                                            @elseif($item->status == 'alpha')
                                                <span class="badge bg-danger">Alpha</span>
                                            @elseif($item->status == 'terlambat')
                                                <span class="badge bg-secondary">Terlambat</span>
                                            @endif
                                        </td>
                                        <td>{{ $item->note ?? '-' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-3">
                            {{ $absensi->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
