@extends('layouts.app')

@section('title', 'Kotak Saran')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Kotak Saran</h1>
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
                            <th>Tanggal</th>
                            <th>Pengirim</th>
                            <th>Email</th>
                            <th>Saran</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sarans as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->created_at->format('d M Y H:i') }}</td>
                            <td>{{ $item->nama_pengirim }}</td>
                            <td>{{ $item->email_pengirim }}</td>
                            <td>{{ $item->isi_saran }}</td>
                            <td>
                                @if($item->status == 'belum_dibaca')
                                    <span class="badge bg-warning text-dark">Belum Dibaca</span>
                                @else
                                    <span class="badge bg-success">Sudah Dibaca</span>
                                @endif
                            </td>
                            <td>
                                @if($item->status == 'belum_dibaca')
                                <form action="{{ route('admin.saran.update-status', $item->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-info btn-sm" title="Tandai Sudah Dibaca">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                                @endif
                                            <form action="{{ route('admin.saran.destroy', $item->id) }}" method="POST" class="d-inline delete-form" data-message="Yakin ingin menghapus saran ini?">
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
