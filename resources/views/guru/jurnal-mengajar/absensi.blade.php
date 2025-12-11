@extends('layouts.app')

@section('title', 'Absensi Siswa - Jurnal Mengajar')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('guru.jurnal-mengajar.index') }}">Jurnal Mengajar</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('guru.jurnal-mengajar.create', ['tanggal' => $jurnal->tanggal]) }}">Isi Jurnal</a></li>
                    <li class="breadcrumb-item active">Absensi Siswa</li>
                </ol>
            </nav>
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1"><i class="fas fa-user-check text-primary me-2"></i>Absensi Siswa</h2>
                    <p class="text-muted mb-0">Edit absensi siswa untuk jurnal mengajar ini</p>
                </div>
                <div>
                    <a href="{{ route('guru.jurnal-mengajar.create', ['tanggal' => $jurnal->tanggal]) }}" class="btn btn-outline-secondary">
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
                <div class="col-md-3">
                    <p class="mb-2"><strong>Tanggal:</strong></p>
                    <p>{{ \Carbon\Carbon::parse($jurnal->tanggal)->format('d F Y') }}</p>
                </div>
                <div class="col-md-3">
                    <p class="mb-2"><strong>Hari:</strong></p>
                    <p>{{ ucfirst($jurnal->hari) }}</p>
                </div>
                <div class="col-md-3">
                    <p class="mb-2"><strong>Jam Pelajaran:</strong></p>
                    <p>
                        @if($jurnal->jam_ke_mulai == $jurnal->jam_ke_selesai)
                            Jam ke-{{ $jurnal->jam_ke_mulai }}
                        @else
                            Jam ke-{{ $jurnal->jam_ke_mulai }} - {{ $jurnal->jam_ke_selesai }}
                        @endif
                    </p>
                </div>
                <div class="col-md-3">
                    <p class="mb-2"><strong>Kelas:</strong></p>
                    <p>Kelas {{ $jurnal->kelas->full_name }}</p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <p class="mb-2"><strong>Mata Pelajaran:</strong></p>
                    <p>{{ $jurnal->mataPelajaran->nama_mapel }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Rekap Absensi -->
    <div class="card mb-4">
        <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Rekap Absensi</h5>
            <small>Total Siswa: {{ $jurnal->jurnalAttendances->count() }}</small>
        </div>
        <div class="card-body">
            @php
                $hadir = $jurnal->jurnalAttendances->where('status', 'hadir')->count();
                $sakit = $jurnal->jurnalAttendances->where('status', 'sakit')->count();
                $izin = $jurnal->jurnalAttendances->where('status', 'izin')->count();
                $alpa = $jurnal->jurnalAttendances->where('status', 'alpa')->count();
            @endphp

            <div class="row">
                <div class="col-md-3 col-6 mb-3">
                    <div class="card text-center border-success">
                        <div class="card-body">
                            <h3 class="text-success mb-0">{{ $hadir }}</h3>
                            <small class="text-muted">Hadir</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-3">
                    <div class="card text-center border-warning">
                        <div class="card-body">
                            <h3 class="text-warning mb-0">{{ $sakit }}</h3>
                            <small class="text-muted">Sakit</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-3">
                    <div class="card text-center border-info">
                        <div class="card-body">
                            <h3 class="text-info mb-0">{{ $izin }}</h3>
                            <small class="text-muted">Izin</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-3">
                    <div class="card text-center border-danger">
                        <div class="card-body">
                            <h3 class="text-danger mb-0">{{ $alpa }}</h3>
                            <small class="text-muted">Alpa</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Daftar Siswa -->
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-users me-2"></i>Daftar Absensi Siswa</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>NIS</th>
                            <th>Nama Siswa</th>
                            <th>Status Awal</th>
                            <th>Status Saat Ini</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($jurnal->jurnalAttendances as $index => $attendance)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $attendance->studentProfile->nis }}</td>
                                <td>{{ $attendance->studentProfile->user->name ?? '-' }}</td>
                                <td>
                                    @if($attendance->status_awal == 'hadir')
                                        <span class="badge bg-success">Hadir</span>
                                    @elseif($attendance->status_awal == 'terlambat')
                                        <span class="badge bg-warning">Terlambat</span>
                                    @elseif($attendance->status_awal == 'sakit')
                                        <span class="badge bg-warning">Sakit</span>
                                    @elseif($attendance->status_awal == 'izin')
                                        <span class="badge bg-info">Izin</span>
                                    @elseif($attendance->status_awal == 'alpa')
                                        <span class="badge bg-danger">Alpa</span>
                                    @else
                                        <span class="badge bg-secondary">Belum Absen</span>
                                    @endif
                                </td>
                                <td>
                                    @if($attendance->status == 'hadir')
                                        <span class="badge bg-success">Hadir</span>
                                    @elseif($attendance->status == 'terlambat')
                                        <span class="badge bg-warning">Terlambat</span>
                                    @elseif($attendance->status == 'sakit')
                                        <span class="badge bg-warning">Sakit</span>
                                    @elseif($attendance->status == 'izin')
                                        <span class="badge bg-info">Izin</span>
                                    @else
                                        <span class="badge bg-danger">Alpa</span>
                                    @endif
                                </td>
                                <td>
                                    <button type="button"
                                            class="btn btn-sm btn-outline-primary"
                                            onclick="editAbsensi({{ $attendance->id }}, {{ $attendance->student_profile_id }}, '{{ $attendance->studentProfile->user->name ?? '-' }}', '{{ $attendance->status }}')">
                                        <i class="fas fa-edit me-1"></i>Edit
                                    </button>
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
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditAbsensiLabel">Edit Absensi Siswa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('guru.jurnal-mengajar.update-jurnal-absensi', $jurnal->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="student_profile_id" id="student_profile_id">

                    <div class="mb-3">
                        <label class="form-label"><strong>Nama Siswa:</strong></label>
                        <p id="namaSiswa" class="text-muted"></p>
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label"><strong>Status Absensi:</strong></label>
                        <select class="form-select" name="status" id="status" required>
                            <option value="hadir">Hadir</option>
                            <option value="sakit">Sakit</option>
                            <option value="izin">Izin</option>
                            <option value="alpa">Alpa</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function editAbsensi(attendanceId, studentProfileId, namaSiswa, currentStatus) {
        document.getElementById('student_profile_id').value = studentProfileId;
        document.getElementById('namaSiswa').textContent = namaSiswa;
        document.getElementById('status').value = currentStatus;

        var modal = new bootstrap.Modal(document.getElementById('modalEditAbsensi'));
        modal.show();
    }
</script>
@endpush

@endsection
