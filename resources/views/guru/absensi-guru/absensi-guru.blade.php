@extends('layouts.app')

@section('title', 'Absensi Guru')

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
                            <i class="fas fa-user-check me-2"></i>Absensi Guru
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
        <!-- Camera Section -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-gradient bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-camera me-2"></i>Ambil Foto Selfie untuk Absensi
                    </h5>
                </div>
                <div class="card-body p-4">
                    <!-- Camera Container -->
                    <div class="camera-container text-center">
                        <div id="camera-placeholder">
                            <div class="text-center py-5">
                                <i class="fas fa-camera text-primary fs-1 mb-3"></i>
                                <h5 class="text-muted">Ambil Foto Selfie</h5>
                                <p class="text-muted small mb-3">Pastikan wajah Anda terlihat jelas dan berada di area sekolah</p>
                                <button id="start-camera" class="btn btn-primary">
                                    <i class="fas fa-camera me-2"></i>Buka Kamera
                                </button>
                                <div class="mt-3">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Lokasi GPS akan otomatis diambil saat Anda mengambil foto
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Camera Action Buttons (Hidden by default) -->
                        <div id="camera-action-buttons" class="mt-3 d-flex gap-2 justify-content-center" style="display: none;">
                            <button id="capture-photo" class="btn btn-success flex-fill">
                                <i class="fas fa-camera me-2"></i>Ambil Foto
                            </button>
                            <button id="switch-camera-btn" class="btn btn-secondary flex-fill">
                                <i class="fas fa-sync-alt me-2"></i>Ganti Kamera
                            </button>
                            <button id="stop-camera" class="btn btn-danger flex-fill">
                                <i class="fas fa-stop me-2"></i>Tutup Kamera
                            </button>
                        </div>

                        <!-- Preview and Confirm (Hidden by default) -->
                        <div id="photo-preview-section" style="display: none;">
                            <canvas id="photo-canvas" style="max-width: 100%; border-radius: 0.5rem;"></canvas>
                            <div class="mt-3">
                                <p class="mb-2">
                                    <i class="fas fa-map-marker-alt text-success me-2"></i>
                                    <strong>Lokasi:</strong> <span id="location-info">Mengambil lokasi...</span>
                                </p>
                                <div class="d-flex gap-2 justify-content-center">
                                    <button id="confirm-photo" class="btn btn-success flex-fill">
                                        <i class="fas fa-check me-2"></i>Konfirmasi & Absen
                                    </button>
                                    <button id="retake-photo" class="btn btn-warning flex-fill">
                                        <i class="fas fa-redo me-2"></i>Ambil Ulang
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Loading Spinner -->
                        <div id="loading-spinner" style="display: none;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="text-muted mt-2">Memproses...</p>
                        </div>
                    </div>

                    <!-- Status Info -->
                    <div class="alert alert-info mt-3" role="alert">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Catatan:</strong> Absensi hanya dapat dilakukan di area sekolah dengan radius maksimal 100 meter dari koordinat sekolah.
                    </div>
                </div>
            </div>
        </div>

        <!-- History Section -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-gradient bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-history me-2"></i>Riwayat Absensi Minggu Ini
                    </h5>
                </div>
                <div class="card-body p-3">
                    <!-- Current Week Info -->
                    <div class="mb-3 text-center">
                        <h6 class="text-muted mb-2">
                            <i class="fas fa-calendar-week me-1"></i>
                            Minggu ke-<span id="week-number">{{ date('W') }}</span>
                        </h6>
                        <small class="text-muted" id="week-range"></small>
                    </div>

                    <!-- Weekly Calendar -->
                    <div id="weekly-calendar" class="weekly-calendar">
                        <!-- Will be populated by JavaScript -->
                    </div>

                    <!-- Statistics -->
                    <div class="row g-2 mt-3">
                        <div class="col-6">
                            <div class="card bg-success text-white text-center">
                                <div class="card-body p-2">
                                    <h4 class="mb-0" id="hadir-count">0</h4>
                                    <small>Hadir</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card bg-warning text-white text-center">
                                <div class="card-body p-2">
                                    <h4 class="mb-0" id="terlambat-count">0</h4>
                                    <small>Terlambat</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card bg-danger text-white text-center">
                                <div class="card-body p-2">
                                    <h4 class="mb-0" id="alpha-count">0</h4>
                                    <small>Alpha</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card bg-info text-white text-center">
                                <div class="card-body p-2">
                                    <h4 class="mb-0" id="izin-count">0</h4>
                                    <small>Izin</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
