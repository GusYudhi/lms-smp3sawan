@extends('layouts.guest')

@section('title', 'Guru & Staf Pegawai - ' . ($schoolData['name'] ?? 'SMPN 3 SAWAN'))

@section('content')
<!-- Page Header Banner -->
<section class="guest-page-banner">
    <div class="container">
        <span class="banner-eyebrow"><i class="fas fa-chalkboard-teacher"></i> Direktori Pendidik</span>
        <h1 class="banner-title">Guru &amp; Staf Pegawai</h1>
        <p class="banner-subtitle">Tenaga pendidik dan kependidikan profesional {{ $schoolData['name'] ?? 'SMP Negeri 3 Sawan' }}.</p>
    </div>
</section>

<section class="guest-section">
    <div class="container">
        <div class="row g-4">
            @forelse($gurus as $guru)
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="card h-100 border hover-card text-center p-4">
                    <div class="mb-3">
                        <img src="{{ $guru->getProfilePhotoUrl() }}" alt="{{ $guru->name }}" class="guest-avatar-lg mx-auto">
                    </div>
                    <h2 class="h6 fw-bold mb-1">{{ $guru->name }}</h2>
                    <p class="text-primary small fw-semibold mb-2">
                        {{ $guru->guruProfile->jabatan_di_sekolah ?? ($guru->role === 'kepala_sekolah' ? 'Kepala Sekolah' : 'Tenaga Pendidik') }}
                    </p>
                    <div class="small text-muted mt-auto pt-2 border-top">
                        NIP: {{ $guru->guruProfile->nip ?? $guru->nomor_induk ?? '-' }}
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="card p-5 text-center text-muted">
                    <i class="fas fa-users fa-2x mb-3 text-secondary"></i>
                    <p class="mb-0">Belum ada data guru dan staf pegawai yang ditampilkan.</p>
                </div>
            </div>
            @endforelse
        </div>

        <div class="d-flex justify-content-center mt-5">
            {{ $gurus->links() }}
        </div>
    </div>
</section>
@endsection
