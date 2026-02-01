@extends('layouts.app')

@section('title', 'Kegiatan Kokurikuler')

@section('content')
<div class="container-fluid">
    @php
        $routePrefix = auth()->user()->isGuru() ? 'guru' : (auth()->user()->isKepalaSekolah() ? 'kepala-sekolah' : 'admin');
    @endphp
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Kegiatan Kokurikuler</h1>
        <a href="{{ route($routePrefix . '.kegiatan-kokurikuler.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Kegiatan
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
                            <th>Lampiran</th>
                            <th>Nama Kegiatan</th>
                            <th>Tanggal</th>
                            <th>Deskripsi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($kegiatans as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td class="text-center">
                                @if($item->tipe == 'foto' && $item->foto)
                                    <img src="{{ asset('storage/' . $item->foto) }}" alt="{{ $item->nama }}" width="100" class="img-thumbnail">
                                @elseif($item->tipe == 'pdf' && $item->foto)
                                    <a href="{{ asset('storage/' . $item->foto) }}" target="_blank" class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-file-pdf fa-2x"></i><br><small>PDF</small>
                                    </a>
                                @elseif($item->tipe == 'link' && $item->link)
                                    <a href="{{ $item->link }}" target="_blank" class="btn btn-sm btn-outline-info">
                                        <i class="fas fa-link fa-2x"></i><br><small>Link</small>
                                    </a>
                                @else
                                    <span class="text-muted">Tidak ada lampiran</span>
                                @endif
                            </td>
                            <td>{{ $item->nama }}</td>
                            <td>{{ $item->tanggal->format('d M Y') }}</td>
                            <td>{{ Str::limit($item->deskripsi, 50) }}</td>
                            <td>
                                <a href="{{ route($routePrefix . '.kegiatan-kokurikuler.edit', $item->id) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                            <form action="{{ route($routePrefix . '.kegiatan-kokurikuler.destroy', $item->id) }}" method="POST" class="d-inline delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
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