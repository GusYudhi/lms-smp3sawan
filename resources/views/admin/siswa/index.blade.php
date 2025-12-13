@extends('layouts.app')

@section('title', 'Data Siswa')

@push('styles')
<link href="{{ asset('css/admin/siswa.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="container-fluid">
    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>{{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('import_errors'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-list me-2"></i><strong>Detail Error Import:</strong>
            <pre class="mt-2 mb-0" style="white-space: pre-wrap; font-size: 0.9em;">{{ session('import_errors') }}</pre>
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
                                <i class="fas fa-user-graduate text-primary me-2"></i>Data Siswa
                            </h1>
                            <p class="text-subtle mb-0 fw-medium">Kelola data siswa: tambahkan, edit, lihat, atau ekspor data</p>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="row g-2 justify-content-end">

                                <div class="col-6">
                                    <button type="button" class="btn btn-outline-success w-100" data-bs-toggle="modal" data-bs-target="#importModal">
                                        <i class="fas fa-file-import me-1"></i>
                                        <span>Import Data</span>
                                    </button>
                                </div>

                                <div class="col-6">
                                    <a href="{{ route('admin.siswa.create') }}" class="btn btn-primary w-100">
                                        <i class="fas fa-plus me-1"></i>
                                        <span>Tambah Siswa</span>
                                    </a>
                                </div>

                                <div class="col-6">
                                    <button class="btn btn-outline-secondary w-100" onclick="exportData()">
                                        <i class="fas fa-file-export me-1"></i>
                                        <span>Ekspor</span>
                                    </button>
                                </div>

                                <div class="col-6">
                                    <button id="bulk-download-btn" class="btn btn-success w-100" onclick="startBulkDownload()">
                                        <i class="fas fa-id-card me-1"></i>
                                        <span>Unduh Kartu Identitas</span>
                                    </button>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistik Siswa -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card card-stats text-center h-100 hover-card">
                <div class="card-body">
                    <div class="text-primary fs-1 mb-3">
                        <i class="fas fa-users"></i>
                    </div>
                    <h6 class="card-title text-medium-contrast fw-semibold mb-2">Total Siswa</h6>
                    <h2 class="text-primary fw-bold mb-1">{{ $totalSiswa ?? 0 }}</h2>
                    <small class="text-subtle fw-medium">Siswa Terdaftar</small>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card card-stats text-center h-100 hover-card">
                <div class="card-body">
                    <div class="text-success fs-1 mb-3">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <h6 class="card-title text-medium-contrast fw-semibold mb-2">Siswa Aktif</h6>
                    <h2 class="text-success fw-bold mb-1">{{ $siswaAktif ?? 0 }}</h2>
                    <small class="text-subtle fw-medium">Status Aktif</small>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card card-stats text-center h-100 hover-card">
                <div class="card-body">
                    <div class="text-info fs-1 mb-3">
                        <i class="fas fa-male"></i>
                    </div>
                    <h6 class="card-title text-medium-contrast fw-semibold mb-2">Laki-laki</h6>
                    <h2 class="text-info fw-bold mb-1">{{ $siswaLakiLaki ?? 0 }}</h2>
                    <small class="text-subtle fw-medium">Siswa Putra</small>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card card-stats text-center h-100 hover-card">
                <div class="card-body">
                    <div class="text-warning fs-1 mb-3">
                        <i class="fas fa-female"></i>
                    </div>
                    <h6 class="card-title text-medium-contrast fw-semibold mb-2">Perempuan</h6>
                    <h2 class="text-warning fw-bold mb-1">{{ $siswaPerempuan ?? 0 }}</h2>
                    <small class="text-subtle fw-medium">Siswa Putri</small>
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
                    <form method="GET" action="{{ route('admin.siswa.index') }}" id="filterForm">
                        <div class="row g-3 align-items-end">
                            <!-- Show Entries -->
                            <div class="col-md-2">
                                <label for="perPageFilter" class="form-label text-medium-contrast fw-medium">Tampilkan</label>
                                <select name="per_page" id="perPageFilter" class="form-select auto-submit">
                                    <option value="15" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15 data</option>
                                    <option value="20" {{ request('per_page') == 20 ? 'selected' : '' }}>20 data</option>
                                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 data</option>
                                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 data</option>
                                    <option value="300" {{ request('per_page') == 300 ? 'selected' : '' }}>300 data</option>
                                    <option value="500" {{ request('per_page') == 500 ? 'selected' : '' }}>500 data</option>
                                    <option value="1000" {{ request('per_page') == 1000 ? 'selected' : '' }}>1000 data</option>
                                </select>
                            </div>

                            <!-- Search Input -->
                            <div class="col-md-3">
                                <label for="studentSearch" class="form-label text-medium-contrast fw-medium">Pencarian</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-search text-muted"></i>
                                    </span>
                                    <input type="text"
                                           name="search"
                                           id="studentSearch"
                                           class="form-control border-start-0 search-input"
                                           placeholder="Cari nama, NIS, NISN..."
                                           value="{{ request('search') }}">
                                </div>
                            </div>

                            <!-- Class Filter -->
                            <div class="col-md-2">
                                <label for="classFilter" class="form-label text-medium-contrast fw-medium">Kelas</label>
                                <select name="kelas" id="classFilter" class="form-select auto-submit">
                                    <option value="">Semua Kelas</option>
                                    <option value="7" {{ request('kelas') == '7' ? 'selected' : '' }}>Kelas 7 (Semua)</option>
                                    <option value="8" {{ request('kelas') == '8' ? 'selected' : '' }}>Kelas 8 (Semua)</option>
                                    <option value="9" {{ request('kelas') == '9' ? 'selected' : '' }}>Kelas 9 (Semua)</option>
                                    <optgroup label="Kelas 7">
                                        @foreach($classes as $kelas)
                                            @if(substr($kelas, 0, 1) == '7')
                                                <option value="{{ $kelas }}" {{ request('kelas') == $kelas ? 'selected' : '' }}>{{ $kelas }}</option>
                                            @endif
                                        @endforeach
                                    </optgroup>
                                    <optgroup label="Kelas 8">
                                        @foreach($classes as $kelas)
                                            @if(substr($kelas, 0, 1) == '8')
                                                <option value="{{ $kelas }}" {{ request('kelas') == $kelas ? 'selected' : '' }}>{{ $kelas }}</option>
                                            @endif
                                        @endforeach
                                    </optgroup>
                                    <optgroup label="Kelas 9">
                                        @foreach($classes as $kelas)
                                            @if(substr($kelas, 0, 1) == '9')
                                                <option value="{{ $kelas }}" {{ request('kelas') == $kelas ? 'selected' : '' }}>{{ $kelas }}</option>
                                            @endif
                                        @endforeach
                                    </optgroup>
                                </select>
                            </div>

                            <!-- Gender Filter -->
                            <div class="col-md-2">
                                <label for="genderFilter" class="form-label text-medium-contrast fw-medium">Jenis Kelamin</label>
                                <select name="jenis_kelamin" id="genderFilter" class="form-select auto-submit">
                                    <option value="">Semua</option>
                                    <option value="L" {{ request('jenis_kelamin') === 'L' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="P" {{ request('jenis_kelamin') === 'P' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>

                            <!-- Status Filter -->
                            <div class="col-md-2">
                                <label for="statusFilter" class="form-label text-medium-contrast fw-medium">Status</label>
                                <select name="status" id="statusFilter" class="form-select auto-submit">
                                    <option value="">Semua Status</option>
                                    <option value="AKTIF" {{ request('status') === 'AKTIF' ? 'selected' : '' }}>Aktif</option>
                                    <option value="LULUS" {{ request('status') === 'LULUS' ? 'selected' : '' }}>Lulus</option>
                                    <option value="TIDAK_AKTIF" {{ request('status') === 'TIDAK_AKTIF' ? 'selected' : '' }}>Tidak Aktif</option>
                                </select>
                            </div>

                            <!-- Action Buttons -->
                            <div class="col-md-2">
                                <a href="{{ route('admin.siswa.index') }}" class="btn btn-outline-secondary w-100 shadow-sm fw-medium">
                                    <i class="fas fa-redo me-1"></i> Reset
                                </a>
                                <small class="text-muted d-block mt-2">
                                    <i class="fas fa-info-circle me-1"></i>Filter otomatis
                                </small>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Tabel Data Siswa -->
    <div class="row">
        <div class="col-12">
            <div class="card card-modern border-0 shadow-sm">
                <div class="card-header bg-light border-bottom-0 py-3">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="card-title mb-0 text-high-contrast fw-semibold">
                                <i class="fas fa-table text-primary me-2"></i>Daftar Siswa
                            </h6>
                        </div>
                        <div class="col-auto">
                            <span class="badge bg-primary-subtle text-primary border fw-medium" id="table-info">
                                @include('admin.siswa.partials.table-info')
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div id="table-container">
                        @include('admin.siswa.partials.table')
                    </div>
                </div>
                <div class="card-footer bg-light border-top-0 py-3">
                    <div id="pagination-container">
                        @include('admin.siswa.partials.pagination')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">Import Data Siswa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.siswa.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">1. Download Template</label>
                        <p class="text-muted small mb-2">Silakan download format template Excel di bawah ini sebelum mengupload data.</p>
                        <a href="{{ route('admin.siswa.template') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-download me-1"></i> Download Template Excel (.xlsx)
                        </a>
                        <div class="p-3 mb-2 bg-info bg-opacity-10 border border-info rounded mt-2">
                            <small>
                                <strong>Tips:</strong>
                                <ul class="mb-0 mt-1">
                                    <li>Kolom NIS dan NISN akan otomatis diformat sebagai teks</li>
                                    <li>Jenis kelamin: gunakan <code>L</code> atau <code>P</code></li>
                                    <li>Format Tanggal Lahir: <code>YYYY-MM-DD</code>, <code>DD-MM-YYYY</code>, <code>DD/MM/YYYY</code></li>
                                    <li>Email akan digenerate otomatis (format: nama.tengah@student.id)</li>
                                    <li>Password default: <code>12345678</code></li>
                                </ul>
                            </small>
                        </div>
                    </div>
                    <hr>
                    <div class="mb-3">
                        <label for="file" class="form-label fw-bold">2. Upload File Excel</label>
                        <input type="file" class="form-control" id="file" name="file" accept=".xlsx,.xls,.xlsm,.xlsb,.xlam,.xltx,.xltm,.csv" required>
                        <small class="text-muted">Format yang didukung: .xlsx, .xls, .xlsm, .xlsb, .xlam, .xltx, .xltm, .csv (Max: 5MB)</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="importBtn">
                        <span id="importBtnText">Import</span>
                        <span id="importBtnLoading" class="d-none">
                            <i class="fas fa-spinner fa-spin me-1"></i>Mengimpor...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bulk Download Modal -->
<div class="modal fade" id="bulkDownloadModal" tabindex="-1" aria-labelledby="bulkDownloadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white border-0">
                <h5 class="modal-title" id="bulkDownloadModalLabel">
                    <i class="fas fa-id-card me-2"></i>Download Kartu Identitas Siswa
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-3">
                    <div class="display-1 text-success mb-3">
                        <i class="fas fa-address-card"></i>
                    </div>
                    <h5 class="mb-2">Konfirmasi Download</h5>
                    <p class="text-muted">
                        Anda akan mengunduh kartu identitas untuk <strong id="totalStudentsToDownload">0</strong> siswa
                        <span id="filterInfoText"></span>
                    </p>
                </div>

                <div class="alert alert-info d-flex align-items-start" role="alert">
                    <i class="fas fa-info-circle me-2 mt-1"></i>
                    <div>
                        <strong>Informasi:</strong>
                        <ul class="mb-0 mt-2 ps-3">
                            <li>Kartu akan diunduh dalam format ZIP</li>
                            <li>Setiap kartu berformat PNG dengan resolusi tinggi</li>
                            <li>Proses mungkin memakan waktu beberapa menit</li>
                            <li>Jangan tutup halaman selama proses download</li>
                        </ul>
                    </div>
                </div>

                <div class="card bg-light border-0">
                    <div class="card-body">
                        <h6 class="card-title mb-3">
                            <i class="fas fa-filter text-primary me-2"></i>Filter yang diterapkan:
                        </h6>
                        <div id="activeFilters" class="d-flex flex-wrap gap-2">
                            <!-- Will be populated by JavaScript -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top-0 pt-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Batal
                </button>
                <button type="button" class="btn btn-success" id="confirmBulkDownloadBtn">
                    <i class="fas fa-download me-1"></i> Ya, Download Sekarang
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title text-danger" id="deleteModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>Konfirmasi Hapus Data
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus data siswa:</p>
                <p class="fw-bold" id="studentNameToDelete"></p>
                <div class="alert alert-warning d-flex align-items-center" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <div>Data yang dihapus tidak dapat dikembalikan!</div>
                </div>
            </div>
            <div class="modal-footer border-top-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Batal
                </button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                    <i class="fas fa-trash me-1"></i> Ya, Hapus Data
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Hidden Delete Form -->
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
// Global variables
let searchTimeout;
let currentPage = 1;
let isLoading = false;

document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('studentSearch');
    const genderFilter = document.getElementById('genderFilter');
    const classFilter = document.getElementById('classFilter');
    const statusFilter = document.getElementById('statusFilter');
    const filterForm = document.getElementById('filterForm');

    // Prevent form submission
    filterForm.addEventListener('submit', function(e) {
        e.preventDefault();
    });

    // Real-time search with debouncing (AJAX)
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            currentPage = 1;
            performAjaxSearch();
        }, 500);
    });

    // Immediate filter on select change (AJAX)
    genderFilter.addEventListener('change', function() {
        currentPage = 1;
        performAjaxSearch();
    });

    classFilter.addEventListener('change', function() {
        currentPage = 1;
        performAjaxSearch();
    });

    statusFilter.addEventListener('change', function() {
        currentPage = 1;
        performAjaxSearch();
    });

    // Auto-hide alerts
    const alerts = document.querySelectorAll('.alert.alert-dismissible');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.3s ease';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 300);
        }, 5000);
    });
});

