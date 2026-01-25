@extends('layouts.guest')

@section('title', 'Berita Sekolah - SMPN 3 SAWAN')

@section('content')
<div class="container">
    <div class="text-center mb-5">
        <h2 class="fw-bold text-primary">Berita Sekolah</h2>
        <p class="text-muted">Informasi terbaru seputar kegiatan dan pengumuman sekolah</p>
    </div>

    <div class="row">
        @forelse($beritas as $berita)
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100 border-0 shadow-sm hover-card">
                @if($berita->foto)
                <img src="{{ asset('storage/' . $berita->foto) }}" class="card-img-top" alt="{{ $berita->judul }}" style="height: 200px; object-fit: cover;">
                @else
                <div class="bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                    <i class="fas fa-newspaper fa-3x text-secondary"></i>
                </div>
                @endif
                <div class="card-body">
                    <small class="text-muted mb-2 d-block">
                        <i class="fas fa-calendar-alt me-1"></i> {{ $berita->tanggal->format('d F Y') }}
                    </small>
                    <h5 class="card-title fw-bold">
                        <a href="{{ route('guest.berita.show', $berita->id) }}" class="text-decoration-none text-dark stretched-link">
                            {{ $berita->judul }}
                        </a>
                    </h5>
                    <p class="card-text text-muted small">{{ Str::limit(strip_tags($berita->konten), 100) }}</p>
                </div>
                <div class="card-footer bg-white border-0 pt-0">
                    <small class="text-primary fw-bold">Baca Selengkapnya <i class="fas fa-arrow-right ms-1"></i></small>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <div class="text-muted">Belum ada berita terbaru.</div>
        </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $beritas->links() }}
    </div>
</div>

<style>
.hover-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.hover-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
}
</style>
@endsection
