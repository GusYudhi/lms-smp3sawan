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
        <div class="row g-4">
            @forelse($galeris as $item)
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 border hover-card overflow-hidden">
                    @if($item->tipe == 'video')
                        <video width="100%" controls class="guest-card-img bg-dark" style="object-fit: cover;">
                            <source src="{{ asset('storage/' . $item->file_path) }}" type="video/mp4">
                            Browser Anda tidak mendukung pemutar video.
                        </video>
                    @else
                        <img src="{{ asset('storage/' . $item->file_path) }}" class="guest-card-img" alt="{{ $item->judul ?? 'Dokumentasi Galeri' }}" loading="lazy">
                    @endif

                    <div class="card-body p-4 d-flex flex-column flex-grow-1">
                        @if($item->judul)
                            <h2 class="h6 card-title fw-bold mb-2">{{ $item->judul }}</h2>
                        @endif

                        @if($item->deskripsi)
                            <p class="card-text text-muted small mb-3">{{ $item->deskripsi }}</p>
                        @endif

                        <div class="d-flex justify-content-between align-items-center pt-2 border-top small text-muted mt-auto">
                            <span>
                                <i class="far fa-calendar-alt me-1 text-primary"></i> {{ \Carbon\Carbon::parse($item->created_at)->format('d M Y') }}
                            </span>
                            @if(isset($item->source))
                                <span class="badge bg-light text-dark border">{{ ucfirst($item->source) }}</span>
                            @endif
                        </div>
                    </div>
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
@endsection
