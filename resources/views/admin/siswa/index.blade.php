@extends('layouts.app')

@section('title', 'Data Siswa')

@section('content')
<div class="siswa-management">
    <!-- Page Header -->
    <div class="page-header">
        <div class="header-content">
            <h1><i class="fas fa-user-graduate"></i> Data Siswa</h1>
            <p class="page-subtitle">Kelola data siswa: tambahkan, edit, lihat, atau ekspor data</p>
        </div>
        <div class="header-actions">
            <a href="{{ route('admin.siswa.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Siswa
            </a>
            <button class="btn btn-outline-secondary btn-export" onclick="exportData()">
                <i class="fas fa-file-export"></i> Ekspor
            </button>
        </div>
    </div>

    <!-- Statistik Siswa (simple) -->
    <div class="student-stats-overview">
        <div class="stats-grid">
            <div class="stat-card siswa-card">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <h3>Total Siswa</h3>
                    <p class="stat-number">{{ $students->total() }}</p>
                    <p class="stat-description">Jumlah seluruh siswa yang terdaftar</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter dan Pencarian -->
    <div class="data-controls">
        <div class="search-filter-container">
            <form method="GET" action="{{ route('admin.siswa.index') }}" id="filterForm">
                <div class="search-section">
                    <div class="search-input-group">
                        <input type="text"
                               name="search"
                               id="studentSearch"
                               placeholder="Cari nama, NIS, NISN..."
                               class="search-input"
                               value="{{ request('search') }}">
                        <i class="fas fa-search"></i>
                    </div>
                </div>

                <div class="filter-section">
                    <select name="jenis_kelamin" id="genderFilter" class="filter-select">
                        <option value="">Semua Jenis Kelamin</option>
                        <option value="laki-laki" {{ request('jenis_kelamin') === 'laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="perempuan" {{ request('jenis_kelamin') === 'perempuan' ? 'selected' : '' }}>Perempuan</option>
                    </select>

                    <select name="kelas" id="classFilter" class="filter-select">
                        <option value="">Semua Kelas</option>
                        @foreach($classes ?? [] as $kelas)
                            <option value="{{ $kelas }}" {{ request('kelas') === $kelas ? 'selected' : '' }}>{{ $kelas }}</option>
                        @endforeach
                    </select>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Cari
                    </button>

                    <a href="{{ route('admin.siswa.index') }}" class="btn btn-secondary reset-filter">
                        <i class="fas fa-redo"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabel Data Siswa -->
    <div class="student-data-table-card">
        <div class="table-header">
            <h2>Daftar Siswa</h2>
            <p class="text-muted" id="table-info">
                @include('admin.siswa.partials.table-info')
            </p>
        </div>

        <div class="table-wrapper" id="table-container">
            @include('admin.siswa.partials.table')
        </div>

        <!-- Pagination -->
        <div id="pagination-container">
            @include('admin.siswa.partials.pagination')
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>
                <i class="fas fa-exclamation-triangle"></i>
                Konfirmasi Hapus Data
            </h3>
            <button class="modal-close" onclick="closeDeleteModal()">&times;</button>
        </div>
        <div class="modal-body">
            <p>Apakah Anda yakin ingin menghapus data siswa:</p>
            <p><strong id="studentNameToDelete"></strong></p>
            <p class="warning-text">
                <i class="fas fa-warning"></i>
                Data yang dihapus tidak dapat dikembalikan!
            </p>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeDeleteModal()">
                <i class="fas fa-times"></i> Batal
            </button>
            <button class="btn btn-danger" id="confirmDeleteBtn">
                <i class="fas fa-trash"></i> Ya, Hapus Data
            </button>
        </div>
    </div>
</div>

<!-- Form Hidden untuk Delete -->
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>


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
            currentPage = 1; // Reset to page 1 on new search
            performAjaxSearch();
        }, 500);
    });

    // Immediate filter on select change (AJAX)
    genderFilter.addEventListener('change', function() {
        currentPage = 1; // Reset to page 1 on filter change
        performAjaxSearch();
    });

    classFilter.addEventListener('change', function() {
        currentPage = 1; // Reset to page 1 on filter change
        performAjaxSearch();
    });

    // Enhanced success message auto-hide with animation
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        alert.style.opacity = '1';
        alert.style.transition = 'opacity 0.3s ease';

        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => {
                if (alert.parentNode) {
                    alert.parentNode.removeChild(alert);
                }
            }, 300);
        }, 5000);
    });
});

