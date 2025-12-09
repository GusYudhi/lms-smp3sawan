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

    <!-- Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('guru.jurnal-mengajar.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="bulan" class="form-label">Bulan</label>
                    <select name="bulan" id="bulan" class="form-select">
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
                <div class="col-md-4">
                    <label for="tahun" class="form-label">Tahun</label>
                    <select name="tahun" id="tahun" class="form-select">
                        @for($i = date('Y'); $i >= date('Y') - 5; $i--)
                            <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-filter me-2"></i>Filter
                    </button>
                    <a href="{{ route('guru.jurnal-mengajar.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-redo me-2"></i>Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Daftar Jurnal -->
    <div class="card">
        <div class="card-body">
            @if($jurnals->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">No</th>
                                <th width="12%">Tanggal</th>
                                <th width="10%">Hari</th>
                                <th width="8%">Jam Ke</th>
                                <th width="10%">Kelas</th>
                                <th width="15%">Mata Pelajaran</th>
                                <th width="25%">Materi</th>
                                <th width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($jurnals as $key => $jurnal)
                                <tr>
                                    <td>{{ $jurnals->firstItem() + $key }}</td>
                                    <td>{{ \Carbon\Carbon::parse($jurnal->tanggal)->format('d/m/Y') }}</td>
                                    <td>{{ ucfirst($jurnal->hari) }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-primary">{{ $jurnal->jam_ke }}</span>
                                    </td>
                                    <td>
                                        <strong>{{ $jurnal->kelas->nama_kelas }}</strong>
                                    </td>
                                    <td>{{ $jurnal->mataPelajaran->nama_mapel }}</td>
                                    <td>
                                        <div class="text-truncate" style="max-width: 300px;" title="{{ $jurnal->materi_pembelajaran }}">
                                            {{ $jurnal->materi_pembelajaran }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('guru.jurnal-mengajar.show', $jurnal->id) }}"
                                               class="btn btn-sm btn-info"
                                               title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('guru.jurnal-mengajar.edit', $jurnal->id) }}"
                                               class="btn btn-sm btn-warning"
                                               title="Edit Jurnal">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button"
                                                    class="btn btn-sm btn-danger"
                                                    onclick="confirmDelete({{ $jurnal->id }})"
                                                    title="Hapus Jurnal">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                        <form id="delete-form-{{ $jurnal->id }}"
                                              action="{{ route('guru.jurnal-mengajar.destroy', $jurnal->id) }}"
                                              method="POST"
                                              style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
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
    function confirmDelete(id) {
        if (confirm('Apakah Anda yakin ingin menghapus jurnal ini?')) {
            document.getElementById('delete-form-' + id).submit();
        }
    }
</script>
@endpush
@endsection
