@extends('layouts.app')

@section('title', 'Isi Jurnal Mengajar')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('guru.jurnal-mengajar.index') }}">Jurnal Mengajar</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('guru.jurnal-mengajar.create', ['tanggal' => $tanggal]) }}">Pilih Jurnal</a></li>
                    <li class="breadcrumb-item active">Isi Jurnal</li>
                </ol>
            </nav>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Terjadi kesalahan!</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Header Info -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h4 class="mb-2"><i class="fas fa-book-open text-primary me-2"></i>Isi Jurnal Mengajar</h4>
                    <p class="mb-1"><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($tanggal)->format('d F Y') }} ({{ ucfirst($hari) }})</p>
                    <p class="mb-1"><strong>Jam Pelajaran:</strong>
                        @if($jamKeMulai == $jamKeSelesai)
                            Jam ke-{{ $jamKeMulai }}
                        @else
                            Jam ke-{{ $jamKeMulai }} - {{ $jamKeSelesai }}
                        @endif
                        @if(isset($jamPelajarans[$jamKeMulai]) && isset($jamPelajarans[$jamKeSelesai]))
                            <span class="badge bg-info">{{ $jamPelajarans[$jamKeMulai]->jam_mulai }} - {{ $jamPelajarans[$jamKeSelesai]->jam_selesai }}</span>
                        @endif
                    </p>
                    <p class="mb-1"><strong>Kelas:</strong> Kelas {{ $kelas->full_name }}</p>
                    <p class="mb-0"><strong>Mata Pelajaran:</strong> {{ $mataPelajaran->nama_mapel }}</p>
                </div>
                <div class="col-md-4 text-end">
                    <a href="{{ route('guru.jurnal-mengajar.create', ['tanggal' => $tanggal]) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>


    <!-- Form Wizard -->
    <form action="{{ route('guru.jurnal-mengajar.store') }}" method="POST" id="formWizard">
        @csrf
        <input type="hidden" name="tanggal" value="{{ $tanggal }}">
        <input type="hidden" name="kelas_id" value="{{ $kelas->id }}">
        <input type="hidden" name="mata_pelajaran_id" value="{{ $mataPelajaran->id }}">
        <input type="hidden" name="jam_ke_mulai" value="{{ $jamKeMulai }}">
        <input type="hidden" name="jam_ke_selesai" value="{{ $jamKeSelesai }}">
        <input type="hidden" name="foto_bukti" id="foto_bukti">

        <!-- Step 1: Absensi Siswa -->
        <div class="wizard-step" id="step-1">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-user-check me-2"></i>Step 1: Absensi Siswa</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Silakan lakukan absensi untuk semua siswa. Status default sesuai dengan absensi pagi hari ini.
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th style="min-width: 36px" width="5%">No</th>
                                    <th style="min-width: 100px" width="15%">NIS</th>
                                    <th style="min-width: 200px" width="40%">Nama Siswa</th>
                                    <th style="min-width: 100px" width="20%">Absensi Pagi</th>
                                    <th style="min-width: 125px" width="20%">Status Absensi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($siswaKelas as $index => $siswa)
                                    @php
                                        $absensiHarian = $absensiPagi->get($siswa->user_id);
                                        $statusPagi = $absensiHarian ? $absensiHarian->status : 'belum_absen';
                                        // Default: hadir jika absen pagi hadir/terlambat, alpa jika tidak
                                        $defaultStatus = ($absensiHarian && in_array($absensiHarian->status, ['hadir', 'terlambat'])) ? 'hadir' : 'alpa';
                                    @endphp
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $siswa->nis }}</td>
                                        <td>{{ $siswa->user->name ?? '-' }}</td>
                                        <td>
                                            @if($statusPagi == 'hadir')
                                                <span class="badge bg-success">Hadir</span>
                                            @elseif($statusPagi == 'terlambat')
                                                <span class="badge bg-warning">Terlambat</span>
                                            @elseif($statusPagi == 'sakit')
                                                <span class="badge bg-warning">Sakit</span>
                                            @elseif($statusPagi == 'izin')
                                                <span class="badge bg-info">Izin</span>
                                            @elseif($statusPagi == 'alpa')
                                                <span class="badge bg-danger">Alpa</span>
                                            @else
                                                <span class="badge bg-secondary">Belum Absen</span>
                                            @endif
                                        </td>
                                        <td>
                                            <select class="form-select form-select-sm" name="absensi[{{ $siswa->id }}]" required>
                                                <option value="hadir" {{ $defaultStatus == 'hadir' ? 'selected' : '' }}>Hadir</option>
                                                <option value="terlambat">Terlambat</option>
                                                <option value="sakit" {{ $statusPagi == 'sakit' ? 'selected' : '' }}>Sakit</option>
                                                <option value="izin" {{ $statusPagi == 'izin' ? 'selected' : '' }}>Izin</option>
                                                <option value="alpa" {{ $defaultStatus == 'alpa' && !in_array($statusPagi, ['sakit', 'izin']) ? 'selected' : '' }}>Alpa</option>
                                            </select>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('guru.jurnal-mengajar.create', ['tanggal' => $tanggal]) }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>Batal
                        </a>
                        <button type="button" class="btn btn-primary" onclick="nextStep()">
                            Lanjut ke Jurnal <i class="fas fa-arrow-right ms-2"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 2: Jurnal Mengajar -->
        <div class="wizard-step" id="step-2" style="display: none;">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-book me-2"></i>Step 2: Jurnal Mengajar</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="materi_pembelajaran" class="form-label">
                            <strong>Materi Pembelajaran <span class="text-danger">*</span></strong>
                        </label>
                        <textarea class="form-control"
                                  id="materi_pembelajaran"
                                  name="materi_pembelajaran"
                                  rows="5"
                                  required
                                  placeholder="Tuliskan materi yang diajarkan pada pertemuan ini..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="keterangan" class="form-label">
                            <strong>Keterangan/Catatan</strong>
                        </label>
                        <textarea class="form-control"
                                  id="keterangan"
                                  name="keterangan"
                                  rows="3"
                                  placeholder="Catatan tambahan (opsional)..."></textarea>
                    </div>

                    <!-- Foto Bukti Section -->
                    <div class="mb-3">
                        <label class="form-label">
                            <strong>Foto Bukti Mengajar <span class="text-danger">*</span></strong>
                        </label>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <button type="button"
                                        class="btn btn-outline-primary w-100"
                                        onclick="openCamera()">
                                    <i class="fas fa-camera me-2"></i>Ambil Foto
                                </button>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="btn btn-outline-secondary w-100 mb-0">
                                    <i class="fas fa-upload me-2"></i>Upload Foto
                                    <input type="file"
                                           id="upload_foto"
                                           accept="image/*"
                                           style="display: none;"
                                           onchange="handleFileUpload(event)">
                                </label>
                            </div>
                        </div>

                        <!-- Preview Foto -->
                        <div id="preview_container" style="display: none;" class="mt-3">
                            <p class="text-muted small mb-2">Preview Foto:</p>
                            <img id="preview_foto"
                                 src=""
                                 alt="Preview"
                                 class="img-fluid rounded"
                                 style="max-height: 300px;">
                            <button type="button"
                                    class="btn btn-sm btn-danger mt-2"
                                    onclick="resetFoto()">
                                <i class="fas fa-times me-1"></i>Hapus Foto
                            </button>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <button type="button" class="btn btn-secondary" onclick="prevStep()">
                            <i class="fas fa-arrow-left me-2"></i>Kembali ke Absensi
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save me-2"></i>Simpan Jurnal
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Modal Kamera -->
<div class="modal fade" id="modalKamera" tabindex="-1" aria-labelledby="modalKameraLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalKameraLabel">Ambil Foto Bukti Mengajar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <video id="video" width="100%" autoplay style="border-radius: 8px;"></video>
                <canvas id="canvas" style="display: none;"></canvas>
                <div id="captured-image" style="display: none;">
                    <img id="captured-img" src="" alt="Captured" style="width: 100%; border-radius: 8px;">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" id="btn-capture" onclick="capturePhoto()">
                    <i class="fas fa-camera me-2"></i>Ambil Foto
                </button>
                <button type="button" class="btn btn-warning" id="btn-retake" style="display: none;" onclick="retakePhoto()">
                    <i class="fas fa-redo me-2"></i>Foto Ulang
                </button>
                <button type="button" class="btn btn-success" id="btn-use-photo" style="display: none;" onclick="usePhoto()">
                    <i class="fas fa-check me-2"></i>Gunakan Foto Ini
                </button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .step-indicator {
        position: relative;
    }

    .step-circle {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background-color: #e9ecef;
        color: #6c757d;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        font-weight: bold;
        margin: 0 auto 10px;
        transition: all 0.3s;
    }

    .step-indicator.active .step-circle {
        background-color: #0d6efd;
        color: white;
    }

    .step-indicator.completed .step-circle {
        background-color: #198754;
        color: white;
    }

    .step-label {
        font-size: 14px;
        font-weight: 500;
        color: #6c757d;
    }

    .step-indicator.active .step-label {
        color: #0d6efd;
        font-weight: 600;
    }

    .step-connector {
        height: 2px;
        background-color: #e9ecef;
        margin: 25px 20px 0;
    }

    .wizard-step {
        animation: fadeIn 0.3s;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endpush

@push('scripts')
<script>
    let currentStream = null;
    let capturedImageData = null;

    // Step navigation
    function nextStep() {
        document.getElementById('step-1').style.display = 'none';
        document.getElementById('step-2').style.display = 'block';

        // Safely update step indicators if they exist
        const indicator1 = document.getElementById('step-indicator-1');
        const indicator2 = document.getElementById('step-indicator-2');

        if (indicator1) {
            indicator1.classList.remove('active');
            indicator1.classList.add('completed');
        }
        if (indicator2) {
            indicator2.classList.add('active');
        }

        window.scrollTo(0, 0);
    }

    function prevStep() {
        document.getElementById('step-2').style.display = 'none';
        document.getElementById('step-1').style.display = 'block';

        // Safely update step indicators if they exist
        const indicator1 = document.getElementById('step-indicator-1');
        const indicator2 = document.getElementById('step-indicator-2');

        if (indicator2) {
            indicator2.classList.remove('active');
        }
        if (indicator1) {
            indicator1.classList.remove('completed');
            indicator1.classList.add('active');
        }

        window.scrollTo(0, 0);
    }

    // Camera functions
    function openCamera() {
        const modal = new bootstrap.Modal(document.getElementById('modalKamera'));
        modal.show();

        const video = document.getElementById('video');

        navigator.mediaDevices.getUserMedia({
            video: {
                facingMode: 'environment',
                width: { ideal: 1280 },
                height: { ideal: 720 }
            }
        })
        .then(stream => {
            currentStream = stream;
            video.srcObject = stream;
        })
        .catch(err => {
            console.error('Error accessing camera:', err);
            alert('Tidak dapat mengakses kamera. Pastikan Anda memberikan izin akses kamera.');
        });

        document.getElementById('modalKamera').addEventListener('hidden.bs.modal', function () {
            stopCamera();
        });
    }

    // Helper function to compress and resize image
    function compressImage(sourceCanvas, maxWidth = 1200, quality = 0.6) {
        const context = sourceCanvas.getContext('2d');
        let width = sourceCanvas.width;
        let height = sourceCanvas.height;

        // Calculate new dimensions
        if (width > maxWidth) {
            height = Math.round((height * maxWidth) / width);
            width = maxWidth;
        }

        // Create new canvas with target dimensions
        const compressCanvas = document.createElement('canvas');
        compressCanvas.width = width;
        compressCanvas.height = height;
        const compressContext = compressCanvas.getContext('2d');

        // Draw resized image
        compressContext.drawImage(sourceCanvas, 0, 0, width, height);

        // Try WebP first (better compression), fallback to JPEG
        let compressedData = compressCanvas.toDataURL('image/webp', quality);

        // If WebP not supported or larger than JPEG, use JPEG
        if (!compressedData.startsWith('data:image/webp')) {
            compressedData = compressCanvas.toDataURL('image/jpeg', quality);
        }

        return compressedData;
    }

    function capturePhoto() {
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const context = canvas.getContext('2d');

        // Capture at original resolution first
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        context.drawImage(video, 0, 0, canvas.width, canvas.height);

        // Compress and resize the captured image
        capturedImageData = compressImage(canvas, 1200, 0.6);

        document.getElementById('captured-img').src = capturedImageData;
        video.style.display = 'none';
        document.getElementById('captured-image').style.display = 'block';
        document.getElementById('btn-capture').style.display = 'none';
        document.getElementById('btn-use-photo').style.display = 'inline-block';
        document.getElementById('btn-retake').style.display = 'inline-block';

        stopCamera();
    }

    function retakePhoto() {
        const video = document.getElementById('video');
        video.style.display = 'block';
        document.getElementById('captured-image').style.display = 'none';
        document.getElementById('btn-capture').style.display = 'inline-block';
        document.getElementById('btn-use-photo').style.display = 'none';
        document.getElementById('btn-retake').style.display = 'none';
        capturedImageData = null;

        navigator.mediaDevices.getUserMedia({
            video: {
                facingMode: 'environment',
                width: { ideal: 1280 },
                height: { ideal: 720 }
            }
        })
        .then(stream => {
            currentStream = stream;
            video.srcObject = stream;
        });
    }

    function usePhoto() {
        if (capturedImageData) {
            document.getElementById('foto_bukti').value = capturedImageData;
            document.getElementById('preview_foto').src = capturedImageData;
            document.getElementById('preview_container').style.display = 'block';

            bootstrap.Modal.getInstance(document.getElementById('modalKamera')).hide();
        }
    }

    function stopCamera() {
        if (currentStream) {
            currentStream.getTracks().forEach(track => track.stop());
            currentStream = null;
        }
    }

    function handleFileUpload(event) {
        const file = event.target.files[0];
        if (file) {
            // Check if file is HEIC/HEIF - needs server-side conversion first
            const fileExtension = file.name.split('.').pop().toLowerCase();
            const isHEIC = fileExtension === 'heic' || fileExtension === 'heif';

            if (isHEIC) {
                // Show loading indicator with Bootstrap
                showLoadingMessage('Memproses foto HEIC...', 'Mohon tunggu, foto sedang dikonversi dan dikompress');

                // Convert HEIC to JPG via server, then compress
                convertAndCompressHEIC(file);
            } else {
                // For JPG, PNG, WebP - compress directly in browser
                compressImageFile(file);
            }
        }
    }

    function compressImageFile(file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = new Image();
            img.onload = function() {
                // Create canvas to draw and compress image
                const canvas = document.createElement('canvas');
                let width = img.width;
                let height = img.height;

                // Calculate new dimensions (max 1200px width)
                const maxWidth = 1200;
                if (width > maxWidth) {
                    height = Math.round((height * maxWidth) / width);
                    width = maxWidth;
                }

                canvas.width = width;
                canvas.height = height;
                const context = canvas.getContext('2d');
                context.drawImage(img, 0, 0, width, height);

                // Try WebP first for better compression
                let compressedData = canvas.toDataURL('image/webp', 0.6);

                // If WebP not supported, use JPEG
                if (!compressedData.startsWith('data:image/webp')) {
                    compressedData = canvas.toDataURL('image/jpeg', 0.6);
                }

                document.getElementById('foto_bukti').value = compressedData;
                document.getElementById('preview_foto').src = compressedData;
                document.getElementById('preview_container').style.display = 'block';
            };
            img.onerror = function() {
                alert('Gagal memuat foto. Pastikan format file didukung.');
            };
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }

    // Helper functions for loading messages
    function showLoadingMessage(title, message) {
        // Create loading overlay if not exists
        let overlay = document.getElementById('heic-loading-overlay');
        if (!overlay) {
            overlay = document.createElement('div');
            overlay.id = 'heic-loading-overlay';
            overlay.style.cssText = 'position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.7); z-index: 9999; display: flex; align-items: center; justify-content: center;';
            overlay.innerHTML = `
                <div style="background: white; padding: 30px; border-radius: 10px; text-align: center; max-width: 400px;">
                    <div class="spinner-border text-primary mb-3" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <h5 id="loading-title">${title}</h5>
                    <p id="loading-message">${message}</p>
                </div>
            `;
            document.body.appendChild(overlay);
        } else {
            overlay.style.display = 'flex';
            document.getElementById('loading-title').textContent = title;
            document.getElementById('loading-message').textContent = message;
        }
    }

    function hideLoadingMessage() {
        const overlay = document.getElementById('heic-loading-overlay');
        if (overlay) {
            overlay.style.display = 'none';
        }
    }

    function showErrorMessage(message) {
        hideLoadingMessage();
        alert(message);
    }

    function convertAndCompressHEIC(file) {
        // Create FormData to send HEIC file to server
        const formData = new FormData();
        formData.append('file', file);
        formData.append('_token', '{{ csrf_token() }}');

        // Send to server for HEIC -> JPG conversion
        fetch('{{ route("convert-heic") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.image) {
                // Server returned base64 JPG, now compress it client-side
                const img = new Image();
                img.onload = function() {
                    const canvas = document.createElement('canvas');
                    let width = img.width;
                    let height = img.height;

                    // Calculate new dimensions (max 1200px width)
                    const maxWidth = 1200;
                    if (width > maxWidth) {
                        height = Math.round((height * maxWidth) / width);
                        width = maxWidth;
                    }

                    canvas.width = width;
                    canvas.height = height;
                    const context = canvas.getContext('2d');
                    context.drawImage(img, 0, 0, width, height);

                    // Compress to WebP
                    let compressedData = canvas.toDataURL('image/webp', 0.6);
                    if (!compressedData.startsWith('data:image/webp')) {
                        compressedData = canvas.toDataURL('image/jpeg', 0.6);
                    }

                    document.getElementById('foto_bukti').value = compressedData;
                    document.getElementById('preview_foto').src = compressedData;
                    document.getElementById('preview_container').style.display = 'block';

                    hideLoadingMessage();
                };
                img.onerror = function() {
                    showErrorMessage('Gagal memproses foto hasil konversi.');
                };
                img.src = data.image;
            } else {
                showErrorMessage(data.message || 'Gagal mengkonversi foto HEIC.');
            }
        })
        .catch(error => {
            console.error('Error converting HEIC:', error);
            showErrorMessage('Terjadi kesalahan saat mengkonversi foto HEIC.');
        });
    }

    function resetFoto() {
        document.getElementById('foto_bukti').value = '';
        document.getElementById('preview_foto').src = '';
        document.getElementById('preview_container').style.display = 'none';
        document.getElementById('upload_foto').value = '';
    }

    // Form validation
    document.getElementById('formWizard').addEventListener('submit', function(e) {
        const fotoBukti = document.getElementById('foto_bukti').value;

        if (!fotoBukti) {
            e.preventDefault();
            alert('Silakan ambil atau upload foto bukti mengajar terlebih dahulu!');
            return false;
        }
    });
</script>
@endpush

@endsection
