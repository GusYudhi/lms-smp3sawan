@extends('layouts.guest')

@section('title', $prestasi->judul . ' - ' . ($schoolData['name'] ?? 'SMPN 3 SAWAN'))

@section('content')
<!-- Page Header Banner -->
<section class="guest-page-banner">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('welcome') }}">Beranda</a></li>
                <li class="breadcrumb-item"><a href="{{ route('guest.prestasi.index') }}">Prestasi</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ Str::limit($prestasi->judul, 36) }}</li>
            </ol>
        </nav>
        <h1 class="banner-title">{{ $prestasi->judul }}</h1>
        <div class="d-flex flex-wrap align-items-center gap-3 text-light opacity-75 small mt-2">
            <span><i class="far fa-calendar-check me-1"></i> {{ $prestasi->tanggal->format('d F Y') }}</span>
            <span>&bull;</span>
            <span><i class="fas fa-medal me-1 text-warning"></i> Capaian Prestasi</span>
        </div>
    </div>
</section>

<section class="guest-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <article class="card border overflow-hidden">
                    @if($prestasi->foto)
                        <img src="{{ asset('storage/' . $prestasi->foto) }}" class="card-img-top" alt="{{ $prestasi->judul }}">
                    @endif
                    <div class="card-body p-4 p-md-5">
                        <div class="guest-article-content text-break">
                            {!! nl2br(e($prestasi->deskripsi)) !!}
                        </div>
                    </div>
                    <div class="card-footer bg-white border-top py-3 px-4 px-md-5">
                        <a href="{{ route('guest.prestasi.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar Prestasi
                        </a>
                    </div>
                </article>
            </div>
        </div>
    </div>
</section>
@endsection
