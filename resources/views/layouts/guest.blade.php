<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'SMPN 3 SAWAN' }}</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <link rel="icon" type="image/png" href="{{ asset('assets/image/logo-sekolah-smpn3sawan.webp') }}">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .guest-header {
            background: rgba(13, 71, 161, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255,255,255,0.2);
        }
        .nav-link {
            color: rgba(255,255,255,0.8) !important;
            font-weight: 500;
            text-transform: uppercase;
            font-size: 0.9rem;
            padding: 1rem !important;
        }
        .nav-link:hover, .nav-link.active {
            color: #fff !important;
        }
        .dropdown-menu {
            border: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .main-content {
            flex: 1;
            padding-top: 2rem;
            padding-bottom: 2rem;
        }
        footer {
            margin-top: auto;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <nav class="navbar navbar-expand-lg navbar-dark guest-header sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('welcome') }}">
                <img src="{{ asset('assets/image/logo-sekolah-smpn3sawan.webp') }}" alt="Logo" width="40" height="40" class="me-2">
                <span class="fw-bold d-none d-md-block">SMPN 3 SAWAN</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link {{ Route::currentRouteName() == 'welcome' ? 'active' : '' }}" href="{{ route('welcome') }}">Beranda</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ Route::currentRouteName() == 'guest.guru-staff' ? 'active' : '' }}" href="#" id="profilDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Profil
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="profilDropdown">
                            <li><a class="dropdown-item" href="{{ route('guest.guru-staff') }}">Guru & Staf Pegawai</a></li>
                            {{-- Add Visi Misi or others if needed --}}
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Str::startsWith(Route::currentRouteName(), 'guest.berita') ? 'active' : '' }}" href="{{ route('guest.berita.index') }}">Berita</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Str::startsWith(Route::currentRouteName(), 'guest.prestasi') ? 'active' : '' }}" href="{{ route('guest.prestasi.index') }}">Prestasi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Route::currentRouteName() == 'guest.galeri' ? 'active' : '' }}" href="{{ route('guest.galeri') }}">Galeri Sekolah</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Route::currentRouteName() == 'guest.kontak' ? 'active' : '' }}" href="{{ route('guest.kontak') }}">Kontak</a>
                    </li>
                    @auth
                        <li class="nav-item ms-lg-3">
                            <a href="{{ url('/home') }}" class="btn btn-light btn-sm mt-2 px-3">
                                Dashboard
                            </a>
                        </li>
                    @else
                        <li class="nav-item ms-lg-3">
                            <a href="{{ route('login') }}" class="btn btn-light btn-sm mt-2 px-3">
                                Masuk
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
    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <h5 class="mb-3">SMPN 3 SAWAN</h5>
                    <p class="small text-white-50">
                        Mewujudkan generasi yang cerdas, berkarakter, dan berwawasan global berlandaskan nilai-nilai luhur.
                    </p>
                </div>
                <div class="col-md-4 mb-3">
                    <h5 class="mb-3">Tautan Cepat</h5>
                    <ul class="list-unstyled small">
                        <li><a href="{{ route('guest.berita.index') }}" class="text-white-50 text-decoration-none">Berita Sekolah</a></li>
                        <li><a href="{{ route('guest.prestasi.index') }}" class="text-white-50 text-decoration-none">Prestasi Siswa</a></li>
                        <li><a href="{{ route('guest.galeri') }}" class="text-white-50 text-decoration-none">Galeri Foto & Video</a></li>
                        <li><a href="{{ route('guest.guru-staff') }}" class="text-white-50 text-decoration-none">Data Pengajar</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5 class="mb-3">Hubungi Kami</h5>
                    <p class="small text-white-50 mb-1">
                        <i class="fas fa-map-marker-alt me-2"></i> Jl. Raya Sawan No. 123, Buleleng
                    </p>
                    <p class="small text-white-50 mb-1">
                        <i class="fas fa-phone me-2"></i> (0362) 123456
                    </p>
                    <p class="small text-white-50">
                        <i class="fas fa-envelope me-2"></i> info@smpn3sawan.sch.id
                    </p>
                </div>
            </div>
            <hr class="border-secondary my-3">
            <div class="text-center small text-white-50">
                &copy; {{ date('Y') }} SMPN 3 SAWAN. All rights reserved.
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
