@extends('layouts.app')

@section('title', 'Manajemen Data Kelas')

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
                                <i class="fas fa-school text-primary me-2"></i>Data Kelas
                            </h1>
                            <p class="text-subtle mb-0 fw-medium">Kelola data kelas, wali kelas, dan informasi angkatan.</p>
                        </div>
                        <div class="col-md-4 text-end">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createKelasModal">
                                <i class="fas fa-plus me-1"></i> Tambah Kelas
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Messages handled by Layout via SweetAlert/Session but kept here for fallback -->
    @if(session('success'))
        <div class="alert alert-success d-none">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger d-none">{{ session('error') }}</div>
    @endif

    <!-- Data Table -->
    <div class="card card-modern border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th width="12%">Nama Kelas</th>
                            <th width="8%">Tingkat</th>
                            <th width="10%">Angkatan</th>
                            <th width="20%">Wali Kelas</th>
                            <th width="20%">Guru Pendamping</th>
                            <th width="12%" class="text-center">Jumlah Siswa</th>
                            <th width="13%" class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kelas as $index => $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td class="fw-bold">{{ $item->full_name }}</td>
                                <td><span class="badge bg-info text-dark">Kelas {{ $item->tingkat }}</span></td>
                                <td>{{ $item->tahun_angkatan }}</td>
                                <td>
                                    @if($item->waliKelas)
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-2">
                                                <img src="{{ $item->waliKelas->getProfilePhotoUrl() }}"
                                                     alt="Foto"
                                                     class="rounded-circle"
                                                     style="width: 32px; height: 32px; object-fit: cover;">
                                            </div>
                                            <div>
                                                <span class="d-block fw-medium">{{ $item->waliKelas->nama }}</span>
                                                <small class="text-muted" style="font-size: 0.75rem;">{{ $item->waliKelas->nip ?? '-' }}</small>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted fst-italic">- Belum ada -</span>
                                    @endif
                                </td>
                                <td>
                                    @if($item->guruPendamping)
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-2">
                                                <img src="{{ $item->guruPendamping->getProfilePhotoUrl() }}"
                                                     alt="Foto"
                                                     class="rounded-circle"
                                                     style="width: 32px; height: 32px; object-fit: cover;">
                                            </div>
                                            <div>
                                                <span class="d-block fw-medium">{{ $item->guruPendamping->nama }}</span>
                                                <small class="text-muted" style="font-size: 0.75rem;">{{ $item->guruPendamping->nip ?? '-' }}</small>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted fst-italic">- Belum ada -</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-secondary rounded-pill px-3">{{ $item->students_count }}</span>
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-primary me-1"
                                            onclick="editKelas({{ $item->id }}, '{{ $item->nama_kelas }}', '{{ $item->tingkat }}', '{{ $item->tahun_angkatan }}', '{{ $item->waliKelas ? $item->waliKelas->id : '' }}', '{{ $item->guru_pendamping_id ?? '' }}', '{{ $item->tahun_pelajaran_id }}')"
                                            title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger"
                                            onclick="confirmDelete({{ $item->id }}, '{{ $item->full_name }}', {{ $item->students_count }})"
                                            title="Hapus"
                                            {{ $item->students_count > 0 ? 'disabled' : '' }}>
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">Belum ada data kelas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Create Kelas -->
<div class="modal fade" id="createKelasModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="fas fa-plus-circle me-2"></i>Tambah Kelas Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.kelas.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="tingkat" class="form-label fw-medium">Tingkat</label>
                        <select class="form-select" name="tingkat" id="tingkat" required>
                            <option value="" selected disabled>-- Pilih Tingkat --</option>
                            <option value="7">Kelas 7</option>
                            <option value="8">Kelas 8</option>
                            <option value="9">Kelas 9</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="nama_kelas" class="form-label fw-medium">Nama Kelas (Suffix)</label>
                        <input type="text" class="form-control" name="nama_kelas" id="nama_kelas" placeholder="Contoh: A, B, Unggulan" required>
                        <small class="text-muted">Hanya masukkan huruf/nama belakangnya saja. Contoh hasil: 7A</small>
                    </div>
                    <div class="mb-3">
                        <label for="tahun_angkatan" class="form-label fw-medium">Tahun Angkatan</label>
                        <input type="number" class="form-control" name="tahun_angkatan" id="tahun_angkatan" value="{{ date('Y') }}" min="2000" required>
                    </div>
                    <div class="mb-3">
                        <label for="tahun_pelajaran_id" class="form-label fw-medium">Tahun Pelajaran</label>
                        <select class="form-select" name="tahun_pelajaran_id" id="tahun_pelajaran_id">
                            <option value="">-- Pilih Tahun Pelajaran --</option>
                            @foreach($tahunPelajarans as $tp)
                                <option value="{{ $tp->id }}" {{ ($activeTahunPelajaran && $activeTahunPelajaran->id == $tp->id) ? 'selected' : '' }}>
                                    {{ $tp->nama }} {{ $tp->is_active ? '(Aktif)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="wali_kelas_id" class="form-label fw-medium">Wali Kelas (Opsional)</label>
                        <select class="form-select" name="wali_kelas_id" id="wali_kelas_id" onchange="syncTeacherDropdowns('create')">
                            <option value="">-- Kosongkan / Belum Ada --</option>
                            @foreach($gurus as $guru)
                                <option value="{{ $guru->id }}">
                                    {{ $guru->nama }}
                                    @if($guru->kelas_id) (Sudah Wali Kelas {{ $guru->kelas->full_name ?? 'Lain' }}) @endif
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Guru yang sudah menjadi wali kelas ditandai.</small>
                    </div>
                    <div class="mb-3">
                        <label for="guru_pendamping_id" class="form-label fw-medium">Guru Pendamping (Kokurikuler)</label>
                        <select class="form-select" name="guru_pendamping_id" id="guru_pendamping_id">
                            <option value="">-- Kosongkan / Belum Ada --</option>
                            @foreach($gurus as $guru)
                                @php
                                    $isWali = !empty($guru->kelas_id);
                                    $assistedClasses = $guru->kelasPendamping->pluck('full_name')->join(', ');
                                @endphp
                                <option value="{{ $guru->id }}" {{ $isWali ? 'disabled' : '' }} data-is-wali="{{ $isWali ? '1' : '0' }}">
                                    {{ $guru->nama }}
                                    @if($isWali)
                                        (Wali Kelas {{ $guru->kelas->full_name ?? 'Lain' }})
                                    @elseif(!empty($assistedClasses))
                                        (Pendamping Kelas {{ $assistedClasses }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Guru pendamping membantu wali kelas dalam absensi dan dokumentasi kokurikuler (wajib non-wali kelas).</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Kelas -->
<div class="modal fade" id="editKelasModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="fas fa-edit me-2"></i>Edit Data Kelas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editKelasForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_tingkat" class="form-label fw-medium">Tingkat</label>
                        <select class="form-select" name="tingkat" id="edit_tingkat" required>
                            <option value="7">Kelas 7</option>
                            <option value="8">Kelas 8</option>
                            <option value="9">Kelas 9</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_nama_kelas" class="form-label fw-medium">Nama Kelas (Suffix)</label>
                        <input type="text" class="form-control" name="nama_kelas" id="edit_nama_kelas" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_tahun_angkatan" class="form-label fw-medium">Tahun Angkatan</label>
                        <input type="number" class="form-control" name="tahun_angkatan" id="edit_tahun_angkatan" min="2000" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_tahun_pelajaran_id" class="form-label fw-medium">Tahun Pelajaran</label>
                        <select class="form-select" name="tahun_pelajaran_id" id="edit_tahun_pelajaran_id">
                            <option value="">-- Pilih Tahun Pelajaran --</option>
                            @foreach($tahunPelajarans as $tp)
                                <option value="{{ $tp->id }}">
                                    {{ $tp->nama }} {{ $tp->is_active ? '(Aktif)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_wali_kelas_id" class="form-label fw-medium">Wali Kelas</label>
                        <select class="form-select" name="wali_kelas_id" id="edit_wali_kelas_id" onchange="syncTeacherDropdowns('edit')">
                            <option value="">-- Kosongkan / Belum Ada --</option>
                            @foreach($gurus as $guru)
                                <option value="{{ $guru->id }}">
                                    {{ $guru->nama }}
                                    @if($guru->kelas_id) (Wali Kelas {{ $guru->kelas->full_name ?? 'Lain' }}) @endif
                                </option>
                            @endforeach
                        </select>
                        <div class="alert alert-info mt-2 py-2 small">
                            <i class="fas fa-info-circle me-1"></i> Jika memilih guru yang sudah punya kelas lain, status wali kelas lama mereka akan diganti ke kelas ini.
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_guru_pendamping_id" class="form-label fw-medium">Guru Pendamping (Kokurikuler)</label>
                        <select class="form-select" name="guru_pendamping_id" id="edit_guru_pendamping_id">
                            <option value="">-- Kosongkan / Belum Ada --</option>
                            @foreach($gurus as $guru)
                                @php
                                    $isWali = !empty($guru->kelas_id);
                                    $assistedClasses = $guru->kelasPendamping->pluck('full_name')->join(', ');
                                @endphp
                                <option value="{{ $guru->id }}" {{ $isWali ? 'disabled' : '' }} data-is-wali="{{ $isWali ? '1' : '0' }}">
                                    {{ $guru->nama }}
                                    @if($isWali)
                                        (Wali Kelas {{ $guru->kelas->full_name ?? 'Lain' }})
                                    @elseif(!empty($assistedClasses))
                                        (Pendamping Kelas {{ $assistedClasses }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Guru pendamping membantu wali kelas dalam absensi dan dokumentasi kokurikuler (wajib non-wali kelas).</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Hidden Delete Form -->
<form id="deleteKelasForm" method="POST" class="d-none">
    @csrf
    @method('DELETE')
</form>

@endsection

@push('scripts')
<script>
    function syncTeacherDropdowns(mode) {
        const waliSelect = document.getElementById(mode === 'create' ? 'wali_kelas_id' : 'edit_wali_kelas_id');
        const pendampingSelect = document.getElementById(mode === 'create' ? 'guru_pendamping_id' : 'edit_guru_pendamping_id');
        const selectedWali = waliSelect ? waliSelect.value : '';

        if (!pendampingSelect) return;

        Array.from(pendampingSelect.options).forEach(opt => {
            if (!opt.value) return;
            const originalIsWali = opt.getAttribute('data-is-wali') === '1';

            if (opt.value === selectedWali) {
                opt.disabled = true;
                if (pendampingSelect.value === opt.value) {
                    pendampingSelect.value = '';
                }
            } else {
                opt.disabled = originalIsWali;
            }
        });
    }

    function editKelas(id, nama, tingkat, angkatan, waliKelasId, guruPendampingId, tahunPelajaranId) {
        // Set values
        document.getElementById('edit_nama_kelas').value = nama;
        document.getElementById('edit_tingkat').value = tingkat;
        document.getElementById('edit_tahun_angkatan').value = angkatan;
        document.getElementById('edit_wali_kelas_id').value = waliKelasId || "";
        document.getElementById('edit_guru_pendamping_id').value = guruPendampingId || "";
        document.getElementById('edit_tahun_pelajaran_id').value = tahunPelajaranId || "";

        syncTeacherDropdowns('edit');

        // Set action url
        const form = document.getElementById('editKelasForm');
        form.action = `/admin/kelas/${id}`;

        // Show modal
        new bootstrap.Modal(document.getElementById('editKelasModal')).show();
    }

    function confirmDelete(id, namaKelas, studentCount) {
        if (studentCount > 0) {
            Swal.fire({
                icon: 'error',
                title: 'Tidak Bisa Dihapus',
                text: `Kelas ${namaKelas} masih memiliki ${studentCount} siswa. Pindahkan atau hapus siswa terlebih dahulu.`
            });
            return;
        }

        Swal.fire({
            title: 'Hapus Kelas?',
            text: `Anda yakin ingin menghapus kelas ${namaKelas}? Data tidak bisa dikembalikan.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('deleteKelasForm');
                form.action = `/admin/kelas/${id}`;
                form.submit();
            }
        });
    }
</script>
@endpush
