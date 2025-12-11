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

        document.getElementById('step-indicator-1').classList.remove('active');
        document.getElementById('step-indicator-1').classList.add('completed');
        document.getElementById('step-indicator-2').classList.add('active');

        window.scrollTo(0, 0);
    }

    function prevStep() {
        document.getElementById('step-2').style.display = 'none';
        document.getElementById('step-1').style.display = 'block';

        document.getElementById('step-indicator-2').classList.remove('active');
        document.getElementById('step-indicator-1').classList.remove('completed');
        document.getElementById('step-indicator-1').classList.add('active');

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

    function capturePhoto() {
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const context = canvas.getContext('2d');

        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        context.drawImage(video, 0, 0, canvas.width, canvas.height);

        capturedImageData = canvas.toDataURL('image/jpeg', 0.8);

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
            const reader = new FileReader();
            reader.onload = function(e) {
                const imageData = e.target.result;
                document.getElementById('foto_bukti').value = imageData;
                document.getElementById('preview_foto').src = imageData;
                document.getElementById('preview_container').style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
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