// AJAX search function
function performAjaxSearch() {
    if (isLoading) return;

    isLoading = true;
    showLoadingState();

    const searchValue = document.getElementById('studentSearch').value;
    const genderValue = document.getElementById('genderFilter').value;
    const classValue = document.getElementById('classFilter').value;
    const statusValue = document.getElementById('statusFilter').value;

    const params = new URLSearchParams({
        search: searchValue,
        jenis_kelamin: genderValue,
        kelas: classValue,
        status: statusValue,
        page: currentPage
    });

    // Remove empty values
    for (let [key, value] of params.entries()) {
        if (!value) params.delete(key);
    }

    const url = `{{ route('admin.siswa.search') }}?${params.toString()}`;

    fetch(url, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        // Update table content
        document.getElementById('table-container').innerHTML = data.html;
        document.getElementById('pagination-container').innerHTML = data.pagination;
        document.getElementById('table-info').innerHTML = data.info;

        // Update URL
        const newUrl = new URL(window.location);
        if (searchValue) newUrl.searchParams.set('search', searchValue);
        else newUrl.searchParams.delete('search');
        if (genderValue) newUrl.searchParams.set('jenis_kelamin', genderValue);
        else newUrl.searchParams.delete('jenis_kelamin');
        if (classValue) newUrl.searchParams.set('kelas', classValue);
        else newUrl.searchParams.delete('kelas');
        if (statusValue) newUrl.searchParams.set('status', statusValue);
        else newUrl.searchParams.delete('status');
        if (currentPage > 1) newUrl.searchParams.set('page', currentPage);
        else newUrl.searchParams.delete('page');

        window.history.pushState({}, '', newUrl);
        hideLoadingState();
        isLoading = false;
    })
    .catch(error => {
        console.error('AJAX Error:', error);
        showToast('error', 'Terjadi kesalahan saat mencari data');
        hideLoadingState();
        isLoading = false;
    });
}

