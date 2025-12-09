@extends('layouts.app')

@section('title', 'Detail Absensi Guru')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h1 class="h3 mb-2 text-success">
                                <i class="fas fa-chalkboard-teacher me-2"></i>Detail Absensi Guru
                                <span class="badge bg-info ms-2">Mode Lihat Saja</span>
                            </h1>
                            <p class="text-muted mb-0">Riwayat kehadiran guru</p>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ route('kepala-sekolah.absensi.guru.index', ['filter' => request('filter'), 'start_date' => request('start_date'), 'end_date' => request('end_date')]) }}"
                               class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Profil Guru -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="avatar-xl mx-auto mb-3">
                        <div class="avatar-title rounded-circle bg-primary text-white" style="width: 100px; height: 100px; font-size: 48px; line-height: 100px;">
                            {{ substr($guru->name, 0, 1) }}
                        </div>
                    </div>
                    <h4 class="mb-1">{{ $guru->name }}</h4>
                    <p class="text-muted mb-2">NIP: {{ $guru->guruProfile->nip ?? '-' }}</p>
                    @if($guru->guruProfile && $guru->guruProfile->mataPelajaran)
                    <span class="badge bg-info fs-6 mb-2">
                        {{ $guru->guruProfile->mataPelajaran->nama }}
                    </span>
                    @endif
                    @if($guru->guruProfile && $guru->guruProfile->jabatan_di_sekolah)
                    <br>
                    <span class="badge bg-secondary fs-6">
                        {{ $guru->guruProfile->jabatan_di_sekolah }}
                    </span>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-pie me-2"></i>Ringkasan Kehadiran
                    </h5>
                    <small class="text-muted">{{ $dateRange['label'] }}</small>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="border rounded p-3 text-center bg-light">
                                <i class="fas fa-check-circle text-success fs-2 mb-2"></i>
                                <h3 class="mb-1">{{ $summary['hadir'] }}</h3>
                                <p class="text-muted mb-0 small">Hadir</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded p-3 text-center bg-light">
                                <i class="fas fa-procedures text-warning fs-2 mb-2"></i>
                                <h3 class="mb-1">{{ $summary['sakit'] }}</h3>
                                <p class="text-muted mb-0 small">Sakit</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded p-3 text-center bg-light">
                                <i class="fas fa-file-alt text-info fs-2 mb-2"></i>
                                <h3 class="mb-1">{{ $summary['izin'] }}</h3>
                                <p class="text-muted mb-0 small">Izin</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded p-3 text-center bg-light">
                                <i class="fas fa-times-circle text-danger fs-2 mb-2"></i>
                                <h3 class="mb-1">{{ $summary['alpha'] }}</h3>
                                <p class="text-muted mb-0 small">Alpha</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded p-3 text-center bg-light">
                                <i class="fas fa-clock text-secondary fs-2 mb-2"></i>
                                <h3 class="mb-1">{{ $summary['terlambat'] }}</h3>
                                <p class="text-muted mb-0 small">Terlambat</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded p-3 text-center bg-primary text-white">
                                <i class="fas fa-percentage fs-2 mb-2"></i>
                                <h3 class="mb-1">{{ $persentaseHadir }}%</h3>
                                <p class="mb-0 small">Persentase Hadir</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Riwayat Absensi -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-history me-2"></i>Riwayat Absensi
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="12%">Tanggal</th>
                                    <th width="10%">Hari</th>
                                    <th width="10%">Status</th>
                                    <th width="12%">Waktu Absen</th>
                                    <th width="10%">Bukti</th>
                                    <th width="41%">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($absensi as $index => $item)
                                <tr>
                                    <td>{{ $absensi->firstItem() + $index }}</td>
                                    <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($item->tanggal)->locale('id')->isoFormat('dddd') }}</td>
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
                                    <td>
                                        @if($item->waktu_absen)
                                        <i class="fas fa-clock text-primary me-1"></i>
                                        {{ \Carbon\Carbon::parse($item->waktu_absen)->format('H:i:s') }}
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($item->status == 'hadir' && $item->photo_path)
                                        <!-- Tombol Foto Bukti Kehadiran -->
                                        <button type="button" class="btn btn-sm btn-primary"
                                                data-bs-toggle="modal"
                                                data-bs-target="#photoModal{{ $item->id }}"
                                                title="Lihat Foto Bukti">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @elseif(in_array($item->status, ['sakit', 'izin']) && $item->dokumen_path)
                                        <!-- File Bukti Izin/Sakit -->
                                        <a href="{{ asset('storage/' . $item->dokumen_path) }}"
                                           target="_blank"
                                           class="btn btn-sm btn-info"
                                           title="Unduh File Bukti">
                                            <i class="fas fa-file-download"></i>
                                        </a>
                                        @else
                                        <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->keterangan)
                                        <small>{{ $item->keterangan }}</small>
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <i class="fas fa-inbox text-muted fs-1 mb-3 d-block"></i>
                                        <p class="text-muted mb-0">Tidak ada data absensi</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-light border-top-0 py-3">
                    <div class="pagination-container">
                        <div class="pagination-info">
                            Menampilkan {{ $absensi->firstItem() ?? 0 }}-{{ $absensi->lastItem() ?? 0 }} dari {{ $absensi->total() }} data
                        </div>
                        <div class="pagination-controls">
                            @if ($absensi->onFirstPage())
                                <button class="btn btn-pagination" disabled>
                                    <i class="fas fa-chevron-left"></i> Sebelumnya
                                </button>
                            @else
                                <a href="{{ $absensi->appends(['filter' => request('filter'), 'start_date' => request('start_date'), 'end_date' => request('end_date')])->previousPageUrl() }}" class="btn btn-pagination">
                                    <i class="fas fa-chevron-left"></i> Sebelumnya
                                </a>
                            @endif

                            <span class="pagination-current">{{ $absensi->currentPage() }}</span>

                            @if ($absensi->hasMorePages())
                                <a href="{{ $absensi->appends(['filter' => request('filter'), 'start_date' => request('start_date'), 'end_date' => request('end_date')])->nextPageUrl() }}" class="btn btn-pagination">
                                    Selanjutnya <i class="fas fa-chevron-right"></i>
                                </a>
                            @else
                                <button class="btn btn-pagination" disabled>
                                    Selanjutnya <i class="fas fa-chevron-right"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Foto Bukti - Dipindahkan ke luar tabel untuk menghindari flickering -->
@foreach($absensi as $item)
    @if($item->status == 'hadir' && $item->photo_path)
    <div class="modal fade" id="photoModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Foto Bukti Kehadiran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-4">
                    <div class="photo-container">
                        <img src="{{ asset('storage/' . $item->photo_path) }}"
                             alt="Foto Bukti Kehadiran"
                             class="photo-evidence">
                    </div>
                    <div class="mt-3 photo-info">
                        <small class="text-muted">
                            <i class="fas fa-calendar me-1"></i>
                            {{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}
                            <i class="fas fa-clock ms-2 me-1"></i>
                            {{ \Carbon\Carbon::parse($item->waktu_absen)->format('H:i:s') }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
@endforeach

<style>
.pagination-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.pagination-info {
    color: #6c757d;
    font-size: 0.9rem;
}

.pagination-controls {
    display: flex;
    gap: 0.5rem;
    align-items: center;
}

.btn-pagination {
    padding: 0.5rem 1rem;
    border: 1px solid #dee2e6;
    background-color: #fff;
    color: #495057;
    text-decoration: none;
    border-radius: 0.375rem;
    font-size: 0.9rem;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-pagination:hover:not(:disabled) {
    background-color: #0d6efd;
    color: #fff;
    border-color: #0d6efd;
}

.btn-pagination:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.pagination-current {
    padding: 0.5rem 1rem;
    background-color: #0d6efd;
    color: #fff;
    border-radius: 0.375rem;
    font-weight: 600;
    min-width: 2.5rem;
    text-align: center;
}

/* Fix flickering issue on modal image */
.photo-container {
    position: relative;
    width: 100%;
    min-height: 300px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #f8f9fa;
    border-radius: 8px;
    overflow: hidden;
}

.photo-evidence {
    display: block;
    max-width: 100%;
    max-height: 500px;
    width: auto;
    height: auto;
    object-fit: contain;
    border-radius: 8px;

    /* Anti-flickering properties */
    -webkit-backface-visibility: hidden;
    -moz-backface-visibility: hidden;
    backface-visibility: hidden;

    -webkit-transform: translate3d(0, 0, 0);
    -moz-transform: translate3d(0, 0, 0);
    transform: translate3d(0, 0, 0);

    -webkit-font-smoothing: subpixel-antialiased;

    /* Prevent user interaction that might cause flickering */
    pointer-events: none;
    user-select: none;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;

    /* Improve rendering */
    image-rendering: -webkit-optimize-contrast;
    image-rendering: crisp-edges;

    /* Smooth transitions */
    will-change: auto;
}

.modal-body {
    overflow: hidden;
    -webkit-overflow-scrolling: touch;
}

.modal-content {
    -webkit-backface-visibility: hidden;
    backface-visibility: hidden;
    transform: translateZ(0);
}

.photo-info {
    pointer-events: auto;
}

/* Remove any hover effects that might cause flickering */
.modal-body img:hover,
.photo-evidence:hover {
    transform: translate3d(0, 0, 0);
}

@media (max-width: 576px) {
    .pagination-container {
        flex-direction: column;
        text-align: center;
    }

    .pagination-controls {
        width: 100%;
        justify-content: center;
    }

    .photo-container {
        min-height: 200px;
    }

    .photo-evidence {
        max-height: 350px;
    }
}
</style>
@endsection
