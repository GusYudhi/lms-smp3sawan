@extends($semester ? 'layouts.semester' : 'layouts.app')

@section('title', 'Manajemen Jadwal Tetap')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Manajemen Jadwal Tetap & Tema Kokurikuler</h1>
            @if($semester)
            <p class="text-muted mb-0">
                <small>{{ $semester->tahunPelajaran->nama }} - {{ $semester->nama }}</small>
            </p>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Tambah Jadwal Tetap</h6>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('admin.fixed-schedule.store') }}" method="POST">
                        @csrf
                        @if($semester)
                        <input type="hidden" name="semester_id" value="{{ $semester->id }}">
                        @endif
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Hari</label>
                            <select class="form-select" name="hari" required>
                                <option value="">Pilih Hari</option>
                                @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $hari)
                                    <option value="{{ $hari }}">{{ $hari }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Jam Ke-</label>
                            <select class="form-select" name="jam_ke" required>
                                <option value="">Pilih Jam</option>
                                @foreach($jamPelajarans as $jam)
                                    <option value="{{ $jam->jam_ke }}">Jam ke-{{ $jam->jam_ke }} ({{ \Carbon\Carbon::parse($jam->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jam->jam_selesai)->format('H:i') }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Kategori Kegiatan</label>
                            <select class="form-select" name="tipe" id="create_tipe" required>
                                <option value="umum">Umum (Istirahat / Kegiatan Non-Penilaian)</option>
                                <option value="kokurikuler">Kokurikuler / Pembiasaan (Upacara Bendera / Tema Harian — Dinilai Wali Kelas)</option>
                            </select>
                            <small class="text-muted d-block mt-1">
                                Pilih <strong>Kokurikuler / Pembiasaan</strong> untuk kegiatan Jam ke-0 (seperti Upacara Bendera &amp; Tema Kokurikuler) yang dinilai oleh Wali Kelas.
                            </small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold" id="create_keterangan_label">Keterangan / Nama Tema</label>
                            <input type="text" class="form-control" name="keterangan" id="create_keterangan_input" placeholder="Contoh: Pembiasaan Upacara Bendera / Tema Literasi" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-save me-1"></i> Simpan Jadwal Tetap
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Daftar Jadwal Tetap (Berlaku Semua Kelas)</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle" width="100%" cellspacing="0">
                            <thead class="table-light">
                                <tr>
                                    <th>Hari</th>
                                    <th>Jam Ke</th>
                                    <th>Kategori</th>
                                    <th>Keterangan / Tema</th>
                                    <th class="text-center" style="width: 120px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($fixedSchedules as $fs)
                                    <tr>
                                        <td class="fw-semibold">{{ $fs->hari }}</td>
                                        <td>
                                            <span class="badge bg-secondary">Jam ke-{{ $fs->jam_ke }}</span>
                                        </td>
                                        <td>
                                            @if($fs->tipe === 'kokurikuler')
                                                <span class="badge bg-success">
                                                    <i class="fas fa-star me-1"></i>Kokurikuler
                                                </span>
                                            @else
                                                <span class="badge bg-light text-dark border">Umum</span>
                                            @endif
                                        </td>
                                        <td>{{ $fs->keterangan }}</td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-warning me-1"
                                                onclick="openEditFixedModal({{ $fs->id }}, @js($fs->hari), {{ $fs->jam_ke }}, @js($fs->tipe ?? 'umum'), @js($fs->keterangan))"
                                                title="Edit Jadwal Tetap">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form action="{{ route('admin.fixed-schedule.destroy', $fs->id) }}" method="POST" class="d-inline delete-form" data-message="Yakin ingin menghapus jadwal tetap ini?">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">Belum ada jadwal tetap</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editFixedScheduleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="editFixedScheduleForm" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Jadwal Tetap / Tema Kokurikuler</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Hari</label>
                        <select class="form-select" name="hari" id="edit_hari" required>
                            @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $hari)
                                <option value="{{ $hari }}">{{ $hari }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Jam Ke-</label>
                        <select class="form-select" name="jam_ke" id="edit_jam_ke" required>
                            @foreach($jamPelajarans as $jam)
                                <option value="{{ $jam->jam_ke }}">Jam ke-{{ $jam->jam_ke }} ({{ \Carbon\Carbon::parse($jam->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jam->jam_selesai)->format('H:i') }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kategori Kegiatan</label>
                        <select class="form-select" name="tipe" id="edit_tipe" required>
                            <option value="umum">Umum (Istirahat / Kegiatan Non-Penilaian)</option>
                            <option value="kokurikuler">Kokurikuler / Pembiasaan (Upacara Bendera / Tema Harian — Dinilai Wali Kelas)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Keterangan / Nama Tema</label>
                        <input type="text" class="form-control" name="keterangan" id="edit_keterangan" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openEditFixedModal(id, hari, jamKe, tipe, keterangan) {
        const form = document.getElementById('editFixedScheduleForm');
        form.action = `{{ url('admin/fixed-schedule') }}/${id}`;
        document.getElementById('edit_hari').value = hari;
        document.getElementById('edit_jam_ke').value = jamKe;
        document.getElementById('edit_tipe').value = tipe || 'umum';
        document.getElementById('edit_keterangan').value = keterangan;

        const modal = new bootstrap.Modal(document.getElementById('editFixedScheduleModal'));
        modal.show();
    }
</script>
@endpush
@endsection
