@extends('layouts.app')

@section('title', 'Absensi Siswa')

@section('content')
<!-- CSRF Token Meta Tag -->
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="bg-white rounded-3 shadow-sm p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-2">
                            <i class="fas fa-user-check text-primary me-2"></i>Absensi Siswa
                        </h1>
                        <p class="text-muted mb-0">Scan QR Code NISN siswa untuk melakukan absensi</p>
                    </div>
                    <div class="text-end">
                        <div class="badge bg-primary fs-6 p-2">
                            <i class="fas fa-calendar-alt me-1"></i>
                            {{ \Carbon\Carbon::now()->format('d F Y') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Scanner Section -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-gradient bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-qrcode me-2"></i>Scanner QR Code
                    </h5>
                </div>
                <div class="card-body p-4">
                    <!-- Scanner Container -->
                    <div class="scanner-container text-center">
                        <div id="scanner" class="mb-3">
                            <div id="scanner-placeholder" class="border border-2 border-dashed border-primary rounded-3 p-5"
                                 style="min-height: 400px; display: flex; align-items: center; justify-content: center;">
                                <div class="text-center">
                                    <div class="spinner-border text-primary mb-3" role="status" id="loading-spinner" style="display: none;">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <div id="scanner-controls">
                                        <i class="fas fa-qrcode text-primary fs-1 mb-3"></i>
                                        <h5 class="text-muted">Scan QR Code NISN Siswa</h5>
                                        <p class="text-muted small mb-3">Arahkan kamera ke QR Code yang ada di kartu identitas siswa</p>
                                        <button id="start-scanner" class="btn btn-primary btn-lg">
                                            <i class="fas fa-play me-2"></i>Mulai Scanner
                                        </button>
                                        <div class="mt-3">
                                            <small class="text-muted">
                                                <i class="fas fa-info-circle me-1"></i>
                                                Pastikan QR Code terlihat jelas dan pencahayaan cukup
                                            </small>
                                        </div>
                                    </div>
                                    <div id="scanner-error" class="text-danger" style="display: none;">
                                        <i class="fas fa-exclamation-triangle fs-1 mb-3"></i>
                                        <h5>Tidak dapat mengakses kamera</h5>
                                        <p>Pastikan kamera tersedia dan izin telah diberikan</p>
                                        <button class="btn btn-outline-primary" onclick="location.reload()">
                                            <i class="fas fa-redo me-1"></i>Coba Lagi
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Switch Camera Button (Hidden by default) -->
                            <div id="camera-switch-container" class="text-center mt-3" style="display: none;">
                                <button id="switch-camera" class="btn btn-outline-secondary rounded-pill px-4">
                                    <i class="fas fa-sync-alt me-2"></i>Ganti Kamera
                                </button>
                            </div>
                        </div>

                        <!-- Manual Input -->
                        <div class="row">
                            <div class="col-md-8 mx-auto">
                                <div class="alert alert-info">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    <strong>Tips:</strong> Jika scanner tidak berfungsi, Anda bisa input NISN secara manual di bawah ini
                                </div>
                                <div class="input-group">
                                    <input type="text" id="manual-nisn" class="form-control" placeholder="Atau masukkan NISN secara manual (10 digit)" maxlength="10">
                                    <button class="btn btn-outline-primary" type="button" id="submit-manual">
                                        <i class="fas fa-check me-1"></i>Submit
                                    </button>
                                </div>
                                <small class="text-muted">NISN terdiri dari 10 digit angka, contoh: 2215051006</small>

                                <div class="mt-3">
                                    <details>
                                        <summary class="text-muted" style="cursor: pointer;">
                                            <small><i class="fas fa-question-circle me-1"></i>Troubleshooting Scanner</small>
                                        </summary>
                                        <div class="mt-2 p-2 bg-light rounded">
                                            <small class="text-muted">
                                                <strong>Jika scanner tidak bekerja:</strong><br>
                                                • Pastikan browser mendapat izin kamera<br>
                                                • Pastikan pencahayaan cukup terang<br>
                                                • Tahan kartu dengan stabil, jarak 10-20cm<br>
                                                • QR Code harus terlihat jelas dan tidak blur<br>
                                                • Tekan <kbd>Ctrl+D</kbd> untuk mode debug<br>
                                                • Jika masih bermasalah, gunakan input manual
                                            </small>
                                        </div>
                                    </details>
                                </div>
                            </div>
                        </div>

                        <!-- Scanner Controls -->
                        <div class="mt-3" id="scanner-action-buttons" style="display: none;">
                            <button id="stop-scanner" class="btn btn-outline-danger me-2">
                                <i class="fas fa-stop me-1"></i>Stop Scanner
                            </button>
                            <button id="restart-scanner" class="btn btn-outline-primary">
                                <i class="fas fa-redo me-1"></i>Restart
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attendance History Section -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-gradient bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-list-check me-2"></i>Absensi Hari Ini
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="p-3 border-bottom bg-light">
                        <div class="row text-center">
                            <div class="col">
                                <div class="text-success fw-bold fs-4" id="hadir-count">0</div>
                                <small class="text-muted">Hadir</small>
                            </div>
                            <div class="col">
                                <div class="text-warning fw-bold fs-4" id="terlambat-count">0</div>
                                <small class="text-muted">Terlambat</small>
                            </div>
                            <div class="col">
                                <div class="text-danger fw-bold fs-4" id="alpha-count">0</div>
                                <small class="text-muted">Alpha</small>
                            </div>
                        </div>
                    </div>

                    <div class="attendance-list" style="max-height: 400px; overflow-y: auto;">
                        <div id="attendance-records" class="p-3">
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-clock fs-3 mb-2"></i>
                                <p class="mb-0">Belum ada absensi hari ini</p>
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
<!-- jQuery CDN (ensure it's loaded first) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<!-- Bootstrap 5 CDN as fallback -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- HTML5-QRCODE Library - Recommended for QR and Barcodes -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script>

<script>
// Wait for DOM and libraries to load
document.addEventListener('DOMContentLoaded', function() {
    // Global variables
    let html5QrCode = null;
    let isScanning = false;
    let isProcessing = false;
    let debugMode = false;
    let currentCameraId = null;
    let cameras = [];
    let currentCameraIndex = 0;

    // jQuery ready function
    $(document).ready(function() {
        // Check if jQuery is available
        if (typeof $ === 'undefined') {
            console.error('jQuery is not loaded');
            showScannerError();
            return;
        }

        // Initialize page
        loadTodayAttendance();

    // Start scanner button (delegated event for dynamic content)
    $(document).on('click', '#start-scanner', function() {
        console.log('Start scanner button clicked');
        startScanner();
    });

    // Stop scanner button
    $(document).on('click', '#stop-scanner', function() {
        stopScanner();
    });

    // Restart scanner button
    $(document).on('click', '#restart-scanner', function() {
        stopScanner();
        setTimeout(() => startScanner(), 500);
    });

    // Switch camera button
    $(document).on('click', '#switch-camera', function() {
        switchCamera();
    });

    // Manual NISN submit
    $('#submit-manual').click(function() {
        const nisn = $('#manual-nisn').val().trim();
        if (nisn.length === 10 && /^\d+$/.test(nisn)) {
            processAttendance(nisn);
            $('#manual-nisn').val('');
        } else {
            showError('NISN harus berupa 10 digit angka');
        }
    });

    // Enter key for manual input
    $('#manual-nisn').keypress(function(e) {
        if (e.which === 13) {
            $('#submit-manual').click();
        }
    });

    // Debug mode toggle (press Ctrl+D)
    $(document).keydown(function(e) {
        if (e.ctrlKey && e.which === 68) { // Ctrl+D
            debugMode = !debugMode;
            if (debugMode) {
                showToast('Debug mode activated', 'info');
                console.log('Debug mode ON');
            } else {
                showToast('Debug mode deactivated', 'info');
                console.log('Debug mode OFF');
            }
            e.preventDefault();
        }
    });

    // Function declarations
    function pauseScanner() {
        if (html5QrCode && isScanning) {
            try {
                html5QrCode.pause();
                console.log('Scanner paused');
            } catch (e) {
                console.error('Error pausing scanner:', e);
            }
        }
    }

    function resumeScanner() {
        if (html5QrCode && isScanning) {
            try {
                html5QrCode.resume();
                console.log('Scanner resumed');
            } catch (e) {
                console.error('Error resuming scanner:', e);
            }
        }
    }

    function startScanner() {
        console.log('Starting HTML5-QRCODE scanner...');
        $('#loading-spinner').show();
        $('#scanner-controls').hide();
        $('#scanner-error').hide();

        initializeHTML5Scanner();
    }

    function initializeHTML5Scanner() {
        console.log('Initializing HTML5-QRCODE...');

        // Check if Html5Qrcode is available
        if (typeof Html5Qrcode === 'undefined') {
            console.error('HTML5-QRCode library not loaded');

            // Attempt to load dynamically as fallback
            console.log('Attempting to load library dynamically...');
            $.getScript("https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js")
                .done(function() {
                    console.log("Library loaded dynamically");
                    initializeHTML5Scanner(); // Retry initialization
                })
                .fail(function(jqxhr, settings, exception) {
                    console.error("Failed to load library dynamically:", exception);
                    showScannerError();
                });
            return;
        }

        // Initialize Html5Qrcode instance if not exists
        if (!html5QrCode) {
            html5QrCode = new Html5Qrcode("scanner-placeholder");
        }

        // Get cameras
        Html5Qrcode.getCameras().then(devices => {
            if (devices && devices.length) {
                cameras = devices;
                console.log("Cameras found:", cameras);

                // Show switch button if multiple cameras
                if (cameras.length > 1) {
                    $('#camera-switch-container').show();
                } else {
                    $('#camera-switch-container').hide();
                }

                // Determine which camera to use
                let cameraId = cameras[0].id;

                // Try to find back camera if not already selected
                if (!currentCameraId) {
                    // Prefer back camera
                    const backCamera = cameras.find(camera => camera.label.toLowerCase().includes('back') || camera.label.toLowerCase().includes('environment'));
                    if (backCamera) {
                        cameraId = backCamera.id;
                        currentCameraIndex = cameras.indexOf(backCamera);
                    }
                    currentCameraId = cameraId;
                } else {
                    cameraId = currentCameraId;
                }

                startScanningWithCamera(cameraId);
            } else {
                console.error("No cameras found");
                showScannerError();
            }
        }).catch(err => {
            console.error("Error getting cameras", err);
            showScannerError();
        });
    }

    function startScanningWithCamera(cameraId) {
        const config = {
            fps: 10,
            qrbox: { width: 250, height: 250 },
            aspectRatio: 1.0
        };

        html5QrCode.start(
            cameraId,
            config,
            (decodedText, decodedResult) => {
                // Success callback
                console.log('QR Code scanned:', decodedText);
                handleQRResult(decodedText);
            },
            (errorMessage) => {
                // Error callback
            }
        ).then(() => {
            console.log("Scanning started");
            onScannerStarted();
        }).catch(err => {
            console.error("Error starting scanner", err);
            showScannerError();
        });
    }

    function switchCamera() {
        if (cameras.length < 2 || !isScanning) return;

        // Stop current scan
        html5QrCode.stop().then(() => {
            // Cycle to next camera
            currentCameraIndex = (currentCameraIndex + 1) % cameras.length;
            currentCameraId = cameras[currentCameraIndex].id;

            console.log("Switching to camera:", cameras[currentCameraIndex].label);

            // Restart scan
            startScanningWithCamera(currentCameraId);
        }).catch(err => {
            console.error("Error stopping scanner for switch", err);
        });
    }

    function onScannerStarted() {
        $('#loading-spinner').hide();
        $('#scanner-action-buttons').show();
        isScanning = true;

        // Add scanning indicator
        const indicator = $('<div class="text-center mt-2 scanning-indicator"><span class="badge bg-success">📷 Scanning... Arahkan kamera ke QR Code</span></div>');
        $('#scanner-placeholder').append(indicator);
    }    function handleQRResult(qrData) {
    console.log('QR Code detected:', qrData);
    debugLog('Raw QR Data received', qrData);

    // Visual feedback for successful scan
    playSuccessSound();
    showScanAnimation();

    // Extract NISN from QR data
    let nisn = qrData.trim();

    console.log('Raw QR Data:', nisn);
    debugLog('Processing QR data', `Length: ${nisn.length}, Content: ${nisn.substring(0, 50)}...`);

    // Try to extract NISN from various QR formats
    if (nisn.length !== 10 || !/^\d+$/.test(nisn)) {
        console.log('Trying to parse non-standard format...');
        debugLog('Non-standard format detected, parsing...');

        // Try JSON format first
        try {
            const qrDataObj = JSON.parse(nisn);
            console.log('Parsed as JSON:', qrDataObj);
            debugLog('Successfully parsed as JSON', qrDataObj);

            if (qrDataObj.nisn) {
                nisn = qrDataObj.nisn.toString();
                debugLog('Found NISN in .nisn property', nisn);
            } else if (qrDataObj.NISN) {
                nisn = qrDataObj.NISN.toString();
                debugLog('Found NISN in .NISN property', nisn);
            } else if (qrDataObj.student_id) {
                nisn = qrDataObj.student_id.toString();
                debugLog('Found NISN in .student_id property', nisn);
            } else if (qrDataObj.id) {
                nisn = qrDataObj.id.toString();
                debugLog('Found NISN in .id property', nisn);
            }
        } catch (e) {
            console.log('Not JSON format, trying regex patterns...');
            debugLog('JSON parsing failed, trying regex patterns');

            // Try various patterns
            let patterns = [
                /NISN[:\s]*(\d{10})/i,
                /ID[:\s]*(\d{10})/i,
                /(\d{10})/,
                /\b(\d{10})\b/,
                /student[_\s]*id[:\s]*(\d{10})/i
            ];

            for (let pattern of patterns) {
                const match = nisn.match(pattern);
                if (match && match[1]) {
                    nisn = match[1];
                    console.log('Found NISN using pattern:', pattern, 'Result:', nisn);
                    debugLog('Pattern match found', `Pattern: ${pattern}, Result: ${nisn}`);
                    break;
                }
            }
        }
    }

    console.log('Extracted NISN:', nisn);
    debugLog('Final NISN extracted', nisn);

    // Validate NISN format (10 digits)
    if (nisn.length === 10 && /^\d+$/.test(nisn)) {
        debugLog('Valid NISN format confirmed', nisn);

        // Prevent multiple detections
        if (isProcessing) return;
        isProcessing = true;

        // Pause scanner to prevent background scanning while processing
        pauseScanner();

        showScanSuccess(nisn);

        // Process attendance immediately without stopping scanner
        processAttendance(nisn);

    } else {
        console.log('Invalid NISN format, continuing scan...');
        debugLog('Invalid NISN format', `Length: ${nisn.length}, Value: ${nisn}`);
        playErrorSound();
        showInvalidQRMessage();

        if (debugMode) {
            showToast(`QR Data: ${qrData.substring(0, 100)}...`, 'warning');
        }
    }
}

function stopScanner() {
    console.log('Stopping HTML5-QRCODE scanner...');

    // Clear HTML5-QRCODE scanner
    if (html5QrCode && isScanning) {
        html5QrCode.stop()
            .then(() => {
                console.log('Scanner stopped successfully');
                // Don't clear instance, just stop scanning
            })
            .catch((err) => {
                console.error('Error stopping scanner:', err);
            });
    }

    isScanning = false;
    $('#camera-switch-container').hide();

    $('#scanner-placeholder').empty().html(`
        <div class="text-center">
            <i class="fas fa-qrcode text-primary fs-1 mb-3"></i>
            <h5 class="text-muted">Scan QR Code NISN Siswa</h5>
            <p class="text-muted small mb-3">Arahkan kamera ke QR Code yang ada di kartu identitas siswa</p>
            <button id="start-scanner" class="btn btn-primary btn-lg">
                <i class="fas fa-play me-2"></i>Mulai Scanner
            </button>
            <div class="mt-3">
                <small class="text-muted">
                    <i class="fas fa-info-circle me-1"></i>
                    Pastikan QR Code terlihat jelas dan pencahayaan cukup
                </small>
            </div>
        </div>
    `);

    $('#scanner-action-buttons').hide();
    $('#scanner-controls').show();

    // Re-bind start button
    $('#start-scanner').click(function() {
        startScanner();
    });
}

function playSuccessSound() {
    try {
        const audioContext = new (window.AudioContext || window.webkitAudioContext)();
        const oscillator = audioContext.createOscillator();
        const gainNode = audioContext.createGain();

        oscillator.connect(gainNode);
        gainNode.connect(audioContext.destination);

        oscillator.frequency.value = 800;
        oscillator.type = 'sine';

        gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
        gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.2);

        oscillator.start(audioContext.currentTime);
        oscillator.stop(audioContext.currentTime + 0.2);
    } catch (e) {
        console.log('Audio not supported');
    }
}

function playErrorSound() {
    try {
        const audioContext = new (window.AudioContext || window.webkitAudioContext)();
        const oscillator = audioContext.createOscillator();
        const gainNode = audioContext.createGain();

        oscillator.connect(gainNode);
        gainNode.connect(audioContext.destination);

        oscillator.frequency.value = 300;
        oscillator.type = 'sawtooth';

        gainNode.gain.setValueAtTime(0.2, audioContext.currentTime);
        gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.3);

        oscillator.start(audioContext.currentTime);
        oscillator.stop(audioContext.currentTime + 0.3);
    } catch (e) {
        console.log('Audio not supported');
    }
}

function showScanAnimation() {
    const scanLine = $('<div class="scan-line"></div>');
    $('#scanner-placeholder').append(scanLine);

    // Add CSS for animation dynamically
    if (!$('#scan-animation-style').length) {
        $('head').append(`
            <style id="scan-animation-style">
                .scan-line {
                    position: absolute;
                    top: 0;
                    left: 0;
                    right: 0;
                    height: 3px;
                    background: linear-gradient(90deg, transparent, #28a745, transparent);
                    animation: scan 0.5s ease-out;
                    z-index: 10;
                }
                @keyframes scan {
                    0% { transform: translateY(0); opacity: 1; }
                    100% { transform: translateY(400px); opacity: 0; }
                }
            </style>
        `);
    }

    setTimeout(() => scanLine.remove(), 500);
}

function showScanSuccess(nisn) {
    const successOverlay = $(`
        <div class="scan-success-overlay">
            <div class="text-center text-white">
                <i class="fas fa-check-circle fs-1 mb-2"></i>
                <h5>QR Code Berhasil Discan!</h5>
                <p class="mb-0">NISN: ${nisn}</p>
                <small>Memproses absensi...</small>
            </div>
        </div>
    `);

    $('#scanner-placeholder').append(successOverlay);

    // Add CSS for overlay
    if (!$('#success-overlay-style').length) {
        $('head').append(`
            <style id="success-overlay-style">
                .scan-success-overlay {
                    position: absolute;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background: rgba(40, 167, 69, 0.9);
                    color: white;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    border-radius: 0.375rem;
                    z-index: 20;
                    animation: fadeIn 0.3s ease-out;
                }
                @keyframes fadeIn {
                    0% { opacity: 0; transform: scale(0.8); }
                    100% { opacity: 1; transform: scale(1); }
                }
            </style>
        `);
    }

    setTimeout(() => successOverlay.remove(), 1500);
}

function showInvalidQRMessage() {
    const warningMsg = $('<div class="alert alert-warning mt-2"><i class="fas fa-exclamation-triangle me-1"></i>QR Code tidak valid, coba lagi...</div>');
    $('#scanner-placeholder').append(warningMsg);
    setTimeout(() => warningMsg.remove(), 2000);
}

function showToast(message, type = 'info') {
    const toastId = 'toast-' + Date.now();
    const toast = $(`
        <div id="${toastId}" class="toast position-fixed top-0 end-0 m-3" style="z-index: 9999;">
            <div class="toast-header">
                <i class="fas fa-info-circle text-${type} me-2"></i>
                <strong class="me-auto">Scanner</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">
                ${message}
            </div>
        </div>
    `);

    $('body').append(toast);
    $('#' + toastId).fadeIn();

    setTimeout(() => {
        $('#' + toastId).fadeOut(function() {
            $(this).remove();
        });
    }, 5000);
}

function debugLog(message, data = null) {
    if (debugMode) {
        console.log('[DEBUG]', message, data || '');
        if (data) {
            showToast(`Debug: ${message}`, 'warning');
        }
    }
}

function showScannerError() {
    $('#loading-spinner').hide();
    $('#scanner-controls').hide();
    $('#scanner-error').show();
    $('#scanner-action-buttons').hide();
}

function processAttendance(nisn) {
    // Show loading state
    const loadingId = 'loading-' + Date.now();

    // Remove "Belum ada absensi" message if it exists
    $('#attendance-records .text-center.py-4').remove();

    // Prepend loading indicator
    $('#attendance-records').prepend(`
        <div id="${loadingId}" class="border-bottom py-2 text-center text-muted bg-light">
            <div class="spinner-border spinner-border-sm text-primary me-2"></div>
            <small>Memproses NISN: ${nisn}...</small>
        </div>
    `);

    // Make AJAX call to Laravel backend
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        url: '{{ route("guru.absensi.process") }}',
        type: 'POST',
        data: {
            nisn: nisn
        },
        success: function(response) {
            $('#' + loadingId).remove(); // Remove loading indicator

            if (response.success) {
                showSuccessModal(response.data);
                addAttendanceRecord(response.data);
                updateAttendanceCount(response.data.status);
            } else {
                showError(response.message);
            }
        },
        error: function(xhr) {
            $('#' + loadingId).remove(); // Remove loading indicator

            let message = 'Terjadi kesalahan';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            } else if (xhr.status === 404) {
                message = "Siswa dengan NISN " + nisn + " tidak ditemukan";
            } else if (xhr.status === 400) {
                message = xhr.responseJSON.message || "Siswa sudah melakukan absensi hari ini";
            }
            showError(message);
        }
    });
}

function showSuccessModal(studentData) {
    const statusBadgeClass = {
        'hadir': 'bg-success',
        'terlambat': 'bg-warning',
        'alpha': 'bg-danger'
    };

    const statusIcon = {
        'hadir': 'fa-check-circle',
        'terlambat': 'fa-clock',
        'alpha': 'fa-times-circle'
    };

    const modal = $(`
        <div class="modal fade" id="successModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-body p-0">
                        <div class="text-center p-4">
                            <div class="success-checkmark mb-3">
                                <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                            </div>
                            <h3 class="text-success mb-3">Absensi Berhasil!</h3>

                            <div class="card border-0 bg-light mb-3">
                                <div class="card-body">
                                    <h5 class="card-title text-primary mb-2">${studentData.name}</h5>
                                    <div class="row text-center">
                                        <div class="col">
                                            <small class="text-muted d-block">NISN</small>
                                            <strong>${studentData.nisn}</strong>
                                        </div>
                                        <div class="col">
                                            <small class="text-muted d-block">Kelas</small>
                                            <strong>${studentData.class || 'N/A'}</strong>
                                        </div>
                                        <div class="col">
                                            <small class="text-muted d-block">Waktu</small>
                                            <strong>${studentData.time}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <span class="badge ${statusBadgeClass[studentData.status]} fs-6 px-3 py-2 mb-3">
                                <i class="fas ${statusIcon[studentData.status]} me-1"></i>
                                ${studentData.status.charAt(0).toUpperCase() + studentData.status.slice(1)}
                            </span>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                                    <i class="fas fa-check me-1"></i>OK
                                </button>
                                <button type="button" class="btn btn-outline-primary" id="scan-again">
                                    <i class="fas fa-qrcode me-1"></i>Scan Lagi
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `);

    // Remove existing modal if any
    $('#successModal').remove();

    // Append and show
    $('body').append(modal);

    // Try bootstrap Modal first, fallback to jQuery
    try {
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            const modalElement = new bootstrap.Modal(document.getElementById('successModal'));
            modalElement.show();

            // Auto close after 2 seconds
            setTimeout(() => {
                modalElement.hide();
            }, 2000);

            // Handle scan again button
            $('#scan-again').click(function() {
                modalElement.hide();
            });
        } else {
            throw new Error('Bootstrap not available');
        }
    } catch (e) {
        // Fallback to jQuery modal
        $('#successModal').modal('show');

        // Auto close after 2 seconds
        setTimeout(() => {
            $('#successModal').modal('hide');
        }, 2000);

        // Handle scan again button
        $('#scan-again').click(function() {
            $('#successModal').modal('hide');
        });
    }

    // Remove modal from DOM when hidden
    $('#successModal').on('hidden.bs.modal', function () {
        $(this).remove();
        isProcessing = false; // Allow next scan
        resumeScanner(); // Resume scanning
    });
}

function showError(message) {
    const errorModal = $(`
        <div class="modal fade" id="errorModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-body text-center p-4">
                        <div class="text-danger mb-3">
                            <i class="fas fa-exclamation-triangle" style="font-size: 4rem;"></i>
                        </div>
                        <h3 class="text-danger mb-3">Oops!</h3>
                        <p class="text-muted mb-3">${message}</p>
                        <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                            <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                                <i class="fas fa-times me-1"></i>Tutup
                            </button>
                            <button type="button" class="btn btn-outline-primary" id="try-again">
                                <i class="fas fa-redo me-1"></i>Coba Lagi
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `);

    // Remove existing error modal if any
    $('#errorModal').remove();

    // Append and show
    $('body').append(errorModal);

    // Try bootstrap Modal first, fallback to jQuery
    try {
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            const modalElement = new bootstrap.Modal(document.getElementById('errorModal'));
            modalElement.show();

            // Handle try again button
            $('#try-again').click(function() {
                modalElement.hide();
            });

            // Auto close after 2 seconds
            setTimeout(() => {
                modalElement.hide();
            }, 2000);
        } else {
            throw new Error('Bootstrap not available');
        }
    } catch (e) {
        // Fallback to jQuery modal
        $('#errorModal').modal('show');

        // Handle try again button
        $('#try-again').click(function() {
            $('#errorModal').modal('hide');
        });

        // Auto close after 2 seconds
        setTimeout(() => {
            $('#errorModal').modal('hide');
        }, 2000);
    }

    // Remove modal from DOM when hidden
    $('#errorModal').on('hidden.bs.modal', function () {
        $(this).remove();
        isProcessing = false; // Allow next scan
        resumeScanner(); // Resume scanning
    });
}

