@extends('layouts.app')

@section('title', 'Absensi Siswa')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="bg-white rounded-3 shadow-sm p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-2 text-primary">
                            <i class="fas fa-user-check me-2"></i>Absensi Siswa
                        </h1>
                        <p class="text-muted mb-0">
                            <i class="fas fa-calendar-alt me-1"></i>
                            <span id="current-date">{{ date('l, d F Y') }}</span>
                        </p>
                    </div>
                    <div class="text-end">
                        <h2 class="mb-0 text-primary" id="current-time">{{ date('H:i:s') }}</h2>
                        <small class="text-muted">Waktu Sekarang</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Form Section -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-gradient bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-file-medical me-2"></i>Laporan Absensi
                    </h5>
                </div>
                <div class="card-body p-4">
                    @if($todayAttendance)
                    <!-- Already Submitted Today -->
                    <div class="card border-success shadow-sm">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-start">
                                <div class="me-3">
                                    <div class="bg-success bg-opacity-10 rounded-circle p-3">
                                        <i class="fas fa-check-circle text-success" style="font-size: 2.5rem;"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h4 class="text-success mb-3">
                                        <i class="fas fa-calendar-check me-2"></i>Hari ini Anda sudah melaporkan absensi
                                    </h4>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="p-3 bg-light rounded">
                                                <small class="text-muted d-block mb-1">Status</small>
                                                <div>
                                                    @if($todayAttendance->status === 'izin')
                                                        <span class="badge bg-warning">Izin</span>
                                                    @elseif($todayAttendance->status === 'sakit')
                                                        <span class="badge bg-info">Sakit</span>
                                                    @else
                                                        <span class="badge bg-secondary">{{ ucfirst($todayAttendance->status) }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="p-3 bg-light rounded">
                                                <small class="text-muted d-block mb-1">Waktu Laporan</small>
                                                <strong>{{ $todayAttendance->time ? $todayAttendance->time->format('H:i:s') : '-' }}</strong>
                                            </div>
                                        </div>
                                        @if($todayAttendance->notes)
                                        <div class="col-12">
                                            <div class="p-3 bg-light rounded">
                                                <small class="text-muted d-block mb-1">Keterangan</small>
                                                <p class="mb-0">{{ $todayAttendance->notes }}</p>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @else
                    <!-- Absence Type Selection -->
                    <ul class="nav nav-pills mb-4" id="absence-type-tabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="izin-tab" data-bs-toggle="pill" data-bs-target="#izin-panel" type="button" role="tab">
                                <i class="fas fa-file-medical me-1"></i>Izin
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="sakit-tab" data-bs-toggle="pill" data-bs-target="#sakit-panel" type="button" role="tab">
                                <i class="fas fa-notes-medical me-1"></i>Sakit
                            </button>
                        </li>
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content" id="absence-type-content">
                        <!-- Izin Tab -->
                        <div class="tab-pane fade show active" id="izin-panel" role="tabpanel">
                            <div class="text-center mb-4">
                                <i class="fas fa-file-medical text-warning fs-1 mb-3"></i>
                                <h5>Form Izin</h5>
                                <p class="text-muted">Isi form di bawah untuk mengajukan izin tidak masuk</p>
                            </div>

                            <form id="izin-form">
                                <div class="mb-3">
                                    <label for="izin-keterangan" class="form-label">Alasan Izin <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="izin-keterangan" name="keterangan" rows="4"
                                        placeholder="Contoh: Mengikuti acara keluarga, mengurus keperluan penting, dll." required></textarea>
                                    <div class="form-text">Minimal 10 karakter. Jelaskan alasan izin Anda dengan lengkap.</div>
                                </div>

                                <div class="mb-3">
                                    <label for="izin-dokumen" class="form-label">Surat Izin (Opsional)</label>
                                    <input type="file" class="form-control" id="izin-dokumen" name="dokumen" accept=".jpg,.jpeg,.png,.pdf">
                                    <div class="form-text">Format: JPG, PNG, atau PDF. Maksimal 2MB.</div>
                                </div>

                                <div class="alert alert-warning" role="alert">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>Perhatian:</strong> Pastikan data yang Anda masukkan sudah benar. Setelah mengirim, Anda tidak dapat mengubah data lagi untuk hari ini.
                                </div>

                                <button type="submit" class="btn btn-warning w-100" id="submit-izin-btn">
                                    <i class="fas fa-paper-plane me-2"></i>Kirim Laporan Izin
                                </button>
                            </form>
                        </div>

                        <!-- Sakit Tab -->
                        <div class="tab-pane fade" id="sakit-panel" role="tabpanel">
                            <div class="text-center mb-4">
                                <i class="fas fa-notes-medical text-info fs-1 mb-3"></i>
                                <h5>Form Sakit</h5>
                                <p class="text-muted">Isi form di bawah untuk melaporkan sakit</p>
                            </div>

                            <form id="sakit-form">
                                <div class="mb-3">
                                    <label for="sakit-keterangan" class="form-label">Keluhan/Keterangan <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="sakit-keterangan" name="keterangan" rows="4"
                                        placeholder="Contoh: Demam tinggi, sakit kepala, flu, dll." required></textarea>
                                    <div class="form-text">Minimal 10 karakter. Jelaskan kondisi kesehatan Anda.</div>
                                </div>

                                <div class="mb-3">
                                    <label for="sakit-dokumen" class="form-label">Surat Keterangan Sakit (Opsional)</label>
                                    <input type="file" class="form-control" id="sakit-dokumen" name="dokumen" accept=".jpg,.jpeg,.png,.pdf">
                                    <div class="form-text">Upload surat dokter jika ada. Format: JPG, PNG, atau PDF. Maksimal 2MB.</div>
                                </div>

                                <div class="alert alert-info" role="alert">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Informasi:</strong> Surat keterangan sakit dari dokter sangat dianjurkan untuk kelengkapan data absensi.
                                </div>

                                <button type="submit" class="btn btn-info w-100" id="submit-sakit-btn">
                                    <i class="fas fa-paper-plane me-2"></i>Kirim Laporan Sakit
                                </button>
                            </form>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Sidebar - Weekly Summary -->
        <div class="col-lg-4">
            <!-- Weekly Attendance Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-gradient bg-info text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-calendar-week me-2"></i>Absensi Minggu Ini
                    </h6>
                </div>
                <div class="card-body p-3">
                    <div id="weekly-summary">
                        <div class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-gradient bg-success text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-pie me-2"></i>Statistik Absensi
                    </h6>
                </div>
                <div class="card-body p-3">
                    <div id="statistics-summary">
                        <div class="text-center py-4">
                            <div class="spinner-border text-success" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .nav-pills .nav-link {
        border-radius: 0.5rem;
        padding: 0.75rem 1.5rem;
        margin-right: 0.5rem;
        transition: all 0.3s ease;
    }

    .nav-pills .nav-link:hover {
        background-color: rgba(13, 110, 253, 0.1);
    }

    .nav-pills .nav-link.active {
        background-color: var(--bs-primary);
    }

    .day-box {
        text-align: center;
        padding: 0.75rem;
        border-radius: 0.5rem;
        transition: all 0.3s ease;
    }

    .day-box:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    .day-box.present {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
    }

    .day-box.izin {
        background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
        color: white;
    }

    .day-box.sakit {
        background: linear-gradient(135deg, #0dcaf0 0%, #0d6efd 100%);
        color: white;
    }

    .day-box.absent {
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        color: white;
    }

    .day-box.no-data {
        background-color: #f8f9fa;
        border: 2px dashed #dee2e6;
        color: #6c757d;
    }

    .stat-item {
        padding: 0.75rem;
        border-radius: 0.5rem;
        text-align: center;
        margin-bottom: 0.75rem;
    }

    .stat-item i {
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
    }

    .stat-item.hadir {
        background-color: rgba(40, 167, 69, 0.1);
        border-left: 4px solid #28a745;
    }

    .stat-item.izin {
        background-color: rgba(255, 193, 7, 0.1);
        border-left: 4px solid #ffc107;
    }

    .stat-item.sakit {
        background-color: rgba(13, 202, 240, 0.1);
        border-left: 4px solid #0dcaf0;
    }

    .stat-item.alpha {
        background-color: rgba(220, 53, 69, 0.1);
        border-left: 4px solid #dc3545;
    }
</style>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Update time every second
        updateTime();
        setInterval(updateTime, 1000);

        // Load weekly attendance
        loadWeeklyAttendance();

        // Form submissions
        const izinForm = document.getElementById('izin-form');
        const sakitForm = document.getElementById('sakit-form');

        if (izinForm) {
            izinForm.addEventListener('submit', function(e) {
                e.preventDefault();
                submitAbsence('izin', this);
            });
        }

        if (sakitForm) {
            sakitForm.addEventListener('submit', function(e) {
                e.preventDefault();
                submitAbsence('sakit', this);
            });
        }
    });

    function updateTime() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        document.getElementById('current-time').textContent = `${hours}:${minutes}:${seconds}`;
    }

    function loadWeeklyAttendance() {
        fetch('{{ route('siswa.absensi.weekly') }}', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Weekly data:', data);
            if (data.success) {
                displayWeeklyAttendance(data.data, data.week_range);
                displayStatistics(data.statistics);
            } else {
                throw new Error(data.message || 'Gagal memuat data');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('weekly-summary').innerHTML = `
                <div class="alert alert-danger mb-0 small">
                    <i class="fas fa-exclamation-triangle me-2"></i>Gagal memuat data: ${error.message}
                </div>
            `;
            document.getElementById('statistics-summary').innerHTML = `
                <div class="alert alert-danger mb-0 small">
                    <i class="fas fa-exclamation-triangle me-2"></i>Gagal memuat statistik
                </div>
            `;
        });
    }

    function displayWeeklyAttendance(weekData, weekRange) {
        let html = `
            <div class="mb-3">
                <small class="text-muted">
                    <i class="fas fa-calendar-alt me-1"></i>${weekRange}
                </small>
            </div>
        `;

        weekData.forEach(day => {
            let statusClass = 'no-data';
            let statusIcon = 'calendar';

            if (day.status === 'hadir') {
                statusClass = 'present';
                statusIcon = 'check';
            } else if (day.status === 'izin') {
                statusClass = 'izin';
                statusIcon = 'file-medical';
            } else if (day.status === 'sakit') {
                statusClass = 'sakit';
                statusIcon = 'notes-medical';
            } else if (day.status === 'alpha') {
                statusClass = 'absent';
                statusIcon = 'times';
            }

            html += `
                <div class="day-box ${statusClass} mb-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="d-block" style="opacity: 0.9;">${day.hari}</small>
                            <strong>${day.tanggal}</strong>
                        </div>
                        <i class="fas fa-${statusIcon}"></i>
                    </div>
                    ${day.keterangan ? `<small class="d-block mt-1" style="opacity: 0.8;">${day.keterangan}</small>` : ''}
                </div>
            `;
        });

        document.getElementById('weekly-summary').innerHTML = html;
    }

    function displayStatistics(stats) {
        const html = `
            <div class="stat-item hadir">
                <i class="fas fa-check-circle text-success"></i>
                <div><strong class="h4 mb-0">${stats.hadir}</strong></div>
                <small>Hadir</small>
            </div>
            <div class="stat-item izin">
                <i class="fas fa-file-medical text-warning"></i>
                <div><strong class="h4 mb-0">${stats.izin}</strong></div>
                <small>Izin</small>
            </div>
            <div class="stat-item sakit">
                <i class="fas fa-notes-medical text-info"></i>
                <div><strong class="h4 mb-0">${stats.sakit}</strong></div>
                <small>Sakit</small>
            </div>
            <div class="stat-item alpha">
                <i class="fas fa-times-circle text-danger"></i>
                <div><strong class="h4 mb-0">${stats.alpha}</strong></div>
                <small>Alpha</small>
            </div>
        `;
        document.getElementById('statistics-summary').innerHTML = html;
    }

    function submitAbsence(type, form) {
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalBtnText = submitBtn.innerHTML;

        // Validate form
        const keteranganInput = form.querySelector('textarea[name="keterangan"]');
        if (!keteranganInput || keteranganInput.value.trim().length < 10) {
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian!',
                text: 'Keterangan harus diisi minimal 10 karakter',
                confirmButtonText: 'OK'
            });
            return;
        }

        // Disable button and show loading
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Mengirim...';

        const formData = new FormData(form);
        formData.append('type', type);

        console.log('Submitting form:', {
            type: type,
            keterangan: keteranganInput.value,
            has_dokumen: form.querySelector('input[type="file"]').files.length > 0
        });

        fetch('{{ route('siswa.absensi.store') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => {
            console.log('Submit response status:', response.status);
            if (!response.ok) {
                return response.json().then(err => {
                    throw new Error(err.message || `HTTP error! status: ${response.status}`);
                });
            }
            return response.json();
        })
        .then(data => {
            console.log('Submit response data:', data);
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.data.message || data.message,
                    confirmButtonText: 'OK',
                    timer: 3000
                }).then(() => {
                    window.location.reload();
                });
            } else {
                throw new Error(data.message || 'Terjadi kesalahan');
            }
        })
        .catch(error => {
            console.error('Submit error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                html: `<p>${error.message || 'Terjadi kesalahan saat mengirim laporan'}</p>
                       <p class="text-muted small mt-2">Silakan cek browser console untuk detail error</p>`,
                confirmButtonText: 'OK'
            });

            // Re-enable button
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
        });
    }
</script>
@endpush
