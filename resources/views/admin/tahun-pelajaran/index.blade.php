@extends('layouts.app')

@section('title', 'Tahun Pelajaran')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body py-4">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                        <div class="mb-3 mb-md-0">
                            <h1 class="h3 mb-2 text-primary">
                                <i class="fas fa-calendar-alt me-2"></i>Tahun Pelajaran
                            </h1>
                            <p class="text-muted mb-0">Kelola tahun pelajaran dan semester</p>
                        </div>
                        <div>
                            <a href="{{ route('admin.tahun-pelajaran.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Tambah Tahun Pelajaran
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show alert-permanent" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('promotion_info'))
    <div class="alert alert-info alert-dismissible fade show alert-permanent" role="alert">
        <i class="fas fa-graduation-cap me-2"></i>
        <strong>Kenaikan Kelas Berhasil!</strong><br>
        <ul class="mb-0 mt-2">
            <li>{{ session('promotion_info')['promoted'] }} siswa berhasil naik kelas</li>
            <li>{{ session('promotion_info')['graduated'] }} siswa lulus (kelas 9)</li>
            <li><strong>Total: {{ session('promotion_info')['total'] }} siswa diproses</strong></li>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show alert-permanent" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Active Tahun Pelajaran Card -->
    @if($activeTahunPelajaran)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-primary shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title text-primary mb-1">
                                <i class="fas fa-star me-2"></i>Tahun Pelajaran Aktif
                            </h5>
                            <h3 class="mb-0">{{ $activeTahunPelajaran->nama }}</h3>
                            <small class="text-muted">
                                {{ $activeTahunPelajaran->tanggal_mulai ? $activeTahunPelajaran->tanggal_mulai->format('d M Y') : '-' }}
                                s/d
                                {{ $activeTahunPelajaran->tanggal_selesai ? $activeTahunPelajaran->tanggal_selesai->format('d M Y') : '-' }}
                            </small>
                        </div>
                        <a href="{{ route('admin.tahun-pelajaran.dashboard', $activeTahunPelajaran->id) }}" class="btn btn-primary">
                            <i class="fas fa-th-large me-2"></i>Buka Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Tahun Pelajaran Table -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0 fw-semibold">
                        <i class="fas fa-list me-2"></i>Daftar Tahun Pelajaran
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center">#</th>
                                    <th>Tahun Pelajaran</th>
                                    <th class="text-center">Periode</th>
                                    <th class="text-center">Jumlah Semester</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tahunPelajaranList as $index => $tahunPelajaran)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration + ($tahunPelajaranList->currentPage() - 1) * $tahunPelajaranList->perPage() }}</td>
                                    <td>
                                        <div class="fw-semibold">{{ $tahunPelajaran->nama }}</div>
                                        @if($tahunPelajaran->keterangan)
                                        <small class="text-muted">{{ Str::limit($tahunPelajaran->keterangan, 50) }}</small>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <small>{{ $tahunPelajaran->tahun_mulai }} - {{ $tahunPelajaran->tahun_selesai }}</small>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info-subtle text-info border">
                                            {{ $tahunPelajaran->semester->count() }} Semester
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if($tahunPelajaran->is_active)
                                        <span class="badge bg-success">
                                            <i class="fas fa-check-circle me-1"></i>Aktif
                                        </span>
                                        @else
                                        <span class="badge bg-secondary">Tidak Aktif</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group-vertical gap-1" role="group">
                                            <a href="{{ route('admin.tahun-pelajaran.dashboard', $tahunPelajaran->id) }}"
                                               class="btn btn-sm btn-primary"
                                               title="Dashboard">
                                                <i class="fas fa-th-large"></i>
                                                <span class="d-none d-lg-inline ms-1">Dashboard</span>
                                            </a>

                                            @if(!$tahunPelajaran->is_active)
                                            <form action="{{ route('admin.tahun-pelajaran.set-active', $tahunPelajaran->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success w-100" title="Aktifkan">
                                                    <i class="fas fa-check-circle"></i>
                                                    <span class="d-none d-lg-inline ms-1">Aktifkan</span>
                                                </button>
                                            </form>
                                            @endif

                                            <a href="{{ route('admin.tahun-pelajaran.edit', $tahunPelajaran->id) }}"
                                               class="btn btn-sm btn-warning"
                                               title="Edit">
                                                <i class="fas fa-edit"></i>
                                                <span class="d-none d-lg-inline ms-1">Edit</span>
                                            </a>

                                            @if(!$tahunPelajaran->is_active)
                                            <button class="btn btn-sm btn-danger"
                                                    onclick="confirmDelete({{ $tahunPelajaran->id }}, '{{ addslashes($tahunPelajaran->nama) }}')"
                                                    title="Hapus">
                                                <i class="fas fa-trash"></i>
                                                <span class="d-none d-lg-inline ms-1">Hapus</span>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="fas fa-calendar-alt fa-3x opacity-25 mb-3 d-block"></i>
                                            <h6 class="mb-2 fw-semibold">Belum ada data tahun pelajaran</h6>
                                            <p class="mb-0">Klik tombol "Tambah Tahun Pelajaran" untuk membuat tahun pelajaran baru</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($tahunPelajaranList->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $tahunPelajaranList->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script>
function confirmDelete(id, nama) {
    if (confirm(`Apakah Anda yakin ingin menghapus Tahun Pelajaran "${nama}"?\n\nSemua data semester dan jadwal yang terkait akan ikut terhapus!`)) {
        const form = document.getElementById('deleteForm');
        form.action = `/admin/tahun-pelajaran/${id}`;
        form.submit();
    }
}
</script>
@endpush
@endsection
