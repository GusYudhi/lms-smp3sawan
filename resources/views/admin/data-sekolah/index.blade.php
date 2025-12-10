@extends('layouts.app')

@section('title', 'Data Sekolah')

@section('content')
<div class="container-fluid">
    <!-- Welcome Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card card-modern border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px; overflow: hidden;">
                                    <img src="{{ asset('assets/image/LogoSMP3SAWAN.webp') }}" alt="Logo SMPN 3 SAWAN" style="width:100%; height:100%; object-fit:cover;">
                                </div>
                                <div>
                                    <h1 class="h3 mb-1 text-high-contrast">{{ $schoolData['name'] ?? 'SMPN 3 SAWAN' }}</h1>
                                    <p class="text-subtle mb-0 fw-medium">Portal Informasi Sekolah</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            @auth
                                <a href="{{ route('home') }}" class="btn btn-outline-secondary me-2 shadow-sm">
                                    <i class="fas fa-tachometer-alt me-1"></i>
                                    Dashboard
                                </a>
                                @if(auth()->user()->isAdmin())
                                    <a href="{{ route('school.edit') }}" class="btn btn-primary shadow-sm">
                                        <i class="fas fa-edit me-1"></i>
                                        Edit Data
                                    </a>
                                @elseif(auth()->user()->isKepalaSekolah())
                                    <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#noAccessModal">
                                        <i class="fas fa-edit me-1"></i>
                                        Edit Data
                                    </button>
                                @endif
                            @else
                                <a href="{{ route('login') }}" class="btn btn-primary shadow-sm">
                                    <i class="fas fa-sign-in-alt me-1"></i>
                                    Login
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-check-circle text-success me-2 fs-5"></i>
                        <div class="flex-grow-1">{{ session('success') }}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- School Information Grid -->
    <div class="row g-4">
        <!-- School Identity Card -->
        <div class="col-xl-6 col-12">
            <div class="card card-stats h-100 hover-card">
                <div class="card-header bg-light border-bottom-0 py-3">
                    <h5 class="card-title mb-0 text-high-contrast fw-semibold">
                        <i class="fas fa-id-card text-primary me-2"></i>Identitas Sekolah
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="border-start border-primary border-3 ps-3">
                                <small class="text-subtle fw-medium text-uppercase">Nama Sekolah</small>
                                <div class="text-high-contrast fw-semibold">{{ $schoolData['name'] ?? 'SMPN 3 SAWAN' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border-start border-success border-3 ps-3">
                                <small class="text-subtle fw-medium text-uppercase">Kepala Sekolah</small>
                                <div class="text-high-contrast fw-semibold">{{ $schoolData['kepala_sekolah'] ?? 'Drs. I Made Sutrisna, M.Pd.' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border-start border-info border-3 ps-3">
                                <small class="text-subtle fw-medium text-uppercase">NPSN</small>
                                <div class="text-high-contrast fw-semibold">{{ $schoolData['npsn'] ?? '50100123' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border-start border-warning border-3 ps-3">
                                <small class="text-subtle fw-medium text-uppercase">Akreditasi</small>
                                <div class="mt-1">
                                    @php $akreditasi = $schoolData['akreditasi'] ?? 'A' @endphp
                                    <span class="badge
                                        @if($akreditasi === 'A') bg-success-subtle text-success
                                        @elseif($akreditasi === 'B') bg-warning-subtle text-warning
                                        @else bg-danger-subtle text-danger
                                        @endif border fw-medium px-3 py-2">
                                        {{ $akreditasi }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border-start border-secondary border-3 ps-3">
                                <small class="text-subtle fw-medium text-uppercase">Tahun Berdiri</small>
                                <div class="text-high-contrast fw-semibold">{{ $schoolData['tahun_berdiri'] ?? '1985' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border-start border-dark border-3 ps-3">
                                <small class="text-subtle fw-medium text-uppercase">Website</small>
                                <div class="mt-1">
                                    @if($schoolData['website'] ?? null)
                                        <a href="http://{{ $schoolData['website'] }}" target="_blank"
                                           class="text-decoration-none text-primary fw-medium">
                                            {{ $schoolData['website'] }}
                                            <i class="fas fa-external-link-alt ms-1 fs-6"></i>
                                        </a>
                                    @else
                                        <span class="text-muted fst-italic">Tidak tersedia</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vision & Mission Card -->
        <div class="col-xl-6 col-12">
            <div class="card card-stats h-100 hover-card">
                <div class="card-header bg-light border-bottom-0 py-3">
                    <h5 class="card-title mb-0 text-high-contrast fw-semibold">
                        <i class="fas fa-bullseye text-primary me-2"></i>Visi & Misi
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Vision Section -->
                    <div class="mb-4">
                        <h6 class="text-primary fw-bold mb-3 d-flex align-items-center">
                            <i class="fas fa-eye me-2"></i>Visi
                        </h6>
                        <div class="bg-primary bg-opacity-10 border border-primary border-opacity-25 rounded-3 p-3">
                            <p class="mb-0 text-high-contrast lh-base">
                                {{ $schoolData['visi'] ?? 'Menjadi sekolah unggulan yang menghasilkan lulusan berkualitas, berkarakter, dan berdaya saing global.' }}
                            </p>
                        </div>
                    </div>

                    <!-- Mission Section -->
                    <div>
                        <h6 class="text-success fw-bold mb-3 d-flex align-items-center">
                            <i class="fas fa-tasks me-2"></i>Misi
                        </h6>
                        <div class="bg-success bg-opacity-10 border border-success border-opacity-25 rounded-3 p-3">
                            @php
                                $defaultMissions = [
                                    'Menyelenggarakan pendidikan yang berkualitas dan berstandar nasional',
                                    'Mengembangkan potensi peserta didik secara optimal',
                                    'Membangun karakter yang berakhlak mulia dan berbudi pekerti luhur',
                                    'Menciptakan lingkungan belajar yang kondusif dan menyenangkan',
                                    'Meningkatkan profesionalisme tenaga pendidik dan kependidikan'
                                ];
                                $missions = $schoolData['misi'] ?? $defaultMissions;
                                if (is_string($missions)) {
                                    $missions = json_decode($missions, true) ?? [$missions];
                                }
                            @endphp
                            <ul class="list-unstyled mb-0">
                                @foreach($missions as $index => $misi)
                                    <li class="d-flex align-items-start mb-2">
                                        <span class="badge bg-success me-3 mt-1 flex-shrink-0">{{ $index + 1 }}</span>
                                        <span class="text-high-contrast lh-base">{{ $misi }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contact Information & Map -->
    <div class="row g-4 mt-2">
        <!-- Contact Information -->
        <div class="col-lg-6 col-12">
            <div class="card card-stats h-100 hover-card">
                <div class="card-header bg-light border-bottom-0 py-3">
                    <h5 class="card-title mb-0 text-high-contrast fw-semibold">
                        <i class="fas fa-address-book text-primary me-2"></i>Informasi Kontak
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="d-flex align-items-start">
                                <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3 flex-shrink-0">
                                    <i class="fas fa-map-marker-alt text-primary"></i>
                                </div>
                                <div>
                                    <small class="text-subtle fw-medium text-uppercase">Alamat</small>
                                    <div class="text-high-contrast fw-medium">
                                        {{ $schoolData['alamat'] ?? 'Jl. Raya Sawan No. 123, Sawan, Buleleng, Bali' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-start">
                                <div class="bg-success bg-opacity-10 rounded-circle p-2 me-3 flex-shrink-0">
                                    <i class="fas fa-phone text-success"></i>
                                </div>
                                <div>
                                    <small class="text-subtle fw-medium text-uppercase">Telepon</small>
                                    <div class="text-high-contrast fw-medium">
                                        <a href="tel:{{ $schoolData['telepon'] ?? '(0362) 123456' }}"
                                           class="text-decoration-none text-high-contrast">
                                            {{ $schoolData['telepon'] ?? '(0362) 123456' }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-start">
                                <div class="bg-info bg-opacity-10 rounded-circle p-2 me-3 flex-shrink-0">
                                    <i class="fas fa-envelope text-info"></i>
                                </div>
                                <div>
                                    <small class="text-subtle fw-medium text-uppercase">Email</small>
                                    <div class="text-high-contrast fw-medium">
                                        <a href="mailto:{{ $schoolData['email'] ?? 'info@smp3sawan.sch.id' }}"
                                           class="text-decoration-none text-high-contrast">
                                            {{ $schoolData['email'] ?? 'info@smp3sawan.sch.id' }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Maps Section -->
        <div class="col-lg-6 col-12">
            <div class="card card-stats h-100 hover-card">
                <div class="card-header bg-light border-bottom-0 py-3">
                    <h5 class="card-title mb-0 text-high-contrast fw-semibold">
                        <i class="fas fa-map text-primary me-2"></i>Lokasi Sekolah
                    </h5>
                </div>
                <div class="card-body">
                    <div class="position-relative rounded-3 overflow-hidden mb-3">
                        @php
                            $latitude = $schoolData['maps_latitude'] ?? '-8.1234567';
                            $longitude = $schoolData['maps_longitude'] ?? '115.1234567';
                            $schoolName = urlencode($schoolData['name'] ?? 'SMPN 3 SAWAN');
                        @endphp
                        <iframe
                            src="https://maps.google.com/maps?q={{ $latitude }},{{ $longitude }}&hl=id&z=16&output=embed"
                            width="100%"
                            height="300"
                            style="border:0;"
                            allowfullscreen=""
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                            class="rounded-3">
                        </iframe>
                        <!-- Hover Overlay -->
                        <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center opacity-0 hover-overlay transition-all"
                             style="background: rgba(0,0,0,0.7);">
                            <div class="text-center text-white">
                                <i class="fas fa-search-plus fs-1 mb-2"></i>
                                <p class="mb-0 fw-medium">Klik untuk memperbesar</p>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <div class="row g-2">
                            <div class="col-sm-6">
                                <a href="https://www.google.com/maps/search/?api=1&query={{ $latitude }},{{ $longitude }}"
                                   target="_blank"
                                   class="btn btn-outline-primary btn-sm w-100 d-flex align-items-center justify-content-center">
                                    <i class="fas fa-external-link-alt me-2"></i>
                                    Buka di Maps
                                </a>
                            </div>
                            <div class="col-sm-6">
                                <a href="https://www.google.com/maps/dir/?api=1&destination={{ $latitude }},{{ $longitude }}"
                                   target="_blank"
                                   class="btn btn-primary btn-sm w-100 d-flex align-items-center justify-content-center">
                                    <i class="fas fa-route me-2"></i>
                                    Petunjuk Arah
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: No Access for Kepala Sekolah -->
<div class="modal fade" id="noAccessModal" tabindex="-1" aria-labelledby="noAccessModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-warning bg-opacity-10 border-bottom-0">
                <h5 class="modal-title text-dark" id="noAccessModalLabel">
                    <i class="fas fa-exclamation-triangle me-2 text-warning"></i>Akses Terbatas
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div class="mb-3">
                    <i class="fas fa-lock text-warning" style="font-size: 4rem;"></i>
                </div>
                <h6 class="fw-bold mb-3">Edit Data Hanya Bisa Melalui Akun Admin</h6>
                <p class="text-muted mb-0">
                    Silahkan hubungi administrator untuk mengubah data sekolah ini.
                </p>
            </div>
            <div class="modal-footer border-top-0 justify-content-center">
                <button type="button" class="btn btn-primary shadow-sm" data-bs-dismiss="modal">
                    <i class="fas fa-check me-1"></i>Mengerti
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.hover-overlay {
    transition: opacity 0.3s ease;
}
.position-relative:hover .hover-overlay {
    opacity: 1 !important;
}
</style>

<script>
// Auto hide alert after 5 seconds
setTimeout(function() {
    const alert = document.querySelector('.alert');
    if (alert) {
        alert.style.transition = 'opacity 0.5s ease';
        alert.style.opacity = '0';
        setTimeout(() => alert.style.display = 'none', 500);
    }
}, 5000);
</script>
@endsection
