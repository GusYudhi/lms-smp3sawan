@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Manajemen Tugas Guru</h4>
                    <a href="{{ route('kepala-sekolah.tugas-guru.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Tambah Tugas Baru
                    </a>
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
                    <form method="GET" action="{{ route('kepala-sekolah.tugas-guru.index') }}" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control" placeholder="Cari tugas..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-3">
                                <select name="status" class="form-select">
                                    <option value="">Semua Status</option>
                                    <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                    <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                    <option value="dibatalkan" {{ request('status') == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-secondary w-100">
                                    <i class="fas fa-filter"></i> Filter
                                </button>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('kepala-sekolah.tugas-guru.index') }}" class="btn btn-outline-secondary w-100">
                                    <i class="fas fa-redo"></i> Reset
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Statistics -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Total Tugas</h5>
                                    <h2 class="mb-0">{{ $tugasList->total() }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Tugas Aktif</h5>
                                    <h2 class="mb-0">{{ $tugasList->where('status', 'aktif')->count() }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Total Guru</h5>
                                    <h2 class="mb-0">{{ $totalGuru }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Judul Tugas</th>
                                    <th>Deadline</th>
                                    <th>Status</th>
                                    <th>Submissions</th>
                                    <th>Lampiran</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tugasList as $tugas)
                                <tr>
                                    <td>{{ $tugasList->firstItem() + $loop->index }}</td>
                                    <td>
                                        <strong>{{ $tugas->judul }}</strong>
                                        @if($tugas->deskripsi)
                                            <br><small class="text-muted">{{ Str::limit($tugas->deskripsi, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $tugas->deadline->format('d M Y H:i') }}
                                        @if($tugas->isExpired())
                                            <br><span class="badge bg-danger">Expired</span>
                                        @else
                                            <br><span class="badge bg-info">{{ $tugas->deadline->diffForHumans() }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $tugas->status_badge }}">
                                            {{ ucfirst($tugas->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">
                                            {{ $tugas->submissions_count }} / {{ $totalGuru }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($tugas->files->count() > 0)
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-paperclip"></i> {{ $tugas->files->count() }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('kepala-sekolah.tugas-guru.show', $tugas->id) }}" class="btn btn-sm btn-info" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('kepala-sekolah.tugas-guru.edit', $tugas->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('kepala-sekolah.tugas-guru.destroy', $tugas->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus tugas ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
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
@endsection
