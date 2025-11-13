@extends('layouts.app')

@section('content')
<div class="guru-management">
    <!-- Page Header -->
    <div class="page-header">
        <div class="header-content">
            <h1><i class="fas fa-chalkboard-teacher"></i> Data Guru</h1>
            <p class="page-subtitle">Kelola informasi data guru dan tenaga pendidik</p>
        </div>
        <div class="header-actions">
            <a href="{{ route('admin.guru.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Guru Baru
            </a>
            <a href="{{ route('admin.guru.export') }}" class="btn btn-secondary">
                <i class="fas fa-download"></i> Download Data
            </a>
        </div>
    </div>

    <!-- Statistik Guru -->
    <div class="teacher-stats-overview">
        <div class="teacher-stats-grid">
            <!-- Total Guru -->
            <div class="teacher-stat-card total-card">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <h3>Total Guru</h3>
                    <div class="stat-number">{{ \App\Models\User::where('role', 'guru')->count() }}</div>
                    <p class="stat-description">Semua Guru</p>
                </div>
            </div>

            <!-- Guru PNS -->
            <div class="teacher-stat-card pns-card">
                <div class="stat-icon">
                    <i class="fas fa-id-badge"></i>
                </div>
                <div class="stat-content">
                    <h3>Guru PNS</h3>
                    <div class="stat-number">{{ rand(15, 25) }}</div>
                    <p class="stat-description">Pegawai Negeri Sipil</p>
                </div>
            </div>

            <!-- Guru PPPK -->
            <div class="teacher-stat-card pppk-card">
                <div class="stat-icon">
                    <i class="fas fa-certificate"></i>
                </div>
                <div class="stat-content">
                    <h3>Guru PPPK</h3>
                    <div class="stat-number">{{ rand(8, 15) }}</div>
                    <p class="stat-description">P3K Non PNS</p>
                </div>
            </div>

            <!-- Guru Honorer -->
            <div class="teacher-stat-card honor-card">
                <div class="stat-icon">
                    <i class="fas fa-handshake"></i>
                </div>
                <div class="stat-content">
                    <h3>Guru Honorer</h3>
                    <div class="stat-number">{{ rand(5, 12) }}</div>
                    <p class="stat-description">Guru Kontrak</p>
                </div>
            </div>

            <!-- Wali Kelas -->
            <div class="teacher-stat-card wali-card">
                <div class="stat-icon">
                    <i class="fas fa-door-open"></i>
                </div>
                <div class="stat-content">
                    <h3>Wali Kelas</h3>
                    <div class="stat-number">18</div>
                    <p class="stat-description">Total Kelas</p>
                </div>
            </div>

            <!-- Guru Aktif -->
            <div class="teacher-stat-card active-card">
                <div class="stat-icon">
                    <i class="fas fa-user-check"></i>
                </div>
                <div class="stat-content">
                    <h3>Status Aktif</h3>
                    <div class="stat-number">{{ \App\Models\User::where('role', 'guru')->count() }}</div>
                    <p class="stat-description">Guru Mengajar</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter dan Pencarian -->
    <div class="data-controls">
        <div class="search-filter-container">
            <form method="GET" action="{{ route('admin.guru.index') }}" id="filterForm">
                <div class="search-section">
                    <div class="search-input-group">
                        <input type="text"
                               name="search"
                               id="teacherSearch"
                               placeholder="Cari berdasarkan nama guru, email, atau NIP..."
                               class="search-input"
                               value="{{ request('search') }}">
                        <i class="fas fa-search"></i>
                    </div>
                </div>

                <div class="filter-section">
                    <select name="status" id="statusFilter" class="filter-select">
                        <option value="">Semua Status</option>
                        <option value="PNS" {{ request('status') === 'PNS' ? 'selected' : '' }}>PNS</option>
                        <option value="PPPK" {{ request('status') === 'PPPK' ? 'selected' : '' }}>PPPK</option>
                        <option value="Honorer" {{ request('status') === 'Honorer' ? 'selected' : '' }}>Honorer</option>
                    </select>

                    <select name="gender" id="genderFilter" class="filter-select">
                        <option value="">Semua Jenis Kelamin</option>
                        <option value="L" {{ request('gender') === 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ request('gender') === 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>

                    <select id="waliFilter" class="filter-select" disabled style="opacity: 0.5;">
                        <option value="">Filter Wali Kelas</option>
                        <option value="Ya">Wali Kelas</option>
                        <option value="Tidak">Bukan Wali</option>
                    </select>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Cari
                    </button>

                    <a href="{{ route('admin.guru.index') }}" class="btn btn-secondary reset-filter">
                        <i class="fas fa-redo"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabel Data Guru -->
    <div class="teacher-data-table-card">
        <div class="table-header">
            <h2><i class="fas fa-table"></i> Daftar Guru</h2>
            <div class="table-actions">
                <span class="data-count" id="table-info">
                    @include('admin.guru.partials.table-info')
                </span>
            </div>
        </div>

        <div id="table-container">
            @include('admin.guru.partials.table')
        </div>

        <!-- Pagination -->
        <div id="pagination-container">
            @include('admin.guru.partials.pagination')
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-exclamation-triangle"></i> Konfirmasi Hapus Data</h3>
            <button class="modal-close" onclick="closeDeleteModal()">&times;</button>
        </div>
        <div class="modal-body">
            <p>Apakah Anda yakin ingin menghapus data guru:</p>
            <p><strong id="teacherNameToDelete"></strong></p>
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

<!-- Script untuk Pencarian dan Filter -->
<script>
// Global variables
let searchTimeout;
let currentPage = 1;
let isLoading = false;

document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('teacherSearch');
    const statusFilter = document.getElementById('statusFilter');
    const genderFilter = document.getElementById('genderFilter');
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
    statusFilter.addEventListener('change', function() {
        currentPage = 1; // Reset to page 1 on filter change
        performAjaxSearch();
    });

    genderFilter.addEventListener('change', function() {
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

    const searchValue = document.getElementById('teacherSearch').value;
    const statusValue = document.getElementById('statusFilter').value;
    const genderValue = document.getElementById('genderFilter').value;

    const params = new URLSearchParams({
        search: searchValue,
        status: statusValue,
        gender: genderValue,
        page: currentPage
    });

    // Remove empty values
    for (let [key, value] of params.entries()) {
        if (!value) params.delete(key);
    }

    const url = `{{ route('admin.guru.search') }}?${params.toString()}`;
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
        if (statusValue) newUrl.searchParams.set('status', statusValue);
        else newUrl.searchParams.delete('status');
        if (genderValue) newUrl.searchParams.set('gender', genderValue);
        else newUrl.searchParams.delete('gender');
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
}

// Function to load specific page
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
    const rows = document.querySelectorAll('.teacher-row');
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
function confirmDelete(teacherId, teacherName) {
    document.getElementById('teacherNameToDelete').textContent = teacherName;
    const modal = document.getElementById('deleteModal');
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';

    const modalContent = modal.querySelector('.modal-content');
    modalContent.style.animation = 'modalSlideIn 0.3s ease-out';

    const confirmBtn = document.getElementById('confirmDeleteBtn');
    confirmBtn.onclick = function() {
        deleteTeacher(teacherId);
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

function deleteTeacher(teacherId) {
    const deleteBtn = document.getElementById('confirmDeleteBtn');
    const originalText = deleteBtn.innerHTML;

    deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menghapus...';
    deleteBtn.disabled = true;

    const form = document.getElementById('deleteForm');
    form.action = `/admin/guru/${teacherId}`;
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
    const exportBtn = document.querySelector('.btn-export, [href*="export"]');
    if (!exportBtn) return;

    const originalText = exportBtn.innerHTML;

    exportBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengunduh...';
    exportBtn.disabled = true;

    // Get current filter values
    const params = new URLSearchParams();
    const search = document.getElementById('teacherSearch').value;
    const status = document.getElementById('statusFilter').value;
    const gender = document.getElementById('genderFilter').value;

    if (search) params.append('search', search);
    if (status) params.append('status', status);
    if (gender) params.append('gender', gender);

    const exportUrl = `{{ route('admin.guru.export') }}?${params.toString()}`;

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
</script>
@endsection
