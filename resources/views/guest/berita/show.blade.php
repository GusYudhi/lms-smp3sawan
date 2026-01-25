@extends('layouts.guest')

@section('title', $berita->judul . ' - SMPN 3 SAWAN')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('welcome') }}">Beranda</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('guest.berita.index') }}">Berita</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ Str::limit($berita->judul, 20) }}</li>
                </ol>
            </nav>

            <div class="card border-0 shadow-sm overflow-hidden">
                @if($berita->foto)
                <img src="{{ asset('storage/' . $berita->foto) }}" class="card-img-top" alt="{{ $berita->judul }}">
                @endif
                <div class="card-body p-4 p-md-5">
                    <div class="mb-3 text-muted">
                        <span class="me-3"><i class="fas fa-calendar-alt me-1"></i> {{ $berita->tanggal->format('d F Y') }}</span>
                        <span><i class="fas fa-user me-1"></i> {{ $berita->penulis->name ?? 'Admin' }}</span>
                    </div>

                    <h1 class="card-title fw-bold mb-4">{{ $berita->judul }}</h1>

                    <div class="card-text text-break" style="line-height: 1.8;">
                        {!! nl2br(e($berita->konten)) !!}
                    </div>
                </div>
                <div class="card-footer bg-white border-0 pb-4 px-4 px-md-5">
                    <a href="{{ route('guest.berita.index') }}" class="btn btn-outline-primary rounded-pill">
                        <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar Berita
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