function addAttendanceRecord(student) {
    const statusClass = student.status === 'hadir' ? 'success' : 'warning';
    const statusIcon = student.status === 'hadir' ? 'check-circle' : 'clock';
    const statusText = student.status === 'hadir' ? 'Hadir' : 'Terlambat';

    const recordHtml = `
        <div class="border-bottom py-2">
            <div class="d-flex align-items-center">
                <div class="text-${statusClass} me-2">
                    <i class="fas fa-${statusIcon}"></i>
                </div>
                <div class="flex-grow-1">
                    <h6 class="mb-0 fw-bold">${student.name}</h6>
                    <small class="text-muted">NISN: ${student.nisn} • ${student.class}</small>
                </div>
                <div class="text-end">
                    <span class="badge bg-${statusClass}">${statusText}</span>
                    <br>
                    <small class="text-muted">${student.time}</small>
                </div>
            </div>
        </div>
    `;

    if ($('#attendance-records .text-center').length > 0) {
        $('#attendance-records').html(recordHtml);
    } else {
        $('#attendance-records').prepend(recordHtml);
    }
}

function updateAttendanceCount(status) {
    const currentCount = parseInt($('#' + status + '-count').text());
    $('#' + status + '-count').text(currentCount + 1);
}

function loadTodayAttendance() {
    // Load today's attendance from the server
    $.get('{{ route("guru.absensi.today") }}')
        .done(function(response) {
            if (response.success) {
                // Update counts
                $('#hadir-count').text(response.counts.hadir);
                $('#terlambat-count').text(response.counts.terlambat);
                $('#alpha-count').text(response.counts.alpha);

                // Show records
                if (response.data.length > 0) {
                    $('#attendance-records').empty();
                    response.data.forEach(function(record) {
                        const student = {
                            name: record.name,
                            nisn: record.nisn,
                            class: record.kelas,
                            status: record.status,
                            time: record.time
                        };
                        addAttendanceRecord(student);
                    });
                } else {
                    $('#attendance-records').html(`
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-clock fs-3 mb-2"></i>
                            <p class="mb-0">Belum ada absensi hari ini</p>
                        </div>
                    `);
                }
            }
        })
        .fail(function() {
            // Show mock data as fallback
            $('#hadir-count').text(0);
            $('#terlambat-count').text(0);
            $('#alpha-count').text(0);
        });
}

    // Cleanup on page unload
    $(window).on('beforeunload', function() {
        if (isScanning && html5QrCode) {
            html5QrCode.stop().catch(console.error);
        }
    });

    }); // End jQuery ready

}); // End DOMContentLoaded
</script>

<style>
.scanner-container {
    position: relative;
}

#scanner-placeholder {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 400px;
    border: 2px dashed #0d6efd;
    border-radius: 0.375rem;
    background: #f8f9fa;
}

#scanner-placeholder video {
    width: 100% !important;
    height: auto !important;
    max-width: 400px;
    border-radius: 0.375rem;
    background: #000;
}

/* QR Scanner specific styles */
.qr-scanner {
    position: relative;
}

.qr-scanner video {
    width: 100%;
    height: auto;
    max-width: 400px;
    border-radius: 0.375rem;
}

.scanning-indicator {
    position: absolute;
    bottom: 10px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 10;
}

.attendance-list::-webkit-scrollbar {
    width: 6px;
}

.attendance-list::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.attendance-list::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.attendance-list::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

.bg-gradient {
    background: linear-gradient(135deg, var(--bs-primary) 0%, #5a67d8 100%) !important;
}

.card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

/* HTML5-QRCode specific styling */
#scanner-placeholder {
    border-radius: 0.375rem;
    overflow: hidden;
}

#scanner-placeholder > div {
    border-radius: 0.375rem !important;
}

#scanner-placeholder video {
    border-radius: 0.375rem !important;
    width: 100% !important;
    max-width: 400px !important;
    height: auto !important;
}

#scanner-placeholder canvas {
    border-radius: 0.375rem !important;
}

/* HTML5-QRCode UI adjustments */
.html5-qrcode-element {
    border: 2px dashed #007bff !important;
    border-radius: 0.375rem !important;
}

#html5-qr-code-select-camera,
#html5-qr-code-start-button,
#html5-qr-code-stop-button {
    display: none !important;
}

@media (max-width: 768px) {
    #scanner-placeholder {
        min-height: 300px !important;
        padding: 0 !important;
        border: none !important;
        background: #000;
    }

    .col-lg-8, .col-lg-4 {
        margin-bottom: 1rem;
    }

    #scanner-placeholder video {
        max-width: 100% !important;
        height: 100% !important;
        object-fit: cover;
    }

    .scanner-container {
        margin: -1rem -1rem 1rem -1rem;
    }

    .card-body {
        padding: 1rem;
    }
}
</style>
@endpush
