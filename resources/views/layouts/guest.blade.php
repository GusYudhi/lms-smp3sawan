<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', $title ?? ($schoolData['name'] ?? 'SMPN 3 SAWAN'))</title>

    <!-- Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

    <link rel="icon" type="image/webp" href="{{ asset('assets/image/logo-sekolah-smpn3sawan.webp') }}">

    <!-- Bootstrap & Material Design 3 CSS via Vite -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>

<body class="layout-guest">
    <!-- Adaptive M3 Header -->
    <nav id="guestNavbar" class="navbar navbar-expand-lg guest-header fixed-top" aria-label="Navigasi Utama">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('welcome') }}">
                <span class="brand-logo-wrap">
                    <img src="{{ asset('assets/image/logo-sekolah-smpn3sawan.webp') }}" alt="Logo {{ $schoolData['name'] ?? 'SMPN 3 SAWAN' }}" width="36" height="36">
                </span>
                <span>
                    <span class="brand-title d-block">{{ $schoolData['name'] ?? 'SMPN 3 SAWAN' }}</span>
                    <span class="brand-subtitle d-none d-sm-block">Kabupaten Buleleng, Bali</span>
                </span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Buka menu navigasi">
                <i class="fas fa-bars toggler-icon-bars"></i>
            </button>

            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav align-items-lg-center gap-lg-1">
                    <li class="nav-item">
                        <a class="nav-link {{ Route::currentRouteName() == 'welcome' ? 'active' : '' }}" href="{{ route('welcome') }}">Beranda</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ Route::currentRouteName() == 'guest.guru-staff' ? 'active' : '' }}" href="#" id="profilDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Profil
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="profilDropdown">
                            <li>
                                <a class="dropdown-item" href="{{ route('welcome') }}#profil-sekolah">
                                    <i class="fas fa-school me-2 text-primary"></i>Tentang & Visi Misi
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ Route::currentRouteName() == 'guest.guru-staff' ? 'active' : '' }}" href="{{ route('guest.guru-staff') }}">
                                    <i class="fas fa-chalkboard-teacher me-2 text-primary"></i>Guru & Staf Pegawai
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Str::startsWith(Route::currentRouteName(), 'guest.berita') ? 'active' : '' }}" href="{{ route('guest.berita.index') }}">Berita</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Str::startsWith(Route::currentRouteName(), 'guest.prestasi') ? 'active' : '' }}" href="{{ route('guest.prestasi.index') }}">Prestasi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Route::currentRouteName() == 'guest.galeri' ? 'active' : '' }}" href="{{ route('guest.galeri') }}">Galeri</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Route::currentRouteName() == 'guest.kontak' ? 'active' : '' }}" href="{{ route('guest.kontak') }}">Kontak</a>
                    </li>
                    @auth
                        <li class="nav-item ms-lg-2 mt-2 mt-lg-0">
                            <a href="{{ url('/home') }}" class="btn btn-nav-cta">
                                <i class="fas fa-th-large me-1"></i> Dashboard
                            </a>
                        </li>
                    @else
                        <li class="nav-item ms-lg-2 mt-2 mt-lg-0">
                            <a href="{{ route('login') }}" class="btn btn-nav-cta">
                                <i class="fas fa-sign-in-alt me-1"></i> Masuk LMS
                            </a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="guest-footer pt-5 pb-4">
        <div class="container">
            <div class="row g-4 pb-4">
                <div class="col-lg-5 col-md-6">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <img src="{{ asset('assets/image/logo-sekolah-smpn3sawan.webp') }}" alt="Logo {{ $schoolData['name'] ?? 'SMPN 3 SAWAN' }}" class="footer-brand-logo">
                        <div>
                            <h5 class="fw-bold text-white mb-0">{{ $schoolData['name'] ?? 'SMPN 3 SAWAN' }}</h5>
                            <span class="small text-secondary">NPSN {{ $schoolData['npsn'] ?? '50100123' }} • Terakreditasi {{ $schoolData['akreditasi'] ?? 'A' }}</span>
                        </div>
                    </div>
                    <p class="small text-secondary mb-3 pe-lg-4">
                        {{ $schoolData['visi'] ?? 'Mewujudkan generasi yang cerdas, berkarakter, dan berwawasan global berlandaskan nilai-nilai luhur.' }}
                    </p>
                </div>

                <div class="col-lg-3 col-md-6">
                    <h6 class="footer-heading">Jelajahi</h6>
                    <ul class="list-unstyled mb-0">
                        <li><a href="{{ route('welcome') }}#profil-sekolah" class="footer-link"><i class="fas fa-angle-right small"></i> Profil & Visi Misi</a></li>
                        <li><a href="{{ route('guest.guru-staff') }}" class="footer-link"><i class="fas fa-angle-right small"></i> Guru & Staf Pegawai</a></li>
                        <li><a href="{{ route('guest.berita.index') }}" class="footer-link"><i class="fas fa-angle-right small"></i> Berita & Pengumuman</a></li>
                        <li><a href="{{ route('guest.prestasi.index') }}" class="footer-link"><i class="fas fa-angle-right small"></i> Prestasi Siswa</a></li>
                        <li><a href="{{ route('guest.galeri') }}" class="footer-link"><i class="fas fa-angle-right small"></i> Galeri Dokumentasi</a></li>
                    </ul>
                </div>

                <div class="col-lg-4 col-md-12">
                    <h6 class="footer-heading">Kontak & Lokasi</h6>
                    <div class="footer-contact-item">
                        <span class="contact-icon"><i class="fas fa-map-marker-alt"></i></span>
                        <span>{{ $schoolData['alamat'] ?? 'Desa Suwug, Kecamatan Sawan, Kabupaten Buleleng, Bali 81171' }}</span>
                    </div>
                    <div class="footer-contact-item">
                        <span class="contact-icon"><i class="fas fa-phone"></i></span>
                        <span>{{ $schoolData['telepon'] ?? '085850190190' }}</span>
                    </div>
                    <div class="footer-contact-item mb-0">
                        <span class="contact-icon"><i class="fas fa-envelope"></i></span>
                        <span>{{ $schoolData['email'] ?? 'admin@smpn3sawan.sch.id' }}</span>
                    </div>
                </div>
            </div>

            <div class="footer-bottom pt-3 d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
                <div>
                    &copy; {{ date('Y') }} {{ $schoolData['name'] ?? 'SMPN 3 SAWAN' }}. Hak cipta dilindungi.
                </div>
                <div>
                    Sistem Informasi Akademik &amp; LMS Terpadu
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