// AJAX search function
function performAjaxSearch() {
    if (isLoading) return;

    console.log('Performing AJAX search...'); // Debug

    isLoading = true;
    showLoadingState();

    const searchValue = document.getElementById('studentSearch').value;
    const genderValue = document.getElementById('genderFilter').value;
    const classValue = document.getElementById('classFilter').value;

    const params = new URLSearchParams({
        search: searchValue,
        jenis_kelamin: genderValue,
        kelas: classValue,
        page: currentPage
    });

    // Remove empty values
    for (let [key, value] of params.entries()) {
        if (!value) params.delete(key);
    }

    const url = `{{ route('admin.siswa.search') }}?${params.toString()}`;
    console.log('AJAX URL:', url); // Debug

    fetch(url, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        console.log('Response status:', response.status); // Debug
        console.log('Response headers:', response.headers); // Debug
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data); // Debug

        // Update table content
        document.getElementById('table-container').innerHTML = data.html;

        // Update pagination
        document.getElementById('pagination-container').innerHTML = data.pagination;

        // Update table info
        document.getElementById('table-info').innerHTML = data.info;

        // Update URL without page reload
        const newUrl = new URL(window.location);
        if (searchValue) newUrl.searchParams.set('search', searchValue);
        else newUrl.searchParams.delete('search');
        if (genderValue) newUrl.searchParams.set('jenis_kelamin', genderValue);
        else newUrl.searchParams.delete('jenis_kelamin');
        if (classValue) newUrl.searchParams.set('kelas', classValue);
        else newUrl.searchParams.delete('kelas');
        if (currentPage > 1) newUrl.searchParams.set('page', currentPage);
        else newUrl.searchParams.delete('page');

        window.history.pushState({}, '', newUrl);

        // Reattach hover effects to new rows
        attachRowHoverEffects();

        hideLoadingState();
        isLoading = false;
    })
    .catch(error => {
        console.error('AJAX Error:', error); // Debug
        showErrorMessage('Terjadi kesalahan saat mencari data');
        hideLoadingState();
        isLoading = false;
    });
}// Function to load specific page
function loadPage(page) {
    currentPage = page;
    performAjaxSearch();
}

// Show loading state
function showLoadingState() {
    const tableContainer = document.getElementById('table-container');
    const overlay = document.createElement('div');
    overlay.id = 'table-loading-overlay';
    overlay.innerHTML = `
        <div class="loading-content">
            <i class="fas fa-spinner fa-spin"></i>
            <p>Memuat data...</p>
        </div>
    `;
    overlay.style.cssText = `
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.8);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 100;
        border-radius: 8px;
    `;

    // Make table container relative
    tableContainer.style.position = 'relative';
    tableContainer.appendChild(overlay);

    const content = overlay.querySelector('.loading-content');
    content.style.cssText = `
        text-align: center;
        color: var(--primary-color);
        font-size: 14px;
    `;
}

// Hide loading state
function hideLoadingState() {
    const overlay = document.getElementById('table-loading-overlay');
    if (overlay) {
        overlay.remove();
    }
}

// Show error message
function showErrorMessage(message) {
    const alert = document.createElement('div');
    alert.className = 'alert alert-danger';
    alert.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        max-width: 300px;
        animation: slideIn 0.3s ease-out;
    `;
    alert.innerHTML = `
        <i class="fas fa-exclamation-circle"></i>
        ${message}
        <button type="button" class="close" onclick="this.parentElement.remove()">
            <span>&times;</span>
        </button>
    `;
    document.body.appendChild(alert);

    setTimeout(() => {
        if (alert.parentNode) {
            alert.remove();
        }
    }, 5000);
}

// Attach hover effects to table rows
function attachRowHoverEffects() {
    const rows = document.querySelectorAll('.student-row');
    rows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.backgroundColor = '#f8f9fa';
            this.style.transition = 'background-color 0.2s ease';
        });

        row.addEventListener('mouseleave', function() {
            this.style.backgroundColor = '';
        });
    });
}

// Enhanced Delete Confirmation Functions
function confirmDelete(studentId, studentName) {
    document.getElementById('studentNameToDelete').textContent = studentName;
    const modal = document.getElementById('deleteModal');
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';

    const modalContent = modal.querySelector('.modal-content');
    modalContent.style.animation = 'modalSlideIn 0.3s ease-out';

    const confirmBtn = document.getElementById('confirmDeleteBtn');
    confirmBtn.onclick = function() {
        deleteStudent(studentId);
    };
}

function closeDeleteModal() {
    const modal = document.getElementById('deleteModal');
    const modalContent = modal.querySelector('.modal-content');

    modalContent.style.animation = 'modalSlideOut 0.2s ease-in';

    setTimeout(() => {
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }, 200);
}

function deleteStudent(studentId) {
    const deleteBtn = document.getElementById('confirmDeleteBtn');
    const originalText = deleteBtn.innerHTML;

    deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menghapus...';
    deleteBtn.disabled = true;

    const form = document.getElementById('deleteForm');
    form.action = `/admin/siswa/${studentId}`;
    form.submit();
}

// Enhanced modal interactions
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('deleteModal');

    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeDeleteModal();
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal.style.display === 'flex') {
            closeDeleteModal();
        }
    });
});

// Enhanced export functionality with current filters
function exportData() {
    const exportBtn = document.querySelector('.btn-export');
    const originalText = exportBtn.innerHTML;

    exportBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengunduh...';
    exportBtn.disabled = true;

    // Get current filter values
    const params = new URLSearchParams();
    const search = document.getElementById('studentSearch').value;
    const gender = document.getElementById('genderFilter').value;
    const kelas = document.getElementById('classFilter').value;

    if (search) params.append('search', search);
    if (gender) params.append('jenis_kelamin', gender);
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

// Initial call to attach hover effects
document.addEventListener('DOMContentLoaded', function() {
    attachRowHoverEffects();
});

// Add CSS for animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes modalSlideIn {
        from {
            opacity: 0;
            transform: scale(0.8) translateY(-50px);
        }
        to {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }

    @keyframes modalSlideOut {
        from {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
        to {
            opacity: 0;
            transform: scale(0.8) translateY(-50px);
        }
    }

    .modal-content {
        animation-fill-mode: forwards;
    }

    .alert .close {
        background: none;
        border: none;
        font-size: 18px;
        cursor: pointer;
        color: inherit;
        opacity: 0.7;
        margin-left: 10px;
    }

    .alert .close:hover {
        opacity: 1;
    }
`;
document.head.appendChild(style);
</script>@endsection
