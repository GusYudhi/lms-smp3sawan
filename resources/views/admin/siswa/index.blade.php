@extends('layouts.app')

@section('title', 'Data Siswa')

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
                                <i class="fas fa-user-graduate text-primary me-2"></i>Data Siswa
                            </h1>
                            <p class="text-subtle mb-0 fw-medium">Kelola data siswa: tambahkan, edit, lihat, atau ekspor data</p>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ route('admin.siswa.create') }}" class="btn btn-primary me-2 shadow-sm">
                                <i class="fas fa-plus me-1"></i> Tambah Siswa
                            </a>
                            <button class="btn btn-outline-secondary shadow-sm" onclick="exportData()">
                                <i class="fas fa-file-export me-1"></i> Ekspor
                            </button>
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
                    <h2 class="text-primary fw-bold mb-1">{{ $students->total() }}</h2>
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
                    <h2 class="text-success fw-bold mb-1">{{ $students->total() }}</h2>
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
                    <h2 class="text-info fw-bold mb-1">{{ rand(90, 120) }}</h2>
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
                    <h2 class="text-warning fw-bold mb-1">{{ rand(80, 110) }}</h2>
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
                            <!-- Search Input -->
                            <div class="col-md-4">
                                <label for="studentSearch" class="form-label text-medium-contrast fw-medium">Pencarian</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-search text-muted"></i>
                                    </span>
                                    <input type="text"
                                           name="search"
                                           id="studentSearch"
                                           class="form-control border-start-0"
                                           placeholder="Cari nama, NIS, NISN..."
                                           value="{{ request('search') }}">
                                </div>
                            </div>

                            <!-- Class Filter -->
                            <div class="col-md-2">
                                <label for="classFilter" class="form-label text-medium-contrast fw-medium">Kelas</label>
                                <select name="kelas" id="classFilter" class="form-select">
                                    <option value="">Semua Kelas</option>
                                    <option value="7" {{ request('kelas') === '7' ? 'selected' : '' }}>Kelas 7</option>
                                    <option value="8" {{ request('kelas') === '8' ? 'selected' : '' }}>Kelas 8</option>
                                    <option value="9" {{ request('kelas') === '9' ? 'selected' : '' }}>Kelas 9</option>
                                </select>
                            </div>

                            <!-- Gender Filter -->
                            <div class="col-md-2">
                                <label for="genderFilter" class="form-label text-medium-contrast fw-medium">Jenis Kelamin</label>
                                <select name="gender" id="genderFilter" class="form-select">
                                    <option value="">Semua</option>
                                    <option value="L" {{ request('gender') === 'L' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="P" {{ request('gender') === 'P' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>

                            <!-- Status Filter -->
                            <div class="col-md-2">
                                <label for="statusFilter" class="form-label text-medium-contrast fw-medium">Status</label>
                                <select name="status" id="statusFilter" class="form-select">
                                    <option value="">Semua Status</option>
                                    <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                                    <option value="tidak_aktif" {{ request('status') === 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                                </select>
                            </div>

                            <!-- Action Buttons -->
                            <div class="col-md-2">
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary shadow-sm fw-medium">
                                        <i class="fas fa-search me-1"></i> Cari
                                    </button>
                                    <a href="{{ route('admin.siswa.index') }}" class="btn btn-outline-secondary shadow-sm fw-medium">
                                        <i class="fas fa-redo me-1"></i> Reset
                                    </a>
                                </div>
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

    // Auto-hide alerts
    const alerts = document.querySelectorAll('.alert');
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

    const params = new URLSearchParams({
        search: searchValue,
        gender: genderValue,
        kelas: classValue,
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
        if (genderValue) newUrl.searchParams.set('gender', genderValue);
        else newUrl.searchParams.delete('gender');
        if (classValue) newUrl.searchParams.set('kelas', classValue);
        else newUrl.searchParams.delete('kelas');
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

    if (search) params.append('search', search);
    if (gender) params.append('gender', gender);
    if (kelas) params.append('kelas', kelas);

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
</script>
@endpush
