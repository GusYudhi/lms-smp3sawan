@extends('layouts.app')

@section('title', 'Dashboard Tahun Pelajaran - ' . $tahunPelajaran->nama)

@section('content')
<div class="container-fluid">
    <!-- Page Header with Back Button -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-primary">
                <div class="card-body py-4">
                    <div class="d-flex flex-column">
                        <!-- Back Button -->
                        <div class="mb-3">
                            <a href="{{ route('admin.tahun-pelajaran.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-arrow-left me-2"></i>Kembali ke Tahun Pelajaran
                            </a>
                        </div>

                        <!-- Header Info -->
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                            <div class="mb-3 mb-md-0">
                                <h1 class="h3 mb-2 text-primary">
                                    <i class="fas fa-calendar-alt me-2"></i>{{ $tahunPelajaran->nama }}
                                    @if($tahunPelajaran->is_active)
                                    <span class="badge bg-success ms-2">Aktif</span>
                                    @endif
                                </h1>
                                <p class="text-muted mb-0">
                                    <i class="fas fa-calendar me-2"></i>
                                    @if($tahunPelajaran->tanggal_mulai && $tahunPelajaran->tanggal_selesai)
                                        {{ $tahunPelajaran->tanggal_mulai->format('d M Y') }} - {{ $tahunPelajaran->tanggal_selesai->format('d M Y') }}
                                    @else
                                        Tahun {{ $tahunPelajaran->tahun_mulai }} - {{ $tahunPelajaran->tahun_selesai }}
                                    @endif
                                </p>
                            </div>
                            <div class="d-flex gap-2">
                                @if(!$tahunPelajaran->is_active)
                                <form action="{{ route('admin.tahun-pelajaran.set-active', $tahunPelajaran->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-check-circle me-2"></i>Aktifkan
                                    </button>
                                </form>
                                @endif
                                <a href="{{ route('admin.tahun-pelajaran.edit', $tahunPelajaran->id) }}" class="btn btn-warning">
                                    <i class="fas fa-edit me-2"></i>Edit
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-list-alt fa-2x text-primary"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Total Semester</h6>
                            <h3 class="mb-0">{{ $statistics['total_semester'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-door-open fa-2x text-success"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Total Kelas</h6>
                            <h3 class="mb-0">{{ $statistics['total_kelas'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-info bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-calendar-check fa-2x text-info"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Status</h6>
                            <h3 class="mb-0">
                                @if($tahunPelajaran->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Non-Aktif</span>
                                @endif
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Semester Management -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-semibold">
                        <i class="fas fa-list me-2"></i>Daftar Semester
                    </h6>
                    <a href="{{ route('admin.semester.create', ['tahun_pelajaran_id' => $tahunPelajaran->id]) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus me-2"></i>Tambah Semester
                    </a>
                </div>
                <div class="card-body">
                    @if($tahunPelajaran->semester->isEmpty())
                    <div class="text-center py-5">
                        <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                        <h6 class="text-muted">Belum ada semester</h6>
                        <p class="text-muted mb-3">Klik tombol "Tambah Semester" untuk membuat semester baru</p>
                        <a href="{{ route('admin.semester.create', ['tahun_pelajaran_id' => $tahunPelajaran->id]) }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Tambah Semester
                        </a>
                    </div>
                    @else
                    <div class="row g-3">
                        @foreach($tahunPelajaran->semester()->orderBy('semester_ke')->get() as $semester)
                        <div class="col-md-6">
                            <div class="card {{ $semester->is_active ? 'border-primary' : 'border-light' }} h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div>
                                            <h5 class="card-title mb-1">
                                                Semester {{ $semester->nama }}
                                                @if($semester->is_active)
                                                <span class="badge bg-success ms-2">Aktif</span>
                                                @endif
                                            </h5>
                                            <small class="text-muted">
                                                @if($semester->tanggal_mulai && $semester->tanggal_selesai)
                                                    {{ $semester->tanggal_mulai->format('d M Y') }} - {{ $semester->tanggal_selesai->format('d M Y') }}
                                                @else
                                                    Semester ke-{{ $semester->semester_ke }}
                                                @endif
                                            </small>
                                        </div>
                                    </div>

                                    @if($semester->keterangan)
                                    <p class="card-text text-muted small mb-3">{{ $semester->keterangan }}</p>
                                    @endif

                                    <div class="d-flex gap-2">
                                        <a href="{{ route('admin.semester.dashboard', $semester->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-th-large me-1"></i>Dashboard
                                        </a>

                                        @if(!$semester->is_active)
                                        <form action="{{ route('admin.semester.set-active', $semester->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success">
                                                <i class="fas fa-check-circle me-1"></i>Aktifkan
                                            </button>
                                        </form>
                                        @endif

                                        <a href="{{ route('admin.semester.edit', $semester->id) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        @if(!$semester->is_active)
                                        <button class="btn btn-sm btn-danger"
                                                onclick="confirmDeleteSemester({{ $semester->id }}, '{{ addslashes($semester->nama) }}')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Semester Form -->
<form id="deleteSemesterForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script>
function confirmDeleteSemester(id, nama) {
    if (confirm(`Apakah Anda yakin ingin menghapus Semester "${nama}"?\n\nSemua data jadwal yang terkait akan ikut terhapus!`)) {
        const form = document.getElementById('deleteSemesterForm');
        form.action = `/admin/semester/${id}`;
        form.submit();
    }
}
</script>
@endpush
@endsection