function loadPage(page) {
    currentPage = page;
    performAjaxSearch();
}

function showLoadingState() {
    const tableContainer = document.getElementById('table-container');
    const spinner = document.createElement('div');
    spinner.id = 'table-loading';
    spinner.className = 'd-flex justify-content-center align-items-center p-5';
    spinner.innerHTML = `
        <div class="spinner-border text-primary me-2" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <span class="text-muted">Memuat data...</span>
    `;

    tableContainer.style.position = 'relative';
    tableContainer.style.opacity = '0.6';
    tableContainer.appendChild(spinner);
}

function hideLoadingState() {
    const spinner = document.getElementById('table-loading');
    if (spinner) spinner.remove();

    const tableContainer = document.getElementById('table-container');
    tableContainer.style.opacity = '1';
}

function confirmDelete(studentId, studentName) {
    document.getElementById('studentNameToDelete').textContent = studentName;

    const confirmBtn = document.getElementById('confirmDeleteBtn');
    confirmBtn.onclick = function() {
        deleteStudent(studentId);
    };

    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

function deleteStudent(studentId) {
    const deleteBtn = document.getElementById('confirmDeleteBtn');
    const originalText = deleteBtn.innerHTML;

    deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Menghapus...';
    deleteBtn.disabled = true;

    const form = document.getElementById('deleteForm');
    form.action = `/admin/siswa/${studentId}`;
    form.submit();
}

function exportData() {
    const exportBtn = document.querySelector('[onclick="exportData()"]');
    const originalText = exportBtn.innerHTML;

    exportBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Mengunduh...';
    exportBtn.disabled = true;

    const params = new URLSearchParams();
    const search = document.getElementById('studentSearch').value;
    const gender = document.getElementById('genderFilter').value;
    const kelas = document.getElementById('classFilter').value;
    const status = document.getElementById('statusFilter').value;

    if (search) params.append('search', search);
    if (gender) params.append('jenis_kelamin', gender);
    if (kelas) params.append('kelas', kelas);
    if (status) params.append('status', status);

    const exportUrl = `{{ route('admin.siswa.export') }}?${params.toString()}`;

    const link = document.createElement('a');
    link.href = exportUrl;
    link.style.display = 'none';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

    setTimeout(() => {
        exportBtn.innerHTML = originalText;
        exportBtn.disabled = false;
    }, 2000);
}

function showToast(type, message) {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type === 'error' ? 'danger' : type} position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    toast.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="fas fa-${type === 'error' ? 'exclamation-circle' : 'check-circle'} me-2"></i>
            <div class="flex-grow-1">${message}</div>
            <button type="button" class="btn-close" onclick="this.parentElement.parentElement.remove()"></button>
        </div>
    `;
    document.body.appendChild(toast);

    setTimeout(() => toast.remove(), 5000);
}

// Handle import form submission
document.addEventListener('DOMContentLoaded', function() {
    const importForm = document.querySelector('#importModal form');
    const importBtn = document.getElementById('importBtn');
    const importBtnText = document.getElementById('importBtnText');
    const importBtnLoading = document.getElementById('importBtnLoading');

    if (importForm) {
        importForm.addEventListener('submit', function() {
            importBtn.disabled = true;
            importBtnText.classList.add('d-none');
            importBtnLoading.classList.remove('d-none');
        });
    }

    // Auto-submit functionality
    const filterForm = document.getElementById('filterForm');
    const autoSubmitElements = document.querySelectorAll('.auto-submit');
    const searchInput = document.querySelector('.search-input');

    // Auto-submit untuk dropdown
    autoSubmitElements.forEach(element => {
        element.addEventListener('change', function() {
            filterForm.submit();
        });
    });

    // Submit saat tekan Enter di search input
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                filterForm.submit();
            }
        });
    }
});

// ==============================================================================
// BULK ID CARD DOWNLOAD FUNCTIONALITY
// ==============================================================================

/**
 * Fetch student canvas data from server
 */
async function fetchStudentCanvasData(studentId) {
    try {
        const response = await fetch(`/admin/siswa/${studentId}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();

        // Return formatted data for canvas rendering
        return {
            id: data.id,
            nama: data.nama_lengkap || data.nama || 'Tidak ada nama',
            nisn: data.nisn || '-',
            nis: data.nis || '-',
            ttl: `${data.tempat_lahir || ''}, ${data.tanggal_lahir || ''}`.trim(),
            fotoUrl: data.foto_url || null, // Set to null if no photo
            logoUrl: '/assets/image/LogoSMP3SAWAN.webp' // Path logo sekolah yang benar
        };
    } catch (error) {
        console.error('Error fetching student data:', error);
        throw error;
    }
}

