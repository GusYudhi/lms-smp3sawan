@extends('layouts.guest')

@section('title', 'Galeri Sekolah - SMPN 3 SAWAN')

@section('content')
<div class="container">
    <div class="text-center mb-5">
        <h2 class="fw-bold text-primary">Galeri Sekolah</h2>
        <p class="text-muted">Dokumentasi kegiatan dan momen berharga di SMP Negeri 3 Sawan</p>
    </div>

    <div class="row" data-masonry='{"percentPosition": true }'>
        @forelse($galeris as $item)
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card border-0 shadow-sm hover-card">
                @if($item->tipe == 'video')
                    <video width="100%" controls class="card-img-top">
                        <source src="{{ asset('storage/' . $item->file_path) }}" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                @else
                    <img src="{{ asset('storage/' . $item->file_path) }}" class="card-img-top" alt="{{ $item->judul }}" style="object-fit: cover;">
                @endif

                @if($item->judul || $item->deskripsi)
                <div class="card-body">
                    @if($item->judul)
                        <h5 class="card-title fw-bold">{{ $item->judul }}</h5>
                    @endif

                    @if($item->deskripsi)
                        <p class="card-text text-muted small">{{ $item->deskripsi }}</p>
                    @endif

                    <small class="text-muted d-block mt-2">
                        <i class="fas fa-calendar-alt me-1"></i> {{ \Carbon\Carbon::parse($item->created_at)->format('d F Y') }}
                        @if(isset($item->source))
                            <span class="badge bg-secondary ms-2">{{ ucfirst($item->source) }}</span>
                        @endif
                    </small>
                </div>
                @endif
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <div class="text-muted">Belum ada galeri.</div>
        </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $galeris->links() }}
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/masonry-layout@4.2.2/dist/masonry.pkgd.min.js" integrity="sha384-GNFwBvfVxBkLMJpYMOABq3c+d3KnQxudP/mGPkzpZSTYykLBNsZEnG2D9G/X/+7D" crossorigin="anonymous" async></script>

<style>
.hover-card {
    transition: transform 0.3s ease;
}
.hover-card:hover {
    transform: translateY(-5px);
}
</style>
@endsection
