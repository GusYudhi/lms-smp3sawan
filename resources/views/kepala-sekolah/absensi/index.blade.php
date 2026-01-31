@extends('layouts.app')

@section('title', 'Data Absensi')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h1 class="h3 mb-2 text-success">
                                <i class="fas fa-clipboard-check me-2"></i>Data Absensi
                            </h1>
                            <p class="text-muted mb-0">Monitoring kehadiran guru dan siswa</p>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ route('kepala-sekolah.dashboard') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Selection Cards -->
    <div class="row g-3">
        <!-- Absensi Siswa -->
        <div class="col-md-6">
            <a href="{{ route('kepala-sekolah.absensi.siswa.index') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm hover-card overflow-hidden" style="transition: all 0.3s ease;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-3">
                                <div class="rounded-3 bg-primary bg-opacity-10 d-flex align-items-center justify-content-center"
                                     style="width: 50px; height: 50px;">
                                    <i class="fas fa-user-graduate text-primary fs-4"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-0 fw-bold text-dark">Rekap Absensi Siswa</h6>
                                <p class="text-muted small mb-0 mt-1" style="font-size: 0.75rem;">Monitor kehadiran siswa per kelas/individu.</p>
                            </div>
                            <div class="ms-2">
                                <i class="fas fa-chevron-right text-muted small"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Absensi Guru -->
        <div class="col-md-6">
            <a href="{{ route('kepala-sekolah.absensi.guru.index') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm hover-card overflow-hidden" style="transition: all 0.3s ease;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-3">
                                <div class="rounded-3 bg-success bg-opacity-10 d-flex align-items-center justify-content-center"
                                     style="width: 50px; height: 50px;">
                                    <i class="fas fa-chalkboard-teacher text-success fs-4"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-0 fw-bold text-dark">Rekap Absensi Guru</h6>
                                <p class="text-muted small mb-0 mt-1" style="font-size: 0.75rem;">Pantau persentase dan detail kehadiran guru.</p>
                            </div>
                            <div class="ms-2">
                                <i class="fas fa-chevron-right text-muted small"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-bold text-success" id="today-title">
                        <i class="fas fa-calendar-day me-2"></i>Absensi Guru Hari ini
                    </h5>
                    <div class="d-flex align-items-center">
                        <label for="sort-select" class="me-2 text-muted small">Urutkan:</label>
                        <select id="sort-select" class="form-select form-select-sm" style="width: 120px;">
                            <option value="terbaru">Terbaru</option>
                            <option value="terlama">Terlama</option>
                        </select>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Jam Absen</th>
                                    <th>Nama</th>
                                    <th>NIP</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody id="attendance-list">
                                <!-- Data will be populated here -->
                            </tbody>
                        </table>
                    </div>
                    <div id="loading-spinner" class="text-center py-4">
                        <div class="spinner-border text-success" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                     <div id="empty-state" class="text-center py-5 d-none">
                        <img src="{{ asset('assets/image/no-data.svg') }}" alt="No Data" style="width: 120px; opacity: 0.5" onerror="this.style.display='none'">
                        <p class="text-muted mt-3">Belum ada data absensi guru hari ini</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.hover-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.15) !important;
}
</style>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const attendanceList = document.getElementById('attendance-list');
        const loadingSpinner = document.getElementById('loading-spinner');
        const emptyState = document.getElementById('empty-state');
        const todayTitle = document.getElementById('today-title');
        const sortSelect = document.getElementById('sort-select');
        let currentSort = 'terbaru';

        // Function to fetch data
        function fetchAttendanceData() {
            fetch(`{{ route('kepala-sekolah.api.absensi-guru-today') }}?sort=${currentSort}`)
                .then(response => response.json())
                .then(data => {
                    // Update Title with Date
                    todayTitle.innerHTML = `<i class="fas fa-calendar-day me-2"></i>Absensi Guru Hari ini (${data.date_string})`;

                    // Handle Empty Data
                    if (data.data.length === 0) {
                        attendanceList.innerHTML = '';
                        loadingSpinner.classList.add('d-none');
                        emptyState.classList.remove('d-none');
                        return;
                    }

                    // Populate Table
                    let html = '';
                    data.data.forEach(item => {
                        let statusClass = 'bg-secondary';
                        if (item.status_full === 'hadir') statusClass = 'bg-success';
                        else if (item.status_full === 'terlambat') statusClass = 'bg-warning text-dark';
                        else if (item.status_full === 'izin') statusClass = 'bg-info text-dark';
                        else if (item.status_full === 'sakit') statusClass = 'bg-danger';
                        else if (item.status_full === 'alpha') statusClass = 'bg-danger';

                        html += `
                            <tr>
                                <td class="ps-4 fw-medium">${item.jam_absen}</td>
                                <td>
                                    <div class="fw-bold text-dark">${item.nama}</div>
                                    <small class="text-muted d-block d-md-none">NIP: ${item.nip}</small>
                                </td>
                                <td>${item.nip}</td>
                                <td class="text-center">
                                    <span class="badge ${statusClass} rounded-pill" style="width: 30px; height: 30px; display: inline-flex; align-items: center; justify-content: center; font-size: 14px;">
                                        ${item.status}
                                    </span>
                                </td>
                            </tr>
                        `;
                    });

                    attendanceList.innerHTML = html;
                    loadingSpinner.classList.add('d-none');
                    emptyState.classList.add('d-none');
                })
                .catch(error => {
                    console.error('Error fetching attendance:', error);
                    // Don't show error state in UI for polling, just log it
                });
        }

        // Initial Load
        fetchAttendanceData();

        // Polling every 5 seconds
        setInterval(fetchAttendanceData, 5000);

        // Sort Event Listener
        sortSelect.addEventListener('change', function() {
            currentSort = this.value;
            loadingSpinner.classList.remove('d-none');
            attendanceList.innerHTML = ''; // Clear list while loading
            fetchAttendanceData();
        });
    });
</script>
@endpush
@endsection
