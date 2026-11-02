@extends('layouts.guest')

@section('title', 'Hubungi Kami - ' . ($schoolData['name'] ?? 'SMPN 3 SAWAN'))

@section('content')
<!-- Page Header Banner -->
<section class="guest-page-banner">
    <div class="container">
        <span class="banner-eyebrow"><i class="fas fa-envelope-open-text"></i> Layanan Informasi</span>
        <h1 class="banner-title">Hubungi {{ $schoolData['name'] ?? 'SMPN 3 SAWAN' }}</h1>
        <p class="banner-subtitle">Saluran informasi resmi, alamat kunjungan, dan kotak masukan untuk masyarakat.</p>
    </div>
</section>

<section class="guest-section">
    <div class="container">
        <div class="unified-contact-surface">
            <div class="row g-0">
                <!-- Contact Info & Map -->
                <div class="col-lg-5">
                    <div class="contact-map-pane">
                        <div>
                            <h2 class="h4 fw-bold mb-4">Informasi Kontak</h2>

                            <div class="quick-contact-list">
                                <div class="quick-contact-row">
                                    <span class="qc-icon"><i class="fas fa-map-marker-alt"></i></span>
                                    <div>
                                        <span class="qc-label">Alamat Lengkap</span>
                                        <p class="qc-val">{{ $schoolData['alamat'] ?? 'Desa Suwug, Kecamatan Sawan, Kabupaten Buleleng, Bali 81171' }}</p>
                                    </div>
                                </div>

                                <div class="quick-contact-row">
                                    <span class="qc-icon"><i class="fas fa-phone"></i></span>
                                    <div>
                                        <span class="qc-label">Nomor Telepon</span>
                                        <p class="qc-val">{{ $schoolData['telepon'] ?? '085850190190' }}</p>
                                    </div>
                                </div>

                                <div class="quick-contact-row">
                                    <span class="qc-icon"><i class="fas fa-envelope"></i></span>
                                    <div>
                                        <span class="qc-label">Email Resmi</span>
                                        <p class="qc-val">{{ $schoolData['email'] ?? 'admin@smpn3sawan.sch.id' }}</p>
                                    </div>
                                </div>

                                @if(!empty($schoolData['website']))
                                <div class="quick-contact-row">
                                    <span class="qc-icon"><i class="fas fa-globe"></i></span>
                                    <div>
                                        <span class="qc-label">Situs Web</span>
                                        <p class="qc-val">{{ $schoolData['website'] }}</p>
                                    </div>
                                </div>
                                @endif
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

                <!-- Suggestion Box -->
                <div class="col-lg-7">
                    <div class="contact-form-pane">
                        <h2 class="h4 fw-bold mb-2">Kirim Pesan atau Saran</h2>
                        <p class="text-muted mb-4">Silakan lengkapi formulir di bawah ini untuk menyampaikan saran, masukan, atau pertanyaan kepada pihak sekolah.</p>

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
                                    <label for="nama_pengirim" class="form-label fw-medium">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('nama_pengirim') is-invalid @enderror" id="nama_pengirim" name="nama_pengirim" placeholder="Contoh: I Made Wirawan" maxlength="100" value="{{ old('nama_pengirim') }}" required>
                                    @error('nama_pengirim')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="email_pengirim" class="form-label fw-medium">Alamat Email <span class="text-muted small">(Opsional)</span></label>
                                    <input type="email" class="form-control @error('email_pengirim') is-invalid @enderror" id="email_pengirim" name="email_pengirim" placeholder="nama@email.com" maxlength="100" value="{{ old('email_pengirim') }}">
                                    @error('email_pengirim')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12">
                                    <label for="isi_saran" class="form-label fw-medium">Pesan atau Saran <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('isi_saran') is-invalid @enderror" id="isi_saran" name="isi_saran" rows="5" placeholder="Tuliskan saran atau pertanyaan Anda secara jelas..." maxlength="1000" required>{{ old('isi_saran') }}</textarea>
                                    @error('isi_saran')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12 pt-2">
                                    <button type="submit" class="btn btn-primary px-4">
                                        <i class="fas fa-paper-plane me-2"></i>Kirim Pesan
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
