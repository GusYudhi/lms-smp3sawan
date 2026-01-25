@extends('layouts.guest')

@section('title', 'Kontak Sekolah - SMPN 3 SAWAN')

@section('content')
<div class="container">
    <div class="text-center mb-5">
        <h2 class="fw-bold text-primary">Hubungi Kami</h2>
        <p class="text-muted">Kami siap melayani dan menjawab pertanyaan Anda</p>
    </div>

    <div class="row g-4">
        <!-- Contact Info -->
        <div class="col-lg-5">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body p-4">
                    <h4 class="mb-4 fw-bold">Informasi Kontak</h4>

                    <div class="d-flex mb-4">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 p-3 rounded-circle text-primary">
                                <i class="fas fa-map-marker-alt fa-lg"></i>
                            </div>
                        </div>
                        <div class="ms-3">
                            <h6 class="fw-bold mb-1">Alamat</h6>
                            <p class="text-muted mb-0">{{ $schoolData['alamat'] ?? 'Desa Suwug, Kecamatan Sawan, Kabupaten Buleleng, Bali 81171' }}</p>
                        </div>
                    </div>

                    <div class="d-flex mb-4">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 p-3 rounded-circle text-success">
                                <i class="fas fa-phone fa-lg"></i>
                            </div>
                        </div>
                        <div class="ms-3">
                            <h6 class="fw-bold mb-1">Telepon</h6>
                            <p class="text-muted mb-0">{{ $schoolData['telepon'] ?? '085850190190' }}</p>
                        </div>
                    </div>

                    <div class="d-flex mb-4">
                        <div class="flex-shrink-0">
                            <div class="bg-info bg-opacity-10 p-3 rounded-circle text-info">
                                <i class="fas fa-envelope fa-lg"></i>
                            </div>
                        </div>
                        <div class="ms-3">
                            <h6 class="fw-bold mb-1">Email</h6>
                            <p class="text-muted mb-0">{{ $schoolData['email'] ?? 'admin@smpn3sawan.sch.id' }}</p>
                        </div>
                    </div>

                    @if(isset($schoolData['website']))
                    <div class="d-flex">
                        <div class="flex-shrink-0">
                            <div class="bg-warning bg-opacity-10 p-3 rounded-circle text-warning">
                                <i class="fas fa-globe fa-lg"></i>
                            </div>
                        </div>
                        <div class="ms-3">
                            <h6 class="fw-bold mb-1">Website</h6>
                            <a href="{{ $schoolData['website'] }}" target="_blank" class="text-decoration-none">
                                {{ $schoolData['website'] }}
                            </a>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Suggestion Box (Reuse form) -->
        <div class="col-lg-7">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body p-4">
                    <h4 class="mb-4 fw-bold">Kirim Pesan / Saran</h4>

                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    <form action="{{ route('guest.saran.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nama_pengirim" class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" id="nama_pengirim" name="nama_pengirim" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email_pengirim" class="form-label">Email (Opsional)</label>
                                <input type="email" class="form-control" id="email_pengirim" name="email_pengirim">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="isi_saran" class="form-label">Pesan / Saran</label>
                            <textarea class="form-control" id="isi_saran" name="isi_saran" rows="5" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary px-4">Kirim Pesan</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Maps -->
        @if(isset($schoolData['maps_latitude']) && isset($schoolData['maps_longitude']))
        <div class="col-12 mt-4">
            <div class="card border-0 shadow-sm overflow-hidden">
                <iframe
                    src="https://maps.google.com/maps?q={{ $schoolData['maps_latitude'] }},{{ $schoolData['maps_longitude'] }}&hl=id&z=16&output=embed"
                    width="100%"
                    height="400"
                    style="border:0;"
                    allowfullscreen=""
                    loading="lazy">
                </iframe>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
