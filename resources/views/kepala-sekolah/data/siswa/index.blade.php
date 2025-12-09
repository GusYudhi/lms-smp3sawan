@extends('layouts.app')

@section('title', 'Data Siswa')

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
                                <i class="fas fa-user-graduate text-success me-2"></i>Data Siswa
                            </h1>
                            <p class="text-subtle mb-0 fw-medium">Lihat informasi data siswa</p>
                            <span class="badge bg-info mt-2">Mode Lihat Saja</span>
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

    <!-- Statistik Siswa -->
    <div class="row g-4 mb-4">
        <!-- Total Siswa -->
        <div class="col-xl-4 col-md-6">
            <div class="card card-stats text-center h-100">
                <div class="card-body">
                    <div class="text-success fs-1 mb-3">
                        <i class="fas fa-users"></i>
                    </div>
                    <h6 class="card-title text-muted">Total Siswa</h6>
                    <h2 class="text-success">{{ \App\Models\User::where('role', 'siswa')->count() }}</h2>
                    <small class="text-muted">Semua Siswa</small>
                </div>
            </div>
        </div>

        <!-- Siswa Kelas 7 -->
        <div class="col-xl-4 col-md-6">
            <div class="card card-stats text-center h-100">
                <div class="card-body">
                    <div class="text-info fs-1 mb-3">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <h6 class="card-title text-muted">Siswa Kelas 7</h6>
                    <h2 class="text-info">{{ \App\Models\StudentProfile::whereHas('kelas', function($q) { $q->where('tingkat', 7); })->count() }}</h2>
                    <small class="text-muted">Kelas VII</small>
                </div>
            </div>
        </div>

        <!-- Total Kelas -->
        <div class="col-xl-4 col-md-6">
            <div class="card card-stats text-center h-100">
                <div class="card-body">
                    <div class="text-primary fs-1 mb-3">
                        <i class="fas fa-door-open"></i>
                    </div>
                    <h6 class="card-title text-muted">Total Kelas</h6>
                    <h2 class="text-primary">{{ \App\Models\Kelas::count() }}</h2>
                    <small class="text-muted">Semua Tingkat</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form action="{{ route('kepala-sekolah.siswa.index') }}" method="GET" class="row g-3">
                        <div class="col-md-7">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text"
                                       name="search"
                                       class="form-control"
                                       placeholder="Cari berdasarkan nama, NISN, NIS, atau email..."
                                       value="{{ $search }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select name="kelas" class="form-select">
                                <option value="">Semua Kelas</option>
                                @foreach($kelasList as $kelas)
                                <option value="{{ $kelas->id }}" {{ $kelasFilter == $kelas->id ? 'selected' : '' }}>
                                    {{ $kelas->tingkat }}{{ $kelas->nama_kelas }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search me-1"></i>Cari
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Siswa Table -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-list me-2"></i>Daftar Siswa
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="25%">Nama Lengkap</th>
                                    <th width="12%">NISN</th>
                                    <th width="12%">NIS</th>
                                    <th width="15%">Kelas</th>
                                    <th width="20%">Email</th>
                                    <th width="11%" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $index => $user)
                                <tr>
                                    <td>{{ $users->firstItem() + $index }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle bg-success text-white me-2">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                            <strong>{{ $user->name }}</strong>
                                        </div>
                                    </td>
                                    <td>{{ $user->studentProfile->nisn ?? '-' }}</td>
                                    <td>{{ $user->studentProfile->nis ?? '-' }}</td>
                                    <td>
                                        @if($user->studentProfile && $user->studentProfile->kelas)
                                        <span class="badge bg-primary">
                                            {{ $user->studentProfile->kelas->tingkat }}{{ $user->studentProfile->kelas->nama_kelas }}
                                        </span>
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('kepala-sekolah.siswa.show', $user->id) }}"
                                           class="btn btn-sm btn-info"
                                           title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <i class="fas fa-inbox text-muted fs-1 mb-3 d-block"></i>
                                        <p class="text-muted mb-0">Tidak ada data siswa</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white border-top">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            Menampilkan {{ $users->firstItem() ?? 0 }} - {{ $users->lastItem() ?? 0 }} dari {{ $users->total() }} data
                        </div>
                        <div>
                            {{ $users->appends(['search' => $search, 'kelas' => $kelasFilter])->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 1.2em;
}

.card-stats {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    transition: all 0.3s ease;
}

.card-stats:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    transform: translateY(-5px);
}
</style>
@endsection
