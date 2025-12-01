@extends($semester ? 'layouts.semester' : 'layouts.app')

@section('title', 'Jadwal Pelajaran')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Manajemen Jadwal Pelajaran</h1>
            @if($semester)
            <p class="text-muted mb-0">
                <small>{{ $semester->tahunPelajaran->nama }} - {{ $semester->nama }}</small>
            </p>
            @endif
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <label for="filter_kelas" class="form-label fw-bold">Pilih Kelas:</label>
                    <select class="form-select" id="filter_kelas">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($kelas as $k)
                            <option value="{{ $k->id }}">{{ $k->full_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-8 text-end">
                    <div id="loading-indicator" class="d-none">
                        <div class="spinner-border text-primary spinner-border-sm" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <span class="ms-2 text-muted">Memuat jadwal...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Schedule Grid -->
    <div id="schedule-grid" class="d-none" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
        <table class="table table-bordered mb-0" style="min-width: 1000px; table-layout: fixed;">
            <colgroup>
                <col style="width: 100px;">
                @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $hari)
                <col style="width: 150px;">
                @endforeach
            </colgroup>
            <thead class="bg-light text-center">
                <tr>
                    <th class="align-middle">Waktu</th>
                    @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $hari)
                        <th class="align-middle">{{ $hari }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @for ($i = 1; $i <= 7; $i++)
                    @php
                        $jam = $jamPelajarans->firstWhere('jam_ke', $i);
                        $waktu = $jam ? \Carbon\Carbon::parse($jam->jam_mulai)->format('H:i') . ' - ' . \Carbon\Carbon::parse($jam->jam_selesai)->format('H:i') : '-';
                    @endphp
                    <tr>
                        <td class="align-middle text-center bg-light">
                            <span class="badge bg-secondary mb-1">Jam Ke-{{ $i }}</span><br>
                            <small class="fw-bold text-dark" style="font-size: 0.8rem;">{{ $waktu }}</small>
                        </td>
                        @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $hari)
                            <td id="cell-{{ $hari }}-{{ $i }}" class="p-1" style="height: 1px;">
                                <!-- Content injected by JS -->
                            </td>
                        @endforeach
                    </tr>
                @endfor
            </tbody>
        </table>
    </div>

    <!-- Empty State -->
    <div id="empty-state" class="text-center py-5">
        <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" alt="Select Class" style="width: 150px; opacity: 0.5;">
        <h5 class="mt-3 text-muted">Silakan pilih kelas terlebih dahulu</h5>
    </div>
</div>

