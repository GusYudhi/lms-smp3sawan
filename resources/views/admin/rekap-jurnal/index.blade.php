@extends('layouts.app')

@section('title', 'Rekapitulasi Jurnal & Absensi')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-2 text-gray-800">Rekapitulasi Jurnal & Absensi</h1>
            <p class="mb-4">Pantau kehadiran siswa berdasarkan Jurnal Mengajar Guru secara real-time.</p>
        </div>
    </div>

    <!-- 1. Stats Cards (Top) -->
    <div class="row mb-4">
        <!-- Total Pertemuan -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Pertemuan</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_pertemuan'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rata-rata Hadir -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Rata-rata Hadir</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['avg_hadir'] ?? 0 }}%</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Izin/Sakit -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total Izin/Sakit</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_izin_sakit'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clinic-medical fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Alpha -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Total Alpha</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_alpha'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-times fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Filter Card (Middle) -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-white d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-filter me-2"></i>Filter Data</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('rekap-jurnal.index') }}" method="GET" id="filterForm">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Kelas</label>
                        <select name="kelas_id" class="form-select auto-submit" required>
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($kelas as $k)
                                <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>
                                    {{ $k->full_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Mata Pelajaran</label>
                        <select name="mapel_id" class="form-select auto-submit">
                            <option value="all">-- Semua Mapel --</option>
                            @foreach($mapels as $m)
                                <option value="{{ $m->id }}" {{ request('mapel_id') == $m->id ? 'selected' : '' }}>
                                    {{ $m->nama_mapel }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Tanggal Mulai</label>
                        <input type="date" name="start_date" class="form-control auto-submit" value="{{ request('start_date', date('Y-m-01')) }}" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Tanggal Selesai</label>
                        <input type="date" name="end_date" class="form-control auto-submit" value="{{ request('end_date', date('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100 me-2"><i class="fas fa-search"></i> Filter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- 3. Matrix Table (Bottom) -->
    @if($isFiltered)
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-white">
                <h6 class="m-0 font-weight-bold text-primary">Data Absensi Siswa</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover table-sm" width="100%" cellspacing="0">
                        <thead class="thead-light text-center align-middle">
                            <tr>
                                <th rowspan="2" style="width: 50px;">No</th>
                                <th rowspan="2" style="min-width: 200px; text-align: left;">Nama Siswa</th>
                                <th colspan="{{ count($headers) }}">Pertemuan (Tanggal & Mapel)</th>
                                <th colspan="4" style="min-width: 150px;">Total</th>
                            </tr>
                            <tr>
                                @foreach($headers as $key => $header)
                                    <th style="min-width: 80px;" title="{{ $header['mapel'] }} - {{ $header['materi'] }}">
                                        {{ $header['date'] }}
                                        <div class="small text-muted" style="font-size: 0.7em;">
                                            {{ \Illuminate\Support\Str::limit($header['mapel'], 10) }}
                                        </div>
                                    </th>
                                @endforeach
                                <th class="bg-success text-white" style="width: 40px;">H</th>
                                <th class="bg-secondary text-white" style="width: 40px;">T</th>
                                <th class="bg-warning text-dark" style="width: 40px;">S</th>
                                <th class="bg-info text-white" style="width: 40px;">I</th>
                                <th class="bg-danger text-white" style="width: 40px;">A</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($matrix as $studentId => $data)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td class="fw-bold">
                                        {{ $data['info']->user->name }}
                                        <div class="small text-muted">{{ $data['info']->nis }}</div>
                                    </td>
                                    
                                    @foreach($headers as $key => $header)
                                        <td class="text-center align-middle">
                                            @php $status = $data['attendance'][$key] ?? '-'; @endphp
                                            @include('admin.rekap-jurnal.status-badge', ['status' => $status])
                                        </td>
                                    @endforeach

                                    <td class="text-center fw-bold">{{ $data['stats']['H'] }}</td>
                                    <td class="text-center fw-bold text-secondary">{{ $data['stats']['T'] }}</td>
                                    <td class="text-center">{{ $data['stats']['S'] }}</td>
                                    <td class="text-center">{{ $data['stats']['I'] }}</td>
                                    <td class="text-center text-danger fw-bold">{{ $data['stats']['A'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ count($headers) + 6 }}" class="text-center py-4">
                                        Tidak ada data siswa ditemukan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-info text-center py-5">
            <i class="fas fa-filter fa-3x mb-3 text-info"></i>
            <h4>Silakan pilih filter terlebih dahulu</h4>
            <p>Pilih kelas dan rentang tanggal untuk melihat rekapitulasi.</p>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const filterForm = document.getElementById('filterForm');
        const autoSubmitElements = document.querySelectorAll('.auto-submit');

        autoSubmitElements.forEach(element => {
            element.addEventListener('change', function() {
                // Submit form only if required fields are filled (specifically kelas_id)
                const kelasId = document.querySelector('select[name="kelas_id"]').value;
                if (kelasId) {
                    filterForm.submit();
                }
            });
        });
    });
</script>
@endpush