/**
 * Draw ID card on canvas for a specific student
 */
function drawStudentIdCard(canvas, studentData) {
    return new Promise((resolve, reject) => {
        try {
            const ctx = canvas.getContext('2d');

            // Clear canvas
            ctx.clearRect(0, 0, canvas.width, canvas.height);

            // Create background gradient
            const gradient = ctx.createLinearGradient(0, 0, canvas.width, canvas.height);
            gradient.addColorStop(0, '#1e3c72');
            gradient.addColorStop(1, '#2a5298');

            // Fill background
            ctx.fillStyle = gradient;
            ctx.fillRect(0, 0, canvas.width, canvas.height);

            // Add background patterns (decorative circles)
            drawBackgroundPattern(ctx, canvas.width, canvas.height);

            // Function to continue drawing even without logo
            const continueDrawing = function() {
                // Draw header text
                drawHeader(ctx);

                // Load and draw student photo (only if exists)
                if (studentData.fotoUrl) {
                    const foto = new Image();
                    foto.crossOrigin = 'anonymous';
                    foto.onload = function() {
                        // Draw photo
                        ctx.save();
                        ctx.fillStyle = 'white';
                        roundRect(ctx, 66, 198, 228, 340, 20);
                        ctx.fill();

                        ctx.shadowColor = 'rgba(0,0,0,0.4)';
                        ctx.shadowBlur = 20;
                        ctx.shadowOffsetX = 4;
                        ctx.shadowOffsetY = 4;

                        roundRect(ctx, 75, 210, 210, 315, 15);
                        ctx.clip();
                        ctx.drawImage(foto, 75, 210, 210, 315);
                        ctx.restore();

                        // Draw student data
                        drawContent(ctx, studentData.nama, studentData.ttl, studentData.nisn, studentData.nis);

                        // Draw QR code and footer
                        drawQRCodeSync(ctx, studentData.nisn)
                            .then(() => {
                                drawFooter(ctx);
                                resolve();
                            })
                            .catch((err) => {
                                console.warn('QR Code error, continuing without it:', err);
                                drawFooter(ctx);
                                resolve(); // Resolve anyway
                            });
                    };
                    foto.onerror = () => {
                        console.warn('Failed to load photo for ' + studentData.nama + ', skipping photo');

                        // Just continue without photo - no placeholder
                        drawContent(ctx, studentData.nama, studentData.ttl, studentData.nisn, studentData.nis);

                        // Draw QR code and footer
                        drawQRCodeSync(ctx, studentData.nisn)
                            .then(() => {
                                drawFooter(ctx);
                                resolve();
                            })
                            .catch((err) => {
                                console.warn('QR Code error:', err);
                                drawFooter(ctx);
                                resolve(); // Resolve anyway
                            });
                    };
                    foto.src = studentData.fotoUrl;
                } else {
                    // No photo URL, just draw without photo
                    drawContent(ctx, studentData.nama, studentData.ttl, studentData.nisn, studentData.nis);

                    // Draw QR code and footer
                    drawQRCodeSync(ctx, studentData.nisn)
                        .then(() => {
                            drawFooter(ctx);
                            resolve();
                        })
                        .catch((err) => {
                            console.warn('QR Code error:', err);
                            drawFooter(ctx);
                            resolve(); // Resolve anyway
                        });
                }
            };

            // Try to load logo
            const logo = new Image();
            logo.crossOrigin = 'anonymous';

            // Set a timeout for logo loading
            let logoLoaded = false;
            const logoTimeout = setTimeout(() => {
                if (!logoLoaded) {
                    console.warn('Logo load timeout, continuing without logo');
                    continueDrawing();
                }
            }, 3000); // 3 second timeout

            logo.onload = function() {
                logoLoaded = true;
                clearTimeout(logoTimeout);

                // Draw logo
                const centerX = canvas.width / 2 - 350;
                const centerY = 90;
                const r = 60;

                ctx.save();
                ctx.fillStyle = 'white';
                ctx.beginPath();
                ctx.arc(centerX, centerY, r, 0, Math.PI * 2);
                ctx.fill();
                ctx.restore();

                ctx.save();
                ctx.shadowColor = 'rgba(0,0,0,0.3)';
                ctx.shadowBlur = 10;
                ctx.shadowOffsetX = 2;
                ctx.shadowOffsetY = 2;

                ctx.beginPath();
                ctx.arc(centerX, centerY, r, 0, Math.PI * 2);
                ctx.closePath();
                ctx.clip();

                const imgSize = r * 2 - 6;
                ctx.drawImage(logo, centerX - imgSize / 2, centerY - imgSize / 2, imgSize, imgSize);
                ctx.restore();

                continueDrawing();
            };

            logo.onerror = () => {
                logoLoaded = true;
                clearTimeout(logoTimeout);
                console.warn('Failed to load logo, continuing without it');
                continueDrawing(); // Continue without logo
            };

            // Try multiple logo paths
            const logoPaths = [
                '/assets/image/LogoSMP3SAWAN.webp',
                '{{ asset("assets/image/LogoSMP3SAWAN.webp") }}'
            ];

            logo.src = logoPaths[0];

        } catch (error) {
            console.error('Error in drawStudentIdCard:', error);
            reject(error);
        }
    });
}

