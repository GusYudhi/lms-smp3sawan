@extends('layouts.app')

@section('title', 'Detail Siswa')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body py-4">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                        <div class="mb-3 mb-md-0">
                            <h1 class="h3 mb-2 text-primary">
                                <i class="fas fa-user-circle me-2"></i>Detail Siswa
                            </h1>
                            <p class="text-muted mb-0">Informasi lengkap data siswa</p>
                        </div>
                        <div>
                            <a href="{{ route('admin.siswa.edit', $student->id) }}" class="btn btn-primary me-2">
                                <i class="fas fa-edit me-2"></i>Edit Data
                            </a>
                            <a href="{{ route('admin.siswa.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Student Profile Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-lg-3 text-center text-lg-start mb-4 mb-lg-0">
                            @if($student->profile_photo)
                                <img src="{{ asset('storage/' . $student->profile_photo) }}"
                                     alt="Foto {{ $student->name }}"
                                     class="rounded-circle border"
                                     style="width: 150px; height: 150px; object-fit: cover;">
                            @else
                                <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-light border text-muted"
                                     style="width: 150px; height: 150px; font-size: 3rem;">
                                    <i class="fas fa-user-graduate"></i>
                                </div>
                            @endif
                        </div>
                        <div class="col-lg-9">
                            <h2 class="mb-2 fw-bold">{{ $student->name }}</h2>
                            <p class="text-muted mb-3">NIS: {{ $student->nis ?? '-' }}</p>

                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="d-flex flex-column">
                                        <span class="small text-muted">Kelas</span>
                                        <span class="fw-semibold">{{ $student->kelas ?? '-' }}</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="d-flex flex-column">
                                        <span class="small text-muted">NISN</span>
                                        <span class="fw-semibold">{{ $student->nisn ?? '-' }}</span>
                                    </div>
                                </div>
                                @if($student->nomor_telepon_orangtua)
                                <div class="col-md-4">
                                    <div class="d-flex flex-column">
                                        <span class="small text-muted">Telp. Orang Tua</span>
                                        <span class="fw-semibold">{{ $student->nomor_telepon_orangtua }}</span>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Detailed Information -->
    <div class="row">
        <!-- Personal Information -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light">
                    <h6 class="mb-0 fw-semibold text-primary">
                        <i class="fas fa-user me-2"></i>Informasi Personal
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="d-flex flex-column">
                                <span class="small text-muted">Nama Lengkap</span>
                                <span class="fw-semibold">{{ $student->name }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex flex-column">
                                <span class="small text-muted">NIS</span>
                                <span class="fw-semibold">{{ $student->nis ?? '-' }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex flex-column">
                                <span class="small text-muted">NISN</span>
                                <span class="fw-semibold">{{ $student->nisn ?? '-' }}</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex flex-column">
                                <span class="small text-muted">Email</span>
                                @if($student->email)
                                    <a href="mailto:{{ $student->email }}" class="text-primary text-decoration-none">
                                        {{ $student->email }}
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex flex-column">
                                <span class="small text-muted">Nomor Telepon</span>
                                @if($student->nomor_telepon)
                                    <a href="tel:{{ $student->nomor_telepon }}" class="text-primary text-decoration-none">
                                        {{ $student->nomor_telepon }}
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex flex-column">
                                <span class="small text-muted">Jenis Kelamin</span>
                                @if($student->jenis_kelamin)
                                    <span class="fw-semibold">
                                        <i class="fas fa-{{ $student->jenis_kelamin === 'Laki-laki' ? 'mars text-info' : 'venus text-warning' }} me-1"></i>
                                        {{ $student->jenis_kelamin }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex flex-column">
                                <span class="small text-muted">Tempat Lahir</span>
                                <span class="fw-semibold">{{ $student->tempat_lahir ?? '-' }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex flex-column">
                                <span class="small text-muted">Tanggal Lahir</span>
                                @if($student->tanggal_lahir)
                                    <span class="fw-semibold">
                                        <i class="fas fa-calendar text-success me-1"></i>
                                        {{ \Carbon\Carbon::parse($student->tanggal_lahir)->format('d F Y') }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Academic & Account Information -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light">
                    <h6 class="mb-0 fw-semibold text-primary">
                        <i class="fas fa-graduation-cap me-2"></i>Informasi Akademik & Akun
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="d-flex flex-column">
                                <span class="small text-muted">Kelas</span>
                                @if($student->kelas)
                                    <span class="badge bg-primary-subtle text-primary border mt-1 align-self-start">{{ $student->kelas }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex flex-column">
                                <span class="small text-muted">Status Akun</span>
                                <span class="badge bg-success-subtle text-success border mt-1 align-self-start">
                                    <i class="fas fa-check-circle me-1"></i>Aktif
                                </span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex flex-column">
                                <span class="small text-muted">Nomor Telepon Orang Tua</span>
                                @if($student->nomor_telepon_orangtua)
                                    <a href="tel:{{ $student->nomor_telepon_orangtua }}" class="text-primary text-decoration-none">
                                        {{ $student->nomor_telepon_orangtua }}
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex flex-column">
                                <span class="small text-muted">Tanggal Bergabung</span>
                                <span class="fw-semibold">
                                    <i class="fas fa-calendar-plus text-success me-1"></i>
                                    {{ $student->created_at->format('d F Y') }}
                                </span>
                                <small class="text-muted">({{ $student->created_at->diffForHumans() }})</small>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex flex-column">
                                <span class="small text-muted">Terakhir Diperbarui</span>
                                <span class="fw-semibold">
                                    <i class="fas fa-clock text-info me-1"></i>
                                    {{ $student->updated_at->format('d F Y, H:i') }}
                                </span>
                                <small class="text-muted">({{ $student->updated_at->diffForHumans() }})</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex flex-column flex-md-row justify-content-center gap-3">
                        <a href="{{ route('admin.siswa.edit', $student->id) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-2"></i>Edit Data Siswa
                        </a>
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <i class="fas fa-trash me-2"></i>Hapus Siswa
                        </button>
                        <a href="{{ route('admin.siswa.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar
                        </a>
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
                    <i class="fas fa-exclamation-triangle me-2"></i>Konfirmasi Hapus Siswa
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus data siswa <strong>{{ $student->name }}</strong>?</p>
                <div class="alert alert-warning d-flex align-items-center" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <div>Tindakan ini tidak dapat dibatalkan! Semua data terkait siswa ini akan dihapus secara permanen.</div>
                </div>
            </div>
            <div class="modal-footer border-top-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Batal
                </button>
                <form action="{{ route('admin.siswa.destroy', $student->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>Ya, Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
                            <i class="fas fa-calendar-plus"></i>
                            {{ $student->created_at->format('d F Y') }}
                            <small class="text-muted">({{ $student->created_at->diffForHumans() }})</small>
                        </span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Terakhir Diperbarui</span>
                        <span class="detail-value">
                            <i class="fas fa-clock"></i>
                            {{ $student->updated_at->format('d F Y, H:i') }}
                            <small class="text-muted">({{ $student->updated_at->diffForHumans() }})</small>
                        </span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Status Akun</span>
                        <span class="detail-value">
                            <span class="status-badge active">
                                <i class="fas fa-check-circle"></i>
                                Aktif
                            </span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="profile-actions">
        <a href="{{ route('admin.siswa.edit', $student->id) }}" class="btn btn-primary">
            <i class="fas fa-edit"></i>
            Edit Data Siswa
        </a>
        <button type="button" class="btn btn-danger" onclick="confirmDelete({{ $student->id }}, '{{ $student->name }}')">
            <i class="fas fa-trash"></i>
            Hapus Siswa
        </button>
        <a href="{{ route('admin.siswa.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i>
            Kembali ke Daftar
        </a>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>
                <i class="fas fa-exclamation-triangle"></i>
                Konfirmasi Hapus Siswa
            </h3>
            <button type="button" class="modal-close" onclick="closeDeleteModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <p>Apakah Anda yakin ingin menghapus data siswa <strong id="studentNameToDelete"></strong>?</p>
            <div class="warning-text">
                <i class="fas fa-exclamation-triangle"></i>
                Tindakan ini tidak dapat dibatalkan! Semua data terkait siswa ini akan dihapus secara permanen.
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">
                <i class="fas fa-times"></i>
                Batal
            </button>
            <form id="deleteForm" method="POST" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash"></i>
                    Ya, Hapus
                </button>
            </form>
        </div>
    </div>
</div>

<style>
.student-profile-container {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    overflow: hidden;
    margin-bottom: 30px;
}

.profile-header {
    background: linear-gradient(135deg, var(--primary-color) 0%, #667eea 100%);
    color: white;
    padding: 40px;
    display: flex;
    gap: 30px;
    align-items: center;
}

.profile-photo {
    flex-shrink: 0;
}

.student-photo-large {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    border: 4px solid white;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    object-fit: cover;
}

.default-avatar-large {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    border: 4px solid white;
    background: rgba(255, 255, 255, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 48px;
    color: white;
}

.profile-info {
    flex: 1;
}

.student-name {
    margin: 0 0 8px 0;
    font-size: 32px;
    font-weight: 700;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
}

.student-id {
    margin: 0 0 20px 0;
    font-size: 16px;
    opacity: 0.9;
    font-weight: 500;
}

.quick-stats {
    display: flex;
    gap: 30px;
    flex-wrap: wrap;
}

.stat-item {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.stat-label {
    font-size: 13px;
    opacity: 0.8;
    font-weight: 500;
}

.stat-value {
    font-size: 16px;
    font-weight: 600;
}

.profile-details {
    padding: 40px;
}

.details-grid {
    display: grid;
    gap: 40px;
}

.detail-section {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 30px;
}

.detail-section h3 {
    margin: 0 0 25px 0;
    font-size: 20px;
    font-weight: 600;
    color: var(--primary-color);
    display: flex;
    align-items: center;
    gap: 10px;
}

.detail-section h3 i {
    font-size: 18px;
}

.detail-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
}

.detail-item {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.detail-label {
    font-size: 14px;
    font-weight: 600;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.detail-value {
    font-size: 16px;
    font-weight: 500;
    color: #2d3748;
}

.email-link, .phone-link {
    color: var(--primary-color);
    text-decoration: none;
}

.email-link:hover, .phone-link:hover {
    text-decoration: underline;
}

.gender-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 600;
}

.gender-badge.laki-laki {
    background: #e3f2fd;
    color: #1565c0;
}

.gender-badge.perempuan {
    background: #fce4ec;
    color: #c2185b;
}

.class-badge {
    background: #e8f5e8;
    color: #2e7d32;
    padding: 6px 12px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 14px;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 600;
}

.status-badge.active {
    background: #e8f5e8;
    color: #2e7d32;
}

.status-badge.default {
    background: #f5f5f5;
    color: #757575;
}

.profile-actions {
    padding: 30px 40px;
    border-top: 2px solid #f1f3f4;
    background: #fafbfc;
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}

.text-muted {
    color: #9ca3af !important;
    font-style: italic;
}

.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
}

.modal-content {
    background: white;
    border-radius: 8px;
    max-width: 500px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
}

.modal-header {
    padding: 20px 30px;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.modal-header h3 {
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
    color: #dc2626;
}

.modal-close {
    background: none;
    border: none;
    font-size: 18px;
    cursor: pointer;
    padding: 5px;
    color: #6b7280;
}

.modal-close:hover {
    color: #374151;
}

.modal-body {
    padding: 30px;
}

.warning-text {
    background: #fef2f2;
    border: 1px solid #fecaca;
    color: #dc2626;
    padding: 12px;
    border-radius: 6px;
    margin-top: 15px;
    display: flex;
    align-items: flex-start;
    gap: 8px;
    font-size: 14px;
}

.modal-footer {
    padding: 20px 30px;
    border-top: 1px solid #e5e7eb;
    display: flex;
    gap: 10px;
    justify-content: flex-end;
}

@media (max-width: 768px) {
    .profile-header {
        flex-direction: column;
        text-align: center;
        padding: 30px 20px;
    }

    .student-name {
        font-size: 24px;
    }

    .quick-stats {
        justify-content: center;
        gap: 20px;
    }

    .profile-details {
        padding: 20px;
    }

    .detail-section {
        padding: 20px;
    }

    .detail-grid {
        grid-template-columns: 1fr;
        gap: 15px;
    }

    .profile-actions {
        padding: 20px;
        flex-direction: column;
    }

    .profile-actions .btn {
        width: 100%;
        text-align: center;
    }

    .modal-content {
        margin: 20px;
        width: calc(100% - 40px);
    }
}

@media (max-width: 480px) {
    .student-photo-large,
    .default-avatar-large {
        width: 100px;
        height: 100px;
    }

    .default-avatar-large {
        font-size: 40px;
    }

    .student-name {
        font-size: 20px;
    }
}
</style>

<script>
function confirmDelete(studentId, studentName) {
    document.getElementById('studentNameToDelete').textContent = studentName;
    document.getElementById('deleteForm').action = `/admin/siswa/${studentId}`;
    document.getElementById('deleteModal').style.display = 'flex';
}

function closeDeleteModal() {
    document.getElementById('deleteModal').style.display = 'none';
}

// Close modal when clicking outside
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDeleteModal();
    }
});
</script>
</div>
@endsection
