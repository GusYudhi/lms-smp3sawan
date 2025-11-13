@extends('layouts.app')

@section('title', 'Detail Siswa')

@section('content')
<div class="siswa-management">
    <!-- Page Header -->
    <div class="page-header">
        <div class="header-content">
            <h1><i class="fas fa-user-circle"></i> Detail Siswa</h1>
            <p class="page-subtitle">Informasi lengkap data siswa</p>
        </div>
        <div class="header-actions">
            <a href="{{ route('admin.siswa.edit', $student->id) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edit Data
            </a>
            <a href="{{ route('admin.siswa.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

<!-- Student Profile Card -->
<div class="student-profile-container">
    <div class="profile-header">
        <div class="profile-photo">
            @if($student->profile_photo)
                <img src="{{ asset('storage/' . $student->profile_photo) }}"
                     alt="Foto {{ $student->name }}"
                     class="student-photo-large">
            @else
                <div class="default-avatar-large">
                    <i class="fas fa-user-graduate"></i>
                </div>
            @endif
        </div>
        <div class="profile-info">
            <h2 class="student-name">{{ $student->name }}</h2>
            <p class="student-id">{{ $student->nis }}</p>

            <div class="quick-stats">
                <div class="stat-item">
                    <span class="stat-label">Kelas</span>
                    <span class="stat-value">{{ $student->kelas ?? '-' }}</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">NISN</span>
                    <span class="stat-value">{{ $student->nisn ?? '-' }}</span>
                </div>
                @if($student->nomor_telepon_orangtua)
                <div class="stat-item">
                    <span class="stat-label">Telp. Orang Tua</span>
                    <span class="stat-value">{{ $student->nomor_telepon_orangtua }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="profile-details">
        <div class="details-grid">
            <!-- Personal Information -->
            <div class="detail-section">
                <h3>
                    <i class="fas fa-user"></i>
                    Informasi Personal
                </h3>
                <div class="detail-grid">
                    <div class="detail-item">
                        <span class="detail-label">Nama Lengkap</span>
                        <span class="detail-value">{{ $student->name }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">NIS</span>
                        <span class="detail-value">{{ $student->nis }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">NISN</span>
                        <span class="detail-value">{{ $student->nisn ?? '-' }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Email</span>
                        <span class="detail-value">
                            <a href="mailto:{{ $student->email }}" class="email-link">
                                {{ $student->email }}
                            </a>
                        </span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Nomor Telepon</span>
                        <span class="detail-value">
                            @if($student->nomor_telepon)
                                <a href="tel:{{ $student->nomor_telepon }}" class="phone-link">
                                    {{ $student->nomor_telepon }}
                                </a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Jenis Kelamin</span>
                        <span class="detail-value">
                            @if($student->jenis_kelamin)
                                <span class="gender-badge {{ $student->jenis_kelamin }}">
                                    <i class="fas fa-{{ $student->jenis_kelamin === 'laki-laki' ? 'mars' : 'venus' }}"></i>
                                    {{ ucfirst($student->jenis_kelamin) }}
                                </span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Tempat Lahir</span>
                        <span class="detail-value">{{ $student->tempat_lahir ?? '-' }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Tanggal Lahir</span>
                        <span class="detail-value">
                            @if($student->tanggal_lahir)
                                <i class="fas fa-calendar"></i>
                                {{ \Carbon\Carbon::parse($student->tanggal_lahir)->format('d F Y') }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            <!-- Academic Information -->
            <div class="detail-section">
                <h3>
                    <i class="fas fa-graduation-cap"></i>
                    Informasi Akademik
                </h3>
                <div class="detail-grid">
                    <div class="detail-item">
                        <span class="detail-label">Kelas</span>
                        <span class="detail-value">
                            @if($student->kelas)
                                <span class="class-badge">{{ $student->kelas }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Nomor Telepon Orang Tua</span>
                        <span class="detail-value">
                            @if($student->nomor_telepon_orangtua)
                                <a href="tel:{{ $student->nomor_telepon_orangtua }}" class="phone-link">
                                    {{ $student->nomor_telepon_orangtua }}
                                </a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            <!-- Account Information -->
            <div class="detail-section">
                <h3>
                    <i class="fas fa-cog"></i>
                    Informasi Akun
                </h3>
                <div class="detail-grid">
                    <div class="detail-item">
                        <span class="detail-label">Tanggal Bergabung</span>
                        <span class="detail-value">
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