/**
 * Helper function to draw QR code synchronously
 */
function drawQRCodeSync(ctx, nisn) {
    return new Promise((resolve, reject) => {
        if (typeof QRCode === 'undefined') {
            console.warn('QRCode library not available');
            resolve();
            return;
        }

        const qrCanvas = document.createElement('canvas');
        QRCode.toCanvas(qrCanvas, nisn, {
            width: 250,
            height: 250,
            margin: 1,
            color: {
                dark: '#000000',
                light: '#FFFFFF'
            },
            errorCorrectionLevel: 'M'
        })
        .then(() => {
            ctx.fillStyle = 'white';
            roundRect(ctx, 750, 280, 372, 400, 24);
            ctx.fill();

            ctx.shadowBlur = 0;
            ctx.shadowOffsetX = 0;
            ctx.shadowOffsetY = 0;

            ctx.drawImage(qrCanvas, 775, 305, 320, 320);

            ctx.fillStyle = '#666';
            ctx.font = 'bold 21px Arial, sans-serif';
            ctx.textAlign = 'center';
            ctx.fillText('SCAN ME', 950, 655);

            resolve();
        })
        .catch(reject);
    });
}

/**
 * Helper functions for canvas drawing
 */
function drawBackgroundPattern(ctx, width, height) {
    ctx.save();
    const gradient1 = ctx.createRadialGradient(width * 0.85, height * 0.15, 0, width * 0.85, height * 0.15, 300);
    gradient1.addColorStop(0, 'rgba(255,255,255,0.1)');
    gradient1.addColorStop(0.5, 'rgba(255,165,0,0.1)');
    gradient1.addColorStop(1, 'rgba(255,255,255,0)');
    ctx.fillStyle = gradient1;
    ctx.beginPath();
    ctx.arc(width * 0.85, height * 0.15, 300, 0, 2 * Math.PI);
    ctx.fill();

    const gradient2 = ctx.createRadialGradient(width * 0.15, height * 0.85, 0, width * 0.15, height * 0.85, 200);
    gradient2.addColorStop(0, 'rgba(255,255,255,0.08)');
    gradient2.addColorStop(0.5, 'rgba(255,165,0,0.08)');
    gradient2.addColorStop(1, 'rgba(255,255,255,0)');
    ctx.fillStyle = gradient2;
    ctx.beginPath();
    ctx.arc(width * 0.15, height * 0.85, 200, 0, 2 * Math.PI);
    ctx.fill();
    ctx.restore();
}

