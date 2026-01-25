@extends('layouts.guest')

@section('title', 'Guru & Staf Pegawai - SMPN 3 SAWAN')

@section('content')
<div class="container">
    <div class="text-center mb-5">
        <h2 class="fw-bold text-primary">Guru & Staf Pegawai</h2>
        <p class="text-muted">Tenaga Pendidik dan Kependidikan SMP Negeri 3 Sawan</p>
    </div>

    <div class="row">
        @forelse($gurus as $guru)
        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
            <div class="card h-100 border-0 shadow-sm text-center p-3">
                <img src="{{ $guru->getProfilePhotoUrl() }}" class="rounded-circle mx-auto mb-3" width="120" height="120" style="object-fit: cover;">
                <h5 class="card-title mb-1">{{ $guru->name }}</h5>
                <p class="text-muted small mb-2">{{ $guru->guruProfile->jabatan ?? 'Guru' }}</p>
                <div class="small text-muted">
                    NIP: {{ $guru->guruProfile->nip ?? $guru->nomor_induk ?? '-' }}
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <div class="text-muted">Belum ada data guru dan staf pegawai.</div>
        </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $gurus->links() }}
    </div>
</div>
@endsection
