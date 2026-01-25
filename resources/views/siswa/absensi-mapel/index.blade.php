@extends('layouts.app')

@section('title', 'Absensi Per Mata Pelajaran')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Absensi Per Mata Pelajaran</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Mata Pelajaran</th>
                            <th>Total Pertemuan</th>
                            <th>Hadir</th>
                            <th>Sakit</th>
                            <th>Izin</th>
                            <th>Alpa</th>
                            <th>Persentase Kehadiran</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendanceStats as $index => $stat)
                        @php
                            $totalMasuk = $stat->hadir + $stat->sakit + $stat->izin + $stat->alpa; // Seharusnya sama dengan total_pertemuan
                            $persentase = $totalMasuk > 0 ? (($stat->hadir) / $totalMasuk) * 100 : 0;
                        @endphp
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $stat->nama_mapel }}</td>
                            <td>{{ $stat->total_pertemuan }}</td>
                            <td class="text-success">{{ $stat->hadir }}</td>
                            <td class="text-info">{{ $stat->sakit }}</td>
                            <td class="text-warning">{{ $stat->izin }}</td>
                            <td class="text-danger">{{ $stat->alpa }}</td>
                            <td>
                                <div class="progress">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $persentase }}%" aria-valuenow="{{ $persentase }}" aria-valuemin="0" aria-valuemax="100">{{ number_format($persentase, 1) }}%</div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">Belum ada data absensi.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
