@extends('layouts.app')

@section('title', 'Materi Pelajaran')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-between gap-2 mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Materi Pelajaran</h1>
            <p class="text-muted mb-0 small">Kelola bahan ajar dan referensi belajar siswa</p>
        </div>
        <a href="{{ route('guru.materi.create') }}" class="btn btn-primary shadow-sm">
            <i class="fas fa-plus me-1"></i> Upload Materi
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            @if($materis->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Belum ada materi pelajaran</h5>
                    <p class="text-muted small">Klik tombol "Upload Materi" di atas untuk menambahkan bahan ajar baru.</p>
                </div>
            @else
                <!-- DESKTOP TABLE VIEW (>= 768px) -->
                <div class="table-responsive d-none d-md-block">
                    <table class="table table-hover align-middle mb-0" id="dataTable" width="100%" cellspacing="0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;" class="text-center">No</th>
                                <th>Mata Pelajaran</th>
                                <th>Kelas</th>
                                <th>Judul Materi</th>
                                <th class="text-center" style="width: 100px;">Tipe</th>
                                <th>Deskripsi</th>
                                <th class="text-center" style="width: 170px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($materis as $index => $item)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td class="fw-bold">{{ $item->mataPelajaran->nama_mapel }}</td>
                                <td>
                                    <span class="badge bg-info">
                                        @if($item->kelas)
                                            {{ $item->kelas->full_name }}
                                        @elseif($item->tingkat)
                                            Semua Kelas {{ $item->tingkat }}
                                        @else
                                            Semua Kelas
                                        @endif
                                    </span>
                                </td>
                                <td><strong>{{ $item->judul }}</strong></td>
                                <td class="text-center">
                                    @if($item->tipe == 'link')
                                        <span class="badge bg-info"><i class="fas fa-link me-1"></i>Link</span>
                                    @else
                                        <span class="badge bg-secondary"><i class="fas fa-file-alt me-1"></i>File</span>
                                    @endif
                                </td>
                                <td><small class="text-muted">{{ Str::limit($item->deskripsi, 60) }}</small></td>
                                <td class="text-center">
                                    @if($item->tipe == 'link')
                                        <a href="{{ $item->link }}" class="btn btn-info btn-sm text-white" target="_blank">
                                            <i class="fas fa-external-link-alt me-1"></i>Buka Link
                                        </a>
                                    @elseif($item->file_path)
                                        <a href="{{ asset('storage/' . $item->file_path) }}" class="btn btn-primary btn-sm" target="_blank">
                                            <i class="fas fa-download me-1"></i>Download
                                        </a>
                                    @endif

                                    <form action="{{ route('guru.materi.destroy', $item->id) }}" method="POST" class="d-inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- MOBILE CARD LIST VIEW (< 768px) -->
                <div class="d-md-none">
                    @foreach($materis as $index => $item)
                    <div class="materi-card-mobile">
                        <div class="materi-header">
                            <div class="materi-type-icon {{ $item->tipe == 'link' ? 'type-link' : 'type-file' }}">
                                <i class="fas {{ $item->tipe == 'link' ? 'fa-link' : 'fa-file-alt' }}"></i>
                            </div>
                            <div class="flex-grow-1 min-w-0">
                                <h5 class="materi-title">{{ $item->judul }}</h5>
                                <div class="d-flex flex-wrap gap-1 mt-1">
                                    <span class="badge bg-primary">{{ $item->mataPelajaran->nama_mapel }}</span>
                                    <span class="badge bg-info">
                                        @if($item->kelas)
                                            {{ $item->kelas->full_name }}
                                        @elseif($item->tingkat)
                                            Semua Kelas {{ $item->tingkat }}
                                        @else
                                            Semua Kelas
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>

                        @if($item->deskripsi)
                            <p class="small text-muted mb-0 mt-2">{{ Str::limit($item->deskripsi, 100) }}</p>
                        @endif

                        <div class="materi-actions">
                            @if($item->tipe == 'link')
                                <a href="{{ $item->link }}" class="btn btn-info text-white flex-grow-1" target="_blank">
                                    <i class="fas fa-external-link-alt me-1"></i>Buka Link
                                </a>
                            @elseif($item->file_path)
                                <a href="{{ asset('storage/' . $item->file_path) }}" class="btn btn-primary flex-grow-1" target="_blank">
                                    <i class="fas fa-download me-1"></i>Unduh File
                                </a>
                            @endif

                            <form action="{{ route('guru.materi.destroy', $item->id) }}" method="POST" class="d-inline delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger px-3" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