<!-- Add/Edit Modal -->
<div class="modal fade" id="scheduleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Tambah Jadwal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="scheduleForm">
                @csrf
                <input type="hidden" id="schedule_id" name="id">
                <input type="hidden" id="kelas_id" name="kelas_id">
                <input type="hidden" id="hari" name="hari">
                <input type="hidden" id="jam_ke" name="jam_ke">
                @if($semester)
                <input type="hidden" name="semester_id" value="{{ $semester->id }}">
                @endif

                <div class="modal-body">
                    <div class="alert alert-info">
                        <strong><span id="modal-hari"></span></strong>, Jam ke-<strong><span id="modal-jam"></span></strong>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Mata Pelajaran</label>
                        <select class="form-select" id="mata_pelajaran_id" name="mata_pelajaran_id" required>
                            <option value="">Pilih Mata Pelajaran</option>
                            @foreach($mapels as $m)
                                <option value="{{ $m->id }}">{{ $m->nama_mapel }} ({{ $m->kode_mapel }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Guru Pengampu</label>
                        <select class="form-select" id="guru_id" name="guru_id" required>
                            <option value="">Pilih Guru</option>
                            @foreach($gurus as $g)
                                <option value="{{ $g->id }}">{{ $g->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3" id="div-jumlah-jam">
                        <label class="form-label">Jumlah Jam (Durasi)</label>
                        <input type="number" class="form-control" id="jumlah_jam" name="jumlah_jam" value="1" min="1" max="7" required>
                        <small class="text-muted">Masukkan jumlah jam pelajaran (misal: 2 untuk 2 jam pelajaran berturut-turut)</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger d-none" id="btn-delete">Hapus</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btn-save">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    const scheduleModal = new bootstrap.Modal(document.getElementById('scheduleModal'));
    let currentKelasId = null;

    // Handle Class Selection
    $('#filter_kelas').change(function() {
        currentKelasId = $(this).val();
        if (currentKelasId) {
            loadSchedules(currentKelasId);
            $('#schedule-grid').removeClass('d-none');
            $('#empty-state').addClass('d-none');
        } else {
            $('#schedule-grid').addClass('d-none');
            $('#empty-state').removeClass('d-none');
        }
    });

    // Load Schedules via AJAX
    function loadSchedules(kelasId) {
        $('#loading-indicator').removeClass('d-none');

        // Clear existing items
        $('[id^="cell-"]').empty();

        const url = `{{ url('admin/jadwal-mapel/get-by-kelas') }}/${kelasId}`;
        const params = @if($semester) { semester_id: {{ $semester->id }} } @else {} @endif;

        $.get(url, params)
            .done(function(data) {
                Object.keys(data).forEach(hari => {
                    const slots = data[hari];

                    // Render 7 slots
                    for (let i = 1; i <= 7; i++) {
                        const cell = $(`#cell-${hari}-${i}`);
                        const item = slots[i];

                        let content = '';
                        let onClick = `onclick="openAddModal('${hari}', ${i})"`;
                        let style = 'cursor: pointer; height: 100%; width: 100%; min-height: 80px;';

                        if (item) {
                            if (item.type === 'fixed') {
                                onClick = '';
                                style = 'height: 100%; width: 100%; min-height: 80px; background-color: #f8f9fa;';
                                content = `
                                    <div class="d-flex align-items-center justify-content-center h-100 w-100 text-center">
                                        <div>
                                            <i class="fas fa-lock mb-1 text-secondary"></i><br>
                                            <small class="fw-bold text-secondary">${item.keterangan}</small>
                                        </div>
                                    </div>
                                `;
                            } else if (item.type === 'lesson') {
                                onClick = `onclick='openEditModal(${JSON.stringify(item)}, "${hari}")'`;
                                style = 'cursor: pointer; height: 100%; width: 100%; min-height: 80px;';
                                content = `
                                    <div class="card border-start border-4 border-primary shadow-sm h-100 w-100 border-0">
                                        <div class="card-body p-2">
                                            <h6 class="mb-1 fw-bold text-dark" style="font-size: 0.9rem;">${item.mata_pelajaran.nama_mapel}</h6>
                                            <div class="small text-secondary">
                                                <i class="far fa-user me-1"></i> ${item.guru.name}
                                            </div>
                                        </div>
                                    </div>
                                `;
                            }
                        } else {
                            // Empty slot
                            content = `
                                <div class="d-flex align-items-center justify-content-center h-100 w-100 text-muted" style="border: 2px dashed #dee2e6; border-radius: 4px;">
                                    <i class="fas fa-plus"></i>
                                </div>
                            `;
                        }

                        cell.html(`<div style="${style}" ${onClick}>${content}</div>`);
                    }
                });
            })
            .fail(function() {
                alert('Gagal memuat jadwal');
            })
            .always(function() {
                $('#loading-indicator').addClass('d-none');
            });
    }

    // Open Modal for Adding
    window.openAddModal = function(hari, jamKe) {
        if (!currentKelasId) return;

        // Reset form
        $('#scheduleForm')[0].reset();
        $('#schedule_id').val('');
        $('#kelas_id').val(currentKelasId);
        $('#hari').val(hari);
        $('#jam_ke').val(jamKe);

        // Show Duration Field
        $('#div-jumlah-jam').removeClass('d-none');
        $('#jumlah_jam').val(1);

        $('#modal-hari').text(hari);
        $('#modal-jam').text(jamKe);

        $('#modalTitle').text('Tambah Jadwal');
        $('#btn-delete').addClass('d-none');
        $('#btn-save').text('Simpan');

        scheduleModal.show();
    };

    // Open Modal for Editing
    window.openEditModal = function(data, hari) {
        $('#schedule_id').val(data.id);
        $('#kelas_id').val(currentKelasId); // Should be same
        $('#hari').val(hari);
        $('#jam_ke').val(data.jam_ke);
        $('#mata_pelajaran_id').val(data.mata_pelajaran.id);
        $('#guru_id').val(data.guru.id);

        // Hide Duration Field (Edit only single slot)
        $('#div-jumlah-jam').addClass('d-none');

        $('#modal-hari').text(hari);
        $('#modal-jam').text(data.jam_ke);

        $('#modalTitle').text('Edit Jadwal');
        $('#btn-delete').removeClass('d-none');
        $('#btn-save').text('Update');

        scheduleModal.show();
    };

    // Handle Form Submission
    $('#scheduleForm').submit(function(e) {
        e.preventDefault();

        const id = $('#schedule_id').val();
        const url = id ? `{{ url('admin/jadwal-mapel') }}/${id}` : `{{ route('admin.jadwal.store') }}`;
        const method = id ? 'PUT' : 'POST';

        $.ajax({
            url: url,
            type: method,
            data: $(this).serialize(),
            success: function(response) {
                scheduleModal.hide();
                loadSchedules(currentKelasId);
                // Show toast or alert
                // alert(response.message);
            },
            error: function(xhr) {
                const msg = xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan';
                alert(msg);
            }
        });
    });

    // Handle Delete
    $('#btn-delete').click(function() {
        if (!confirm('Yakin ingin menghapus jadwal ini?')) return;

        const id = $('#schedule_id').val();

        $.ajax({
            url: `{{ url('admin/jadwal-mapel') }}/${id}`,
            type: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                scheduleModal.hide();
                loadSchedules(currentKelasId);
            },
            error: function(xhr) {
                alert('Gagal menghapus jadwal');
            }
        });
    });
});
</script>
@endpush
