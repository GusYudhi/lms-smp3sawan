@extends('layouts.app')

@section('title', 'Jurnal Mengajar')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1"><i class="fas fa-book text-primary me-2"></i>Jurnal Mengajar</h2>
                    <p class="text-muted mb-0">Kelola jurnal mengajar harian Anda</p>
                </div>
                <div>
                    <a href="{{ route('guru.jurnal-mengajar.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Isi Jurnal Baru
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fas fa-book-open fa-3x text-primary mb-3"></i>
            <h4>Selamat Datang di Jurnal Mengajar</h4>
            <p class="text-muted mb-4">Mulai isi jurnal mengajar Anda untuk mencatat kegiatan pembelajaran harian.</p>
            <a href="{{ route('guru.jurnal-mengajar.index') }}" class="btn btn-primary">
                <i class="fas fa-list me-2"></i>Lihat Daftar Jurnal
            </a>
        </div>
    </div>
</div>
@endsection
