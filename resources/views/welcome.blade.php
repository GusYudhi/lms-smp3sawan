@extends('layouts.guest')

@section('title', ($schoolData['name'] ?? 'SMPN 3 SAWAN') . ' - Portal Resmi & Learning Management System')

@section('content')
<!-- 1. Full-Bleed Hero Slideshow -->
<section class="guest-hero">
    <!-- Background Slideshow / Static Wallpaper -->
    <div id="heroCarousel" class="carousel slide carousel-fade hero-carousel-bg"
         @if(($heroMode ?? 'slideshow') === 'slideshow' && $heroSlides->count() > 1)
             data-bs-ride="carousel" data-bs-interval="6000" data-bs-pause="false"
         @endif>
        <div class="carousel-inner">
            @forelse($heroSlides as $index => $slide)
                <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                    <img src="{{ $slide['image'] }}" class="hero-slide-img" alt="{{ $slide['title'] }}">
                </div>
            @empty
                <div class="carousel-item active">
                    <div class="hero-fallback-surface"></div>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Directional Scrim Overlay (Guarantees WCAG Contrast for Top Nav & Hero Text) -->
    <div class="hero-scrim-overlay"></div>

    <!-- Fixed School Identity Foreground -->
    <div class="container hero-foreground">
        <div class="row">
            <div class="col-lg-8 col-xl-7">
                <div class="hero-eyebrow-pill">
                    <span class="eyebrow-dot"></span>
                    <span>Terakreditasi {{ $schoolData['akreditasi'] ?? 'A' }} &bull; NPSN {{ $schoolData['npsn'] ?? '50100123' }}</span>
                </div>

                <h1 class="hero-display-title">
                    {{ $schoolData['name'] ?? 'SMP Negeri 3 Sawan' }}
                </h1>

                <p class="hero-lead-text">
                    {{ $schoolData['visi'] ?? 'Menjadi sekolah unggulan yang menghasilkan lulusan berkualitas, berkarakter, dan berdaya saing global.' }}
                </p>

                <div class="hero-cta-group">
                    @auth
                        <a href="{{ url('/home') }}" class="btn btn-hero-primary">
                            <i class="fas fa-th-large me-2"></i>Buka Dashboard LMS
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-hero-primary">
                            <i class="fas fa-sign-in-alt me-2"></i>Masuk Portal LMS
                        </a>
                    @endauth
                    <a href="#profil-sekolah" class="btn btn-hero-tonal">
                        Jelajahi Profil Sekolah <i class="fas fa-arrow-down ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Dynamic Slide Source Caption Bar (Shown if Slideshow has multiple slides) -->
    @if(($heroMode ?? 'slideshow') === 'slideshow' && isset($heroSlides) && $heroSlides->count() > 1)
    <div class="container hero-bottom-bar">
        <div class="hero-caption-surface">
            @foreach($heroSlides as $index => $slide)
                <div class="hero-caption-item {{ $index === 0 ? 'active' : '' }}" data-slide-index="{{ $index }}">
                    <span class="m3-source-chip m3-chip-galeri">
                        <i class="fas fa-camera"></i>
                        Galeri Latar
                    </span>
                    <div class="caption-text-wrap">
                        <span class="caption-title-link">
                            {{ $slide['title'] }}
                        </span>
                        @if(!empty($slide['subtitle']))
                            <span class="caption-date">{{ $slide['subtitle'] }}</span>
                        @endif
                    </div>
                </div>
            @endforeach

            <div class="hero-slider-controls">
                <span class="slide-counter">
                    <span id="heroSlideCurrent">01</span> / {{ str_pad($heroSlides->count(), 2, '0', STR_PAD_LEFT) }}
                </span>
                <button type="button" class="btn btn-hero-nav" data-bs-target="#heroCarousel" data-bs-slide="prev" aria-label="Slide sebelumnya">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button type="button" class="btn btn-hero-nav" data-bs-target="#heroCarousel" data-bs-slide="next" aria-label="Slide berikutnya">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
    </div>
    @endif
</section>

