@extends('layouts.guest')

@section('title', 'Galeri Sekolah - ' . ($schoolData['name'] ?? 'SMPN 3 SAWAN'))

@section('content')
<!-- Page Header Banner -->
<section class="guest-page-banner">
    <div class="container">
        <span class="banner-eyebrow"><i class="fas fa-camera"></i> Arsip Visual</span>
        <h1 class="banner-title">Galeri Dokumentasi Sekolah</h1>
        <p class="banner-subtitle">Kumpulan momen kegiatan pembelajaran, ekstrakurikuler, dan acara di {{ $schoolData['name'] ?? 'SMP Negeri 3 Sawan' }}.</p>
    </div>
</section>

<section class="guest-section">
    <div class="container">
        <div class="row g-4" data-masonry='{"percentPosition": true }'>
            @forelse($galeris as $item)
            <div class="col-lg-4 col-md-6">
                <div class="card border hover-card overflow-hidden">
                    @if($item->tipe == 'video')
                        <video width="100%" controls class="card-img-top">
                            <source src="{{ asset('storage/' . $item->file_path) }}" type="video/mp4">
                            Browser Anda tidak mendukung pemutar video.
                        </video>
                    @else
                        <img src="{{ asset('storage/' . $item->file_path) }}" class="card-img-top" alt="{{ $item->judul ?? 'Dokumentasi Galeri' }}">
                    @endif

                    @if($item->judul || $item->deskripsi)
                    <div class="card-body p-4">
                        @if($item->judul)
                            <h2 class="h6 card-title fw-bold mb-2">{{ $item->judul }}</h2>
                        @endif

                        @if($item->deskripsi)
                            <p class="card-text text-muted small mb-3">{{ $item->deskripsi }}</p>
                        @endif

                        <div class="d-flex justify-content-between align-items-center pt-2 border-top small text-muted">
                            <span>
                                <i class="far fa-calendar-alt me-1"></i> {{ \Carbon\Carbon::parse($item->created_at)->format('d M Y') }}
                            </span>
                            @if(isset($item->source))
                                <span class="badge bg-light border">{{ ucfirst($item->source) }}</span>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="card p-5 text-center text-muted">
                    <i class="fas fa-images fa-2x mb-3 text-secondary"></i>
                    <p class="mb-0">Belum ada dokumentasi galeri yang ditambahkan.</p>
                </div>
            </div>
            @endforelse
        </div>

        <div class="d-flex justify-content-center mt-5">
            {{ $galeris->links() }}
        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/masonry-layout@4.2.2/dist/masonry.pkgd.min.js" integrity="sha384-GNFwBvfVxBkLMJpYMOABq3c+d3KnQxudP/mGPkzpZSTYykLBNsZEnG2D9G/X/+7D" crossorigin="anonymous" async></script>
@endsection
