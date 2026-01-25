@extends('layouts.guest')

@section('title', 'Prestasi Siswa - SMPN 3 SAWAN')

@section('content')
<div class="container">
    <div class="text-center mb-5">
        <h2 class="fw-bold text-primary">Prestasi Siswa</h2>
        <p class="text-muted">Jejak langkah keberhasilan siswa-siswi SMP Negeri 3 Sawan</p>
    </div>

    <div class="row">
        @forelse($prestasis as $prestasi)
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100 border-0 shadow-sm hover-card">
                @if($prestasi->foto)
                <img src="{{ asset('storage/' . $prestasi->foto) }}" class="card-img-top" alt="{{ $prestasi->judul }}" style="height: 200px; object-fit: cover;">
                @else
                <div class="bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                    <i class="fas fa-trophy fa-3x text-warning"></i>
                </div>
                @endif
                <div class="card-body">
                    <small class="text-muted mb-2 d-block">
                        <i class="fas fa-calendar-alt me-1"></i> {{ $prestasi->tanggal->format('d F Y') }}
                    </small>
                    <h5 class="card-title fw-bold">
                        <a href="{{ route('guest.prestasi.show', $prestasi->id) }}" class="text-decoration-none text-dark stretched-link">
                            {{ $prestasi->judul }}
                        </a>
                    </h5>
                    <p class="card-text text-muted small">{{ Str::limit($prestasi->deskripsi, 100) }}</p>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <div class="text-muted">Belum ada data prestasi.</div>
        </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $prestasis->links() }}
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
