@extends('layouts.app')

@section('title', 'Isi Jurnal Mengajar')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('guru.jurnal-mengajar.index') }}">Jurnal Mengajar</a></li>
                    <li class="breadcrumb-item active">Isi Jurnal Baru</li>
                </ol>
            </nav>
            <h2 class="mb-1"><i class="fas fa-book-medical text-primary me-2"></i>Isi Jurnal Mengajar</h2>
            <p class="text-muted mb-0">Catat kegiatan mengajar Anda hari ini</p>
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

    <!-- Pilih Tanggal -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('guru.jurnal-mengajar.create') }}" class="row g-3">
                <div class="col-md-6">
                    <label for="tanggal" class="form-label"><strong>Pilih Tanggal</strong></label>
                    <input type="date"
                           class="form-control form-control-lg"
                           id="tanggal"
                           name="tanggal"
                           value="{{ $tanggal }}"
                           max="{{ date('Y-m-d') }}"
                           onchange="this.form.submit()">
                    <small class="text-muted">Hari: <strong>{{ ucfirst($hari) }}</strong></small>
                </div>
            </form>
        </div>
    </div>

    @if($jadwals->count() > 0)
        <div class="row">
            @foreach($jadwals as $jadwal)
                <div class="col-md-6 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="fas fa-clock me-2"></i>Jam ke-{{ $jadwal->jam_ke }}
                                </h5>
                                <span class="badge bg-light text-dark">
                                    {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} -
                                    {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="text-muted small">Kelas</label>
                                <h6><i class="fas fa-door-open me-2 text-primary"></i>{{ $jadwal->kelas->nama_kelas }}</h6>
                            </div>
                            <div class="mb-3">
                                <label class="text-muted small">Mata Pelajaran</label>
                                <h6><i class="fas fa-book me-2 text-success"></i>{{ $jadwal->mataPelajaran->nama_mapel }}</h6>
                            </div>

                            <form action="{{ route('guru.jurnal-mengajar.store') }}" method="POST" id="formJurnal_{{ $jadwal->id }}">
                                @csrf
                                <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                                <input type="hidden" name="kelas_id" value="{{ $jadwal->kelas_id }}">
                                <input type="hidden" name="jam_ke" value="{{ $jadwal->jam_ke }}">
                                <input type="hidden" name="jam_mulai" value="{{ $jadwal->jam_mulai }}">
                                <input type="hidden" name="jam_selesai" value="{{ $jadwal->jam_selesai }}">
                                <input type="hidden" name="mata_pelajaran_id" value="{{ $jadwal->mata_pelajaran_id }}">
                                <input type="hidden" name="foto_bukti" id="foto_bukti_{{ $jadwal->id }}">

                                <div class="mb-3">
                                    <label for="materi_{{ $jadwal->id }}" class="form-label">
                                        <strong>Materi Pembelajaran <span class="text-danger">*</span></strong>
                                    </label>
                                    <textarea class="form-control"
                                              id="materi_{{ $jadwal->id }}"
                                              name="materi_pembelajaran"
                                              rows="4"
                                              required
                                              placeholder="Tuliskan materi yang diajarkan..."></textarea>
                                </div>

                                <div class="mb-3">
                                    <label for="keterangan_{{ $jadwal->id }}" class="form-label">
                                        <strong>Keterangan/Catatan</strong>
                                    </label>
                                    <textarea class="form-control"
                                              id="keterangan_{{ $jadwal->id }}"
                                              name="keterangan"
                                              rows="2"
                                              placeholder="Catatan tambahan (opsional)..."></textarea>
                                </div>

                                <!-- Foto Bukti Section -->
                                <div class="mb-3">
                                    <label class="form-label">
                                        <strong>Foto Bukti Mengajar <span class="text-danger">*</span></strong>
                                    </label>
                                    <div class="row">
                                        <div class="col-6">
                                            <button type="button" class="btn btn-outline-primary w-100" onclick="openCamera({{ $jadwal->id }})">
                                                <i class="fas fa-camera me-2"></i>Ambil Foto
                                            </button>
                                        </div>
                                        <div class="col-6">
                                            <label for="upload_foto_{{ $jadwal->id }}" class="btn btn-outline-secondary w-100 mb-0">
                                                <i class="fas fa-upload me-2"></i>Upload Foto
                                            </label>
                                            <input type="file"
                                                   id="upload_foto_{{ $jadwal->id }}"
                                                   accept="image/*"
                                                   style="display: none;"
                                                   onchange="handleFileUpload(event, {{ $jadwal->id }})">
                                        </div>
                                    </div>
                                    <div id="preview_container_{{ $jadwal->id }}" class="mt-3" style="display: none;">
                                        <img id="preview_foto_{{ $jadwal->id }}"
                                             src=""
                                             alt="Preview"
                                             class="img-fluid rounded"
                                             style="max-height: 200px;">
                                        <button type="button"
                                                class="btn btn-sm btn-danger mt-2"
                                                onclick="resetFoto({{ $jadwal->id }})">
                                            <i class="fas fa-times me-1"></i>Hapus Foto
                                        </button>
                                    </div>
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Simpan Jurnal
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Tidak Ada Jadwal Mengajar</h5>
                <p class="text-muted">Anda tidak memiliki jadwal mengajar pada hari <strong>{{ ucfirst($hari) }}</strong>.</p>
                <a href="{{ route('guru.jurnal-mengajar.create') }}" class="btn btn-outline-primary">
                    <i class="fas fa-calendar-alt me-2"></i>Pilih Tanggal Lain
                </a>
            </div>
        </div>
    @endif
</div>

<!-- Modal Kamera -->
<div class="modal fade" id="modalKamera" tabindex="-1" aria-labelledby="modalKameraLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalKameraLabel">Ambil Foto Bukti Mengajar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <video id="video" width="100%" autoplay style="max-height: 400px; border-radius: 8px;"></video>
                <canvas id="canvas" style="display: none;"></canvas>
                <div id="captured-image" style="display: none;">
                    <img id="captured-img" src="" alt="Captured" style="max-width: 100%; border-radius: 8px;">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btn-capture" onclick="capturePhoto()">
                    <i class="fas fa-camera me-2"></i>Ambil Foto
                </button>
                <button type="button" class="btn btn-success" id="btn-use-photo" style="display: none;" onclick="usePhoto()">
                    <i class="fas fa-check me-2"></i>Gunakan Foto
                </button>
                <button type="button" class="btn btn-warning" id="btn-retake" style="display: none;" onclick="retakePhoto()">
                    <i class="fas fa-redo me-2"></i>Ambil Ulang
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let currentStream = null;
    let currentJadwalId = null;
    let capturedImageData = null;

    // Open camera
    function openCamera(jadwalId) {
        currentJadwalId = jadwalId;
        const modal = new bootstrap.Modal(document.getElementById('modalKamera'));
        modal.show();

        const video = document.getElementById('video');

        // Request camera access
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
            video.style.display = 'block';
            document.getElementById('captured-image').style.display = 'none';
            document.getElementById('btn-capture').style.display = 'inline-block';
            document.getElementById('btn-use-photo').style.display = 'none';
            document.getElementById('btn-retake').style.display = 'none';
        })
        .catch(error => {
            console.error('Error accessing camera:', error);
            alert('Tidak dapat mengakses kamera. Pastikan Anda memberikan izin akses kamera.');
        });

        // Stop camera when modal closed
        document.getElementById('modalKamera').addEventListener('hidden.bs.modal', function () {
            stopCamera();
        });
    }

    // Capture photo
    function capturePhoto() {
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const context = canvas.getContext('2d');

        // Set canvas size
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;

        // Draw video frame to canvas
        context.drawImage(video, 0, 0, canvas.width, canvas.height);

        // Convert to base64
        capturedImageData = canvas.toDataURL('image/jpeg', 0.8);

        // Show captured image
        document.getElementById('captured-img').src = capturedImageData;
        video.style.display = 'none';
        document.getElementById('captured-image').style.display = 'block';
        document.getElementById('btn-capture').style.display = 'none';
        document.getElementById('btn-use-photo').style.display = 'inline-block';
        document.getElementById('btn-retake').style.display = 'inline-block';

        // Stop camera
        stopCamera();
    }

    // Retake photo
    function retakePhoto() {
        const video = document.getElementById('video');
        video.style.display = 'block';
        document.getElementById('captured-image').style.display = 'none';
        document.getElementById('btn-capture').style.display = 'inline-block';
        document.getElementById('btn-use-photo').style.display = 'none';
        document.getElementById('btn-retake').style.display = 'none';
        capturedImageData = null;

        // Restart camera
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

    // Use photo
    function usePhoto() {
        if (capturedImageData && currentJadwalId) {
            // Set hidden input value
            document.getElementById('foto_bukti_' + currentJadwalId).value = capturedImageData;

            // Show preview
            document.getElementById('preview_foto_' + currentJadwalId).src = capturedImageData;
            document.getElementById('preview_container_' + currentJadwalId).style.display = 'block';

            // Close modal
            bootstrap.Modal.getInstance(document.getElementById('modalKamera')).hide();
        }
    }

    // Stop camera
    function stopCamera() {
        if (currentStream) {
            currentStream.getTracks().forEach(track => track.stop());
            currentStream = null;
        }
    }

    // Handle file upload
    function handleFileUpload(event, jadwalId) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const imageData = e.target.result;

                // Set hidden input value
                document.getElementById('foto_bukti_' + jadwalId).value = imageData;

                // Show preview
                document.getElementById('preview_foto_' + jadwalId).src = imageData;
                document.getElementById('preview_container_' + jadwalId).style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    }

    // Reset foto
    function resetFoto(jadwalId) {
        document.getElementById('foto_bukti_' + jadwalId).value = '';
        document.getElementById('preview_foto_' + jadwalId).src = '';
        document.getElementById('preview_container_' + jadwalId).style.display = 'none';
        document.getElementById('upload_foto_' + jadwalId).value = '';
    }

    // Validate form before submit
    document.querySelectorAll('form[id^="formJurnal_"]').forEach(form => {
        form.addEventListener('submit', function(e) {
            const jadwalId = this.id.replace('formJurnal_', '');
            const fotoBukti = document.getElementById('foto_bukti_' + jadwalId).value;

            if (!fotoBukti) {
                e.preventDefault();
                alert('Silakan ambil atau upload foto bukti mengajar terlebih dahulu!');
                return false;
            }
        });
    });
</script>
@endpush
@endsection
