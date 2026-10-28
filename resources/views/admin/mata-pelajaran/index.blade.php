@extends('layouts.app')

@section('title', 'Master Data Mata Pelajaran')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Master Data Mata Pelajaran</h1>
            <p class="text-muted mb-0">
                <small>Kelola data master mata pelajaran yang berlaku di seluruh jenjang dan semester.</small>
            </p>
        </div>
    </div>

    <!-- Alerts -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(isset($errors) && $errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Content Row -->
    <div class="row">
        <!-- Form Section -->
        <div class="col-xl-4 col-lg-5 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Tambah Mata Pelajaran</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.mapel.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="kode_mapel" class="form-label">Kode Mapel <span class="text-danger">*</span></label>
                            <input type="text" class="form-control text-uppercase @error('kode_mapel') is-invalid @enderror" id="kode_mapel" name="kode_mapel" placeholder="Contoh: MTK, BIND" value="{{ old('kode_mapel') }}" required maxlength="10">
                            <small class="text-muted">Kode unik untuk format jadwal & import excel.</small>
                        </div>

                        <div class="mb-3">
                            <label for="nama_mapel" class="form-label">Nama Mata Pelajaran <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nama_mapel') is-invalid @enderror" id="nama_mapel" name="nama_mapel" placeholder="Contoh: Matematika" value="{{ old('nama_mapel') }}" required>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="is_universal" name="is_universal" value="1" {{ old('is_universal') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_universal">
                                Mapel Multi-Guru / Umum
                            </label>
                            <small class="text-muted d-block">Centang jika mapel ini dapat diampu oleh semua guru (misal: Kokurikuler, P5).</small>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i> Tambah Mata Pelajaran
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- List Section -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Daftar Master Mata Pelajaran ({{ $mapels->count() }})</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle" id="dataTable" width="100%" cellspacing="0">
                            <thead class="table-light">
                                <tr>
                                    <th width="12%">Kode</th>
                                    <th>Nama Mata Pelajaran</th>
                                    <th width="15%" class="text-center">Sifat</th>
                                    <th width="15%" class="text-center">Status</th>
                                    <th width="18%" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($mapels as $mapel)
                                    <tr class="{{ !$mapel->is_active ? 'table-light opacity-75' : '' }}">
                                        <td><span class="badge bg-dark font-monospace">{{ $mapel->kode_mapel }}</span></td>
                                        <td>
                                            <strong>{{ $mapel->nama_mapel }}</strong>
                                        </td>
                                        <td class="text-center">
                                            @if($mapel->is_universal)
                                                <span class="badge bg-info text-dark">Multi-Guru</span>
                                            @else
                                                <span class="badge bg-secondary">Standar</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($mapel->is_active)
                                                <span class="badge bg-success">Aktif</span>
                                            @else
                                                <span class="badge bg-danger">Nonaktif</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <!-- Toggle Status Button -->
                                            <form action="{{ route('admin.mapel.toggle-status', $mapel->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                @if($mapel->is_active)
                                                    <button type="submit" class="btn btn-sm btn-outline-warning me-1" title="Nonaktifkan Mapel">
                                                        <i class="fas fa-toggle-on"></i>
                                                    </button>
                                                @else
                                                    <button type="submit" class="btn btn-sm btn-outline-success me-1" title="Aktifkan Mapel">
                                                        <i class="fas fa-toggle-off"></i>
                                                    </button>
                                                @endif
                                            </form>

                                            <!-- Edit Button -->
                                            <button type="button" class="btn btn-sm btn-info text-white me-1" data-bs-toggle="modal" data-bs-target="#editModal{{ $mapel->id }}" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>

                                            <!-- Delete Button -->
                                            <form action="{{ route('admin.mapel.destroy', $mapel->id) }}" method="POST" class="d-inline delete-form" data-message="Yakin ingin menghapus mata pelajaran {{ $mapel->nama_mapel }}? Hanya dapat dihapus jika belum pernah digunakan dalam riwayat akademik.">
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
                                        <td colspan="5" class="text-center py-4 text-muted">
                                            <i class="fas fa-book fs-1 mb-2 d-block text-secondary"></i>
                                            Belum ada data mata pelajaran di master data.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modals -->
@foreach($mapels as $mapel)
<div class="modal fade" id="editModal{{ $mapel->id }}" tabindex="-1" aria-labelledby="editModalLabel{{ $mapel->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel{{ $mapel->id }}">Edit Mata Pelajaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.mapel.update', $mapel->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Kode Mapel <span class="text-danger">*</span></label>
                        <input type="text" class="form-control text-uppercase" name="kode_mapel" value="{{ $mapel->kode_mapel }}" required maxlength="10">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Mata Pelajaran <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama_mapel" value="{{ $mapel->nama_mapel }}" required>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="is_universal_{{ $mapel->id }}" name="is_universal" value="1" {{ $mapel->is_universal ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_universal_{{ $mapel->id }}">
                            Mapel Multi-Guru / Umum
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

@endsection
