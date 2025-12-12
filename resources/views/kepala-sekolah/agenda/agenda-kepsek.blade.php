@extends('layouts.app')

@section('title', 'Agenda Kepala Sekolah')

@section('content')
<div class="container-fluid">
    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card card-modern border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h1 class="h3 mb-2 text-high-contrast">
                                <i class="fas fa-calendar-alt text-primary me-2"></i>Agenda Kepala Sekolah
                            </h1>
                            <p class="text-subtle mb-0 fw-medium">Kelola agenda dan jurnal kegiatan kepala sekolah</p>
                        </div>
                        <div class="col-md-4 text-end">
                            <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addAgendaModal">
                                <i class="fas fa-plus me-1"></i>
                                Tambah Agenda
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter dan Pencarian -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card card-modern border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="card-title text-high-contrast fw-semibold mb-3">
                        <i class="fas fa-filter text-primary me-2"></i>Filter & Pencarian
                    </h6>
                    <form method="GET" action="{{ route('kepala-sekolah.agenda') }}" id="filterForm">
                        <div class="row g-3 align-items-end">
                            <!-- Search Input -->
                            <div class="col-md-4">
                                <label for="agendaSearch" class="form-label text-medium-contrast fw-medium">Pencarian</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-search text-muted"></i>
                                    </span>
                                    <input type="text"
                                           name="search"
                                           id="agendaSearch"
                                           class="form-control border-start-0 search-input"
                                           placeholder="Cari materi atau kompetensi..."
                                           value="{{ request('search') }}">
                                </div>
                            </div>

                            <!-- Kelas Filter -->
                            <div class="col-md-2">
                                <label for="kelasFilter" class="form-label text-medium-contrast fw-medium">Kelas</label>
                                <select name="kelas" id="kelasFilter" class="form-select auto-submit">
                                    <option value="">Semua Kelas</option>
                                    @foreach($kelasList as $kelas)
                                        <option value="{{ $kelas->full_name }}" {{ request('kelas') == $kelas->full_name ? 'selected' : '' }}>
                                            {{ $kelas->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Status Jurnal Filter -->
                            <div class="col-md-2">
                                <label for="statusFilter" class="form-label text-medium-contrast fw-medium">Status Jurnal</label>
                                <select name="status" id="statusFilter" class="form-select auto-submit">
                                    <option value="">Semua Status</option>
                                    <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                    <option value="belum_selesai" {{ request('status') == 'belum_selesai' ? 'selected' : '' }}>Belum Selesai</option>
                                </select>
                            </div>

                            <!-- Keterangan Filter -->
                            <div class="col-md-2">
                                <label for="keteranganFilter" class="form-label text-medium-contrast fw-medium">Keterangan</label>
                                <select name="keterangan" id="keteranganFilter" class="form-select auto-submit">
                                    <option value="">Semua</option>
                                    <option value="pengayaan" {{ request('keterangan') == 'pengayaan' ? 'selected' : '' }}>Pengayaan</option>
                                    <option value="perbaikan" {{ request('keterangan') == 'perbaikan' ? 'selected' : '' }}>Perbaikan</option>
                                    <option value="-" {{ request('keterangan') == '-' ? 'selected' : '' }}>Tidak Ada</option>
                                </select>
                            </div>

                            <!-- Action Buttons -->
                            <div class="col-md-2">
                                <a href="{{ route('kepala-sekolah.agenda') }}" class="btn btn-secondary w-100 shadow-sm fw-medium">
                                    <i class="fas fa-redo me-1"></i> Reset
                                </a>
                                <small class="text-muted d-block mt-2">
                                    <i class="fas fa-info-circle me-1"></i>Auto-submit
                                </small>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Agenda -->
    <div class="row">
        <div class="col-12">
            <div class="card card-modern border-0 shadow-sm">
                <div class="card-header bg-light border-bottom-0 py-3">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="card-title mb-0 text-high-contrast fw-semibold">
                                <i class="fas fa-table text-primary me-2"></i>Daftar Agenda dan Jurnal
                            </h6>
                        </div>
                        <div class="col-auto">
                            <span class="badge bg-primary-subtle text-primary border fw-medium">
                                Total: <strong>{{ $agendas->total() }}</strong> Agenda
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
                        <table class="table table-hover align-middle mb-0" id="agendaTable" style="min-width: 1100px; width: 100%;">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" class="text-center fw-semibold" style="min-width: 50px;">#</th>
                                    <th scope="col" class="fw-semibold" style="min-width: 150px;">Hari/Tanggal</th>
                                    <th scope="col" class="text-center fw-semibold" style="min-width: 100px;">Kelas</th>
                                    <th scope="col" class="text-center fw-semibold" style="min-width: 100px;">Jam Ke-</th>
                                    <th scope="col" class="fw-semibold" style="min-width: 250px;">Batasan Materi/Kompetensi</th>
                                    <th scope="col" class="text-center fw-semibold" style="min-width: 150px;">Jurnal</th>
                                    <th scope="col" class="text-center fw-semibold" style="min-width: 150px;">Keterangan</th>
                                    <th scope="col" class="text-center fw-semibold" style="min-width: 150px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($agendas as $index => $agenda)
                                <tr>
                                    <td class="text-center fw-bold" style="min-width: 50px;">{{ $agendas->firstItem() + $index }}</td>
                                    <td style="min-width: 150px;">
                                        <div class="text-start">
                                            <div class="fw-semibold mb-1">{{ $agenda->hari }}</div>
                                            <small class="text-muted fst-italic">{{ $agenda->tanggal->format('d M Y') }}</small>
                                        </div>
                                    </td>
                                    <td class="text-center" style="min-width: 100px;">
                                        <span class="badge bg-primary-subtle text-primary border">{{ $agenda->kelas }}</span>
                                    </td>
                                    <td class="text-center" style="min-width: 100px;">
                                        <span class="badge bg-info-subtle text-info border">{{ $agenda->jam_ke }}</span>
                                    </td>
                                    <td style="min-width: 250px;">
                                        <div class="text-start" title="{{ $agenda->materi }}">
                                            {{ $agenda->materi }}
                                        </div>
                                    </td>
                                    <td class="text-center" style="min-width: 150px;">
                                        @if($agenda->status_jurnal == 'selesai')
                                            <span class="badge bg-success-subtle text-success border">
                                                <i class="fas fa-check-circle me-1"></i>Selesai
                                            </span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger border">
                                                <i class="fas fa-times-circle me-1"></i>Belum Selesai
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center" style="min-width: 150px;">
                                        @if($agenda->keterangan == 'pengayaan')
                                            <span class="badge bg-warning-subtle text-warning border">
                                                <i class="fas fa-star me-1"></i>Pengayaan
                                            </span>
                                        @elseif($agenda->keterangan == 'perbaikan')
                                            <span class="badge bg-info-subtle text-info border">
                                                <i class="fas fa-wrench me-1"></i>Perbaikan
                                            </span>
                                        @else
                                            <span class="text-muted fst-italic">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center" style="min-width: 150px;">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button"
                                                    class="btn btn-outline-warning"
                                                    onclick="editAgenda({{ $agenda->id }}, '{{ $agenda->tanggal->format('Y-m-d') }}', '{{ $agenda->kelas }}', {{ $agenda->jam_mulai_id }}, {{ $agenda->jam_selesai_id }}, `{{ addslashes($agenda->materi) }}`, '{{ $agenda->status_jurnal }}', '{{ $agenda->keterangan }}')"
                                                    title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button"
                                                    class="btn btn-outline-danger"
                                                    onclick="deleteAgenda({{ $agenda->id }})"
                                                    title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="fas fa-calendar-times fa-3x mb-3 opacity-50"></i>
                                            <p class="mb-0 fw-medium">Belum ada agenda yang ditambahkan</p>
                                            <small>Klik tombol "Tambah Agenda" untuk membuat agenda baru</small>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-light border-top-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            Menampilkan <strong>{{ $agendas->firstItem() ?? 0 }}-{{ $agendas->lastItem() ?? 0 }}</strong> dari <strong>{{ $agendas->total() }}</strong> agenda
                        </div>
                        <nav>
                            {{ $agendas->links('pagination::bootstrap-5') }}
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Agenda -->
<div class="modal fade" id="addAgendaModal" tabindex="-1" aria-labelledby="addAgendaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="addAgendaModalLabel">
                    <i class="fas fa-plus-circle me-2"></i>Tambah Agenda Baru
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('kepala-sekolah.agenda.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <!-- Tanggal -->
                        <div class="col-md-6">
                            <label for="tanggal" class="form-label">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="tanggal" name="tanggal" required>
                        </div>

                        <!-- Kelas -->
                        <div class="col-md-6">
                            <label for="kelas" class="form-label">Kelas <span class="text-danger">*</span></label>
                            <select class="form-select" id="kelas" name="kelas" required>
                                <option value="">Pilih Kelas</option>
                                @foreach($kelasList as $kelas)
                                    <option value="{{ $kelas->full_name }}">{{ $kelas->full_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Jam Mulai -->
                        <div class="col-md-6">
                            <label for="jam_mulai_id" class="form-label">Jam Mulai <span class="text-danger">*</span></label>
                            <select class="form-select" id="jam_mulai_id" name="jam_mulai_id" required>
                                <option value="">Pilih Jam Mulai</option>
                                @foreach($jamPelajarans as $jam)
                                    <option value="{{ $jam->id }}">Jam ke-{{ $jam->jam_ke }} ({{ $jam->jam_mulai }} - {{ $jam->jam_selesai }})</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Jam Selesai -->
                        <div class="col-md-6">
                            <label for="jam_selesai_id" class="form-label">Jam Selesai <span class="text-danger">*</span></label>
                            <select class="form-select" id="jam_selesai_id" name="jam_selesai_id" required>
                                <option value="">Pilih Jam Selesai</option>
                                @foreach($jamPelajarans as $jam)
                                    <option value="{{ $jam->id }}">Jam ke-{{ $jam->jam_ke }} ({{ $jam->jam_mulai }} - {{ $jam->jam_selesai }})</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Batasan Materi/Kompetensi -->
                        <div class="col-12">
                            <label for="materi" class="form-label">Batasan Materi/Kompetensi <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="materi" name="materi" rows="3" placeholder="Tuliskan batasan materi atau kompetensi yang akan diajarkan..." required></textarea>
                        </div>

                        <!-- Status Jurnal -->
                        <div class="col-md-6">
                            <label for="status_jurnal" class="form-label">Status Jurnal <span class="text-danger">*</span></label>
                            <select class="form-select" id="status_jurnal" name="status_jurnal" required>
                                <option value="">Pilih Status</option>
                                <option value="selesai">Selesai</option>
                                <option value="belum_selesai">Belum Selesai</option>
                            </select>
                        </div>

                        <!-- Keterangan -->
                        <div class="col-md-6">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <select class="form-select" id="keterangan" name="keterangan">
                                <option value="-">Tidak Ada</option>
                                <option value="pengayaan">Pengayaan</option>
                                <option value="perbaikan">Perbaikan</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Simpan Agenda
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Agenda -->
<div class="modal fade" id="editAgendaModal" tabindex="-1" aria-labelledby="editAgendaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="editAgendaModalLabel">
                    <i class="fas fa-edit me-2"></i>Edit Agenda
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editAgendaForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row g-3">
                        <!-- Tanggal -->
                        <div class="col-md-6">
                            <label for="edit_tanggal" class="form-label">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="edit_tanggal" name="tanggal" required>
                        </div>

                        <!-- Kelas -->
                        <div class="col-md-6">
                            <label for="edit_kelas" class="form-label">Kelas <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_kelas" name="kelas" required>
                                <option value="">Pilih Kelas</option>
                                @foreach($kelasList as $kelas)
                                    <option value="{{ $kelas->full_name }}">{{ $kelas->full_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Jam Mulai -->
                        <div class="col-md-6">
                            <label for="edit_jam_mulai_id" class="form-label">Jam Mulai <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_jam_mulai_id" name="jam_mulai_id" required>
                                <option value="">Pilih Jam Mulai</option>
                                @foreach($jamPelajarans as $jam)
                                    <option value="{{ $jam->id }}">Jam ke-{{ $jam->jam_ke }} ({{ $jam->jam_mulai }} - {{ $jam->jam_selesai }})</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Jam Selesai -->
                        <div class="col-md-6">
                            <label for="edit_jam_selesai_id" class="form-label">Jam Selesai <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_jam_selesai_id" name="jam_selesai_id" required>
                                <option value="">Pilih Jam Selesai</option>
                                @foreach($jamPelajarans as $jam)
                                    <option value="{{ $jam->id }}">Jam ke-{{ $jam->jam_ke }} ({{ $jam->jam_mulai }} - {{ $jam->jam_selesai }})</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Batasan Materi/Kompetensi -->
                        <div class="col-12">
                            <label for="edit_materi" class="form-label">Batasan Materi/Kompetensi <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="edit_materi" name="materi" rows="3" required></textarea>
                        </div>

                        <!-- Status Jurnal -->
                        <div class="col-md-6">
                            <label for="edit_status_jurnal" class="form-label">Status Jurnal <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_status_jurnal" name="status_jurnal" required>
                                <option value="selesai">Selesai</option>
                                <option value="belum_selesai">Belum Selesai</option>
                            </select>
                        </div>

                        <!-- Keterangan -->
                        <div class="col-md-6">
                            <label for="edit_keterangan" class="form-label">Keterangan</label>
                            <select class="form-select" id="edit_keterangan" name="keterangan">
                                <option value="-">Tidak Ada</option>
                                <option value="pengayaan">Pengayaan</option>
                                <option value="perbaikan">Perbaikan</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save me-1"></i> Update Agenda
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="deleteAgendaModal" tabindex="-1" aria-labelledby="deleteAgendaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white border-bottom-0">
                <h5 class="modal-title" id="deleteAgendaModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>Konfirmasi Hapus
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <i class="fas fa-trash-alt text-danger mb-3" style="font-size: 3rem;"></i>
                <h6 class="fw-bold mb-3">Apakah Anda yakin ingin menghapus agenda ini?</h6>
                <p class="text-muted mb-0">Data yang dihapus tidak dapat dikembalikan!</p>
            </div>
            <div class="modal-footer border-top-0 justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Batal
                </button>
                <form id="deleteAgendaForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i> Ya, Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Auto-submit functionality
    $('.auto-submit').on('change', function() {
        $('#filterForm').submit();
    });

    // Search input - submit on Enter
    $('.search-input').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            $('#filterForm').submit();
        }
    });

    // Edit Agenda
    function editAgenda(id, tanggal, kelas, jamMulaiId, jamSelesaiId, materi, statusJurnal, keterangan) {
        document.getElementById('edit_tanggal').value = tanggal;
        document.getElementById('edit_kelas').value = kelas;
        document.getElementById('edit_jam_mulai_id').value = jamMulaiId;
        document.getElementById('edit_jam_selesai_id').value = jamSelesaiId;
        document.getElementById('edit_materi').value = materi;
        document.getElementById('edit_status_jurnal').value = statusJurnal;
        document.getElementById('edit_keterangan').value = keterangan;

        document.getElementById('editAgendaForm').action = `/kepala-sekolah/agenda/${id}`;

        const modal = new bootstrap.Modal(document.getElementById('editAgendaModal'));
        modal.show();
    }

    // Delete Agenda
    function deleteAgenda(id) {
        document.getElementById('deleteAgendaForm').action = `/kepala-sekolah/agenda/${id}`;

        const modal = new bootstrap.Modal(document.getElementById('deleteAgendaModal'));
        modal.show();
    }

    // Auto hide alert after 5 seconds
    setTimeout(function() {
        const alert = document.querySelector('.alert');
        if (alert) {
            alert.style.transition = 'opacity 0.5s ease';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }
    }, 5000);

    // Handle scroll indicator untuk table
    document.addEventListener('DOMContentLoaded', function() {
        const tableResponsive = document.querySelector('.table-responsive');
        if (tableResponsive) {
            // Check if scrollable
            function checkScroll() {
                const isScrollable = tableResponsive.scrollWidth > tableResponsive.clientWidth;
                const isScrolledToEnd = tableResponsive.scrollLeft >= (tableResponsive.scrollWidth - tableResponsive.clientWidth - 10);

                if (isScrolledToEnd) {
                    tableResponsive.classList.add('scrolled-end');
                } else {
                    tableResponsive.classList.remove('scrolled-end');
                }
            }

            tableResponsive.addEventListener('scroll', checkScroll);
            window.addEventListener('resize', checkScroll);
            checkScroll(); // Initial check
        }
    });
</script>
@endpush

@push('styles')
<style>
    /* Removed - using inline styles instead for better mobile compatibility */
</style>
@endpush
@endsection
