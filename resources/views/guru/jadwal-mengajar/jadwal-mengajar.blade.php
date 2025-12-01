@extends('layouts.app')

@section('title', 'Jadwal Mengajar')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="bg-white rounded-3 shadow-sm p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-2 text-primary">
                            <i class="fas fa-chalkboard-teacher me-2"></i>Jadwal Mengajar
                        </h1>
                        <p class="text-muted mb-0">
                            Lihat jadwal mengajar seluruh kelas. Jadwal Anda ditandai dengan warna hijau.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <label for="filter_kelas" class="form-label fw-bold">
                        <i class="fas fa-filter me-2"></i>Pilih Kelas:
                    </label>
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

    <!-- Legend -->
    <div id="legend-section" class="card shadow-sm mb-4 border-0 d-none">
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <small class="fw-bold text-muted">Keterangan:</small>
                </div>
                <div class="col-auto">
                    <span class="badge bg-success me-2">
                        <i class="fas fa-user-tie me-1"></i>Jadwal Anda
                    </span>
                </div>
                <div class="col-auto">
                    <span class="badge bg-primary me-2">
                        <i class="fas fa-chalkboard me-1"></i>Jadwal Guru Lain
                    </span>
                </div>
                <div class="col-auto">
                    <span class="badge bg-secondary me-2">
                        <i class="fas fa-lock me-1"></i>Jadwal Tetap
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Schedule Grid -->
    <div id="schedule-grid" class="card shadow-sm border-0 d-none">
        <div class="card-body p-0">
            <div style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
                <table class="table table-bordered mb-0" style="min-width: 1000px; table-layout: fixed;">
                    <colgroup>
                        <col style="width: 120px;">
                        @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $hari)
                        <col style="width: 180px;">
                        @endforeach
                    </colgroup>
                    <thead class="table-light text-center">
                        <tr>
                            <th class="align-middle fw-bold">
                                <i class="far fa-clock me-1"></i>Waktu
                            </th>
                            @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $hari)
                                <th class="align-middle fw-bold">
                                    <i class="far fa-calendar me-1"></i>{{ $hari }}
                                </th>
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
                                <td class="align-middle text-center table-light">
                                    <span class="badge bg-secondary mb-1">Jam Ke-{{ $i }}</span><br>
                                    <small class="fw-bold text-dark" style="font-size: 0.85rem;">{{ $waktu }}</small>
                                </td>
                                @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $hari)
                                    <td id="cell-{{ $hari }}-{{ $i }}" class="p-2" style="height: 1px; vertical-align: middle;">
                                        <!-- Content injected by JS -->
                                    </td>
                                @endforeach
                            </tr>
                        @endfor
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Empty State -->
    <div id="empty-state" class="text-center py-5">
        <div class="card shadow-sm border-0">
            <div class="card-body py-5">
                <i class="fas fa-calendar-alt text-muted mb-3" style="font-size: 80px; opacity: 0.3;"></i>
                <h5 class="mt-3 text-muted">Silakan Pilih Kelas</h5>
                <p class="text-muted">Pilih kelas dari dropdown di atas untuk melihat jadwal mengajar</p>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let currentKelasId = null;

    // Handle Class Selection
    $('#filter_kelas').change(function() {
        currentKelasId = $(this).val();
        if (currentKelasId) {
            loadSchedules(currentKelasId);
            $('#schedule-grid').removeClass('d-none');
            $('#legend-section').removeClass('d-none');
            $('#empty-state').addClass('d-none');
        } else {
            $('#schedule-grid').addClass('d-none');
            $('#legend-section').addClass('d-none');
            $('#empty-state').removeClass('d-none');
        }
    });

    // Load Schedules via AJAX
    function loadSchedules(kelasId) {
        $('#loading-indicator').removeClass('d-none');

        // Clear existing items
        $('[id^="cell-"]').empty();

        $.get(`{{ url('guru/jadwal-mengajar/get-by-kelas') }}/${kelasId}`)
            .done(function(data) {
                Object.keys(data).forEach(hari => {
                    const slots = data[hari];

                    // Render 7 slots
                    for (let i = 1; i <= 7; i++) {
                        const cell = $(`#cell-${hari}-${i}`);
                        const item = slots[i];

                        let content = '';

                        if (item) {
                            if (item.type === 'fixed') {
                                // Fixed schedule (istirahat, upacara, etc)
                                content = `
                                    <div class="h-100 w-100 d-flex align-items-center justify-content-center" style="min-height: 80px; background-color: #f8f9fa; border-radius: 4px;">
                                        <div class="text-center">
                                            <i class="fas fa-lock mb-1 text-secondary"></i><br>
                                            <small class="fw-bold text-secondary">${item.keterangan}</small>
                                        </div>
                                    </div>
                                `;
                            } else if (item.type === 'lesson') {
                                // Lesson schedule
                                const isCurrentTeacher = item.is_current_teacher;
                                const borderColor = isCurrentTeacher ? 'success' : 'primary';
                                const badgeColor = isCurrentTeacher ? 'success' : 'primary';
                                const badgeIcon = isCurrentTeacher ? 'fa-user-tie' : 'fa-chalkboard';
                                const shadowClass = isCurrentTeacher ? 'shadow' : 'shadow-sm';

                                content = `
                                    <div class="card border-start border-4 border-${borderColor} ${shadowClass} h-100 w-100 border-0" style="min-height: 80px;">
                                        <div class="card-body p-2">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="mb-0 fw-bold text-dark" style="font-size: 0.9rem; line-height: 1.3;">
                                                    ${item.mata_pelajaran.nama_mapel}
                                                </h6>
                                                ${isCurrentTeacher ? '<span class="badge bg-success" style="font-size: 0.7rem;"><i class="fas fa-check me-1"></i>Anda</span>' : ''}
                                            </div>
                                            <div class="small text-muted">
                                                <i class="fas ${badgeIcon} me-1"></i>
                                                ${item.guru.name}
                                            </div>
                                            <div class="mt-1">
                                                <span class="badge bg-light text-dark" style="font-size: 0.7rem;">
                                                    <i class="fas fa-book me-1"></i>${item.mata_pelajaran.kode_mapel}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                `;
                            }
                        } else {
                            // Empty slot
                            content = `
                                <div class="d-flex align-items-center justify-content-center h-100 w-100 text-muted" style="min-height: 80px; border: 2px dashed #dee2e6; border-radius: 4px;">
                                    <small class="text-muted">Kosong</small>
                                </div>
                            `;
                        }

                        cell.html(content);
                    }
                });
            })
            .fail(function(xhr) {
                alert('Gagal memuat jadwal');
                console.error(xhr);
            })
            .always(function() {
                $('#loading-indicator').addClass('d-none');
            });
    }
});
</script>

<style>
/* Smooth transitions */
.card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

/* Table styling */
.table-bordered td, .table-bordered th {
    border-color: #e9ecef;
}

thead th {
    position: sticky;
    top: 0;
    z-index: 10;
    background: #f8f9fa;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .table {
        font-size: 0.85rem;
    }

    .card-body h6 {
        font-size: 0.8rem !important;
    }

    .small {
        font-size: 0.75rem !important;
    }
}

/* Animation for loading */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.card {
    animation: fadeIn 0.3s ease-in-out;
}
</style>
@endpush
