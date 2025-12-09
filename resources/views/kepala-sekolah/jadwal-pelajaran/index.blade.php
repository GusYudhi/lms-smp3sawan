@extends($semester ? 'layouts.semester' : 'layouts.app')

@section('title', 'Jadwal Pelajaran')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-calendar-alt me-2"></i>Jadwal Pelajaran
            </h1>
            @if($semester)
            <p class="text-muted mb-0">
                <small>{{ $semester->tahunPelajaran->nama }} - {{ $semester->nama }}</small>
            </p>
            @endif
        </div>
        <div>
            <a href="{{ route('kepala-sekolah.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Kembali
            </a>
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
        <div class="card shadow">
            <div class="card-body p-0">
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
        </div>
    </div>

    <!-- Empty State -->
    <div id="empty-state" class="text-center py-5">
        <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" alt="Select Class" style="width: 150px; opacity: 0.5;">
        <h5 class="mt-3 text-muted">Silakan pilih kelas terlebih dahulu</h5>
        <p class="text-muted">Pilih kelas di dropdown di atas untuk melihat jadwal pelajaran</p>
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

        const url = `{{ url('kepala-sekolah/jadwal-pelajaran/get-by-kelas') }}/${kelasId}`;
        const params = @if($semester) { semester_id: {{ $semester->id }} } @else {} @endif;

        $.get(url, params)
            .done(function(response) {
                console.log('Response:', response);

                const schedule = response.schedule || response;
                console.log('Schedule:', schedule);

                if (!schedule || typeof schedule !== 'object') {
                    console.error('Invalid schedule data:', schedule);
                    alert('Format data jadwal tidak valid');
                    return;
                }

                const days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

                days.forEach(hari => {
                    const slots = schedule[hari] || {};

                    // Render 7 slots
                    for (let i = 1; i <= 7; i++) {
                        const cell = $(`#cell-${hari}-${i}`);
                        const item = slots[i];

                        let content = '';
                        let style = 'height: 100%; width: 100%; min-height: 80px;';

                        if (item) {
                            if (item.type === 'fixed') {
                                style = 'height: 100%; width: 100%; min-height: 80px; background-color: #f8f9fa;';
                                content = `
                                    <div class="d-flex align-items-center justify-content-center h-100 w-100 text-center">
                                        <div>
                                            <i class="fas fa-flag mb-1 text-secondary"></i><br>
                                            <small class="fw-bold text-secondary">${item.label}</small>
                                        </div>
                                    </div>
                                `;
                            } else if (item.type === 'regular') {
                                style = 'height: 100%; width: 100%; min-height: 80px;';
                                content = `
                                    <div class="card border-start border-4 border-primary shadow-sm h-100 w-100 border-0">
                                        <div class="card-body p-2">
                                            <h6 class="mb-1 fw-bold text-dark" style="font-size: 0.9rem;">${item.mapel}</h6>
                                            <div class="small text-secondary">
                                                <i class="far fa-user me-1"></i> ${item.guru}
                                            </div>
                                        </div>
                                    </div>
                                `;
                            }
                        } else {
                            // Empty slot
                            content = `
                                <div class="d-flex align-items-center justify-content-center h-100 w-100 text-muted" style="background-color: #f8f9fa; border-radius: 4px;">
                                    <small class="text-muted">Kosong</small>
                                </div>
                            `;
                        }

                        cell.html(`<div style="${style}">${content}</div>`);
                    }
                });
            })
            .fail(function(xhr) {
                console.error('Error loading schedule:', xhr);
                console.error('Status:', xhr.status);
                console.error('Response:', xhr.responseText);
                alert('Gagal memuat jadwal. Silakan cek console untuk detail error.');
            })
            .always(function() {
                $('#loading-indicator').addClass('d-none');
            });
    }
});
</script>
@endpush
