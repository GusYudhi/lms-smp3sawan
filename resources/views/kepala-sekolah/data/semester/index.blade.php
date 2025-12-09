@extends('layouts.app')

@section('title', 'Data Semester')

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
                                <i class="fas fa-calendar me-2"></i>Data Semester
                            </h1>
                            <p class="text-muted mb-0">Tahun Pelajaran: <strong>{{ $tahunPelajaran->nama }}</strong></p>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ route('kepala-sekolah.tahun-pelajaran.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Semester List -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-list me-2"></i>Daftar Semester
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="30%">Nama Semester</th>
                                    <th width="10%">Semester</th>
                                    <th width="15%">Tanggal Mulai</th>
                                    <th width="15%">Tanggal Selesai</th>
                                    <th width="15%" class="text-center">Status</th>
                                    <th width="10%" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($semesters as $index => $semester)
                                <tr>
                                    <td>{{ $semesters->firstItem() + $index }}</td>
                                    <td>
                                        <strong>{{ $semester->nama }}</strong>
                                        @if($semester->is_active)
                                        <span class="badge bg-success ms-2">Aktif</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $semester->semester == 1 ? 'bg-info' : 'bg-warning' }}">
                                            Semester {{ $semester->semester }}
                                        </span>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($semester->tanggal_mulai)->format('d M Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($semester->tanggal_selesai)->format('d M Y') }}</td>
                                    <td class="text-center">
                                        @if($semester->is_active)
                                        <span class="badge bg-success">Aktif</span>
                                        @else
                                        <span class="badge bg-secondary">Tidak Aktif</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('kepala-sekolah.semester.show', $semester->id) }}"
                                           class="btn btn-sm btn-info"
                                           title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <i class="fas fa-inbox text-muted fs-1 mb-3 d-block"></i>
                                        <p class="text-muted mb-0">Tidak ada data semester</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($semesters->hasPages())
                <div class="card-footer bg-white border-top">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            Menampilkan {{ $semesters->firstItem() ?? 0 }} - {{ $semesters->lastItem() ?? 0 }} dari {{ $semesters->total() }} data
                        </div>
                        <div>
                            {{ $semesters->links() }}
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
