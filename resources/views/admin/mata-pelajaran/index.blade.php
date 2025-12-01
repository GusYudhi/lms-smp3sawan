@extends($semester ? 'layouts.semester' : 'layouts.app')

@section('title', 'Manajemen Mata Pelajaran')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Manajemen Mata Pelajaran</h1>
            @if($semester)
            <p class="text-muted mb-0">
                <small>{{ $semester->tahunPelajaran->nama }} - {{ $semester->nama }}</small>
            </p>
            @endif
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Form Section -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Tambah Mata Pelajaran</h6>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('admin.mapel.store') }}" method="POST">
                        @csrf
                        @if($semester)
                        <input type="hidden" name="semester_id" value="{{ $semester->id }}">
                        @endif
                        <div class="mb-3">
                            <label for="nama_mapel" class="form-label">Nama Mata Pelajaran</label>
                            <input type="text" class="form-control" id="nama_mapel" name="nama_mapel" required>
                        </div>

                        <div class="mb-3">
                            <label for="kode_mapel" class="form-label">Kode Mapel</label>
                            <input type="text" class="form-control" id="kode_mapel" name="kode_mapel" required>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Simpan
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
                    <h6 class="m-0 font-weight-bold text-primary">Daftar Mata Pelajaran</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                            <thead class="table-light">
                                <tr>
                                    <th>Kode</th>
                                    <th>Nama Mata Pelajaran</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($mapels as $mapel)
                                    <tr>
                                        <td>{{ $mapel->kode_mapel }}</td>
                                        <td>{{ $mapel->nama_mapel }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-info me-1" data-bs-toggle="modal" data-bs-target="#editModal{{ $mapel->id }}">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form action="{{ route('admin.mapel.destroy', $mapel->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus mata pelajaran ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>

                                    <!-- Edit Modal -->
                                    <div class="modal fade" id="editModal{{ $mapel->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Edit Mata Pelajaran</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form action="{{ route('admin.mapel.update', $mapel->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label class="form-label">Nama Mata Pelajaran</label>
                                                            <input type="text" class="form-control" name="nama_mapel" value="{{ $mapel->nama_mapel }}" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Kode Mapel</label>
                                                            <input type="text" class="form-control" name="kode_mapel" value="{{ $mapel->kode_mapel }}" required>
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
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-muted">
                                            <i class="fas fa-book fs-1 mb-2 d-block"></i>
                                            Belum ada mata pelajaran yang ditambahkan
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
@endsection