<!-- 2. Editorial Split: Tentang Sekolah & Visi Misi -->
<section id="profil-sekolah" class="guest-section">
    <div class="container">
        <div class="row g-4 g-lg-5 align-items-stretch">
            <!-- Left Column: Institutional Profile & Fact Strip -->
            <div class="col-lg-6 d-flex flex-column justify-content-between">
                <div>
                    <div class="section-header-bar mb-3">
                        <div>
                            <span class="section-eyebrow">Identitas Institusi</span>
                            <h2 class="section-heading">Tentang {{ $schoolData['name'] ?? 'SMPN 3 SAWAN' }}</h2>
                        </div>
                    </div>

                    <p class="editorial-narrative mb-3">
                        Berdiri sejak tahun <strong>{{ $schoolData['tahun_berdiri'] ?? '1995' }}</strong> di Kecamatan Sawan, Kabupaten Buleleng, {{ $schoolData['name'] ?? 'SMPN 3 SAWAN' }} berkomitmen menyelenggarakan layanan pendidikan yang inklusif, disiplin, dan berorientasi pada pengembangan karakter serta kompetensi akademik peserta didik.
                    </p>
                    <p class="editorial-narrative mb-0">
                        Melalui integrasi pembelajaran tatap muka dan sistem manajemen pembelajaran digital, sekolah terus mendorong kolaborasi aktif antara guru, siswa, dan orang tua.
                    </p>
                </div>

                <!-- 2x2 Fact Strip Grid -->
                <div class="fact-strip-grid">
                    <div class="fact-item-card">
                        <span class="fact-label"><i class="fas fa-user-tie"></i> Kepala Sekolah</span>
                        <p class="fact-value">{{ $schoolData['kepala_sekolah'] ?? '-' }}</p>
                    </div>
                    <div class="fact-item-card">
                        <span class="fact-label"><i class="fas fa-award"></i> Akreditasi</span>
                        <p class="fact-value">Peringkat {{ $schoolData['akreditasi'] ?? 'A' }}</p>
                    </div>
                    <div class="fact-item-card">
                        <span class="fact-label"><i class="fas fa-fingerprint"></i> Nomor Pokok (NPSN)</span>
                        <p class="fact-value">{{ $schoolData['npsn'] ?? '-' }}</p>
                    </div>
                    <div class="fact-item-card">
                        <span class="fact-label"><i class="fas fa-calendar-check"></i> Tahun Berdiri</span>
                        <p class="fact-value">{{ $schoolData['tahun_berdiri'] ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Right Column: Visi Quote & Numbered Misi -->
            <div class="col-lg-6">
                <div class="visi-misi-surface">
                    <div class="visi-quote-card">
                        <span class="visi-badge"><i class="fas fa-compass"></i> Visi Sekolah</span>
                        <p class="visi-text">&ldquo;{{ $schoolData['visi'] ?? '-' }}&rdquo;</p>
                    </div>

                    <h3 class="h6 fw-bold text-uppercase text-muted mb-3">Misi Penyelenggaraan Pendidikan</h3>
                    <ol class="misi-list">
                        @if(isset($schoolData['misi']) && is_array($schoolData['misi']) && count($schoolData['misi']) > 0)
                            @foreach($schoolData['misi'] as $index => $misi)
                                <li class="misi-item">
                                    <span class="misi-number">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                                    <span>{{ $misi }}</span>
                                </li>
                            @endforeach
                        @else
                            <li class="misi-item">
                                <span class="misi-number">01</span>
                                <span>Menyelenggarakan pendidikan berkualitas yang berstandar nasional.</span>
                            </li>
                        @endif
                    </ol>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 3. Berita Terbaru (Featured Bento Layout) -->
<section class="guest-section guest-section-tonal">
    <div class="container">
        <div class="section-header-bar">
            <div>
                <span class="section-eyebrow">Kabar &amp; Pengumuman</span>
                <h2 class="section-heading">Berita Terbaru Sekolah</h2>
                <p class="section-subtitle">Dokumentasi kegiatan akademik, agenda kesiswaan, dan pengumuman resmi terbaru.</p>
            </div>
            <a href="{{ route('guest.berita.index') }}" class="btn btn-outline-primary">
                Lihat Semua Berita <i class="fas fa-arrow-right ms-1"></i>
            </a>
        </div>

        @if($beritas->count() > 0)
            @php
                $featuredBerita = $beritas->first();
                $sideBeritas = $beritas->skip(1);
            @endphp
            <div class="row g-4 align-items-stretch">
                <!-- Featured Main Article -->
                <div class="col-lg-7">
                    <article class="news-featured-card hover-card position-relative">
                        <div class="featured-media-wrap">
                            @if($featuredBerita->foto)
                                <img src="{{ asset('storage/' . $featuredBerita->foto) }}" alt="{{ $featuredBerita->judul }}">
                            @else
                                <div class="w-100 h-100 d-flex align-items-center justify-content-center">
                                    <i class="fas fa-newspaper fa-3x text-secondary"></i>
                                </div>
                            @endif
                        </div>
                        <div class="featured-body">
                            <div class="news-meta">
                                <span><i class="far fa-calendar-alt me-1 text-primary"></i> {{ $featuredBerita->tanggal->format('d M Y') }}</span>
                                @if($featuredBerita->penulis)
                                    <span>&bull;</span>
                                    <span><i class="far fa-user me-1"></i> {{ $featuredBerita->penulis->name }}</span>
                                @endif
                            </div>
                            <h3 class="featured-title">
                                <a href="{{ route('guest.berita.show', $featuredBerita->id) }}" class="stretched-link">
                                    {{ $featuredBerita->judul }}
                                </a>
                            </h3>
                            <p class="featured-excerpt">
                                {{ Str::limit(strip_tags($featuredBerita->konten), 160) }}
                            </p>
                            <div class="mt-auto">
                                <span class="text-primary fw-bold small">
                                    Baca Artikel Lengkap <i class="fas fa-arrow-right ms-1"></i>
                                </span>
                            </div>
                        </div>
                    </article>
                </div>

                <!-- Right Side Compact Stack -->
                <div class="col-lg-5">
                    <div class="news-side-stack">
                        @forelse($sideBeritas as $berita)
                            <article class="news-compact-card">
                                <div class="compact-thumb">
                                    @if($berita->foto)
                                        <img src="{{ asset('storage/' . $berita->foto) }}" alt="{{ $berita->judul }}">
                                    @else
                                        <i class="fas fa-newspaper fa-lg text-secondary"></i>
                                    @endif
                                </div>
                                <div class="compact-content">
                                    <span class="compact-date">
                                        <i class="far fa-calendar-alt me-1"></i> {{ $berita->tanggal->format('d M Y') }}
                                    </span>
                                    <h4 class="compact-title">
                                        <a href="{{ route('guest.berita.show', $berita->id) }}" class="stretched-link">
                                            {{ $berita->judul }}
                                        </a>
                                    </h4>
                                    <p class="compact-excerpt">
                                        {{ Str::limit(strip_tags($berita->konten), 75) }}
                                    </p>
                                </div>
                            </article>
                        @empty
                            <div class="card border h-100 d-flex align-items-center justify-content-center p-4 text-center text-muted">
                                <div>
                                    <i class="far fa-newspaper fa-2x mb-2"></i>
                                    <p class="small mb-0">Belum ada berita tambahan lainnya.</p>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        @else
            <div class="card p-5 text-center text-muted">
                <i class="far fa-newspaper fa-2x mb-3 text-secondary"></i>
                <p class="mb-0">Belum ada berita terbaru yang dipublikasikan.</p>
            </div>
        @endif
    </div>
</section>

<!-- 4. Prestasi Siswa (Portrait Trophy Showcase) -->
<section class="guest-section">
    <div class="container">
        <div class="section-header-bar">
            <div>
                <span class="section-eyebrow">Rekam Jejak Keunggulan</span>
                <h2 class="section-heading">Prestasi Siswa &amp; Sekolah</h2>
                <p class="section-subtitle">Capaian membanggakan siswa-siswi di bidang akademik, olahraga, dan seni budaya.</p>
            </div>
            <a href="{{ route('guest.prestasi.index') }}" class="btn btn-outline-primary">
                Lihat Semua Prestasi <i class="fas fa-arrow-right ms-1"></i>
            </a>
        </div>

        <div class="row g-4">
            @forelse($prestasis as $prestasi)
                <div class="col-lg-3 col-md-6">
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
                                <i class="far fa-calendar-check me-1"></i> {{ $prestasi->tanggal->format('d M Y') }}
                            </span>
                            <h3 class="trophy-title">
                                <a href="{{ route('guest.prestasi.show', $prestasi->id) }}" class="stretched-link">
                                    {{ $prestasi->judul }}
                                </a>
                            </h3>
                            <p class="trophy-desc">
                                {{ Str::limit($prestasi->deskripsi, 90) }}
                            </p>
                        </div>
                    </article>
                </div>
            @empty
                <div class="col-12">
                    <div class="card p-5 text-center text-muted">
                        <i class="fas fa-trophy fa-2x mb-3 text-warning"></i>
                        <p class="mb-0">Belum ada data prestasi siswa yang tercatat.</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</section>

<!-- 5. Unified Location Map & Kotak Saran -->
<section id="hubungi-kami" class="guest-section guest-section-tonal">
    <div class="container">
        <div class="unified-contact-surface">
            <div class="row g-0">
                <!-- Left Pane: School Location & Interactive Map -->
                <div class="col-lg-6">
                    <div class="contact-map-pane">
                        <div>
                            <span class="text-primary fw-bold text-uppercase small d-block mb-1">Lokasi &amp; Kontak</span>
                            <h2 class="h3 fw-bold mb-3">Kunjungi {{ $schoolData['name'] ?? 'SMPN 3 SAWAN' }}</h2>

                            <div class="quick-contact-list">
                                <div class="quick-contact-row">
                                    <span class="qc-icon"><i class="fas fa-map-marker-alt"></i></span>
                                    <div>
                                        <span class="qc-label">Alamat Sekolah</span>
                                        <p class="qc-val">{{ $schoolData['alamat'] ?? 'Desa Suwug, Kecamatan Sawan, Kabupaten Buleleng, Bali 81171' }}</p>
                                    </div>
                                </div>
                                <div class="quick-contact-row">
                                    <span class="qc-icon"><i class="fas fa-phone"></i></span>
                                    <div>
                                        <span class="qc-label">Telepon Layanan</span>
                                        <p class="qc-val">{{ $schoolData['telepon'] ?? '085850190190' }}</p>
                                    </div>
                                </div>
                                <div class="quick-contact-row">
                                    <span class="qc-icon"><i class="fas fa-envelope"></i></span>
                                    <div>
                                        <span class="qc-label">Surat Elektronik</span>
                                        <p class="qc-val">{{ $schoolData['email'] ?? 'admin@smpn3sawan.sch.id' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if(isset($schoolData['maps_latitude']) && isset($schoolData['maps_longitude']))
                            <div class="map-frame-wrapper">
                                <iframe
                                    title="Peta Lokasi {{ $schoolData['name'] ?? 'SMPN 3 SAWAN' }}"
                                    src="https://maps.google.com/maps?q={{ $schoolData['maps_latitude'] }},{{ $schoolData['maps_longitude'] }}&hl=id&z=16&output=embed"
                                    allowfullscreen=""
                                    loading="lazy">
                                </iframe>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Right Pane: Suggestion Form (WCAG Accessible Labels) -->
                <div class="col-lg-6">
                    <div class="contact-form-pane">
                        <span class="text-primary fw-bold text-uppercase small d-block mb-1">Suara Komunitas</span>
                        <h2 class="h3 fw-bold mb-2">Kotak Saran &amp; Masukan</h2>
                        <p class="text-muted mb-4">
                            Setiap masukan dari orang tua, siswa, dan masyarakat sangat berarti bagi peningkatan mutu layanan pendidikan kami.
                        </p>

                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                            </div>
                        @endif

                        @if($errors->has('saran'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>{{ $errors->first('saran') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                            </div>
                        @endif

                        <form action="{{ route('guest.saran.store') }}" method="POST">
                            @csrf
                            {{-- Honeypot field (anti bot spam) --}}
                            <div class="honeypot-field" aria-hidden="true">
                                <input type="text" name="website" tabindex="-1" autocomplete="off" value="">
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="welcome_nama_pengirim" class="form-label fw-medium">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input
                                        type="text"
                                        id="welcome_nama_pengirim"
                                        class="form-control @error('nama_pengirim') is-invalid @enderror"
                                        name="nama_pengirim"
                                        placeholder="Contoh: I Made Wirawan"
                                        maxlength="100"
                                        value="{{ old('nama_pengirim') }}"
                                        required>
                                    @error('nama_pengirim')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="welcome_email_pengirim" class="form-label fw-medium">Alamat Email <span class="text-muted small">(Opsional)</span></label>
                                    <input
                                        type="email"
                                        id="welcome_email_pengirim"
                                        class="form-control @error('email_pengirim') is-invalid @enderror"
                                        name="email_pengirim"
                                        placeholder="nama@email.com"
                                        maxlength="100"
                                        value="{{ old('email_pengirim') }}">
                                    @error('email_pengirim')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label for="welcome_isi_saran" class="form-label fw-medium">Pesan atau Saran <span class="text-danger">*</span></label>
                                    <textarea
                                        id="welcome_isi_saran"
                                        class="form-control @error('isi_saran') is-invalid @enderror"
                                        name="isi_saran"
                                        rows="4"
                                        placeholder="Tuliskan saran, tanggapan, atau pertanyaan Anda..."
                                        maxlength="1000"
                                        required>{{ old('isi_saran') }}</textarea>
                                    @error('isi_saran')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 pt-2">
                                    <button type="submit" class="btn btn-primary px-4">
                                        <i class="fas fa-paper-plane me-2"></i>Kirim Masukan
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
