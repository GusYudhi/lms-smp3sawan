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

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(!empty($isWali) && $waliKelas)
        <div class="alert alert-info border-0 shadow-sm d-flex align-items-center mb-4" role="alert">
            <i class="fas fa-user-shield fs-4 me-3 text-primary"></i>
            <div>
                <strong>Akses Wali Kelas Aktif (Kelas {{ $waliKelas->full_name }}):</strong>
                <span class="d-block small text-muted">Anda memiliki hak akses pemantauan untuk melihat seluruh jurnal mengajar pada angkatan tingkat {{ $waliKelas->tingkat }}.</span>
            </div>
        </div>
    @endif

    <!-- Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('guru.jurnal-mengajar.index') }}" class="row g-3" id="filterForm">
                <div class="col-md-3">
                    <label for="bulan" class="form-label">Bulan</label>
                    <select name="bulan" id="bulan" class="form-select auto-submit">
                        <option value="01" {{ $bulan == '01' ? 'selected' : '' }}>Januari</option>
                        <option value="02" {{ $bulan == '02' ? 'selected' : '' }}>Februari</option>
                        <option value="03" {{ $bulan == '03' ? 'selected' : '' }}>Maret</option>
                        <option value="04" {{ $bulan == '04' ? 'selected' : '' }}>April</option>
                        <option value="05" {{ $bulan == '05' ? 'selected' : '' }}>Mei</option>
                        <option value="06" {{ $bulan == '06' ? 'selected' : '' }}>Juni</option>
                        <option value="07" {{ $bulan == '07' ? 'selected' : '' }}>Juli</option>
                        <option value="08" {{ $bulan == '08' ? 'selected' : '' }}>Agustus</option>
                        <option value="09" {{ $bulan == '09' ? 'selected' : '' }}>September</option>
                        <option value="10" {{ $bulan == '10' ? 'selected' : '' }}>Oktober</option>
                        <option value="11" {{ $bulan == '11' ? 'selected' : '' }}>November</option>
                        <option value="12" {{ $bulan == '12' ? 'selected' : '' }}>Desember</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="tahun" class="form-label">Tahun</label>
                    <select name="tahun" id="tahun" class="form-select auto-submit">
                        @for($i = date('Y'); $i >= date('Y') - 5; $i--)
                            <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="kelas_id" class="form-label">Kelas</label>
                    <select name="kelas_id" id="kelas_id" class="form-select auto-submit">
                        <option value="">Semua Kelas</option>
                        @foreach($kelasList as $kelas)
                            <option value="{{ $kelas->id }}" {{ $kelasId == $kelas->id ? 'selected' : '' }}>
                                {{ $kelas->full_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="mata_pelajaran_id" class="form-label">Mata Pelajaran</label>
                    <select name="mata_pelajaran_id" id="mata_pelajaran_id" class="form-select auto-submit">
                        <option value="">Semua Mata Pelajaran</option>
                        @foreach($mataPelajaranList as $mapel)
                            <option value="{{ $mapel->id }}" {{ $mataPelajaranId == $mapel->id ? 'selected' : '' }}>
                                {{ $mapel->nama_mapel }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-12">
                    <a href="{{ route('guru.jurnal-mengajar.index') }}" class="btn btn-secondary">
                        <i class="fas fa-redo me-1"></i>Reset
                    </a>
                    <small class="text-muted ms-2">
                        <i class="fas fa-info-circle me-1"></i>Filter akan diterapkan otomatis
                    </small>
                </div>
            </form>
        </div>
    </div>

    <!-- Daftar Jurnal -->
    <div class="card">
        <div class="card-body">
            @if($jurnals->count() > 0)
                <!-- Desktop Table View -->
                <div class="table-responsive d-none d-md-block">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">No</th>
                                <th width="12%">Tanggal</th>
                                <th width="8%">Jam Ke</th>
                                <th width="10%">Kelas</th>
                                <th width="15%">Mata Pelajaran</th>
                                <th width="20%">Materi</th>
                                <th width="15%">Guru</th>
                                <th width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($jurnals as $key => $jurnal)
                                <tr>
                                    <td>{{ $jurnals->firstItem() + $key }}</td>
                                    <td>
                                        <div><strong>{{ \Carbon\Carbon::parse($jurnal->tanggal)->format('d/m/Y') }}</strong></div>
                                        <small class="text-muted">{{ ucfirst($jurnal->hari) }}</small>
                                    </td>
                                    <td class="text-center">
                                        @if($jurnal->jam_ke_mulai == $jurnal->jam_ke_selesai)
                                            <span class="badge bg-primary">{{ $jurnal->jam_ke_mulai }}</span>
                                        @else
                                            <span class="badge bg-primary">{{ $jurnal->jam_ke_mulai }}-{{ $jurnal->jam_ke_selesai }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $jurnal->kelas->full_name }}</span>
                                    </td>
                                    <td>{{ $jurnal->mataPelajaran->nama_mapel }}</td>
                                    <td>
                                        <div class="text-truncate" style="max-width: 250px;" title="{{ $jurnal->materi_pembelajaran }}">
                                            {{ $jurnal->materi_pembelajaran }}
                                        </div>
                                    </td>
                                    <td>
                                        @if($jurnal->guru_id === Auth::id())
                                            <span class="badge bg-success-subtle text-success border border-success-subtle">Saya</span>
                                        @else
                                            <small class="fw-semibold text-secondary">{{ $jurnal->guru->name ?? 'Guru Lain' }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-inline-flex align-items-center gap-1">
                                            <a href="{{ route('guru.jurnal-mengajar.show', $jurnal->id) }}"
                                               class="btn btn-sm btn-outline-primary"
                                               title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($jurnal->guru_id === Auth::id())
                                                <a href="{{ route('guru.jurnal-mengajar.edit', $jurnal->id) }}"
                                                   class="btn btn-sm btn-outline-warning"
                                                   title="Edit Jurnal">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button"
                                                        class="btn btn-sm btn-outline-danger"
                                                        onclick="confirmDelete({{ $jurnal->id }})"
                                                        title="Hapus Jurnal">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endif
                                        </div>
                                        @if($jurnal->guru_id === Auth::id())
                                            <form id="delete-form-{{ $jurnal->id }}"
                                                  action="{{ route('guru.jurnal-mengajar.destroy', $jurnal->id) }}"
                                                  method="POST"
                                                  style="display: none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card View -->
                <div class="d-md-none d-flex flex-column gap-3">
                    @foreach($jurnals as $key => $jurnal)
                        <div class="card border border-light-subtle shadow-sm rounded-3">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-primary">Kelas {{ $jurnal->kelas->full_name }}</span>
                                        <span class="badge bg-secondary">
                                            Jam {{ $jurnal->jam_ke_mulai }}{{ $jurnal->jam_ke_mulai != $jurnal->jam_ke_selesai ? '-'.$jurnal->jam_ke_selesai : '' }}
                                        </span>
                                    </div>
                                    <small class="text-muted fw-semibold">
                                        {{ \Carbon\Carbon::parse($jurnal->tanggal)->format('d M Y') }}
                                    </small>
                                </div>

                                <h6 class="fw-bold text-dark mb-1">{{ $jurnal->mataPelajaran->nama_mapel }}</h6>
                                <p class="text-muted small mb-2 text-truncate-2">
                                    {{ $jurnal->materi_pembelajaran }}
                                </p>

                                <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                    <div>
                                        @if($jurnal->guru_id === Auth::id())
                                            <span class="badge bg-success-subtle text-success">Oleh Saya</span>
                                        @else
                                            <small class="text-muted"><i class="fas fa-chalkboard-teacher me-1"></i>{{ $jurnal->guru->name ?? 'Guru Lain' }}</small>
                                        @endif
                                    </div>
                                    <div class="d-flex align-items-center gap-1">
                                        <a href="{{ route('guru.jurnal-mengajar.show', $jurnal->id) }}"
                                           class="btn btn-sm btn-outline-primary px-2 py-1">
                                            <i class="fas fa-eye me-1"></i>Detail
                                        </a>
                                        @if($jurnal->guru_id === Auth::id())
                                            <a href="{{ route('guru.jurnal-mengajar.edit', $jurnal->id) }}"
                                               class="btn btn-sm btn-outline-warning px-2 py-1">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button"
                                                    class="btn btn-sm btn-outline-danger px-2 py-1"
                                                    onclick="confirmDelete({{ $jurnal->id }})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">
                        Menampilkan {{ $jurnals->firstItem() }} - {{ $jurnals->lastItem() }} dari {{ $jurnals->total() }} data
                    </div>
                    <div>
                        {{ $jurnals->links() }}
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-book-open fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Belum ada jurnal mengajar untuk periode ini.</p>
                    <a href="{{ route('guru.jurnal-mengajar.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Isi Jurnal Pertama
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Auto-submit functionality
    $('.auto-submit').on('change', function() {
        $('#filterForm').submit();
    });

    function confirmDelete(id) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Jurnal ini akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }
</script>
@endpush
@endsection
