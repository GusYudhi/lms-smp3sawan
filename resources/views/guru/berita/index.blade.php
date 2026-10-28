@extends('layouts.app')

@section('title', 'Berita Sekolah')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800">Berita Sekolah</h1>
            <p class="text-muted small mb-0">Kelola publikasi dan informasi berita sekolah</p>
        </div>
        <a href="{{ route('guru.berita.create') }}" class="btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50 me-1"></i> Tambah Berita
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle" id="dataTable" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th width="15%">Foto</th>
                            <th>Judul</th>
                            <th width="15%">Tanggal</th>
                            <th width="15%">Penulis</th>
                            <th width="12%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($beritas as $index => $item)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="text-center">
                                @if($item->foto)
                                <img src="{{ asset('storage/' . $item->foto) }}" alt="{{ $item->judul }}" width="90" class="img-thumbnail rounded">
                                @else
                                <span class="badge bg-secondary-subtle text-secondary border">Tidak ada foto</span>
                                @endif
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $item->judul }}</div>
                                <div class="text-muted small">{{ Str::limit(strip_tags($item->konten), 80) }}</div>
                            </td>
                            <td>
                                <i class="far fa-calendar-alt text-muted me-1"></i>
                                {{ $item->tanggal ? $item->tanggal->format('d M Y') : '-' }}
                            </td>
                            <td>
                                <span class="badge bg-primary-subtle text-primary border">
                                    <i class="fas fa-user-edit me-1"></i>{{ $item->penulis->name ?? 'Admin' }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('guru.berita.edit', $item->id) }}" class="btn btn-warning btn-sm" title="Edit Berita">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('guru.berita.destroy', $item->id) }}" method="POST" class="d-inline delete-form" data-message="Apakah Anda yakin ingin menghapus berita ini?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Hapus Berita">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Belum ada berita yang ditambahkan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

