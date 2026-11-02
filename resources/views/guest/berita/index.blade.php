@extends('layouts.guest')

@section('title', 'Berita Sekolah - ' . ($schoolData['name'] ?? 'SMPN 3 SAWAN'))

@section('content')
<!-- Page Header Banner -->
<section class="guest-page-banner">
    <div class="container">
        <span class="banner-eyebrow"><i class="fas fa-newspaper"></i> Publikasi Resmi</span>
        <h1 class="banner-title">Berita &amp; Pengumuman Sekolah</h1>
        <p class="banner-subtitle">Informasi terkini seputar kegiatan akademik, kesiswaan, dan agenda {{ $schoolData['name'] ?? 'SMP Negeri 3 Sawan' }}.</p>
    </div>
</section>

<section class="guest-section">
    <div class="container">
        <div class="row g-4">
            @forelse($beritas as $berita)
            <div class="col-lg-4 col-md-6">
                <article class="card h-100 border hover-card overflow-hidden position-relative">
                    @if($berita->foto)
                        <img src="{{ asset('storage/' . $berita->foto) }}" class="guest-card-img" alt="{{ $berita->judul }}">
                    @else
                        <div class="guest-card-placeholder">
                            <i class="fas fa-newspaper fa-3x text-secondary"></i>
                        </div>
                    @endif
                    <div class="card-body p-4 d-flex flex-column">
                        <small class="text-muted mb-2 d-block fw-medium">
                            <i class="far fa-calendar-alt me-1 text-primary"></i> {{ $berita->tanggal->format('d F Y') }}
                        </small>
                        <h2 class="h5 card-title fw-bold mb-2">
                            <a href="{{ route('guest.berita.show', $berita->id) }}" class="text-decoration-none text-dark stretched-link">
                                {{ $berita->judul }}
                            </a>
                        </h2>
                        <p class="card-text text-muted small mb-3">{{ Str::limit(strip_tags($berita->konten), 115) }}</p>
                        <div class="mt-auto pt-2">
                            <span class="text-primary fw-bold small">Baca Selengkapnya <i class="fas fa-arrow-right ms-1"></i></span>
                        </div>
                    </div>
                </article>
            </div>
            @empty
            <div class="col-12">
                <div class="card p-5 text-center text-muted">
                    <i class="far fa-newspaper fa-2x mb-3 text-secondary"></i>
                    <p class="mb-0">Belum ada berita terbaru yang dipublikasikan.</p>
                </div>
            </div>
            @endforelse
        </div>

        <div class="d-flex justify-content-center mt-5">
            {{ $beritas->links() }}
        </div>
    </div>
</section>
@endsection