// School coordinates from config
const SCHOOL_COORDINATES = {
    latitude: {{ config('school.latitude') }},
    longitude: {{ config('school.longitude') }},
    radius: {{ config('school.attendance_radius') }}
};

let videoStream = null;
let currentCamera = 'user'; // 'user' for front camera, 'environment' for back camera
let capturedPhoto = null;
let currentLocation = null;

$(document).ready(function() {
    // Update time every second
    setInterval(updateTime, 1000);

    // Load weekly attendance
    loadWeeklyAttendance();

    // Button handlers
    $('#start-camera').click(startCamera);
    $('#stop-camera').click(stopCamera);
    $('#switch-camera-btn').click(switchCamera);
    $('#capture-photo').click(capturePhoto);
    $('#retake-photo').click(retakePhoto);
    $('#confirm-photo').click(confirmAttendance);
});

function updateTime() {
    const now = new Date();
    const timeString = now.toLocaleTimeString('id-ID', {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    });
    $('#current-time').text(timeString);
}

async function startCamera() {
    try {
        $('#loading-spinner').show();

        // Request camera permission
        const constraints = {
            video: {
                facingMode: currentCamera,
                width: { ideal: 1280 },
                height: { ideal: 720 }
            }
        };

        videoStream = await navigator.mediaDevices.getUserMedia(constraints);

        // Create video element
        const videoElement = $('<video id="camera-video" autoplay playsinline></video>');
        $('#camera-placeholder').html(videoElement);

        document.getElementById('camera-video').srcObject = videoStream;

        $('#loading-spinner').hide();
        $('#camera-action-buttons').show();

    } catch (error) {
        console.error('Error accessing camera:', error);
        $('#loading-spinner').hide();
        showError('Gagal mengakses kamera. Pastikan Anda memberikan izin akses kamera.');
    }
}

function stopCamera() {
    if (videoStream) {
        videoStream.getTracks().forEach(track => track.stop());
        videoStream = null;
    }

    $('#camera-placeholder').html(`
        <div class="text-center py-5">
            <i class="fas fa-camera text-primary fs-1 mb-3"></i>
            <h5 class="text-muted">Ambil Foto Selfie</h5>
            <p class="text-muted small mb-3">Pastikan wajah Anda terlihat jelas dan berada di area sekolah</p>
            <button id="start-camera" class="btn btn-primary">
                <i class="fas fa-camera me-2"></i>Buka Kamera
            </button>
            <div class="mt-3">
                <small class="text-muted">
                    <i class="fas fa-info-circle me-1"></i>
                    Lokasi GPS akan otomatis diambil saat Anda mengambil foto
                </small>
            </div>
        </div>
    `);

    $('#camera-action-buttons').hide();
    $('#photo-preview-section').hide();
    $('#start-camera').click(startCamera);
}

async function switchCamera() {
    currentCamera = currentCamera === 'user' ? 'environment' : 'user';
    stopCamera();
    await startCamera();
}

async function capturePhoto() {
    $('#loading-spinner').show();

    // Get current location
    try {
        currentLocation = await getCurrentLocation();

        // Verify location is within school radius
        const distance = calculateDistance(
            currentLocation.latitude,
            currentLocation.longitude,
            SCHOOL_COORDINATES.latitude,
            SCHOOL_COORDINATES.longitude
        );

        if (distance > SCHOOL_COORDINATES.radius) {
            $('#loading-spinner').hide();
            showLocationError(distance);
            return;
        }

        // Capture photo from video
        const video = document.getElementById('camera-video');
        const canvas = document.getElementById('photo-canvas');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;

        const ctx = canvas.getContext('2d');
        ctx.drawImage(video, 0, 0);

        // Convert to blob
        canvas.toBlob((blob) => {
            capturedPhoto = blob;
        }, 'image/jpeg', 0.95);

        // Stop camera
        if (videoStream) {
            videoStream.getTracks().forEach(track => track.stop());
        }

        // Show preview
        $('#camera-placeholder').hide();
        $('#camera-action-buttons').hide();
        $('#photo-preview-section').show();
        $('#loading-spinner').hide();

        // Display location info
        $('#location-info').html(`
            <span class="text-success">✓ Di dalam area sekolah</span><br>
            <small class="text-muted">Jarak: ${distance.toFixed(2)} meter dari sekolah</small>
        `);

    } catch (error) {
        console.error('Error capturing photo:', error);
        $('#loading-spinner').hide();
        showError('Gagal mengambil lokasi. Pastikan GPS aktif dan izin lokasi diberikan.');
    }
}

