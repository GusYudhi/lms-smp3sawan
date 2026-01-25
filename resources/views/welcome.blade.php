@extends('layouts.guest')

@section('title', $schoolData['name'] ?? 'Selamat Datang' . ' - Learning Management System')

@section('content')
<!-- Hero Section -->
<div class="hero-section text-white text-center py-5" style="background: linear-gradient(135deg, #0d47a1 0%, #1565c0 100%);">
    <div class="container py-5">
        <div class="school-logo mb-4">
            <img src="{{ asset('assets/image/logo-sekolah-smpn3sawan.webp') }}" alt="Logo" width="100" height="100" class="bg-white rounded-circle p-2">
        </div>
        <h1 class="display-4 fw-bold mb-3">{{ $schoolData['name'] ?? 'SMPN 3 SAWAN' }}</h1>
        <p class="lead mb-4">Learning Management System</p>
        <p class="fs-5">Sistem pembelajaran terpadu untuk kemajuan pendidikan yang berkualitas</p>
    </div>
</div>

<div class="container my-5">
    <!-- Info Section (School Info & Visi Misi) -->
    <div class="row g-4 mb-5">
        <div class="col-lg-6">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body p-4">
                    <h4 class="fw-bold text-primary mb-3"><i class="fas fa-school me-2"></i>Tentang Sekolah</h4>
                    <p class="text-muted">
                        {{ $schoolData['name'] ?? 'SMPN 3 SAWAN' }} berdiri sejak tahun {{ $schoolData['tahun_berdiri'] ?? '-' }} dan telah terakreditasi {{ $schoolData['akreditasi'] ?? 'A' }}.
                        Kami berkomitmen untuk mencetak generasi penerus bangsa yang unggul dalam prestasi dan berakhlak mulia.
                    </p>
                    <div class="row mt-4">
                        <div class="col-md-6 mb-3">
                            <h6 class="fw-bold">Kepala Sekolah</h6>
                            <p class="text-muted">{{ $schoolData['kepala_sekolah'] ?? '-' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h6 class="fw-bold">NPSN</h6>
                            <p class="text-muted">{{ $schoolData['npsn'] ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body p-4">
                    <h4 class="fw-bold text-success mb-3"><i class="fas fa-bullseye me-2"></i>Visi & Misi</h4>
                    <div class="mb-3">
                        <h6 class="fw-bold">Visi</h6>
                        <p class="text-muted fst-italic">"{{ $schoolData['visi'] ?? '-' }}"</p>
                    </div>
                    <div>
                        <h6 class="fw-bold">Misi</h6>
                        <ul class="text-muted small ps-3 mb-0">
                            @if(isset($schoolData['misi']) && is_array($schoolData['misi']))
                                @foreach($schoolData['misi'] as $misi)
                                    <li>{{ $misi }}</li>
                                @endforeach
                            @else
                                <li>Menyelenggarakan pendidikan berkualitas.</li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Berita Section -->
    <section class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold text-primary border-start border-4 border-primary ps-3">Berita Terbaru</h3>
            <a href="{{ route('guest.berita.index') }}" class="btn btn-outline-primary btn-sm rounded-pill">Lihat Semua <i class="fas fa-arrow-right ms-1"></i></a>
        </div>
        <div class="row">
            @forelse($beritas as $berita)
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card h-100 border-0 shadow-sm hover-card">
                    @if($berita->foto)
                    <img src="{{ asset('storage/' . $berita->foto) }}" class="card-img-top" alt="{{ $berita->judul }}" style="height: 160px; object-fit: cover;">
                    @else
                    <div class="bg-light d-flex align-items-center justify-content-center" style="height: 160px;">
                        <i class="fas fa-newspaper fa-2x text-secondary"></i>
                    </div>
                    @endif
                    <div class="card-body p-3">
                        <small class="text-muted d-block mb-2">{{ $berita->tanggal->format('d M Y') }}</small>
                        <h6 class="card-title fw-bold mb-2">
                            <a href="{{ route('guest.berita.show', $berita->id) }}" class="text-decoration-none text-dark stretched-link">
                                {{ Str::limit($berita->judul, 40) }}
                            </a>
                        </h6>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center text-muted">Belum ada berita terbaru.</div>
            @endforelse
        </div>
    </section>

    <!-- Prestasi Section -->
    <section class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold text-warning border-start border-4 border-warning ps-3">Prestasi Siswa</h3>
            <a href="{{ route('guest.prestasi.index') }}" class="btn btn-outline-warning btn-sm rounded-pill text-dark">Lihat Semua <i class="fas fa-arrow-right ms-1"></i></a>
        </div>
        <div class="row">
            @forelse($prestasis as $prestasi)
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card h-100 border-0 shadow-sm hover-card">
                    @if($prestasi->foto)
                    <img src="{{ asset('storage/' . $prestasi->foto) }}" class="card-img-top" alt="{{ $prestasi->judul }}" style="height: 160px; object-fit: cover;">
                    @else
                    <div class="bg-light d-flex align-items-center justify-content-center" style="height: 160px;">
                        <i class="fas fa-trophy fa-2x text-warning"></i>
                    </div>
                    @endif
                    <div class="card-body p-3">
                        <small class="text-muted d-block mb-2">{{ $prestasi->tanggal->format('d M Y') }}</small>
                        <h6 class="card-title fw-bold mb-2">
                            <a href="{{ route('guest.prestasi.show', $prestasi->id) }}" class="text-decoration-none text-dark stretched-link">
                                {{ Str::limit($prestasi->judul, 40) }}
                            </a>
                        </h6>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center text-muted">Belum ada data prestasi.</div>
            @endforelse
        </div>
    </section>

    <!-- Maps Location -->
    @if(isset($schoolData['maps_latitude']) && isset($schoolData['maps_longitude']))
    <section class="mb-5">
        <div class="card border-0 shadow-sm overflow-hidden">
            <div class="card-body p-0">
                <iframe
                    src="https://maps.google.com/maps?q={{ $schoolData['maps_latitude'] }},{{ $schoolData['maps_longitude'] }}&hl=id&z=16&output=embed"
                    width="100%"
                    height="350"
                    style="border:0;"
                    allowfullscreen=""
                    loading="lazy">
                </iframe>
            </div>
        </div>
    </section>
    @endif

    <!-- Kotak Saran -->
    <section>
        <div class="card border-0 shadow-sm bg-primary text-white">
            <div class="card-body p-5 text-center">
                <h3 class="fw-bold mb-3">Punya Saran atau Masukan?</h3>
                <p class="mb-4 text-white-50">Kami sangat menghargai setiap masukan untuk kemajuan sekolah kami.</p>
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show text-dark text-start" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        @endif
                        <form action="{{ route('guest.saran.store') }}" method="POST" class="text-start">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="nama_pengirim" placeholder="Nama Lengkap" required>
                                </div>
                                <div class="col-md-6">
                                    <input type="email" class="form-control" name="email_pengirim" placeholder="Email (Opsional)">
                                </div>
                                <div class="col-12">
                                    <textarea class="form-control" name="isi_saran" rows="3" placeholder="Tulis saran Anda di sini..." required></textarea>
                                </div>
                                <div class="col-12 text-center">
                                    <button type="submit" class="btn btn-light text-primary px-5 fw-bold mt-2">Kirim Saran</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
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
