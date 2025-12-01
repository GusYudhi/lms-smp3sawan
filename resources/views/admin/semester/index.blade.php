@extends('layouts.app')

@section('title', 'Semester - ' . $tahunPelajaran->nama)

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body py-4">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                        <div class="mb-3 mb-md-0">
                            <nav aria-label="breadcrumb" class="mb-2">
                                <ol class="breadcrumb mb-0">
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('admin.tahun-pelajaran.index') }}">Tahun Pelajaran</a>
                                    </li>
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('admin.tahun-pelajaran.dashboard', $tahunPelajaran->id) }}">{{ $tahunPelajaran->nama }}</a>
                                    </li>
                                    <li class="breadcrumb-item active">Semester</li>
                                </ol>
                            </nav>
                            <h1 class="h3 mb-2 text-primary">
                                <i class="fas fa-calendar-alt me-2"></i>Semester
                            </h1>
                            <p class="text-muted mb-0">Kelola semester di tahun pelajaran {{ $tahunPelajaran->nama }}</p>
                        </div>
                        <div>
                            <a href="{{ route('admin.tahun-pelajaran.dashboard', $tahunPelajaran->id) }}" class="btn btn-secondary me-2">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </a>
                            <a href="{{ route('admin.semester.create', ['tahun_pelajaran_id' => $tahunPelajaran->id]) }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Tambah Semester
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
    <div class="row mb-3">
        <div class="col-12">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    </div>
    @endif

    <!-- Semester Cards -->
    <div class="row">
        @forelse($semesters as $semester)
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100 shadow-sm {{ $semester->is_active ? 'border-primary' : '' }}">
                <div class="card-header bg-{{ $semester->is_active ? 'primary' : 'light' }} text-{{ $semester->is_active ? 'white' : 'dark' }}">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-semibold">
                            <i class="fas fa-calendar-check me-2"></i>{{ $semester->nama }}
                        </h6>
                        @if($semester->is_active)
                        <span class="badge bg-success">
                            <i class="fas fa-check-circle me-1"></i>Aktif
                        </span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <!-- Semester Info -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">
                                <i class="fas fa-hashtag me-1"></i>Semester Ke:
                            </span>
                            <span class="fw-bold">{{ $semester->semester_ke }}</span>
                        </div>
                        @if($semester->tanggal_mulai && $semester->tanggal_selesai)
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">
                                <i class="fas fa-calendar me-1"></i>Periode:
                            </span>
                            <span class="fw-medium">{{ $semester->tanggal_mulai->format('d/m/Y') }} - {{ $semester->tanggal_selesai->format('d/m/Y') }}</span>
                        </div>
                        @endif
                    </div>

                    @if($semester->keterangan)
                    <div class="alert alert-info py-2 px-3 mb-3">
                        <small>{{ Str::limit($semester->keterangan, 100) }}</small>
                    </div>
                    @endif

                    <!-- Actions -->
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.semester.dashboard', $semester->id) }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>

                        @if(!$semester->is_active)
                        <form action="{{ route('admin.semester.set-active', $semester->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-success w-100">
                                <i class="fas fa-check-circle me-2"></i>Aktifkan
                            </button>
                        </form>
                        @endif

                        <div class="btn-group" role="group">
                            <a href="{{ route('admin.semester.edit', $semester->id) }}" class="btn btn-sm btn-outline-warning">
                                <i class="fas fa-edit me-1"></i>Edit
                            </a>
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDeleteSemester({{ $semester->id }}, '{{ $semester->nama }}')">
                                <i class="fas fa-trash me-1"></i>Hapus
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <!-- Empty State -->
        <div class="col-12">
            <div class="card shadow-sm border-dashed">
                <div class="card-body text-center py-5">
                    <div class="mb-3">
                        <i class="fas fa-calendar-times fa-4x text-muted"></i>
                    </div>
                    <h5 class="text-muted mb-3">Belum Ada Semester</h5>
                    <p class="text-muted mb-4">Tambahkan semester untuk tahun pelajaran {{ $tahunPelajaran->nama }}</p>
                    <a href="{{ route('admin.semester.create', ['tahun_pelajaran_id' => $tahunPelajaran->id]) }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Tambah Semester Pertama
                    </a>
                </div>
            </div>
        </div>
        @endforelse
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>Konfirmasi Hapus
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-3">Apakah Anda yakin ingin menghapus semester <strong id="semesterName"></strong>?</p>
                <div class="alert alert-warning mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    <small>Data semester yang sudah dihapus tidak dapat dikembalikan. Semua data terkait (jadwal, mata pelajaran, dll) akan ikut terhapus.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="deleteForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function confirmDeleteSemester(id, name) {
    document.getElementById('semesterName').textContent = name;
    document.getElementById('deleteForm').action = `/admin/semester/${id}`;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>
@endpush