function retakePhoto() {
    $('#photo-preview-section').hide();
    $('#camera-placeholder').show();
    capturedPhoto = null;
    currentLocation = null;
    startCamera();
}

async function confirmAttendance() {
    if (!capturedPhoto || !currentLocation) {
        showError('Data tidak lengkap. Silakan ambil foto ulang.');
        return;
    }

    $('#loading-spinner').show();
    $('#confirm-photo').prop('disabled', true);

    // Prepare form data
    const formData = new FormData();
    formData.append('photo', capturedPhoto, 'selfie.jpg');
    formData.append('latitude', currentLocation.latitude);
    formData.append('longitude', currentLocation.longitude);
    formData.append('accuracy', currentLocation.accuracy);

    try {
        $.ajax({
            url: '{{ route("guru.absensi-guru.store") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#loading-spinner').hide();
                $('#confirm-photo').prop('disabled', false);

                if (response.success) {
                    showSuccessModal(response.data);
                    loadWeeklyAttendance();
                    stopCamera();
                } else {
                    showError(response.message);
                }
            },
            error: function(xhr) {
                $('#loading-spinner').hide();
                $('#confirm-photo').prop('disabled', false);

                let message = 'Terjadi kesalahan saat menyimpan absensi';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                showError(message);
            }
        });
    } catch (error) {
        console.error('Error:', error);
        $('#loading-spinner').hide();
        $('#confirm-photo').prop('disabled', false);
        showError('Terjadi kesalahan. Silakan coba lagi.');
    }
}

function getCurrentLocation() {
    return new Promise((resolve, reject) => {
        if (!navigator.geolocation) {
            reject(new Error('Geolocation tidak didukung oleh browser'));
            return;
        }

        navigator.geolocation.getCurrentPosition(
            (position) => {
                resolve({
                    latitude: position.coords.latitude,
                    longitude: position.coords.longitude,
                    accuracy: position.coords.accuracy
                });
            },
            (error) => {
                reject(error);
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            }
        );
    });
}

function calculateDistance(lat1, lon1, lat2, lon2) {
    const R = 6371000; // Earth radius in meters
    const φ1 = lat1 * Math.PI / 180;
    const φ2 = lat2 * Math.PI / 180;
    const Δφ = (lat2 - lat1) * Math.PI / 180;
    const Δλ = (lon2 - lon1) * Math.PI / 180;

    const a = Math.sin(Δφ/2) * Math.sin(Δφ/2) +
              Math.cos(φ1) * Math.cos(φ2) *
              Math.sin(Δλ/2) * Math.sin(Δλ/2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));

    return R * c; // Distance in meters
}

function showLocationError(distance) {
    const modal = $(`
        <div class="modal fade" id="locationErrorModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-danger text-white border-0">
                        <h5 class="modal-title">
                            <i class="fas fa-map-marker-alt me-2"></i>Lokasi Di Luar Area Sekolah
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center p-4">
                        <i class="fas fa-exclamation-triangle text-danger fs-1 mb-3"></i>
                        <h5>Anda Berada Di Luar Area Sekolah</h5>
                        <p class="text-muted mb-3">
                            Jarak Anda dari sekolah: <strong>${distance.toFixed(2)} meter</strong><br>
                            Maksimal jarak yang diperbolehkan: <strong>${SCHOOL_COORDINATES.radius} meter</strong>
                        </p>
                        <div class="alert alert-warning">
                            <i class="fas fa-info-circle me-2"></i>
                            Silakan mendekati area sekolah dan ambil foto ulang.
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                            <i class="fas fa-redo me-2"></i>Coba Lagi
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `);

    $('body').append(modal);
    const modalElement = new bootstrap.Modal(document.getElementById('locationErrorModal'));
    modalElement.show();

    $('#locationErrorModal').on('hidden.bs.modal', function() {
        $(this).remove();
    });
}

function showSuccessModal(data) {
    const statusClass = data.status === 'hadir' ? 'success' : 'warning';
    const statusIcon = data.status === 'hadir' ? 'check-circle' : 'clock';
    const statusText = data.status === 'hadir' ? 'Hadir' : 'Terlambat';

    const modal = $(`
        <div class="modal fade" id="successModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-${statusClass} text-white border-0">
                        <h5 class="modal-title">
                            <i class="fas fa-${statusIcon} me-2"></i>Absensi Berhasil
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center p-4">
                        <i class="fas fa-${statusIcon} text-${statusClass} fs-1 mb-3"></i>
                        <h5>${statusText}</h5>
                        <p class="text-muted mb-3">
                            Waktu: <strong>${data.waktu_absen}</strong><br>
                            Status: <strong>${statusText}</strong>
                        </p>
                        ${data.catatan ? `<div class="alert alert-info"><small>${data.catatan}</small></div>` : ''}
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                            <i class="fas fa-check me-2"></i>Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `);

    $('body').append(modal);
    const modalElement = new bootstrap.Modal(document.getElementById('successModal'));
    modalElement.show();

    $('#successModal').on('hidden.bs.modal', function() {
        $(this).remove();
    });
}

