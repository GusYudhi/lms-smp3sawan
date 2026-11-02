@extends('layouts.guest')

@section('title', 'Prestasi Siswa - ' . ($schoolData['name'] ?? 'SMPN 3 SAWAN'))

@section('content')
<!-- Page Header Banner -->
<section class="guest-page-banner">
    <div class="container">
        <span class="banner-eyebrow"><i class="fas fa-trophy"></i> Rekam Jejak Keunggulan</span>
        <h1 class="banner-title">Prestasi Siswa &amp; Sekolah</h1>
        <p class="banner-subtitle">Jejak langkah keberhasilan siswa-siswi {{ $schoolData['name'] ?? 'SMP Negeri 3 Sawan' }} di berbagai bidang kompetisi.</p>
    </div>
</section>

<section class="guest-section">
    <div class="container">
        <div class="row g-4">
            @forelse($prestasis as $prestasi)
            <div class="col-lg-4 col-md-6">
                <article class="prestasi-trophy-card hover-card">
                    <div class="trophy-media-wrap">
                        @if($prestasi->foto)
                            <img src="{{ asset('storage/' . $prestasi->foto) }}" alt="{{ $prestasi->judul }}">
                        @else
                            <div class="w-100 h-100 d-flex align-items-center justify-content-center">
                                <i class="fas fa-trophy fa-3x text-warning"></i>
                            </div>
                        @endif
                        <span class="trophy-badge-floating">
                            <i class="fas fa-medal"></i> Penghargaan
                        </span>
                    </div>
                    <div class="trophy-body">
                        <span class="trophy-date">
                            <i class="far fa-calendar-check me-1"></i> {{ $prestasi->tanggal->format('d F Y') }}
                        </span>
                        <h2 class="trophy-title">
                            <a href="{{ route('guest.prestasi.show', $prestasi->id) }}" class="stretched-link">
                                {{ $prestasi->judul }}
                            </a>
                        </h2>
                        <p class="trophy-desc">{{ Str::limit($prestasi->deskripsi, 110) }}</p>
                    </div>
                </article>
            </div>
            @empty
            <div class="col-12">
                <div class="card p-5 text-center text-muted">
                    <i class="fas fa-trophy fa-2x mb-3 text-warning"></i>
                    <p class="mb-0">Belum ada data prestasi yang dipublikasikan.</p>
                </div>
            </div>
            @endforelse
        </div>

        <div class="d-flex justify-content-center mt-5">
            {{ $prestasis->links() }}
        </div>
    </div>
</section>
@endsection
