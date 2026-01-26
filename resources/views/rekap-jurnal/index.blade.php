@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            
            {{-- Filter Section --}}
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filter Rekapitulasi Absensi Jurnal</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('rekap-jurnal.index') }}" method="GET">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="kelas_id" class="form-label">Pilih Kelas</label>
                                <select name="kelas_id" id="kelas_id" class="form-select" required>
                                    <option value="">-- Pilih Kelas --</option>
                                    @foreach($kelas as $k)
                                        <option value="{{ $k->id }}" {{ (isset($request->kelas_id) && $request->kelas_id == $k->id) ? 'selected' : '' }}>
                                            {{ $k->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label for="mapel_id" class="form-label">Pilih Mapel</label>
                                <select name="mapel_id" id="mapel_id" class="form-select">
                                    <option value="all" {{ (isset($request->mapel_id) && $request->mapel_id == 'all') ? 'selected' : '' }}>Semua Mata Pelajaran</option>
                                    @foreach($mapels as $m)
                                        <option value="{{ $m->id }}" {{ (isset($request->mapel_id) && $request->mapel_id == $m->id) ? 'selected' : '' }}>
                                            {{ $m->nama_mapel }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label for="start_date" class="form-label">Dari Tanggal</label>
                                <input type="date" name="start_date" id="start_date" class="form-control" 
                                    value="{{ $request->start_date ?? date('Y-m-01') }}" required>
                            </div>

                            <div class="col-md-2">
                                <label for="end_date" class="form-label">Sampai Tanggal</label>
                                <input type="date" name="end_date" id="end_date" class="form-control" 
                                    value="{{ $request->end_date ?? date('Y-m-d') }}" required>
                            </div>

                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search me-1"></i> Tampilkan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Result Section --}}
            @if(isset($rekapInfo))
                <div class="card shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1 fw-bold">Hasil Rekapitulasi</h5>
                            <small class="text-muted">
                                Kelas: <strong>{{ $rekapInfo['kelas'] }}</strong> | 
                                Mapel: <strong>{{ $rekapInfo['mapel'] }}</strong> | 
                                Periode: {{ $rekapInfo['periode'] }}
                            </small>
                        </div>
                        <div>
                            <button onclick="window.print()" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-print"></i> Cetak
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover mb-0" style="min-width: 800px;">
                                <thead class="table-light text-center align-middle">
                                    <tr>
                                        <th style="width: 50px;">No</th>
                                        <th style="width: 250px; text-align: left;">Nama Siswa</th>
                                        @foreach($headers as $key => $header)
                                            <th style="min-width: 80px;">
                                                @if(isset($header['kode']))
                                                    {{-- Mode All Mapel --}}
                                                    <span title="{{ $header['label'] }}">{{ $header['label'] }}</span>
                                                @else
                                                    {{-- Mode Specific Mapel (Dates) --}}
                                                    {{ $header['label'] }}
                                                    <br><small class="text-muted" style="font-size: 0.7em">{{ $header['full_date'] ?? '' }}</small>
                                                @endif
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($students as $index => $student)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    {{-- Optional Avatar if wanted --}}
                                                    <div class="fw-bold">{{ $student->user->name ?? '-' }}</div>
                                                </div>
                                                <small class="text-muted">{{ $student->nis }}</small>
                                            </td>
                                            @foreach($headers as $key => $header)
                                                <td class="text-center">
                                                    @php
                                                        $statusData = $attendanceData[$student->id][$key] ?? null;
                                                    @endphp

                                                    @if($statusData)
                                                        @if(is_array($statusData) && isset($statusData[0]['status']))
                                                            {{-- Mode All Mapel (Array of statuses) --}}
                                                            @foreach($statusData as $item)
                                                                @include('rekap-jurnal.status-badge', ['status' => $item['status'], 'date' => $item['date']])
                                                            @endforeach
                                                        @else
                                                            {{-- Mode Specific Mapel (Single Status String) --}}
                                                            @include('rekap-jurnal.status-badge', ['status' => $statusData, 'date' => null])
                                                        @endif
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="{{ count($headers) + 2 }}" class="text-center py-4">
                                                Tidak ada data siswa ditemukan untuk kelas ini.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <div class="mt-3">
                    <small class="text-muted">
                        <strong>Keterangan:</strong> 
                        <span class="badge bg-success me-1">H</span> Hadir
                        <span class="badge bg-info me-1">I</span> Izin
                        <span class="badge bg-warning text-dark me-1">S</span> Sakit
                        <span class="badge bg-danger me-1">A</span> Alpha
                        <span class="badge bg-secondary me-1">T</span> Terlambat
                    </small>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    @media print {
        .card-header button, form { display: none; }
        .card { border: none; shadow: none; }
    }
</style>
@endsection
