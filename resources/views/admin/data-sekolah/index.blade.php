@extends('layouts.app')

@section('content')
<div class="school-profile-page">
    <!-- Welcome Header -->
    <div class="school-welcome-header">
        <div class="welcome-content">
            <div class="school-logo-section">
                <div class="school-logo">
                    <i class="fas fa-school"></i>
                </div>
                <div class="welcome-text">
                    <h1>Selamat Datang di {{ $schoolData['name'] ?? 'SMPN 3 SAWAN' }}</h1>
                    <p class="welcome-subtitle">Portal Informasi Sekolah</p>
                </div>
            </div>
            <div class="header-actions">
                @auth
                    <a href="{{ route('home') }}" class="btn btn-secondary">
                        <i class="fas fa-tachometer-alt"></i>
                        Dashboard
                    </a>
                    @if(auth()->user()->isAdmin() || auth()->user()->isKepalaSekolah())
                        <a href="{{ route('school.edit') }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i>
                            Edit Data
                        </a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt"></i>
                        Login
                    </a>
                @endauth
            </div>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
            <button type="button" class="close" onclick="this.parentElement.style.display='none';">
                <span>&times;</span>
            </button>
        </div>
    @endif

    <!-- School Information Grid -->
    <div class="school-info-container">
        <!-- School Identity Card -->
        <div class="school-identity-card">
            <div class="card-header">
                <h2><i class="fas fa-id-card"></i> Identitas Sekolah</h2>
            </div>
            <div class="card-body">
                <div class="identity-grid">
                    <div class="identity-item">
                        <div class="identity-label">Nama Sekolah</div>
                        <div class="identity-value">{{ $schoolData['name'] ?? 'SMPN 3 SAWAN' }}</div>
                    </div>
                    <div class="identity-item">
                        <div class="identity-label">Kepala Sekolah</div>
                        <div class="identity-value">{{ $schoolData['kepala_sekolah'] ?? 'Drs. I Made Sutrisna, M.Pd.' }}</div>
                    </div>
                    <div class="identity-item">
                        <div class="identity-label">NPSN</div>
                        <div class="identity-value">{{ $schoolData['npsn'] ?? '50100123' }}</div>
                    </div>
                    <div class="identity-item">
                        <div class="identity-label">Akreditasi</div>
                        <div class="identity-value">
                            <span class="akreditasi-badge akreditasi-{{ strtolower($schoolData['akreditasi'] ?? 'A') }}">
                                {{ $schoolData['akreditasi'] ?? 'A' }}
                            </span>
                        </div>
                    </div>
                    <div class="identity-item">
                        <div class="identity-label">Tahun Berdiri</div>
                        <div class="identity-value">{{ $schoolData['tahun_berdiri'] ?? '1985' }}</div>
                    </div>
                    <div class="identity-item">
                        <div class="identity-label">Website</div>
                        <div class="identity-value">
                            @if($schoolData['website'] ?? null)
                                <a href="http://{{ $schoolData['website'] }}" target="_blank" class="website-link">
                                    {{ $schoolData['website'] }}
                                    <i class="fas fa-external-link-alt"></i>
                                </a>
                            @else
                                <span class="text-muted">Tidak tersedia</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vision & Mission Section -->
        <div class="vision-mission-section">
            <div class="vision-card">
                <div class="card-header">
                    <h2><i class="fas fa-eye"></i> Visi Sekolah</h2>
                </div>
                <div class="card-body">
                    <p class="vision-text">{{ $schoolData['visi'] ?? 'Menjadi sekolah unggulan yang menghasilkan lulusan berkualitas, berkarakter, dan berdaya saing global.' }}</p>
                </div>
            </div>

            <div class="mission-card">
                <div class="card-header">
                    <h2><i class="fas fa-bullseye"></i> Misi Sekolah</h2>
                </div>
                <div class="card-body">
                    <ul class="mission-list">
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
                        @foreach($missions as $misi)
                            <li>{{ $misi }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="contact-info-section">
            <div class="contact-card">
                <div class="card-header">
                    <h2><i class="fas fa-address-book"></i> Informasi Kontak</h2>
                </div>
                <div class="card-body">
                    <div class="contact-grid">
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="contact-details">
                                <h4>Alamat</h4>
                                <p>{{ $schoolData['alamat'] ?? 'Jl. Raya Sawan No. 123, Sawan, Buleleng, Bali' }}</p>
                            </div>
                        </div>

                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-phone"></i>
                            </div>
                            <div class="contact-details">
                                <h4>Nomor Telepon</h4>
                                <p>
                                    <a href="tel:{{ $schoolData['telepon'] ?? '(0362) 123456' }}">
                                        {{ $schoolData['telepon'] ?? '(0362) 123456' }}
                                    </a>
                                </p>
                            </div>
                        </div>

                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="contact-details">
                                <h4>Email</h4>
                                <p>
                                    <a href="mailto:{{ $schoolData['email'] ?? 'info@smpn3sawan.sch.id' }}">
                                        {{ $schoolData['email'] ?? 'info@smpn3sawan.sch.id' }}
                                    </a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Maps Section -->
        <div class="maps-section">
            <div class="maps-card">
                <div class="card-header">
                    <h2><i class="fas fa-map"></i> Lokasi Sekolah</h2>
                </div>
                <div class="card-body">
                    <div class="maps-container">
                        @php
                            $latitude = $schoolData['maps_latitude'] ?? '-8.1234567';
                            $longitude = $schoolData['maps_longitude'] ?? '115.1234567';
                            $schoolName = urlencode($schoolData['name'] ?? 'SMPN 3 SAWAN');
                        @endphp
                        <iframe
                            src="https://maps.google.com/maps?q={{ $latitude }},{{ $longitude }}&hl=id&z=16&output=embed"
                            width="100%"
                            height="400"
                            style="border:0;"
                            allowfullscreen=""
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    </div>
                    <div class="maps-actions">
                        <a href="https://www.google.com/maps/search/?api=1&query={{ $latitude }},{{ $longitude }}"
                           target="_blank" class="btn btn-secondary">
                            <i class="fas fa-external-link-alt"></i>
                            Buka di Google Maps
                        </a>
                        <a href="https://www.google.com/maps/dir/?api=1&destination={{ $latitude }},{{ $longitude }}"
                           target="_blank" class="btn btn-primary">
                            <i class="fas fa-route"></i>
                            Petunjuk Arah
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* School Profile Page Styling */
.school-profile-page {
    min-height: 100vh;
    background: #f8f9fa;
    padding: 2rem 0;
}

.school-welcome-header {
    background: white;
    border-radius: 15px;
    margin: 0 2rem 2rem;
    padding: 2rem;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
}

.welcome-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.school-logo-section {
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.school-logo {
    background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
    color: white;
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.welcome-text h1 {
    font-size: 1.8rem;
    font-weight: 700;
    color: var(--primary-color);
    margin: 0 0 0.5rem;
}

.welcome-subtitle {
    font-size: 1rem;
    color: #6c757d;
    margin: 0;
}

.header-actions .btn {
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    margin-left: 0.5rem;
}

.header-actions .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.12);
}

/* Alert Styling */
.alert {
    margin: 0 2rem 2rem;
    padding: 1.25rem 1.5rem;
    border-radius: 10px;
    border: none;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    position: relative;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border-left: 4px solid #28a745;
}

.alert .close {
    position: absolute;
    right: 1rem;
    background: none;
    border: none;
    font-size: 1.2rem;
    cursor: pointer;
    opacity: 0.7;
}

.alert .close:hover {
    opacity: 1;
}

/* School Information Container */
.school-info-container {
    max-width: 1000px;
    margin: 0 auto;
    padding: 0 2rem;
    display: grid;
    gap: 2rem;
    grid-template-columns: 1fr;
}

/* Card Base Styling */
.school-identity-card,
.vision-card,
.mission-card,
.contact-card,
.maps-card {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.school-identity-card:hover,
.vision-card:hover,
.mission-card:hover,
.contact-card:hover,
.maps-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
}

.card-header {
    background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
    color: white;
    padding: 1.5rem 2rem;
}

.card-header h2 {
    margin: 0;
    font-size: 1.4rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.card-body {
    padding: 2rem;
}

/* Identity Card */
.identity-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
}

.identity-item {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.identity-label {
    font-size: 0.9rem;
    color: var(--text-light);
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.identity-value {
    font-size: 1.1rem;
    color: var(--text-dark);
    font-weight: 600;
}

.akreditasi-badge {
    display: inline-block;
    padding: 0.4rem 1rem;
    border-radius: 20px;
    font-weight: 700;
    font-size: 1rem;
    color: white;
}

.akreditasi-a {
    background: linear-gradient(135deg, #28a745, #20c997);
}

.akreditasi-b {
    background: linear-gradient(135deg, #ffc107, #fd7e14);
}

.akreditasi-c {
    background: linear-gradient(135deg, #dc3545, #e83e8c);
}

.website-link {
    color: var(--primary-color);
    text-decoration: none;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.website-link:hover {
    text-decoration: underline;
}

/* Vision & Mission Section */
.vision-mission-section {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
}

.vision-text {
    font-size: 1.1rem;
    line-height: 1.8;
    color: var(--text-dark);
    font-style: italic;
    text-align: center;
    padding: 1rem;
    background: linear-gradient(135deg, #f8f9ff, #e3f2fd);
    border-radius: 15px;
    border-left: 4px solid var(--primary-color);
}

.mission-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.mission-list li {
    padding: 1rem;
    margin-bottom: 0.75rem;
    background: linear-gradient(135deg, #f8f9ff, #e8f5e8);
    border-radius: 10px;
    border-left: 4px solid var(--accent-color);
    position: relative;
    padding-left: 3rem;
    font-size: 1rem;
    line-height: 1.6;
    color: var(--text-dark);
}

.mission-list li:before {
    content: counter(mission-counter);
    counter-increment: mission-counter;
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    background: var(--accent-color);
    color: white;
    width: 1.5rem;
    height: 1.5rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.8rem;
}

.mission-list {
    counter-reset: mission-counter;
}

/* Contact Information */
.contact-grid {
    display: grid;
    gap: 2rem;
}

.contact-item {
    display: flex;
    align-items: flex-start;
    gap: 1.5rem;
    padding: 1.5rem;
    background: linear-gradient(135deg, #f8f9ff, #e3f2fd);
    border-radius: 15px;
    border-left: 4px solid var(--primary-color);
}

.contact-icon {
    background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
    color: white;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    flex-shrink: 0;
}

.contact-details h4 {
    margin: 0 0 0.5rem;
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--text-dark);
}

.contact-details p {
    margin: 0;
    color: var(--text-light);
    line-height: 1.5;
}

.contact-details a {
    color: var(--primary-color);
    text-decoration: none;
    font-weight: 500;
}

.contact-details a:hover {
    text-decoration: underline;
}

/* Maps Section */
.maps-container {
    border-radius: 15px;
    overflow: hidden;
    margin-bottom: 1.5rem;
    transition: all 0.3s ease;
}

.maps-container:hover {
    transform: translateY(-2px);
}

.maps-container iframe {
    border-radius: 15px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
}

.maps-container:hover iframe {
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
}

.maps-actions {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.maps-actions .btn {
    flex: 1;
    min-width: 200px;
    padding: 0.75rem 1rem;
    border-radius: 8px;
    font-weight: 500;
    text-decoration: none;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.maps-actions .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
}

/* Responsive Design */
@media (max-width: 992px) {
    .vision-mission-section {
        grid-template-columns: 1fr;
    }

    .welcome-content {
        flex-direction: column;
        text-align: center;
    }

    .school-logo-section {
        flex-direction: column;
        text-align: center;
    }

    .identity-grid {
        grid-template-columns: 1fr;
    }

    .header-actions {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
        justify-content: center;
    }
}

@media (max-width: 768px) {
    .school-profile-page {
        padding: 1rem 0;
    }

    .school-welcome-header,
    .school-info-container {
        margin: 0 1rem;
    }

    .school-info-container {
        padding: 0 1rem;
    }

    .school-welcome-header {
        padding: 1.5rem;
    }

    .welcome-text h1 {
        font-size: 1.5rem;
    }

    .card-body {
        padding: 1.5rem;
    }

    .contact-item {
        padding: 1rem;
    }

    .maps-actions {
        flex-direction: column;
    }

    .maps-actions .btn {
        min-width: auto;
    }
}

@media (max-width: 480px) {
    .welcome-text h1 {
        font-size: 1.3rem;
    }

    .card-header {
        padding: 1rem 1.5rem;
    }

    .card-header h2 {
        font-size: 1.2rem;
    }

    .card-body {
        padding: 1rem;
    }

    .contact-item {
        flex-direction: column;
        text-align: center;
    }

    .mission-list li {
        padding-left: 2.5rem;
    }
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
