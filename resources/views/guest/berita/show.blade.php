@extends('layouts.guest')

@section('title', $berita->judul . ' - ' . ($schoolData['name'] ?? 'SMPN 3 SAWAN'))

@section('content')
<!-- Page Header Banner -->
<section class="guest-page-banner">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('welcome') }}">Beranda</a></li>
                <li class="breadcrumb-item"><a href="{{ route('guest.berita.index') }}">Berita</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ Str::limit($berita->judul, 36) }}</li>
            </ol>
        </nav>
        <h1 class="banner-title">{{ $berita->judul }}</h1>
        <div class="d-flex flex-wrap align-items-center gap-3 text-light opacity-75 small mt-2">
            <span><i class="far fa-calendar-alt me-1"></i> {{ $berita->tanggal->format('d F Y') }}</span>
            <span>&bull;</span>
            <span><i class="far fa-user me-1"></i> {{ $berita->penulis->name ?? 'Admin Sekolah' }}</span>
        </div>
    </div>
</section>

<section class="guest-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <article class="card border overflow-hidden">
                    @if($berita->foto)
                        <img src="{{ asset('storage/' . $berita->foto) }}" class="card-img-top" alt="{{ $berita->judul }}">
                    @endif
                    <div class="card-body p-4 p-md-5">
                        <div class="guest-article-content text-break">
                            {!! nl2br(e($berita->konten)) !!}
                        </div>
                    </div>
                    @if($berita->images && $berita->images->count() > 0)
                        <div class="px-4 px-md-5 pb-4">
                            <h5 class="fw-bold mb-3 text-dark"><i class="fas fa-images text-primary me-2"></i>Galeri Foto Tambahan</h5>
                            <div class="row g-3">
                                @foreach($berita->images as $img)
                                    <div class="col-6 col-md-4">
                                        <a href="{{ asset('storage/' . $img->foto) }}" target="_blank" class="d-block rounded-3 overflow-hidden shadow-sm border border-light-subtle">
                                            <img src="{{ asset('storage/' . $img->foto) }}" class="img-fluid w-100 object-fit-cover" style="height: 160px;" alt="Dokumentasi Berita">
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    <div class="card-footer bg-white border-top py-3 px-4 px-md-5">
                        <a href="{{ route('guest.berita.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar Berita
                        </a>
                    </div>
                </article>
            </div>
        </div>
    </div>
</section>
@endsection
