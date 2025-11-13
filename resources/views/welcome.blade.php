<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $schoolData['name'] ?? 'Selamat Datang' }} - Learning Management System</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('assets/landing.css') }}" rel="stylesheet">
</head>

<body class="school-profile-page">
    <!-- Header Section -->
    <header class="school-header">
        <div class="container">
            <div class="header-content">
                <div class="school-logo-section">
                    <div class="school-logo">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <div class="school-title">
                        <h1>{{ $schoolData['name'] ?? 'SMPN 3 SAWAN' }}</h1>
                        <p>Learning Management System</p>
                    </div>
                </div>

                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/home') }}" class="btn-login-header">
                            <i class="fas fa-home"></i>
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn-login-header">
                            <i class="fas fa-sign-in-alt"></i>
                            Login
                        </a>
                    @endauth
                @endif
            </div>
        </div>
    </header>

    <!-- Welcome Banner -->
    <section class="welcome-banner">
        <div class="container">
            <div class="welcome-content">
                <h2>Selamat Datang di Portal Pendidikan</h2>
                <p>Sistem pembelajaran terpadu untuk kemajuan pendidikan yang berkualitas</p>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <main class="school-content">
        <div class="container">
            <div class="school-grid">
                <!-- Main School Information -->
                <div class="info-card main-info full-width">
                    <div class="card-header">
                        <h3>
                            <i class="fas fa-school"></i>
                            Informasi Sekolah
                        </h3>
                    </div>
                    <div class="card-content">
                        <div class="info-row">
                            <span class="info-label">Nama Sekolah</span>
                            <span class="info-value">{{ $schoolData['name'] ?? 'SMPN 3 SAWAN' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">NPSN</span>
                            <span class="info-value">{{ $schoolData['npsn'] ?? '-' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Kepala Sekolah</span>
                            <span class="info-value">{{ $schoolData['kepala_sekolah'] ?? '-' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Tahun Berdiri</span>
                            <span class="info-value">{{ $schoolData['tahun_berdiri'] ?? '-' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Akreditasi</span>
                            <span class="info-value akreditasi-{{ strtolower($schoolData['akreditasi'] ?? 'a') }}">
                                {{ $schoolData['akreditasi'] ?? 'A' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Visi -->
                <div class="info-card visi-card">
                    <div class="card-header">
                        <h3>
                            <i class="fas fa-eye"></i>
                            Visi
                        </h3>
                    </div>
                    <div class="card-content">
                        <p class="visi-text">
                            {{ $schoolData['visi'] ?? 'Menjadi sekolah unggulan yang menghasilkan lulusan berkualitas, berkarakter, dan berdaya saing global.' }}
                        </p>
                    </div>
                </div>

                <!-- Misi -->
                <div class="info-card misi-card">
                    <div class="card-header">
                        <h3>
                            <i class="fas fa-bullseye"></i>
                            Misi
                        </h3>
                    </div>
                    <div class="card-content">
                        <div class="misi-list">
                            @if(isset($schoolData['misi']) && is_array($schoolData['misi']))
                                @foreach($schoolData['misi'] as $index => $misi)
                                    <div class="misi-item">
                                        <div class="misi-number">{{ $index + 1 }}</div>
                                        <div class="misi-text">{{ $misi }}</div>
                                    </div>
                                @endforeach
                            @else
                                <div class="misi-item">
                                    <div class="misi-number">1</div>
                                    <div class="misi-text">Menyelenggarakan pendidikan yang berkualitas dan berstandar nasional</div>
                                </div>
                                <div class="misi-item">
                                    <div class="misi-number">2</div>
                                    <div class="misi-text">Mengembangkan potensi peserta didik secara optimal</div>
                                </div>
                                <div class="misi-item">
                                    <div class="misi-number">3</div>
                                    <div class="misi-text">Membangun karakter yang berakhlak mulia dan berbudi pekerti luhur</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="info-card contact-card">
                    <div class="card-header">
                        <h3>
                            <i class="fas fa-address-book"></i>
                            Informasi Kontak
                        </h3>
                    </div>
                    <div class="card-content">
                        <div class="contact-info">
                            <div class="contact-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <div class="contact-details">
                                    <span class="contact-label">Alamat</span>
                                    <span class="contact-value">{{ $schoolData['alamat'] ?? 'Jl. Raya Sawan No. 123, Sawan, Buleleng, Bali' }}</span>
                                </div>
                            </div>
                            <div class="contact-item">
                                <i class="fas fa-phone"></i>
                                <div class="contact-details">
                                    <span class="contact-label">Telepon</span>
                                    <span class="contact-value">{{ $schoolData['telepon'] ?? '(0362) 123456' }}</span>
                                </div>
                            </div>
                            <div class="contact-item">
                                <i class="fas fa-envelope"></i>
                                <div class="contact-details">
                                    <span class="contact-label">Email</span>
                                    <span class="contact-value">{{ $schoolData['email'] ?? 'info@smpn3sawan.sch.id' }}</span>
                                </div>
                            </div>
                            @if(isset($schoolData['website']) && !empty($schoolData['website']))
                            <div class="contact-item">
                                <i class="fas fa-globe"></i>
                                <div class="contact-details">
                                    <span class="contact-label">Website</span>
                                    <span class="contact-value">{{ $schoolData['website'] }}</span>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Maps -->
                @if(isset($schoolData['maps_latitude']) && isset($schoolData['maps_longitude']))
                <div class="info-card maps-card">
                    <div class="card-header">
                        <h3>
                            <i class="fas fa-map"></i>
                            Lokasi Sekolah
                        </h3>
                    </div>
                    <div class="card-content">
                        <div class="maps-container">
                            <iframe
                                src="https://maps.google.com/maps?q={{ $schoolData['maps_latitude'] }},{{ $schoolData['maps_longitude'] }}&hl=id&z=16&output=embed"
                                width="100%"
                                height="300"
                                style="border:0;"
                                allowfullscreen=""
                                loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade">
                            </iframe>
                        </div>
                        <div class="maps-info">
                            <p>
                                <i class="fas fa-info-circle"></i>
                                Lokasi sekolah dapat diakses dengan mudah menggunakan transportasi umum
                            </p>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="school-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h4>{{ $schoolData['name'] ?? 'SMPN 3 SAWAN' }}</h4>
                    <p>
                        <i class="fas fa-map-marker-alt"></i>
                        {{ $schoolData['alamat'] ?? 'Jl. Raya Sawan No. 123, Sawan, Buleleng, Bali' }}
                    </p>
                    <p>
                        <i class="fas fa-phone"></i>
                        {{ $schoolData['telepon'] ?? '(0362) 123456' }}
                    </p>
                    <p>
                        <i class="fas fa-envelope"></i>
                        {{ $schoolData['email'] ?? 'info@smpn3sawan.sch.id' }}
                    </p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; {{ date('Y') }} {{ $schoolData['name'] ?? 'SMPN 3 SAWAN' }}. Semua hak cipta dilindungi.</p>
            </div>
        </div>
    </footer>
</body>
</html>