function drawHeader(ctx) {
    const offsetY = 20;
    ctx.fillStyle = 'white';
    ctx.font = 'bold 33px Arial, sans-serif';
    ctx.textAlign = 'center';
    ctx.fillText('KARTU IDENTITAS MURID', ctx.canvas.width / 2, 55 + offsetY);

    ctx.font = 'bold 41px Arial, sans-serif';
    ctx.fillText('SMP NEGERI 3 SAWAN', ctx.canvas.width / 2, 95 + offsetY);

    ctx.font = 'italic 27px Arial, sans-serif';
    ctx.fillStyle = 'rgba(255,255,255,0.9)';
    ctx.fillText('Student Identity Card', ctx.canvas.width / 2, 125 + offsetY);
    ctx.textAlign = 'left';
}

function drawContent(ctx, nama, ttl, nisn, nis) {
    ctx.fillStyle = 'white';
    ctx.shadowColor = 'rgba(0,0,0,0.7)';
    ctx.shadowBlur = 0;
    ctx.shadowOffsetX = 0;
    ctx.shadowOffsetY = 0;

    ctx.font = 'bold 51px Arial, sans-serif';
    ctx.textAlign = 'left';
    ctx.fillText(nama.toUpperCase().trim().substring(0, 24), 360, 238);

    const dataY = 300;
    const lineHeight = 90;

    drawDataField(ctx, 'Tempat, Tanggal Lahir', ttl, 360, dataY, '#4ecdc4');
    drawDataField(ctx, 'NISN', nisn, 360, dataY + lineHeight, '#ffd93d');
    drawDataField(ctx, 'NIS', nis, 360, dataY + (lineHeight * 2), '#4ecdc4');
}

