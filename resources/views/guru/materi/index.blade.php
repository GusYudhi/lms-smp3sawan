@extends('layouts.app')

@section('title', 'Materi Pelajaran')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Materi Pelajaran</h1>
        <a href="{{ route('guru.materi.create') }}" class="btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Upload Materi
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
                            <th>Mata Pelajaran</th>
                            <th>Kelas</th>
                            <th>Judul Materi</th>
                            <th>Tipe</th>
                            <th>Deskripsi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($materis as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->mataPelajaran->nama_mapel }}</td>
                            <td>
                                @if($item->kelas)
                                    {{ $item->kelas->full_name }}
                                @elseif($item->tingkat)
                                    Semua Kelas {{ $item->tingkat }}
                                @else
                                    Semua Kelas
                                @endif
                            </td>
                            <td>{{ $item->judul }}</td>
                            <td>
                                @if($item->tipe == 'link')
                                    <span class="badge bg-info"><i class="fas fa-link"></i> Link</span>
                                @else
                                    <span class="badge bg-secondary"><i class="fas fa-file"></i> File</span>
                                @endif
                            </td>
                            <td>{{ Str::limit($item->deskripsi, 50) }}</td>
                            <td>
                                @if($item->tipe == 'link')
                                    <a href="{{ $item->link }}" class="btn btn-info btn-sm" target="_blank">
                                        <i class="fas fa-external-link-alt"></i> Buka Link
                                    </a>
                                @elseif($item->file_path)
                                    <a href="{{ asset('storage/' . $item->file_path) }}" class="btn btn-primary btn-sm" target="_blank">
                                        <i class="fas fa-download"></i> Download
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
        </div>
    </div>
</div>
@endsection
