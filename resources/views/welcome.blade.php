<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $schoolData['name'] ?? 'Selamat Datang' }} - Learning Management System</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <link rel="icon" type="image/png" href="{{ asset('assets/image/LogoSMP3SAWAN.webp') }}">

    <!-- Bootstrap CSS -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            min-height: 100vh;
        }
        .welcome-header {
            background: rgba(13, 71, 161, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255,255,255,0.2);
        }
        .welcome-content {
            background: rgba(255,255,255,0.95);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            margin: 2rem 0;
        }
        .hero-section {
            background: #0d47a1;
            color: white;
            text-align: center;
            padding: 4rem 2rem;
        }
        .info-section {
            padding: 3rem 2rem;
        }
        .school-logo {
            width: 100px;
            height: 100px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 2.5rem;
        }
        .contact-item {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }
        .contact-item i {
            width: 30px;
            text-align: center;
            color: #0d47a1;
            margin-right: 1rem;
        }
        .mission-item {
            background: #f8f9fa;
            border-left: 4px solid #0d47a1;
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 0 10px 10px 0;
        }
        .maps-container {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
    </style>
</head>

<body>
    <!-- Header -->
    <nav class="navbar navbar-expand-lg navbar-dark welcome-header">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <i class="fas fa-graduation-cap me-2 fs-3"></i>
                <span class="fw-bold">{{ $schoolData['name'] ?? 'SMPN 3 SAWAN' }}</span>
            </a>

            @if (Route::has('login'))
                <div class="navbar-nav ms-auto">
                    @auth
                        <a href="{{ url('/home') }}" class="btn btn-light">
                            <i class="fas fa-home me-1"></i>Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-light">
                            <i class="fas fa-sign-in-alt me-1"></i>Login
                        </a>
                    @endauth
                </div>
            @endif
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container">
        <div class="welcome-content">
            <!-- Hero Section -->
            <div class="hero-section">
                <div class="school-logo">
                    <img src="{{ asset('assets/image/LogoSMP3SAWAN.webp') }}" alt="Logo SMP 3 SAWAN" style="width: 80px; height: 80px; object-fit: contain;">
                </div>
                <h1 class="display-4 fw-bold mb-3">{{ $schoolData['name'] ?? 'SMPN 3 SAWAN' }}</h1>
                <p class="lead mb-4">Learning Management System</p>
                <p class="fs-5">Sistem pembelajaran terpadu untuk kemajuan pendidikan yang berkualitas</p>
            </div>

            <!-- Information Section -->
            <div class="info-section">
                <div class="row g-4">
                    <!-- School Information -->
                    <div class="col-lg-6">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-header bg-primary text-white">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-info-circle me-2"></i>Informasi Sekolah
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-sm-4"><strong>NPSN:</strong></div>
                                    <div class="col-sm-8">{{ $schoolData['npsn'] ?? '-' }}</div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-4"><strong>Kepala Sekolah:</strong></div>
                                    <div class="col-sm-8">{{ $schoolData['kepala_sekolah'] ?? '-' }}</div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-4"><strong>Tahun Berdiri:</strong></div>
                                    <div class="col-sm-8">{{ $schoolData['tahun_berdiri'] ?? '-' }}</div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-4"><strong>Akreditasi:</strong></div>
                                    <div class="col-sm-8">
                                        <span class="badge bg-success">{{ $schoolData['akreditasi'] ?? 'A' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="col-lg-6">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-header bg-success text-white">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-address-book me-2"></i>Informasi Kontak
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="contact-item">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <div>
                                        <strong>Alamat:</strong><br>
                                        {{ $schoolData['alamat'] ?? 'Jl. Raya Sawan No. 123, Sawan, Buleleng, Bali' }}
                                    </div>
                                </div>
                                <div class="contact-item">
                                    <i class="fas fa-phone"></i>
                                    <div>
                                        <strong>Telepon:</strong><br>
                                        {{ $schoolData['telepon'] ?? '(0362) 123456' }}
                                    </div>
                                </div>
                                <div class="contact-item">
                                    <i class="fas fa-envelope"></i>
                                    <div>
                                        <strong>Email:</strong><br>
                                        {{ $schoolData['email'] ?? 'info@smpn3sawan.sch.id' }}
                                    </div>
                                </div>
                                @if(isset($schoolData['website']) && !empty($schoolData['website']))
                                <div class="contact-item">
                                    <i class="fas fa-globe"></i>
                                    <div>
                                        <strong>Website:</strong><br>
                                        {{ $schoolData['website'] }}
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Visi -->
                    <div class="col-lg-6">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-header bg-info text-white">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-eye me-2"></i>Visi
                                </h5>
                            </div>
                            <div class="card-body">
                                <p class="card-text">
                                    {{ $schoolData['visi'] ?? 'Menjadi sekolah unggulan yang menghasilkan lulusan berkualitas, berkarakter, dan berdaya saing global.' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Misi -->
                    <div class="col-lg-6">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-header bg-warning text-dark">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-bullseye me-2"></i>Misi
                                </h5>
                            </div>
                            <div class="card-body">
                                @if(isset($schoolData['misi']) && is_array($schoolData['misi']))
                                    @foreach($schoolData['misi'] as $index => $misi)
                                        <div class="mission-item">
                                            <strong>{{ $index + 1 }}.</strong> {{ $misi }}
                                        </div>
                                    @endforeach
                                @else
                                    <div class="mission-item">
                                        <strong>1.</strong> Menyelenggarakan pendidikan yang berkualitas dan berstandar nasional
                                    </div>
                                    <div class="mission-item">
                                        <strong>2.</strong> Mengembangkan potensi peserta didik secara optimal
                                    </div>
                                    <div class="mission-item mb-0">
                                        <strong>3.</strong> Membangun karakter yang berakhlak mulia dan berbudi pekerti luhur
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Maps (if available) -->
                    @if(isset($schoolData['maps_latitude']) && isset($schoolData['maps_longitude']))
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-secondary text-white">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-map me-2"></i>Lokasi Sekolah
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="maps-container">
                                    <iframe
                                        src="https://maps.google.com/maps?q={{ $schoolData['maps_latitude'] }},{{ $schoolData['maps_longitude'] }}&hl=id&z=16&output=embed"
                                        width="100%"
                                        height="400"
                                        style="border:0;"
                                        allowfullscreen=""
                                        loading="lazy"
                                        referrerpolicy="no-referrer-when-downgrade">
                                    </iframe>
                                </div>
                                <div class="text-center mt-3">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Lokasi sekolah dapat diakses dengan mudah menggunakan transportasi umum
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <h5>{{ $schoolData['name'] ?? 'SMPN 3 SAWAN' }}</h5>
                    <p class="mb-1">
                        <i class="fas fa-map-marker-alt me-2"></i>
                        {{ $schoolData['alamat'] ?? 'Jl. Raya Sawan No. 123, Sawan, Buleleng, Bali' }}
                    </p>
                    <p class="mb-1">
                        <i class="fas fa-phone me-2"></i>
                        {{ $schoolData['telepon'] ?? '(0362) 123456' }}
                    </p>
                    <p class="mb-0">
                        <i class="fas fa-envelope me-2"></i>
                        {{ $schoolData['email'] ?? 'info@smpn3sawan.sch.id' }}
                    </p>
                </div>
                <div class="col-md-4 text-end">
                    <p class="mb-0">&copy; {{ date('Y') }} {{ $schoolData['name'] ?? 'SMPN 3 SAWAN' }}</p>
                    <p class="mb-0 small">Semua hak cipta dilindungi.</p>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