function drawDataField(ctx, label, value, x, y, borderColor) {
    ctx.fillStyle = borderColor;
    ctx.fillRect(x, y - 25, 6, 50);

    ctx.font = '23px Arial, sans-serif';
    ctx.fillStyle = 'rgba(255,255,255,0.8)';
    ctx.fillText(label, x + 20, y - 5);

    ctx.font = 'bold 31px Arial, sans-serif';
    ctx.fillStyle = 'white';
    ctx.fillText(value, x + 20, y + 30);
}

function drawFooter(ctx) {
    ctx.fillStyle = 'rgba(255,255,255,0.7)';
    ctx.font = '17px Arial, sans-serif';
    ctx.textAlign = 'left';
    ctx.fillText('SMP Negeri 3 Sawan - Suwug, Kec. Sawan, Kabupaten Buleleng, Bali 81171', 40, 720);

    const currentDate = new Date().toLocaleString('id-ID', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
    ctx.fillText('Generated: ' + currentDate, 40, 745);
}

function roundRect(ctx, x, y, width, height, radius) {
    ctx.beginPath();
    ctx.moveTo(x + radius, y);
    ctx.lineTo(x + width - radius, y);
    ctx.quadraticCurveTo(x + width, y, x + width, y + radius);
    ctx.lineTo(x + width, y + height - radius);
    ctx.quadraticCurveTo(x + width, y + height, x + width - radius, y + height);
    ctx.lineTo(x + radius, y + height);
    ctx.quadraticCurveTo(x, y + height, x, y + height - radius);
    ctx.lineTo(x, y + radius);
    ctx.quadraticCurveTo(x, y, x + radius, y);
    ctx.closePath();
}

/**
 * Main function: Download bulk ID cards
 */
async function downloadBulkIdCards(selectedStudentIds) {
    if (!selectedStudentIds || selectedStudentIds.length === 0) {
        showToast('error', 'Tidak ada siswa yang dipilih');
        return;
    }

    // Show loading overlay
    const overlay = document.createElement('div');
    overlay.id = 'bulk-download-overlay';
    overlay.style.cssText = 'position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.8); z-index: 9999; display: flex; align-items: center; justify-content: center;';
    overlay.innerHTML = `
        <div class="card" style="min-width: 400px; max-width: 500px;">
            <div class="card-body text-center p-4">
                <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <h5 class="mb-2">Membuat Kartu Identitas</h5>
                <p class="text-muted mb-2">Mohon tunggu, sedang memproses...</p>
                <div class="progress" style="height: 25px;">
                    <div id="bulk-progress-bar" class="progress-bar progress-bar-striped progress-bar-animated"
                         role="progressbar" style="width: 0%">0 / ${selectedStudentIds.length}</div>
                </div>
                <small class="text-muted mt-2 d-block">Jangan tutup halaman ini</small>
            </div>
        </div>
    `;
    document.body.appendChild(overlay);

    try {
        // Create hidden canvas
        const canvas = document.createElement('canvas');
        canvas.width = 1200;
        canvas.height = 800;
        canvas.style.display = 'none';
        document.body.appendChild(canvas);

        const results = [];
        const progressBar = document.getElementById('bulk-progress-bar');
        let successCount = 0;
        let errorCount = 0;

        // Process each student
        for (let i = 0; i < selectedStudentIds.length; i++) {
            const studentId = selectedStudentIds[i];

            try {
                // Update progress
                const progress = Math.round(((i + 1) / selectedStudentIds.length) * 100);
                progressBar.style.width = progress + '%';
                progressBar.textContent = `${i + 1} / ${selectedStudentIds.length} (✓${successCount} ✗${errorCount})`;

                // Fetch student data
                const studentData = await fetchStudentCanvasData(studentId);

                // Draw on canvas
                await drawStudentIdCard(canvas, studentData);

                // Wait a bit for canvas to render completely
                await new Promise(resolve => setTimeout(resolve, 500));

                // Convert to base64
                const base64 = canvas.toDataURL('image/png', 1.0);

                results.push({
                    id: studentData.id,
                    name: studentData.nama,
                    nisn: studentData.nisn,
                    base64: base64
                });

                successCount++;

            } catch (error) {
                console.error(`Error processing student ${studentId}:`, error);
                errorCount++;
                // Continue with next student
            }
        }

        // Remove canvas
        document.body.removeChild(canvas);

        if (results.length === 0) {
            throw new Error('Tidak ada kartu yang berhasil dibuat');
        }

        // Update overlay message
        overlay.querySelector('h5').textContent = 'Mengunduh File ZIP...';
        overlay.querySelector('p').textContent = 'Sedang membuat file ZIP...';

        // Send to server
        const response = await fetch('{{ route("admin.siswa.download_bulk_idcard") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                cards: results
            })
        });

        if (!response.ok) {
            throw new Error('Gagal membuat file ZIP');
        }

        // Download the ZIP file
        const blob = await response.blob();
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `Kartu_Identitas_Siswa_${new Date().getTime()}.zip`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);

        // Remove overlay
        document.body.removeChild(overlay);

        showToast('success', `Berhasil mengunduh ${results.length} kartu identitas`);

    } catch (error) {
        console.error('Bulk download error:', error);

        // Remove overlay
        const existingOverlay = document.getElementById('bulk-download-overlay');
        if (existingOverlay) {
            document.body.removeChild(existingOverlay);
        }

        showToast('error', 'Terjadi kesalahan: ' + error.message);
    }
}

