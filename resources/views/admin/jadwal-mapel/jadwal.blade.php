@extends($semester ? 'layouts.semester' : 'layouts.app')

@section('title', 'Jadwal Pelajaran')

@section('content')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

<style>
    .draggable-item {
        cursor: grab;
        transition: transform 0.1s ease, box-shadow 0.1s ease;
    }
    .draggable-item:active {
        cursor: grabbing;
    }
    .draggable-item.dragging {
        opacity: 0.5;
        transform: scale(0.95);
    }
    .drop-zone {
        transition: background-color 0.2s ease;
    }
    .drop-zone.drag-over {
        background-color: #e9ecef !important;
        border: 2px dashed #0d6efd !important;
    }
</style>

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
            <form id="filterForm" method="GET">
                <div class="row align-items-end g-3">
                    <div class="col-md-4">
                        <label for="filter_kelas" class="form-label fw-bold">Pilih Kelas:</label>
                        <select class="form-select auto-submit" id="filter_kelas" name="kelas_id">
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($kelas as $k)
                                <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>{{ $k->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="filter_hari" class="form-label fw-bold">Tampilan:</label>
                        <select class="form-select auto-submit" id="filter_hari" name="hari">
                            <option value="">Semua Hari</option>
                            <option value="hari-ini" {{ request('hari') == 'hari-ini' ? 'selected' : '' }}>Jadwal Hari Ini</option>
                            <option value="Senin" {{ request('hari') == 'Senin' ? 'selected' : '' }}>Senin</option>
                            <option value="Selasa" {{ request('hari') == 'Selasa' ? 'selected' : '' }}>Selasa</option>
                            <option value="Rabu" {{ request('hari') == 'Rabu' ? 'selected' : '' }}>Rabu</option>
                            <option value="Kamis" {{ request('hari') == 'Kamis' ? 'selected' : '' }}>Kamis</option>
                            <option value="Jumat" {{ request('hari') == 'Jumat' ? 'selected' : '' }}>Jumat</option>
                            <option value="Sabtu" {{ request('hari') == 'Sabtu' ? 'selected' : '' }}>Sabtu</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('admin.jadwal.index', ['semester_id' => $semester->id]) }}" class="btn btn-secondary">
                            <i class="fas fa-redo me-1"></i>Reset
                        </a>
                    </div>
                    <div class="col-md-2 text-end">
                        <div id="loading-indicator" class="d-none">
                            <div class="spinner-border text-primary spinner-border-sm" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <span class="ms-2 text-muted">Memuat...</span>
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-12">
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>Filter akan diterapkan otomatis
                        </small>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Schedule Grid -->
    <div id="schedule-grid" class="d-none" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
        @php
            $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            $filterHari = request('hari');

            // Determine which days to show
            if ($filterHari == 'hari-ini') {
                $today = \Carbon\Carbon::now()->locale('id')->dayName;
                $daysToShow = in_array($today, $days) ? [$today] : ['Senin']; // Default to Senin if not found
            } elseif ($filterHari && $filterHari != '') {
                $daysToShow = [$filterHari];
            } else {
                $daysToShow = $days;
            }
        @endphp

        <table class="table table-bordered mb-0" style="min-width: 1000px; table-layout: fixed;">
            <colgroup>
                <col style="width: 100px;">
                @foreach($daysToShow as $hari)
                <col style="width: 150px;">
                @endforeach
            </colgroup>
            <thead class="bg-light text-center">
                <tr>
                    <th class="align-middle">Waktu</th>
                    @foreach($daysToShow as $hari)
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
                        @foreach($daysToShow as $hari)
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
                        <select class="form-select select2" id="mata_pelajaran_id" name="mata_pelajaran_id" required>
                            <option value="">Tidak Ada</option>
                            @foreach($mapels as $m)
                                <option value="{{ $m->id }}">{{ $m->nama_mapel }} ({{ $m->kode_mapel }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Guru Pengampu</label>
                        <div class="w-100">
                            <select class="form-select select2" id="guru_id" name="guru_id" required>
                                <option value="">Pilih Guru</option>
                                @foreach($gurus as $g)
                                    <option value="{{ $g->id }}" data-mapel-id="{{ $g->guruProfile->mata_pelajaran_id ?? '' }}">
                                        {{ $g->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-3" id="div-jumlah-jam">
                        <label class="form-label">Jumlah Jam (Durasi)</label>
                        <input type="number" class="form-control" id="jumlah_jam" name="jumlah_jam" value="1" min="1" max="7" required>
                        <small class="text-muted">Masukkan jumlah jam pelajaran (misal: 2 untuk 2 jam pelajaran berturut-turut)</small>
                    </div>

                    <!-- Conflict Alert -->
                    <div id="conflict-alert" class="bg-warning bg-opacity-10 border border-warning rounded p-3 mt-3 d-none">
                        <div class="d-flex">
                            <div class="me-2 text-warning">
                                <i class="fas fa-exclamation-triangle fa-lg mt-1"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1 text-dark">Konflik Jadwal!</h6>
                                <p class="mb-0 small text-dark" id="conflict-message"></p>
                            </div>
                        </div>
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
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        theme: 'bootstrap-5',
        dropdownParent: $('#scheduleModal'),
        width: '100%'
    });

    // Auto-focus search input on open
    $(document).on('select2:open', () => {
        document.querySelector('.select2-search__field').focus();
    });

    // Also for Copy Modal (if used)
    $('#copyModal .form-select').select2({
        theme: 'bootstrap-5',
        dropdownParent: $('#copyModal'),
        width: '100%'
    });

    // --- Dynamic Dropdown Logic ---
    const $guruSelect = $('#guru_id');
    const $mapelSelect = $('#mata_pelajaran_id');
    
    // Store original teacher options for filtering
    let allGuruOptions = [];
    $guruSelect.find('option').each(function() {
        if ($(this).val()) { // Skip placeholder
            allGuruOptions.push({
                id: $(this).val(),
                text: $(this).text(),
                mapelId: $(this).data('mapel-id')
            });
        }
    });

    // 1. When Subject Changes -> Filter Teachers
    $mapelSelect.on('change', function() {
        const selectedMapelId = $(this).val();
        
        // Save current teacher selection if it matches the new mapel
        const currentGuruId = $guruSelect.val();
        
        // Clear current options (except placeholder)
        $guruSelect.empty().append('<option value="">Pilih Guru</option>');
        
        let foundCurrent = false;

        if (selectedMapelId) {
            // Filter teachers who teach this subject
            allGuruOptions.forEach(guru => {
                // Loose comparison (==) because data attr might be string/int
                if (guru.mapelId == selectedMapelId) {
                    const newOption = new Option(guru.text, guru.id, false, false);
                    $(newOption).attr('data-mapel-id', guru.mapelId);
                    $guruSelect.append(newOption);
                    
                    if (guru.id == currentGuruId) foundCurrent = true;
                }
            });
        } else {
            // If no subject selected, show all teachers
            allGuruOptions.forEach(guru => {
                const newOption = new Option(guru.text, guru.id, false, false);
                $(newOption).attr('data-mapel-id', guru.mapelId);
                $guruSelect.append(newOption);
                if (guru.id == currentGuruId) foundCurrent = true;
            });
        }

        // Restore selection if valid, otherwise clear
        if (foundCurrent) {
            $guruSelect.val(currentGuruId);
        } else {
            $guruSelect.val('');
        }
        
        // Re-trigger Select2 update
        $guruSelect.trigger('change.select2'); 
        
        // Trigger conflict check
        checkScheduleConflict();
    });

    // 2. When Teacher Changes -> Auto-select Subject
    $guruSelect.on('change', function() {
        const selectedGuruId = $(this).val();
        
        // Prevent infinite loop if triggered by script
        if (!selectedGuruId) return;

        const selectedOption = $(this).find('option:selected');
        const mapelId = selectedOption.data('mapel-id');

        if (mapelId) {
            // Check if current mapel is different to avoid unnecessary triggers
            if ($mapelSelect.val() != mapelId) {
                $mapelSelect.val(mapelId).trigger('change');
            }
        }
        
        checkScheduleConflict();
    });
    // --- End Dynamic Dropdown Logic ---

    // Conflict Detection
    function checkScheduleConflict() {
        const guruId = $('#guru_id').val();
        const hari = $('#hari').val();
        const jamKe = $('#jam_ke').val();
        const scheduleId = $('#schedule_id').val();
        const semesterId = $('input[name="semester_id"]').val();
        const kelasId = $('#kelas_id').val();
        const jumlahJam = $('#jumlah_jam').val();

        if (guruId && hari && jamKe && kelasId) {
            $.post("{{ route('admin.jadwal.check-conflict') }}", {
                _token: "{{ csrf_token() }}",
                guru_id: guruId,
                hari: hari,
                jam_ke: jamKe,
                semester_id: semesterId,
                kelas_id: kelasId,
                jumlah_jam: jumlahJam,
                ignore_id: scheduleId
            })
            .done(function(data) {
                if (data.conflict) {
                    $('#conflict-message').text(data.message);
                    $('#conflict-alert').removeClass('d-none').show(); // Ensure it's visible
                    $('#btn-save').prop('disabled', true);
                } else {
                    $('#conflict-alert').addClass('d-none').hide();
                    $('#btn-save').prop('disabled', false);
                }
            })
            .fail(function() {
                // Optional: Handle server error
                console.error('Failed to check conflict');
            });
        } else {
            $('#conflict-alert').addClass('d-none');
            $('#btn-save').prop('disabled', false);
        }
    }

    $('#guru_id').on('change', checkScheduleConflict);
    $('#jumlah_jam').on('input change', checkScheduleConflict);

    const scheduleModal = new bootstrap.Modal(document.getElementById('scheduleModal'));
    let currentKelasId = null;
    let selectedHari = null;

    // Auto-submit functionality
    $('.auto-submit').on('change', function() {
        $('#filterForm').submit();
    });

    // Get current filter values from URL
    const urlParams = new URLSearchParams(window.location.search);
    const kelasIdParam = urlParams.get('kelas_id');
    const hariParam = urlParams.get('hari');

    // Auto-load if kelas is selected
    if (kelasIdParam) {
        currentKelasId = kelasIdParam;
        selectedHari = hariParam;
        loadSchedules(kelasIdParam, hariParam);
        $('#schedule-grid').removeClass('d-none');
        $('#empty-state').addClass('d-none');
    }

    // Handle Class Selection (for direct change without URL params)
    $('#filter_kelas').change(function() {
        // Auto-submit will handle this
    });

    // Handle Hari Selection
    $('#filter_hari').change(function() {
        // Auto-submit will handle this
    });

    // Load Schedules via AJAX
    window.loadSchedulesGlobal = function(kelasId, hari) {
        loadSchedules(kelasId, hari);
    };

    function loadSchedules(kelasId, hari) {
        $('#loading-indicator').removeClass('d-none');

        // Clear existing items
        $('[id^="cell-"]').empty();

        let url = `{{ route('admin.jadwal.get-by-kelas', ['semester_id' => $semester->id, 'kelasId' => 'ID_KELAS']) }}`;
        url = url.replace('ID_KELAS', kelasId);

        const params = @if($semester) { semester_id: {{ $semester->id }} } @else {} @endif;

        $.get(url, params)
            .done(function(data) {
                Object.keys(data).forEach(hari => {
                    const slots = data[hari];

                    // Render 7 slots
                    for (let i = 1; i <= 7; i++) {
                        const cell = $(`#cell-${hari}-${i}`);
                        const item = slots[i];

                        // Add Drop Zone attributes
                        cell.addClass('drop-zone')
                            .attr('data-hari', hari)
                            .attr('data-jam', i)
                            .attr('ondragover', 'allowDrop(event)')
                            .attr('ondrop', 'handleDrop(event)')
                            .attr('ondragenter', 'handleDragEnter(event)')
                            .attr('ondragleave', 'handleDragLeave(event)');

                        let content = '';
                        // Default Click for Add
                        let onClick = `onclick="openAddModal('${hari}', ${i})"`;
                        
                        // Default styling
                        let style = 'height: 100%; width: 100%; min-height: 80px;';

                        if (item) {
                            if (item.type === 'fixed') {
                                onClick = '';
                                style += 'background-color: #f8f9fa; cursor: default;';
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
                                
                                // Draggable Item
                                content = `
                                    <div class="card border-start border-4 border-primary shadow-sm h-100 w-100 border-0 draggable-item" 
                                         draggable="true" 
                                         ondragstart='handleDragStart(event, ${JSON.stringify(item)})'
                                         data-id="${item.id}">
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
                                <div class="d-flex align-items-center justify-content-center h-100 w-100 text-muted" style="border: 2px dashed #dee2e6; border-radius: 4px; cursor: pointer;">
                                    <i class="fas fa-plus"></i>
                                </div>
                            `;
                        }

                        // Only add onclick to the WRAPPER if it's NOT a draggable lesson (to prevent click when dragging starts)
                        // Actually, click works fine with drag if handled correctly.
                        // But to be safe, let's put onclick on the content div for empty slots, and on the card for lessons.
                        // Simplified:
                        
                        if (item && item.type === 'lesson') {
                             cell.html(`<div style="${style}" ${onClick}>${content}</div>`);
                        } else {
                             cell.html(`<div style="${style}" ${onClick}>${content}</div>`);
                        }
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

        // Reset Select2
        $('.select2').val('').trigger('change');
        $('#conflict-alert').addClass('d-none');
        $('#btn-save').prop('disabled', false);

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

        // Update Select2 values
        $('#mata_pelajaran_id').val(data.mata_pelajaran.id).trigger('change');
        $('#guru_id').val(data.guru.id).trigger('change');
        $('#conflict-alert').addClass('d-none');
        $('#btn-save').prop('disabled', false);

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
        const url = id ? `{{ route('admin.jadwal.update', ['semester_id' => $semester->id, 'jadwal_mapel' => 'ID']) }}`.replace('ID', id) : `{{ route('admin.jadwal.store', ['semester_id' => $semester->id]) }}`;
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
        // Use SweetAlert2 for confirmation
        Swal.fire({
            title: 'Yakin ingin menghapus jadwal ini?',
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const id = $('#schedule_id').val();

                $.ajax({
                    url: `{{ route('admin.jadwal.destroy', ['semester_id' => $semester->id, 'jadwal_mapel' => 'ID']) }}`.replace('ID', id),
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        scheduleModal.hide();
                        loadSchedules(currentKelasId);
                        Swal.fire(
                            'Terhapus!',
                            'Jadwal telah berhasil dihapus.',
                            'success'
                        );
                    },
                    error: function(xhr) {
                        Swal.fire(
                            'Gagal!',
                            'Gagal menghapus jadwal.',
                            'error'
                        );
                    }
                });
            }
        });
    });
});

// Drag and Drop Functions (Global Scope)
let draggedItem = null;

function handleDragStart(e, item) {
    draggedItem = item;
    e.target.classList.add('dragging');
    e.dataTransfer.effectAllowed = 'move';
    // Use a small delay to keep the element visible while dragging ghost image
    setTimeout(() => e.target.style.opacity = '0.5', 0);
}

function allowDrop(e) {
    e.preventDefault();
    e.dataTransfer.dropEffect = 'move';
}

function handleDragEnter(e) {
    e.preventDefault();
    // Find closest TD
    const cell = e.target.closest('td.drop-zone');
    if (cell) {
        cell.classList.add('drag-over');
    }
}

function handleDragLeave(e) {
    const cell = e.target.closest('td.drop-zone');
    if (cell) {
        cell.classList.remove('drag-over');
    }
}

function handleDrop(e) {
    e.preventDefault();
    const cell = e.target.closest('td.drop-zone');
    if (!cell) return;

    cell.classList.remove('drag-over');
    
    // Get target coordinates
    const targetHari = cell.getAttribute('data-hari');
    const targetJam = cell.getAttribute('data-jam');
    
    // If dropped on same slot, do nothing
    if (draggedItem.hari === targetHari && draggedItem.jam_ke == targetJam) {
        return;
    }

    // Call Backend to Move
    moveSchedule(draggedItem.id, targetHari, targetJam);
}

function moveSchedule(id, targetHari, targetJam) {
    $('#loading-indicator').removeClass('d-none');
    
    const semesterId = $('input[name="semester_id"]').val();
    const kelasId = $('#filter_kelas').val(); // Assuming current filter is the class

    $.post("{{ route('admin.jadwal.move') }}", {
        _token: "{{ csrf_token() }}",
        id: id,
        hari: targetHari,
        jam_ke: targetJam,
        semester_id: semesterId,
        kelas_id: kelasId
    })
    .done(function(response) {
        // Expose loadSchedules to window
        if (window.loadSchedulesGlobal) {
            window.loadSchedulesGlobal(kelasId);
        } else {
            location.reload(); 
        }
        
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: response.message,
            timer: 2000,
            showConfirmButton: false
        });
    })
    .fail(function(xhr) {
        const msg = xhr.responseJSON ? xhr.responseJSON.message : 'Gagal memindahkan jadwal';
        Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: msg
        });
        // Reload to reset UI state
        if (window.loadSchedulesGlobal) window.loadSchedulesGlobal(kelasId);
    })
    .always(function() {
        $('#loading-indicator').addClass('d-none');
    });
}
</script>
@endpush
