@extends('layouts.app')

@section('title', 'Detail Guru')

@section('content')
<div class="guru-management">
    <!-- Page Header -->
    <div class="page-header">
        <div class="header-content">
            <h1><i class="fas fa-user-circle"></i> Detail Guru</h1>
            <p class="page-subtitle">Informasi lengkap data guru dan tenaga pendidik</p>
        </div>
        <div class="header-actions">
            <a href="{{ route('admin.guru.edit', $teacher->id) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edit Data
            </a>
            <a href="{{ route('admin.guru.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

<!-- Teacher Profile Card -->
<div class="teacher-profile-container">
    <div class="profile-header">
        <div class="profile-photo">
            @if($teacher->profile_photo)
                <img src="{{ asset('storage/' . $teacher->profile_photo) }}"
                     alt="Foto {{ $teacher->name }}"
                     class="teacher-photo-large">
            @else
                <div class="default-avatar-large">
                    <i class="fas fa-user"></i>
                </div>
            @endif
        </div>
        <div class="profile-info">
            <h2 class="teacher-name">{{ $teacher->name }}</h2>
            <p class="teacher-id">{{ $teacher->nomor_induk }}</p>

            <div class="quick-stats">
                <div class="stat-item">
                    <span class="stat-label">Mata Pelajaran</span>
                    <span class="stat-value">{{ $teacher->mata_pelajaran ?? '-' }}</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Status</span>
                    <span class="status-badge {{ $teacher->status_kepegawaian ?? 'default' }}">
                        {{ ucfirst(str_replace('_', ' ', $teacher->status_kepegawaian ?? 'Tidak diketahui')) }}
                    </span>
                </div>
                @if($teacher->wali_kelas)
                <div class="stat-item">
                    <span class="stat-label">Wali Kelas</span>
                    <span class="wali-kelas-badge">{{ $teacher->wali_kelas }}</span>
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
                        <span class="detail-value">{{ $teacher->name }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Nomor Induk</span>
                        <span class="detail-value">{{ $teacher->nomor_induk }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Email</span>
                        <span class="detail-value">
                            <a href="mailto:{{ $teacher->email }}" class="email-link">
                                {{ $teacher->email }}
                            </a>
                        </span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Nomor Telepon</span>
                        <span class="detail-value">
                            @if($teacher->nomor_telepon)
                                <a href="tel:{{ $teacher->nomor_telepon }}" class="phone-link">
                                    {{ $teacher->nomor_telepon }}
                                </a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Jenis Kelamin</span>
                        <span class="detail-value">
                            @if($teacher->jenis_kelamin)
                                <span class="gender-badge {{ $teacher->jenis_kelamin }}">
                                    <i class="fas fa-{{ $teacher->jenis_kelamin === 'laki-laki' ? 'mars' : 'venus' }}"></i>
                                    {{ ucfirst($teacher->jenis_kelamin) }}
                                </span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            <!-- Professional Information -->
            <div class="detail-section">
                <h3>
                    <i class="fas fa-briefcase"></i>
                    Informasi Kepegawaian
                </h3>
                <div class="detail-grid">
                    <div class="detail-item">
                        <span class="detail-label">Status Kepegawaian</span>
                        <span class="detail-value">
                            <span class="status-badge {{ $teacher->status_kepegawaian ?? 'default' }}">
                                {{ ucfirst(str_replace('_', ' ', $teacher->status_kepegawaian ?? 'Tidak diketahui')) }}
                            </span>
                        </span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Mata Pelajaran</span>
                        <span class="detail-value">
                            @if($teacher->mata_pelajaran)
                                <span class="subject-tag">{{ $teacher->mata_pelajaran }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Wali Kelas</span>
                        <span class="detail-value">
                            @if($teacher->wali_kelas)
                                <span class="wali-kelas-badge">{{ $teacher->wali_kelas }}</span>
                            @else
                                <span class="text-muted">Tidak ada</span>
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
                            {{ $teacher->created_at->format('d F Y') }}
                            <small class="text-muted">({{ $teacher->created_at->diffForHumans() }})</small>
                        </span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Terakhir Diperbarui</span>
                        <span class="detail-value">
                            <i class="fas fa-clock"></i>
                            {{ $teacher->updated_at->format('d F Y, H:i') }}
                            <small class="text-muted">({{ $teacher->updated_at->diffForHumans() }})</small>
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
        <a href="{{ route('admin.guru.edit', $teacher->id) }}" class="btn btn-primary">
            <i class="fas fa-edit"></i>
            Edit Data Guru
        </a>
        <button type="button" class="btn btn-danger" onclick="confirmDelete({{ $teacher->id }}, '{{ $teacher->name }}')">
            <i class="fas fa-trash"></i>
            Hapus Guru
        </button>
        <a href="{{ route('admin.guru.index') }}" class="btn btn-outline-secondary">
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
                Konfirmasi Hapus Guru
            </h3>
            <button type="button" class="modal-close" onclick="closeDeleteModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <p>Apakah Anda yakin ingin menghapus data guru <strong id="teacherNameToDelete"></strong>?</p>
            <div class="warning-text">
                <i class="fas fa-exclamation-triangle"></i>
                Tindakan ini tidak dapat dibatalkan! Semua data terkait guru ini akan dihapus secara permanen.
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
.teacher-profile-container {
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

.teacher-photo-large {
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

.teacher-name {
    margin: 0 0 8px 0;
    font-size: 32px;
    font-weight: 700;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
}

.teacher-id {
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

@media (max-width: 768px) {
    .profile-header {
        flex-direction: column;
        text-align: center;
        padding: 30px 20px;
    }

    .teacher-name {
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
}

@media (max-width: 480px) {
    .teacher-photo-large,
    .default-avatar-large {
        width: 100px;
        height: 100px;
    }

    .default-avatar-large {
        font-size: 40px;
    }

    .teacher-name {
        font-size: 20px;
    }
}
</style>

<script>
function confirmDelete(teacherId, teacherName) {
    document.getElementById('teacherNameToDelete').textContent = teacherName;
    document.getElementById('deleteForm').action = `/admin/guru/${teacherId}`;
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
