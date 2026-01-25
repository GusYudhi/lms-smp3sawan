@extends('layouts.app')

@section('title', 'Galeri Sekolah')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Galeri Sekolah</h1>
        <a href="{{ route('admin.galeri.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Galeri
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Media</th>
                            <th>Judul</th>
                            <th>Tipe</th>
                            <th>Deskripsi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($galeris as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                @if($item->tipe == 'foto')
                                    <img src="{{ asset('storage/' . $item->file_path) }}" alt="{{ $item->judul }}" width="100" class="img-thumbnail">
                                @else
                                    <video width="100" controls>
                                        <source src="{{ asset('storage/' . $item->file_path) }}" type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>
                                @endif
                            </td>
                            <td>{{ $item->judul ?? '-' }}</td>
                            <td>
                                <span class="badge {{ $item->tipe == 'foto' ? 'bg-primary' : 'bg-danger' }}">
                                    {{ ucfirst($item->tipe) }}
                                </span>
                            </td>
                            <td>{{ Str::limit($item->deskripsi, 50) }}</td>
                            <td>
                                <a href="{{ route('admin.galeri.edit', $item->id) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.galeri.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
