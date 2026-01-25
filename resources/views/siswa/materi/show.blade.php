@extends('layouts.app')

@section('title', 'Daftar Materi - ' . $mapel->nama_mapel)

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Materi: {{ $mapel->nama_mapel }}</h1>
        <a href="{{ route('siswa.materi.index') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    <div class="row">
        @forelse($materis as $materi)
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4 h-100">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">{{ $materi->judul }}</h6>
                    <div class="dropdown no-arrow">
                        <small class="text-muted">{{ $materi->created_at->format('d M Y') }}</small>
                    </div>
                </div>
                <div class="card-body">
                    <p class="mb-2"><strong>Guru:</strong> {{ $materi->guru->name }}</p>
                    <p class="mb-3">{{ $materi->deskripsi ?? 'Tidak ada deskripsi.' }}</p>

                    <div class="mt-auto">
                        @if($materi->tipe == 'link')
                            <a href="{{ $materi->link }}" class="btn btn-info btn-block" target="_blank">
                                <i class="fas fa-external-link-alt"></i> Buka Link Materi
                            </a>
                        @elseif($materi->file_path)
                            <a href="{{ asset('storage/' . $materi->file_path) }}" class="btn btn-primary btn-block" target="_blank">
                                <i class="fas fa-download"></i> Download Materi
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="alert alert-info text-center">
                Belum ada materi untuk mata pelajaran ini.
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection
