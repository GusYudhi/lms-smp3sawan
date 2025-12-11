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
                        <i class="fas fa-user-check me-2"></i>Absensi Guru
                    </h5>
                </div>
                <div class="card-body p-4">
                    <!-- Attendance Type Selection -->
                    <ul class="nav nav-pills mb-4" id="attendance-type-tabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="hadir-tab" data-bs-toggle="pill" data-bs-target="#hadir-panel" type="button" role="tab">
                                <i class="fas fa-camera me-1"></i>Hadir (Foto)
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="izin-tab" data-bs-toggle="pill" data-bs-target="#izin-panel" type="button" role="tab">
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
                    <div class="tab-content" id="attendance-type-content">
                        <!-- Hadir Tab (Camera) -->
                        <div class="tab-pane fade show active" id="hadir-panel" role="tabpanel">

                    @if($todayAttendance)
                    <!-- Already Attended Today -->
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
                                        <i class="fas fa-calendar-check me-2"></i>Hari ini Anda sudah melakukan absensi
                                    </h4>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="p-3 bg-light rounded">
                                                <small class="text-muted d-block mb-1">Status Absensi</small>
                                                <div>{!! $todayAttendance->getStatusBadge() !!}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="p-3 bg-light rounded">
                                                <small class="text-muted d-block mb-1">Waktu Absensi</small>
                                                <strong>{{ $todayAttendance->waktu_absen ? $todayAttendance->waktu_absen->format('H:i:s') : '-' }}</strong>
                                            </div>
                                        </div>
                                        @if($todayAttendance->keterangan)
                                        <div class="col-12">
                                            <div class="p-3 bg-light rounded">
                                                <small class="text-muted d-block mb-1">Keterangan</small>
                                                <p class="mb-0">{{ $todayAttendance->keterangan }}</p>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @else
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
                        <strong>Catatan:</strong> Absensi hadir hanya dapat dilakukan di area sekolah dengan radius maksimal 500 meter dari koordinat sekolah.
                    </div>
                    @endif
                        </div>

                        <!-- Izin Tab -->
                        <div class="tab-pane fade" id="izin-panel" role="tabpanel">
                            @if($todayAttendance)
                            <!-- Already Attended Today -->
                            <div class="card border-info shadow-sm">
                                <div class="card-body p-4 text-center">
                                    <div class="mb-3">
                                        <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex p-3">
                                            <i class="fas fa-info-circle text-info" style="font-size: 2.5rem;"></i>
                                        </div>
                                    </div>
                                    <h5 class="mb-3">Anda sudah melakukan absensi hari ini</h5>
                                    <p class="text-muted mb-3">Form izin tidak dapat diakses karena Anda sudah absen dengan status:</p>
                                    <div>{!! $todayAttendance->getStatusBadge() !!}</div>
                                </div>
                            </div>
                            @else
                            <div class="text-center mb-4">
                                <i class="fas fa-file-medical text-info fs-1 mb-3"></i>
                                <h5>Form Izin</h5>
                                <p class="text-muted">Isi form di bawah untuk mengajukan izin tidak masuk</p>
                            </div>

                            <form id="izin-form">
                                <div class="mb-3">
                                    <label for="izin-alasan" class="form-label">Alasan Izin <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="izin-alasan" name="alasan" rows="4" required placeholder="Tuliskan alasan izin Anda..."></textarea>
                                </div>

                                <div class="mb-3">
                                    <label for="izin-dokumen" class="form-label">Lampiran Dokumen (Opsional)</label>
                                    <input type="file" class="form-control" id="izin-dokumen" name="dokumen" accept="image/*,.pdf">
                                    <small class="text-muted">Format: JPG, PNG, PDF (Max: 2MB)</small>
                                </div>

                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>Perhatian:</strong> Pastikan alasan izin yang Anda berikan sesuai dengan kondisi sebenarnya.
                                </div>

                                <button type="submit" class="btn btn-info w-100" id="submit-izin-btn">
                                    <i class="fas fa-paper-plane me-2"></i>Kirim Pengajuan Izin
                                </button>
                            </form>
                            @endif
                        </div>

                        <!-- Sakit Tab -->
                        <div class="tab-pane fade" id="sakit-panel" role="tabpanel">
                            @if($todayAttendance)
                            <!-- Already Attended Today -->
                            <div class="card border-info shadow-sm">
                                <div class="card-body p-4 text-center">
                                    <div class="mb-3">
                                        <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex p-3">
                                            <i class="fas fa-info-circle text-info" style="font-size: 2.5rem;"></i>
                                        </div>
                                    </div>
                                    <h5 class="mb-3">Anda sudah melakukan absensi hari ini</h5>
                                    <p class="text-muted mb-3">Form sakit tidak dapat diakses karena Anda sudah absen dengan status:</p>
                                    <div>{!! $todayAttendance->getStatusBadge() !!}</div>
                                </div>
                            </div>
                            @else
                            <div class="text-center mb-4">
                                <i class="fas fa-notes-medical text-danger fs-1 mb-3"></i>
                                <h5>Form Sakit</h5>
                                <p class="text-muted">Isi form di bawah untuk melaporkan kondisi sakit</p>
                            </div>

                            <form id="sakit-form">
                                <div class="mb-3">
                                    <label for="sakit-keterangan" class="form-label">Keterangan Sakit <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="sakit-keterangan" name="keterangan" rows="4" required placeholder="Jelaskan kondisi sakit Anda..."></textarea>
                                </div>

                                <div class="mb-3">
                                    <label for="sakit-surat-dokter" class="form-label">Surat Dokter (Opsional)</label>
                                    <input type="file" class="form-control" id="sakit-surat-dokter" name="surat_dokter" accept="image/*,.pdf">
                                    <small class="text-muted">Format: JPG, PNG, PDF (Max: 2MB)</small>
                                </div>

                                <div class="alert alert-danger">
                                    <i class="fas fa-heartbeat me-2"></i>
                                    <strong>Informasi:</strong> Jika sakit lebih dari 3 hari, harap melampirkan surat dokter.
                                </div>

                                <button type="submit" class="btn btn-danger w-100" id="submit-sakit-btn">
                                    <i class="fas fa-paper-plane me-2"></i>Kirim Laporan Sakit
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Sidebar - Calendar Attendance -->
        <div class="col-lg-4">
            <!-- Calendar Attendance Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-gradient bg-success text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">
                            <i class="fas fa-calendar-alt me-2"></i>Riwayat Absensi
                        </h6>
                        <button class="btn btn-sm btn-light" id="btn-current-month" title="Kembali ke bulan ini">
                            <i class="fas fa-redo"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body p-3">
                    <!-- Month Navigation -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <button class="btn btn-sm btn-outline-secondary" id="btn-prev-month">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <h6 class="mb-0" id="calendar-month-year">{{ date('F Y') }}</h6>
                        <button class="btn btn-sm btn-outline-secondary" id="btn-next-month">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>

                    <!-- Calendar Grid -->
                    <div id="calendar-container">
                        <div class="text-center py-4">
                            <div class="spinner-border text-success spinner-border-sm" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>

                    <!-- Legend -->
                    <div class="mt-3 pt-3 border-top">
                        <small class="text-muted d-block mb-2"><strong>Keterangan:</strong></small>
                        <div class="row g-2">
                            <div class="col-6">
                                <div class="d-flex align-items-center p-2 rounded" style="background-color: #28a745;">
                                    <small class="text-white fw-semibold">Hadir</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex align-items-center p-2 rounded" style="background-color: #6c757d;">
                                    <small class="text-white fw-semibold">Terlambat</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex align-items-center p-2 rounded" style="background-color: #0dcaf0;">
                                    <small class="text-dark fw-semibold">Izin</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex align-items-center p-2 rounded" style="background-color: #ffc107;">
                                    <small class="text-dark fw-semibold">Sakit</small>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="d-flex align-items-center p-2 rounded" style="background-color: #dc3545;">
                                    <small class="text-white fw-semibold">Alpha</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Monthly Statistics -->
                    <div class="mt-3 pt-3 border-top" id="monthly-statistics">
                        <div class="text-center py-2">
                            <div class="spinner-border text-success spinner-border-sm" role="status">
                                <span class="visually-hidden">Loading...</span>
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
// School configuration - will be loaded from API
let schoolConfig = {
    latitude: null,
    longitude: null,
    radius: 500,
    name: ''
};

let videoStream = null;
let currentCamera = 'user'; // 'user' for front camera, 'environment' for back camera
let capturedPhoto = null;
let currentLocation = null;
let currentMonth = new Date().getMonth();
let currentYear = new Date().getFullYear();

$(document).ready(function() {
    // Load school location from database
    loadSchoolLocation();

    // Update time every second
    setInterval(updateTime, 1000);

    // Load monthly calendar
    loadMonthlyCalendar(currentYear, currentMonth);

    // Calendar navigation
    $('#btn-prev-month').click(function() {
        currentMonth--;
        if (currentMonth < 0) {
            currentMonth = 11;
            currentYear--;
        }
        loadMonthlyCalendar(currentYear, currentMonth);
    });

    $('#btn-next-month').click(function() {
        currentMonth++;
        if (currentMonth > 11) {
            currentMonth = 0;
            currentYear++;
        }
        loadMonthlyCalendar(currentYear, currentMonth);
    });

    $('#btn-current-month').click(function() {
        currentMonth = new Date().getMonth();
        currentYear = new Date().getFullYear();
        loadMonthlyCalendar(currentYear, currentMonth);
    });

    // Button handlers
    $('#start-camera').click(startCamera);
    $('#stop-camera').click(stopCamera);
    $('#switch-camera-btn').click(switchCamera);
    $('#capture-photo').click(capturePhoto);
    $('#retake-photo').click(retakePhoto);
    $('#confirm-photo').click(confirmAttendance);

    // Form handlers
    $('#izin-form').submit(submitIzinForm);
    $('#sakit-form').submit(submitSakitForm);
});

function loadSchoolLocation() {
    $.ajax({
        url: '{{ route("guru.absensi-guru.school-location") }}',
        type: 'GET',
        success: function(response) {
            if (response.success) {
                schoolConfig.latitude = response.data.latitude;
                schoolConfig.longitude = response.data.longitude;
                schoolConfig.radius = response.data.radius;
                schoolConfig.name = response.data.school_name;
                console.log('School location loaded:', schoolConfig);
            }
        },
        error: function(xhr) {
            console.error('Failed to load school location:', xhr);
            showError('Gagal memuat koordinat sekolah. Hubungi administrator.');
        }
    });
}

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

        // Check if school location is loaded
        if (!schoolConfig.latitude || !schoolConfig.longitude) {
            $('#loading-spinner').hide();
            showError('Koordinat sekolah belum dimuat. Refresh halaman atau hubungi administrator.');
            return;
        }

        // Verify location is within school radius
        const distance = calculateDistance(
            currentLocation.latitude,
            currentLocation.longitude,
            schoolConfig.latitude,
            schoolConfig.longitude
        );

        if (distance > schoolConfig.radius) {
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

        // Compress and resize the image
        const maxWidth = 1200;
        let width = canvas.width;
        let height = canvas.height;

        if (width > maxWidth) {
            height = Math.round((height * maxWidth) / width);
            width = maxWidth;
        }

        // Create compressed canvas
        const compressedCanvas = document.createElement('canvas');
        compressedCanvas.width = width;
        compressedCanvas.height = height;
        const compressedCtx = compressedCanvas.getContext('2d');
        compressedCtx.drawImage(canvas, 0, 0, width, height);

        // Convert to blob with compression (60% quality, WebP format)
        compressedCanvas.toBlob((blob) => {
            capturedPhoto = blob;
        }, 'image/webp', 0.6);

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
                    loadMonthlyCalendar(currentYear, currentMonth);
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
                            Maksimal jarak yang diperbolehkan: <strong>${schoolConfig.radius} meter</strong>
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
        // Refresh halaman untuk menampilkan data terbaru
        location.reload();
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

function loadMonthlyCalendar(year, month) {
    // Update month-year display
    const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                       'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    $('#calendar-month-year').text(`${monthNames[month]} ${year}`);

    // Show loading
    $('#calendar-container').html(`
        <div class="text-center py-4">
            <div class="spinner-border text-success spinner-border-sm" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    `);

    // Fetch monthly data
    $.ajax({
        url: '{{ route("guru.absensi-guru.monthly") }}',
        type: 'GET',
        data: {
            year: year,
            month: month + 1
        },
        success: function(response) {
            console.log('Monthly data:', response);
            if (response.success) {
                displayCalendar(year, month, response.data);
                displayMonthlyStatistics(response.statistics);
            } else {
                throw new Error(response.message || 'Gagal memuat data');
            }
        },
        error: function(xhr) {
            console.error('Error:', xhr);
            $('#calendar-container').html(`
                <div class="alert alert-danger mb-0 small">
                    <i class="fas fa-exclamation-triangle me-2"></i>Gagal memuat kalender
                </div>
            `);
        }
    });
}

function displayCalendar(year, month, attendanceData) {
    const today = new Date();
    const firstDay = new Date(year, month, 1);
    const lastDay = new Date(year, month + 1, 0);
    const daysInMonth = lastDay.getDate();
    const startingDayOfWeek = firstDay.getDay(); // 0 = Sunday

    let html = '<div class="calendar-grid">';

    // Day headers
    const dayNames = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
    dayNames.forEach(day => {
        html += `<div class="calendar-header">${day}</div>`;
    });

    // Empty cells before first day
    for (let i = 0; i < startingDayOfWeek; i++) {
        html += '<div class="calendar-day empty"></div>';
    }

    // Days of the month
    for (let day = 1; day <= daysInMonth; day++) {
        const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        const attendance = attendanceData[dateStr];

        let classes = 'calendar-day';
        let title = '';

        // Check if today
        if (year === today.getFullYear() && month === today.getMonth() && day === today.getDate()) {
            classes += ' today';
        }

        // Add status class
        if (attendance) {
            classes += ` ${attendance.status}`;
            title = `${attendance.status_label}`;
            if (attendance.waktu) {
                title += ` - ${attendance.waktu}`;
            }
            if (attendance.notes) {
                title += `: ${attendance.notes}`;
            }
        }

        html += `<div class="${classes}" title="${title}">
                    <span class="day-number">${day}</span>
                 </div>`;
    }

    html += '</div>';
    $('#calendar-container').html(html);
}

function displayMonthlyStatistics(stats) {
    const html = `
        <small class="text-muted d-block mb-2"><strong>Statistik Bulan Ini:</strong></small>
        <div class="stat-mini hadir">
            <span><i class="fas fa-check-circle me-1"></i>Hadir</span>
            <strong>${stats.hadir || 0}</strong>
        </div>
        <div class="stat-mini terlambat">
            <span><i class="fas fa-clock me-1"></i>Terlambat</span>
            <strong>${stats.terlambat || 0}</strong>
        </div>
        <div class="stat-mini izin">
            <span><i class="fas fa-file-medical me-1"></i>Izin</span>
            <strong>${stats.izin || 0}</strong>
        </div>
        <div class="stat-mini sakit">
            <span><i class="fas fa-notes-medical me-1"></i>Sakit</span>
            <strong>${stats.sakit || 0}</strong>
        </div>
        <div class="stat-mini alpha">
            <span><i class="fas fa-times-circle me-1"></i>Alpha</span>
            <strong>${stats.alpha || 0}</strong>
        </div>
    `;
    $('#monthly-statistics').html(html);
}

// Izin Form Handler
function submitIzinForm(e) {
    e.preventDefault();

    const alasan = $('#izin-alasan').val().trim();
    if (!alasan) {
        showError('Alasan izin harus diisi');
        return;
    }

    $('#submit-izin-btn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Mengirim...');

    const formData = new FormData();
    formData.append('type', 'izin');
    formData.append('alasan', alasan);

    const dokumen = $('#izin-dokumen')[0].files[0];
    if (dokumen) {
        formData.append('dokumen', dokumen);
    }

    $.ajax({
        url: '{{ route("guru.absensi-guru.store-non-hadir") }}',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            $('#submit-izin-btn').prop('disabled', false).html('<i class="fas fa-paper-plane me-2"></i>Kirim Pengajuan Izin');

            if (response.success) {
                $('#izin-form')[0].reset();
                showSuccessNonHadirModal(response.data, 'Izin');
                loadMonthlyCalendar(currentYear, currentMonth);
            } else {
                showError(response.message);
            }
        },
        error: function(xhr) {
            $('#submit-izin-btn').prop('disabled', false).html('<i class="fas fa-paper-plane me-2"></i>Kirim Pengajuan Izin');

            let message = 'Terjadi kesalahan saat mengirim izin';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            }
            showError(message);
        }
    });
}

// Sakit Form Handler
function submitSakitForm(e) {
    e.preventDefault();

    const keterangan = $('#sakit-keterangan').val().trim();
    if (!keterangan) {
        showError('Keterangan sakit harus diisi');
        return;
    }

    $('#submit-sakit-btn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Mengirim...');

    const formData = new FormData();
    formData.append('type', 'sakit');
    formData.append('keterangan', keterangan);

    const suratDokter = $('#sakit-surat-dokter')[0].files[0];
    if (suratDokter) {
        formData.append('surat_dokter', suratDokter);
    }

    $.ajax({
        url: '{{ route("guru.absensi-guru.store-non-hadir") }}',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            $('#submit-sakit-btn').prop('disabled', false).html('<i class="fas fa-paper-plane me-2"></i>Kirim Laporan Sakit');

            if (response.success) {
                $('#sakit-form')[0].reset();
                showSuccessNonHadirModal(response.data, 'Sakit');
                loadMonthlyCalendar(currentYear, currentMonth);
            } else {
                showError(response.message);
            }
        },
        error: function(xhr) {
            $('#submit-sakit-btn').prop('disabled', false).html('<i class="fas fa-paper-plane me-2"></i>Kirim Laporan Sakit');

            let message = 'Terjadi kesalahan saat mengirim laporan sakit';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            }
            showError(message);
        }
    });
}

function showSuccessNonHadirModal(data, type) {
    const statusClass = type === 'Izin' ? 'info' : 'danger';
    const statusIcon = type === 'Izin' ? 'file-medical' : 'notes-medical';

    const modal = $(`
        <div class="modal fade" id="successModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-${statusClass} text-white border-0">
                        <h5 class="modal-title">
                            <i class="fas fa-${statusIcon} me-2"></i>Laporan ${type} Berhasil
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center p-4">
                        <i class="fas fa-check-circle text-success fs-1 mb-3"></i>
                        <h5>Laporan ${type} Tersimpan</h5>
                        <p class="text-muted mb-3">
                            Tanggal: <strong>${data.tanggal}</strong><br>
                            Status: <strong>${type}</strong>
                        </p>
                        <div class="alert alert-${statusClass}">
                            <small>${data.message || 'Laporan Anda telah dicatat. Semoga cepat sembuh dan bisa kembali beraktivitas.'}</small>
                        </div>
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
        // Refresh halaman untuk menampilkan data terbaru
        location.reload();
    });
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

.bg-gradient {
    background: linear-gradient(135deg, var(--bs-primary) 0%, #5a67d8 100%) !important;
}

.card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

/* Calendar Styles */
.calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 2px;
}

.calendar-header {
    text-align: center;
    font-weight: 600;
    font-size: 0.75rem;
    padding: 0.5rem 0.25rem;
    background-color: #f8f9fa;
    color: #495057;
}

.calendar-day {
    aspect-ratio: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    border-radius: 0.25rem;
    background-color: #fff;
    border: 1px solid #e9ecef;
    cursor: pointer;
    transition: all 0.2s ease;
    position: relative;
}

.calendar-day:hover {
    transform: scale(1.05);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    z-index: 1;
}

.calendar-day.empty {
    background-color: #f8f9fa;
    border: none;
    cursor: default;
}

.calendar-day.empty:hover {
    transform: none;
    box-shadow: none;
}

.calendar-day.today {
    border: 2px solid #0d6efd;
    font-weight: bold;
}

.calendar-day.hadir {
    background-color: #28a745;
    color: white;
    border-color: #28a745;
}

.calendar-day.terlambat {
    background-color: #6c757d;
    color: white;
    border-color: #6c757d;
}

.calendar-day.izin {
    background-color: #0dcaf0;
    color: #000;
    border-color: #0dcaf0;
}

.calendar-day.sakit {
    background-color: #ffc107;
    color: #000;
    border-color: #ffc107;
}

.calendar-day.alpha {
    background-color: #dc3545;
    color: white;
    border-color: #dc3545;
}

.calendar-day .day-number {
    position: relative;
    z-index: 1;
}

.stat-mini {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.4rem 0.6rem;
    border-radius: 0.25rem;
    font-size: 0.75rem;
    margin-bottom: 0.4rem;
}

.stat-mini:last-child {
    margin-bottom: 0;
}

.stat-mini.hadir {
    background-color: rgba(40, 167, 69, 0.1);
    color: #28a745;
}

.stat-mini.terlambat {
    background-color: rgba(255, 193, 7, 0.1);
    color: #856404;
}

.stat-mini.izin {
    background-color: rgba(13, 202, 240, 0.1);
    color: #055160;
}

.stat-mini.sakit {
    background-color: rgba(23, 162, 184, 0.1);
    color: #0c5460;
}

.stat-mini.alpha {
    background-color: rgba(220, 53, 69, 0.1);
    color: #dc3545;
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
