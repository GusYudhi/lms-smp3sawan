@extends('layouts.guest')

@section('title', $prestasi->judul . ' - SMPN 3 SAWAN')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('welcome') }}">Beranda</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('guest.prestasi.index') }}">Prestasi</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ Str::limit($prestasi->judul, 20) }}</li>
                </ol>
            </nav>

            <div class="card border-0 shadow-sm overflow-hidden">
                @if($prestasi->foto)
                <img src="{{ asset('storage/' . $prestasi->foto) }}" class="card-img-top" alt="{{ $prestasi->judul }}">
                @endif
                <div class="card-body p-4 p-md-5">
                    <div class="mb-3 text-muted">
                        <i class="fas fa-calendar-alt me-1"></i> {{ $prestasi->tanggal->format('d F Y') }}
                    </div>

                    <h1 class="card-title fw-bold mb-4">{{ $prestasi->judul }}</h1>

                    <div class="card-text text-break" style="line-height: 1.8;">
                        {!! nl2br(e($prestasi->deskripsi)) !!}
                    </div>
                </div>
                <div class="card-footer bg-white border-0 pb-4 px-4 px-md-5">
                    <a href="{{ route('guest.prestasi.index') }}" class="btn btn-outline-primary rounded-pill">
                        <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar Prestasi
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
