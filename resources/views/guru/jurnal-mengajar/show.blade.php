@extends('layouts.app')

@section('title', 'Detail Jurnal Mengajar')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('guru.jurnal-mengajar.index') }}">Jurnal Mengajar</a></li>
                    <li class="breadcrumb-item active">Detail Jurnal</li>
                </ol>
            </nav>
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1"><i class="fas fa-book-open text-primary me-2"></i>Detail Jurnal Mengajar</h2>
                    <p class="text-muted mb-0">Informasi lengkap jurnal dan absensi siswa</p>
                </div>
                <div>
                    <a href="{{ route('guru.jurnal-mengajar.edit', $jurnal->id) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-2"></i>Edit Jurnal
                    </a>
                    <a href="{{ route('guru.jurnal-mengajar.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Kembali
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

    <!-- Informasi Jurnal -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Jurnal</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <td width="40%"><strong>Tanggal</strong></td>
                            <td>: {{ \Carbon\Carbon::parse($jurnal->tanggal)->format('d F Y') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Hari</strong></td>
                            <td>: {{ ucfirst($jurnal->hari) }}</td>
                        </tr>
                        <tr>
                            <td><strong>Jam Pelajaran</strong></td>
                            <td>: Jam ke-{{ $jurnal->jam_ke }}
                                ({{ \Carbon\Carbon::parse($jurnal->jam_mulai)->format('H:i') }} -
                                {{ \Carbon\Carbon::parse($jurnal->jam_selesai)->format('H:i') }})
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <td width="40%"><strong>Kelas</strong></td>
                            <td>: {{ $jurnal->kelas->nama_kelas }}</td>
                        </tr>
                        <tr>
                            <td><strong>Mata Pelajaran</strong></td>
                            <td>: {{ $jurnal->mataPelajaran->nama_mapel }}</td>
                        </tr>
                        <tr>
                            <td><strong>Guru Pengajar</strong></td>
                            <td>: {{ $jurnal->guru->guruProfile->nama ?? $jurnal->guru->name }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <hr>

            <div class="mb-3">
                <h6><strong>Materi Pembelajaran:</strong></h6>
                <div class="p-3 bg-light rounded">
                    {{ $jurnal->materi_pembelajaran }}
                </div>
            </div>

            @if($jurnal->keterangan)
                <div class="mb-3">
                    <h6><strong>Keterangan/Catatan:</strong></h6>
                    <div class="p-3 bg-light rounded">
                        {{ $jurnal->keterangan }}
                    </div>
                </div>
            @endif

            @if($jurnal->foto_bukti)
                <div>
                    <h6><strong>Foto Bukti Mengajar:</strong></h6>
                    <div class="p-3 bg-light rounded text-center">
                        <img src="{{ asset('storage/' . $jurnal->foto_bukti) }}"
                             alt="Foto Bukti Mengajar"
                             class="img-fluid rounded shadow"
                             style="max-height: 400px; cursor: pointer;"
                             onclick="showImageModal(this.src)">
                        <p class="text-muted small mt-2 mb-0">Klik gambar untuk memperbesar</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Rekap Absensi Siswa -->
    <div class="card">
        <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-user-check me-2"></i>Rekap Absensi Siswa</h5>
            <small>Total Siswa: {{ $siswaKelas->count() }}</small>
        </div>
        <div class="card-body">
            @php
                $hadir = $absensi->where('status', 'hadir')->count();
                $sakit = $absensi->where('status', 'sakit')->count();
                $izin = $absensi->where('status', 'izin')->count();
                $alpa = $absensi->where('status', 'alpa')->count();
                $belumAbsen = $siswaKelas->count() - $absensi->count();
            @endphp

            <!-- Summary Cards -->
            <div class="row mb-4">
                <div class="col-md-2 col-6 mb-3">
                    <div class="card text-center border-success">
                        <div class="card-body">
                            <h3 class="text-success mb-0">{{ $hadir }}</h3>
                            <small class="text-muted">Hadir</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 col-6 mb-3">
                    <div class="card text-center border-warning">
                        <div class="card-body">
                            <h3 class="text-warning mb-0">{{ $sakit }}</h3>
                            <small class="text-muted">Sakit</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 col-6 mb-3">
                    <div class="card text-center border-info">
                        <div class="card-body">
                            <h3 class="text-info mb-0">{{ $izin }}</h3>
                            <small class="text-muted">Izin</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 col-6 mb-3">
                    <div class="card text-center border-danger">
                        <div class="card-body">
                            <h3 class="text-danger mb-0">{{ $alpa }}</h3>
                            <small class="text-muted">Alpa</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 col-6 mb-3">
                    <div class="card text-center border-secondary">
                        <div class="card-body">
                            <h3 class="text-secondary mb-0">{{ $belumAbsen }}</h3>
                            <small class="text-muted">Belum Absen</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Daftar Siswa -->
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th width="15%">NIS</th>
                            <th width="35%">Nama Siswa</th>
                            <th width="15%">Status</th>
                            <th width="20%">Keterangan</th>
                            <th width="10%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($siswaKelas as $key => $siswa)
                            @php
                                $dataAbsensi = $absensi->get($siswa->user_id);
                                $status = $dataAbsensi ? $dataAbsensi->status : 'belum';
                                $notes = $dataAbsensi ? $dataAbsensi->notes : '-';
                            @endphp
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $siswa->nis }}</td>
                                <td>{{ $siswa->nama_lengkap }}</td>
                                <td>
                                    @if($status == 'hadir')
                                        <span class="badge bg-success">Hadir</span>
                                    @elseif($status == 'sakit')
                                        <span class="badge bg-warning">Sakit</span>
                                    @elseif($status == 'izin')
                                        <span class="badge bg-info">Izin</span>
                                    @elseif($status == 'alpa')
                                        <span class="badge bg-danger">Alpa</span>
                                    @else
                                        <span class="badge bg-secondary">Belum Absen</span>
                                    @endif
                                </td>
                                <td>
                                    <small>{{ $notes }}</small>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-primary dropdown-toggle"
                                                type="button"
                                                id="dropdownAbsensi{{ $siswa->id }}"
                                                data-bs-toggle="dropdown"
                                                aria-expanded="false">
                                            <i class="fas fa-edit me-1"></i>Edit
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownAbsensi{{ $siswa->id }}">
                                            <li>
                                                <a class="dropdown-item" href="#"
                                                   onclick="updateAbsensi({{ $siswa->user_id }}, 'hadir', '{{ $siswa->nama_lengkap }}'); return false;">
                                                    <i class="fas fa-check text-success me-2"></i>Hadir
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="#"
                                                   onclick="updateAbsensi({{ $siswa->user_id }}, 'sakit', '{{ $siswa->nama_lengkap }}'); return false;">
                                                    <i class="fas fa-notes-medical text-warning me-2"></i>Sakit
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="#"
                                                   onclick="updateAbsensi({{ $siswa->user_id }}, 'izin', '{{ $siswa->nama_lengkap }}'); return false;">
                                                    <i class="fas fa-clipboard-list text-info me-2"></i>Izin
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="#"
                                                   onclick="updateAbsensi({{ $siswa->user_id }}, 'alpa', '{{ $siswa->nama_lengkap }}'); return false;">
                                                    <i class="fas fa-times text-danger me-2"></i>Alpa
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Absensi -->
<div class="modal fade" id="modalEditAbsensi" tabindex="-1" aria-labelledby="modalEditAbsensiLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formEditAbsensi" action="{{ route('guru.jurnal-mengajar.update-absensi', $jurnal->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditAbsensiLabel">Edit Absensi Siswa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="user_id" id="userId">
                    <input type="hidden" name="status" id="statusAbsensi">

                    <div class="mb-3">
                        <label class="form-label"><strong>Nama Siswa</strong></label>
                        <p id="namaSiswa" class="form-control-plaintext"></p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><strong>Status</strong></label>
                        <p id="statusText" class="form-control-plaintext"></p>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Keterangan (Opsional)</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Tambahkan keterangan jika diperlukan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Simpan Absensi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function updateAbsensi(userId, status, namaSiswa) {
        document.getElementById('userId').value = userId;
        document.getElementById('statusAbsensi').value = status;
        document.getElementById('namaSiswa').textContent = namaSiswa;

        let statusBadge = '';
        switch(status) {
            case 'hadir':
                statusBadge = '<span class="badge bg-success">Hadir</span>';
                break;
            case 'sakit':
                statusBadge = '<span class="badge bg-warning">Sakit</span>';
                break;
            case 'izin':
                statusBadge = '<span class="badge bg-info">Izin</span>';
                break;
            case 'alpa':
                statusBadge = '<span class="badge bg-danger">Alpa</span>';
                break;
        }
        document.getElementById('statusText').innerHTML = statusBadge;

        // Clear previous notes
        document.getElementById('notes').value = '';

        // Show modal
        var modal = new bootstrap.Modal(document.getElementById('modalEditAbsensi'));
        modal.show();
    }

    function showImageModal(src) {
        document.getElementById('modalImage').src = src;
        var modal = new bootstrap.Modal(document.getElementById('modalImageZoom'));
        modal.show();
    }
</script>
@endpush

<!-- Modal Image Zoom -->
<div class="modal fade" id="modalImageZoom" tabindex="-1" aria-labelledby="modalImageZoomLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalImageZoomLabel">Foto Bukti Mengajar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" alt="Foto Bukti" class="img-fluid">
            </div>
        </div>
    </div>
</div>

@endsection