function showError(message) {
    const modal = $(`
        <div class="modal fade" id="errorModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-danger text-white border-0">
                        <h5 class="modal-title">
                            <i class="fas fa-exclamation-circle me-2"></i>Error
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center p-4">
                        <i class="fas fa-times-circle text-danger fs-1 mb-3"></i>
                        <p class="mb-0">${message}</p>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    `);

    $('body').append(modal);
    const modalElement = new bootstrap.Modal(document.getElementById('errorModal'));
    modalElement.show();

    $('#errorModal').on('hidden.bs.modal', function() {
        $(this).remove();
    });
}

function loadWeeklyAttendance() {
    $.ajax({
        url: '{{ route("guru.absensi-guru.weekly") }}',
        type: 'GET',
        success: function(response) {
            if (response.success) {
                renderWeeklyCalendar(response.data);
                updateStatistics(response.statistics);
                $('#week-range').text(response.week_range);
            }
        },
        error: function(xhr) {
            console.error('Error loading weekly attendance:', xhr);
        }
    });
}

function renderWeeklyCalendar(attendanceData) {
    const days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    const dayShort = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];

    let calendarHtml = '';

    days.forEach((day, index) => {
        const data = attendanceData[index];
        let statusClass = 'secondary';
        let statusIcon = 'question';
        let statusText = 'Belum Absen';

        if (data && data.status) {
            switch(data.status) {
                case 'hadir':
                    statusClass = 'success';
                    statusIcon = 'check-circle';
                    statusText = 'Hadir';
                    break;
                case 'terlambat':
                    statusClass = 'warning';
                    statusIcon = 'clock';
                    statusText = 'Terlambat';
                    break;
                case 'izin':
                    statusClass = 'info';
                    statusIcon = 'file-medical';
                    statusText = 'Izin';
                    break;
                case 'alpha':
                    statusClass = 'danger';
                    statusIcon = 'times-circle';
                    statusText = 'Alpha';
                    break;
            }
        }

        calendarHtml += `
            <div class="day-card card mb-2 border-${statusClass}">
                <div class="card-body p-2">
                    <div class="d-flex align-items-center">
                        <div class="me-2">
                            <i class="fas fa-${statusIcon} text-${statusClass} fs-4"></i>
                        </div>
                        <div class="flex-grow-1">
                            <strong>${day}</strong><br>
                            <small class="text-muted">${data ? data.tanggal : '-'}</small>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-${statusClass}">${statusText}</span>
                            ${data && data.waktu ? `<br><small class="text-muted">${data.waktu}</small>` : ''}
                        </div>
                    </div>
                </div>
            </div>
        `;
    });

    $('#weekly-calendar').html(calendarHtml);
}

function updateStatistics(stats) {
    $('#hadir-count').text(stats.hadir || 0);
    $('#terlambat-count').text(stats.terlambat || 0);
    $('#alpha-count').text(stats.alpha || 0);
    $('#izin-count').text(stats.izin || 0);
}
</script>

<style>
.camera-container {
    position: relative;
}

#camera-placeholder {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 400px;
    border: 2px dashed #0d6efd;
    border-radius: 0.5rem;
    background: #f8f9fa;
}

#camera-video {
    width: 100%;
    max-width: 640px;
    height: auto;
    border-radius: 0.5rem;
    background: #000;
}

#photo-canvas {
    max-width: 100%;
    border: 3px solid #198754;
    border-radius: 0.5rem;
}

.weekly-calendar .day-card {
    transition: transform 0.2s ease-in-out;
}

.weekly-calendar .day-card:hover {
    transform: translateX(5px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.bg-gradient {
    background: linear-gradient(135deg, var(--bs-primary) 0%, #5a67d8 100%) !important;
}

.card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

@media (max-width: 768px) {
    #camera-placeholder {
        min-height: 300px;
    }

    #camera-video {
        max-width: 100%;
    }
}
</style>
@endpush
