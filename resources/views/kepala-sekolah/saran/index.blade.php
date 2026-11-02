@extends('layouts.app')

@section('title', 'Kotak Saran')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card card-modern border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h1 class="h3 mb-2 text-high-contrast">
                                <i class="fas fa-envelope-open-text text-primary me-2"></i>Kotak Saran & Aspirasi
                            </h1>
                            <p class="text-subtle mb-0 fw-medium">Kelola dan tinjau saran maupun kritik dari warga sekolah</p>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ route('kepala-sekolah.dashboard') }}" class="btn btn-outline-secondary shadow-sm">
                                <i class="fas fa-arrow-left me-1"></i>Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card card-modern border-0 shadow-sm mb-4">
        <div class="card-body">
            @if($sarans->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3 d-block opacity-50"></i>
                    <h5 class="text-muted">Kotak Saran Kosong</h5>
                    <p class="text-muted mb-0">Belum ada saran atau pesan masuk.</p>
                </div>
            @else
                <!-- Desktop Table View -->
                <div class="table-responsive d-none d-md-block">
                    <table class="table table-hover align-middle mb-0" id="dataTable">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 5%;">No</th>
                                <th>Tanggal</th>
                                <th>Pengirim</th>
                                <th>Email</th>
                                <th>Saran</th>
                                <th>Status</th>
                                <th class="text-center" style="width: 120px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sarans as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <strong>{{ $item->created_at->format('d M Y') }}</strong><br>
                                    <small class="text-muted">{{ $item->created_at->format('H:i') }}</small>
                                </td>
                                <td><strong class="text-body-emphasis">{{ $item->nama_pengirim }}</strong></td>
                                <td><a href="mailto:{{ $item->email_pengirim }}" class="text-decoration-none text-muted">{{ $item->email_pengirim }}</a></td>
                                <td>{{ $item->isi_saran }}</td>
                                <td>
                                    @if($item->status == 'belum_dibaca')
                                        <span class="badge bg-warning text-dark">Belum Dibaca</span>
                                    @else
                                        <span class="badge bg-success">Sudah Dibaca</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        @if($item->status == 'belum_dibaca')
                                        <form action="{{ route('kepala-sekolah.saran.update-status', $item->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn btn-outline-success" title="Tandai Sudah Dibaca">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                        @endif
                                        <form action="{{ route('kepala-sekolah.saran.destroy', $item->id) }}" method="POST" class="d-inline delete-form" data-message="Yakin ingin menghapus saran ini?">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Adaptive Cards View -->
                <div class="d-md-none">
                    @foreach($sarans as $index => $item)
                        <div class="saran-card-mobile {{ $item->status == 'belum_dibaca' ? '' : 'status-dibaca' }}">
                            <div class="saran-sender-info">
                                <div>
                                    <h6 class="mb-0 fw-bold text-body-emphasis">{{ $item->nama_pengirim }}</h6>
                                    <small class="text-muted">{{ $item->email_pengirim }}</small>
                                </div>
                                @if($item->status == 'belum_dibaca')
                                    <span class="badge bg-warning text-dark">Belum Dibaca</span>
                                @else
                                    <span class="badge bg-success">Sudah Dibaca</span>
                                @endif
                            </div>
                            <div class="saran-body-text">
                                {{ $item->isi_saran }}
                            </div>
                            <div class="saran-actions">
                                <small class="text-muted me-auto align-self-center">
                                    <i class="fas fa-clock me-1"></i>{{ $item->created_at->format('d M Y H:i') }}
                                </small>
                                @if($item->status == 'belum_dibaca')
                                <form action="{{ route('kepala-sekolah.saran.update-status', $item->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-sm btn-outline-success">
                                        <i class="fas fa-check me-1"></i>Tandai Dibaca
                                    </button>
                                </form>
                                @endif
                                <form action="{{ route('kepala-sekolah.saran.destroy', $item->id) }}" method="POST" class="d-inline delete-form" data-message="Yakin ingin menghapus saran ini?">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash me-1"></i>Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