/**
 * Start bulk download process
 * Gets all students from current filtered view
 */
function startBulkDownload() {
    const btn = document.getElementById('bulk-download-btn');

    // Get current filter parameters
    const searchValue = document.getElementById('studentSearch').value;
    const genderValue = document.getElementById('genderFilter').value;
    const classValue = document.getElementById('classFilter').value;
    const statusValue = document.getElementById('statusFilter').value;
    const perPageValue = document.getElementById('perPageFilter').value;

    const params = new URLSearchParams({
        search: searchValue,
        jenis_kelamin: genderValue,
        kelas: classValue,
        status: statusValue,
        per_page: perPageValue,
        all: '1' // Get all results without pagination
    });

    // Remove empty values
    for (let [key, value] of params.entries()) {
        if (!value && key !== 'all' && key !== 'per_page') params.delete(key);
    }

    // Fetch all students matching filter
    fetch(`{{ route('admin.siswa.search') }}?${params.toString()}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        // Extract student IDs from response
        const studentIds = extractStudentIds(data.html);

        if (studentIds.length === 0) {
            showToast('warning', 'Tidak ada siswa untuk diunduh');
            return;
        }

        // Show modal with information
        showBulkDownloadModal(studentIds, {
            search: searchValue,
            gender: genderValue,
            kelas: classValue,
            status: statusValue
        });
    })
    .catch(error => {
        console.error('Error fetching students:', error);
        showToast('error', 'Gagal mengambil data siswa');
    });
}

/**
 * Show bulk download confirmation modal
 */
function showBulkDownloadModal(studentIds, filters) {
    // Update total students
    document.getElementById('totalStudentsToDownload').textContent = studentIds.length;

    // Update filter info text
    let filterText = '';
    if (filters.search || filters.gender || filters.kelas || filters.status) {
        filterText = ' yang sesuai dengan filter';
    } else {
        filterText = ' (semua siswa)';
    }
    document.getElementById('filterInfoText').textContent = filterText;

    // Display active filters
    const activeFiltersContainer = document.getElementById('activeFilters');
    activeFiltersContainer.innerHTML = '';

    let hasFilters = false;

    if (filters.search) {
        hasFilters = true;
        activeFiltersContainer.innerHTML += `
            <span class="badge bg-primary">
                <i class="fas fa-search me-1"></i>Pencarian: ${filters.search}
            </span>
        `;
    }

    if (filters.kelas) {
        hasFilters = true;
        activeFiltersContainer.innerHTML += `
            <span class="badge bg-info">
                <i class="fas fa-school me-1"></i>Kelas: ${filters.kelas}
            </span>
        `;
    }

    if (filters.gender) {
        hasFilters = true;
        const genderText = filters.gender === 'L' ? 'Laki-laki' : 'Perempuan';
        activeFiltersContainer.innerHTML += `
            <span class="badge bg-warning text-dark">
                <i class="fas fa-venus-mars me-1"></i>${genderText}
            </span>
        `;
    }

    if (filters.status) {
        hasFilters = true;
        activeFiltersContainer.innerHTML += `
            <span class="badge bg-success">
                <i class="fas fa-check-circle me-1"></i>${filters.status}
            </span>
        `;
    }

    if (!hasFilters) {
        activeFiltersContainer.innerHTML = `
            <span class="badge bg-secondary">
                <i class="fas fa-list me-1"></i>Tidak ada filter (semua data)
            </span>
        `;
    }

    // Store student IDs for later use
    window.pendingDownloadStudentIds = studentIds;

    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('bulkDownloadModal'));
    modal.show();

    // Attach event to confirm button
    document.getElementById('confirmBulkDownloadBtn').onclick = function() {
        // Close modal
        modal.hide();

        // Start download process
        const btn = document.getElementById('bulk-download-btn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i><span>Mempersiapkan...</span>';

        downloadBulkIdCards(window.pendingDownloadStudentIds);

        // Re-enable button after a short delay
        setTimeout(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-id-card me-1"></i><span>Unduh Kartu Identitas</span>';
        }, 2000);
    };
}

/**
 * Extract student IDs from HTML table
 */
function extractStudentIds(htmlString) {
    const parser = new DOMParser();
    const doc = parser.parseFromString(htmlString, 'text/html');
    const rows = doc.querySelectorAll('table tbody tr');
    const ids = [];

    rows.forEach(row => {
        // Look for data-id attribute or extract from action buttons
        const editBtn = row.querySelector('a[href*="/siswa/"][href*="/edit"]');
        if (editBtn) {
            const href = editBtn.getAttribute('href');
            const match = href.match(/\/siswa\/(\d+)\/edit/);
            if (match && match[1]) {
                ids.push(parseInt(match[1]));
            }
        }
    });

    return ids;
}
</script>
@endpush
