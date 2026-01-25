@extends('layouts.app')

@section('title', 'Kegiatan Kokurikuler')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Kegiatan Kokurikuler</h1>
    </div>

    <div class="row">
        @forelse($kegiatans as $kegiatan)
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card shadow h-100">
                @if($kegiatan->foto)
                <img src="{{ asset('storage/' . $kegiatan->foto) }}" class="card-img-top" alt="{{ $kegiatan->nama }}" style="height: 200px; object-fit: cover;">
                @else
                <div class="bg-gray-200 d-flex align-items-center justify-content-center" style="height: 200px;">
                    <i class="fas fa-image fa-3x text-gray-400"></i>
                </div>
                @endif
                <div class="card-body">
                    <h5 class="card-title font-weight-bold text-primary">{{ $kegiatan->nama }}</h5>
                    <p class="text-muted small mb-2"><i class="fas fa-calendar-alt me-1"></i> {{ $kegiatan->tanggal->format('d F Y') }}</p>
                    <p class="card-text">{{ Str::limit($kegiatan->deskripsi, 100) }}</p>
                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modal{{ $kegiatan->id }}">
                        Lihat Detail
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="modal{{ $kegiatan->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $kegiatan->nama }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @if($kegiatan->foto)
                        <img src="{{ asset('storage/' . $kegiatan->foto) }}" class="img-fluid rounded mb-3 w-100" alt="{{ $kegiatan->nama }}">
                        @endif
                        <p class="text-muted"><i class="fas fa-calendar-alt me-1"></i> {{ $kegiatan->tanggal->format('d F Y') }}</p>
                        <div class="mt-3">
                            <p style="white-space: pre-line;">{{ $kegiatan->deskripsi }}</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="alert alert-info text-center">
                Belum ada data kegiatan kokurikuler.
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection
