@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1"><i class="fas fa-book text-primary me-2"></i>Daftar Tugas Guru</h2>
                    <p class="text-muted mb-0">Kerjakan tugas guru dengan tepat waktu</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Daftar Tugas Guru</h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Filter -->
                    <form method="GET" action="{{ route('guru.tugas-guru.index') }}" class="mb-4" id="filterForm">
                        <div class="row g-3">
                            <div class="col-md-5">
                                <input type="text" name="search" class="form-control search-input" placeholder="Cari tugas..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-3">
                                <select name="status" class="form-select auto-submit">
                                    <option value="">Semua Status</option>
                                    <option value="dikumpulkan" {{ request('status') == 'dikumpulkan' ? 'selected' : '' }}>Sudah Dikumpulkan</option>
                                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="terlambat" {{ request('status') == 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <a href="{{ route('guru.tugas-guru.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-redo me-1"></i> Reset
                                </a>
                                <small class="text-muted ms-2">
                                    <i class="fas fa-info-circle me-1"></i>Auto-submit aktif
                                </small>
                            </div>
                        </div>
                    </form>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Judul Tugas</th>
                                    <th>Deadline</th>
                                    <th>Status Pengumpulan</th>
                                    <th>Nilai</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tugasList as $tugas)
                                <tr class="{{ $tugas->isExpired() && $tugas->submission_status == 'belum' ? 'table-danger' : '' }}">
                                    <td>{{ $tugasList->firstItem() + $loop->index }}</td>
                                    <td>
                                        <strong>{{ $tugas->judul }}</strong>
                                        @if($tugas->deskripsi)
                                            <br><small class="text-muted">{{ Str::limit($tugas->deskripsi, 50) }}</small>
                                        @endif
                                        @if($tugas->files->count() > 0)
                                            <br><span class="badge bg-secondary"><i class="fas fa-paperclip"></i> {{ $tugas->files->count() }} file</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $tugas->deadline->format('d M Y') }}<br>
                                        <small>{{ $tugas->deadline->format('H:i') }}</small>
                                        @if($tugas->isExpired())
                                            <br><span class="badge bg-danger">Expired</span>
                                        @else
                                            <br><span class="badge bg-info">{{ $tugas->deadline->diffForHumans() }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($tugas->submission_status == 'belum')
                                            <span class="badge bg-secondary">Belum Dikumpulkan</span>
                                        @elseif($tugas->submission_status == 'draft')
                                            <span class="badge bg-warning">Draft</span>
                                        @elseif($tugas->submission_status == 'dikumpulkan')
                                            <span class="badge bg-success">Dikumpulkan</span>
                                            @if($tugas->my_submission && $tugas->my_submission->tanggal_submit)
                                                <br><small>{{ $tugas->my_submission->tanggal_submit->format('d M Y') }}</small>
                                            @endif
                                        @elseif($tugas->submission_status == 'terlambat')
                                            <span class="badge bg-danger">Terlambat</span>
                                            @if($tugas->my_submission && $tugas->my_submission->tanggal_submit)
                                                <br><small>{{ $tugas->my_submission->tanggal_submit->format('d M Y') }}</small>
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        @if($tugas->my_submission && $tugas->my_submission->nilai)
                                            <span class="badge bg-success fs-6">{{ $tugas->my_submission->nilai }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('guru.tugas-guru.show', $tugas->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i> Lihat
                                        </a>
                                        @if($tugas->submission_status == 'belum')
                                            <span class="badge bg-warning">Belum Submit</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">Belum ada tugas</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $tugasList->links() }}
                    </div>
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

    // Search input - submit on Enter
    $('.search-input').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            $('#filterForm').submit();
        }
    });
</script>
@endpush
@endsection
