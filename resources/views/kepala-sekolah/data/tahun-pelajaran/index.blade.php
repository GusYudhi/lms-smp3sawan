@extends('layouts.app')

@section('title', 'Data Tahun Pelajaran')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h1 class="h3 mb-2 text-primary">
                                <i class="fas fa-calendar-alt me-2"></i>Data Tahun Pelajaran
                            </h1>
                            <p class="text-muted mb-0">Lihat informasi tahun pelajaran dan semester</p>
                            <span class="badge bg-info mt-2">Mode Lihat Saja</span>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ route('kepala-sekolah.dashboard') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tahun Pelajaran List -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-list me-2"></i>Daftar Tahun Pelajaran
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="30%">Tahun Pelajaran</th>
                                    <th width="15%">Tanggal Mulai</th>
                                    <th width="15%">Tanggal Selesai</th>
                                    <th width="10%" class="text-center">Semester</th>
                                    <th width="15%" class="text-center">Status</th>
                                    <th width="10%" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tahunPelajaran as $index => $tp)
                                <tr>
                                    <td>{{ $tahunPelajaran->firstItem() + $index }}</td>
                                    <td>
                                        <strong>{{ $tp->nama }}</strong>
                                        @if($tp->is_active)
                                        <span class="badge bg-success ms-2">Aktif</span>
                                        @endif
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($tp->tanggal_mulai)->format('d M Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($tp->tanggal_selesai)->format('d M Y') }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-primary">{{ $tp->semesters_count }} Semester</span>
                                    </td>
                                    <td class="text-center">
                                        @if($tp->is_active)
                                        <span class="badge bg-success">Aktif</span>
                                        @else
                                        <span class="badge bg-secondary">Tidak Aktif</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('kepala-sekolah.tahun-pelajaran.show', $tp->id) }}"
                                               class="btn btn-info"
                                               title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('kepala-sekolah.semester.index', $tp->id) }}"
                                               class="btn btn-primary"
                                               title="Lihat Semester">
                                                <i class="fas fa-calendar"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <i class="fas fa-inbox text-muted fs-1 mb-3 d-block"></i>
                                        <p class="text-muted mb-0">Tidak ada data tahun pelajaran</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($tahunPelajaran->hasPages())
                <div class="card-footer bg-white border-top">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            Menampilkan {{ $tahunPelajaran->firstItem() ?? 0 }} - {{ $tahunPelajaran->lastItem() ?? 0 }} dari {{ $tahunPelajaran->total() }} data
                        </div>
                        <div>
                            {{ $tahunPelajaran->links() }}
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
