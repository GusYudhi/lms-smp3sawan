@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card card-modern border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                        <div>
                            <h1 class="h3 text-high-contrast fw-bold mb-2">
                                <img src="{{ asset('assets/image/logo-sekolah-smpn3sawan.webp') }}" alt="Logo SMPN 3 SAWAN" style="width:40px; height:40px; object-fit:cover;" class="me-2 rounded-circle">
                                Edit Data Sekolah
                            </h1>
                            <p class="text-subtle mb-0">Perbarui informasi profil dan data sekolah</p>
                        </div>
                        <div class="flex-shrink-0">
                            <a href="{{ route('school.profile') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Kembali ke Profil
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
    @if($errors->any())
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                    <div class="d-flex align-items-start">
                        <i class="fas fa-exclamation-triangle text-danger me-2 mt-1 flex-shrink-0"></i>
                        <div class="flex-grow-1">
                            <strong>Terdapat kesalahan input:</strong>
                            <ul class="list-unstyled mb-0 mt-2">
                                @foreach($errors->all() as $error)
                                    <li class="mb-1">• {{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if(session('success'))
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-check-circle text-success me-2 fs-5"></i>
                        <div class="flex-grow-1">{{ session('success') }}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Main Edit Form -->
    <div class="row">
        <div class="col-12">
            <form action="{{ route('school.update') }}" method="POST" class="school-edit-form" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- School Identity Section -->
                <div class="card card-stats mb-4 hover-card">
                    <div class="card-header bg-light border-bottom-0 py-3">
                        <h5 class="card-title mb-0 text-high-contrast fw-semibold">
                            <i class="fas fa-id-card text-primary me-2"></i>Identitas Sekolah
                        </h5>
                        <p class="text-subtle mb-0 mt-1">Informasi dasar dan identitas sekolah</p>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label fw-medium text-high-contrast">
                                        Nama Sekolah <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                           id="name"
                                           name="name"
                                           class="form-control @error('name') is-invalid @enderror"
                                           value="{{ old('name', $schoolData['name'] ?? '') }}"
                                           required
                                           placeholder="Contoh: SMPN 3 SAWAN">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="kepala_sekolah" class="form-label fw-medium text-high-contrast">
                                        Kepala Sekolah <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                           id="kepala_sekolah"
                                           name="kepala_sekolah"
                                           class="form-control @error('kepala_sekolah') is-invalid @enderror"
                                           value="{{ old('kepala_sekolah', $schoolData['kepala_sekolah'] ?? '') }}"
                                           required
                                           placeholder="Contoh: Drs. I Made Sutrisna, M.Pd.">
                                    @error('kepala_sekolah')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="npsn" class="form-label fw-medium text-high-contrast">
                                        NPSN <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                           id="npsn"
                                           name="npsn"
                                           class="form-control @error('npsn') is-invalid @enderror"
                                           value="{{ old('npsn', $schoolData['npsn'] ?? '') }}"
                                           required
                                           placeholder="Contoh: 50100123">
                                    @error('npsn')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="tahun_berdiri" class="form-label fw-medium text-high-contrast">
                                        Tahun Berdiri <span class="text-danger">*</span>
                                    </label>
                                    <input type="number"
                                           id="tahun_berdiri"
                                           name="tahun_berdiri"
                                           class="form-control @error('tahun_berdiri') is-invalid @enderror"
                                           value="{{ old('tahun_berdiri', $schoolData['tahun_berdiri'] ?? '') }}"
                                           required
                                           min="1900"
                                           max="{{ date('Y') }}"
                                           placeholder="Contoh: 1985">
                                    @error('tahun_berdiri')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="akreditasi" class="form-label fw-medium text-high-contrast">
                                        Akreditasi <span class="text-danger">*</span>
                                    </label>
                                    <select id="akreditasi"
                                            name="akreditasi"
                                            class="form-select @error('akreditasi') is-invalid @enderror"
                                            required>
                                        <option value="">Pilih Akreditasi</option>
                                        <option value="A" {{ old('akreditasi', $schoolData['akreditasi'] ?? '') == 'A' ? 'selected' : '' }}>
                                            A (Sangat Baik)
                                        </option>
                                        <option value="B" {{ old('akreditasi', $schoolData['akreditasi'] ?? '') == 'B' ? 'selected' : '' }}>
                                            B (Baik)
                                        </option>
                                        <option value="C" {{ old('akreditasi', $schoolData['akreditasi'] ?? '') == 'C' ? 'selected' : '' }}>
                                            C (Cukup)
                                        </option>
                                    </select>
                                    @error('akreditasi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="website" class="form-label fw-medium text-high-contrast">Website</label>
                                    <input type="url"
                                           id="website"
                                           name="website"
                                           class="form-control @error('website') is-invalid @enderror"
                                           value="{{ old('website', $schoolData['website'] ?? '') }}"
                                           placeholder="Contoh: www.smpn3sawan.sch.id">
                                    <div class="form-text">Opsional - kosongkan jika tidak ada</div>
                                    @error('website')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Vision Section -->
                <div class="card card-stats mb-4 hover-card">
                    <div class="card-header bg-light border-bottom-0 py-3">
                        <h5 class="card-title mb-0 text-high-contrast fw-semibold">
                            <i class="fas fa-eye text-primary me-2"></i>Visi Sekolah
                        </h5>
                        <p class="text-subtle mb-0 mt-1">Visi dan pandangan masa depan sekolah</p>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label for="visi" class="form-label fw-medium text-high-contrast">
                                Visi Sekolah <span class="text-danger">*</span>
                            </label>
                            <textarea id="visi"
                                      name="visi"
                                      class="form-control @error('visi') is-invalid @enderror"
                                      rows="4"
                                      required
                                      placeholder="Tuliskan visi sekolah yang inspiratif dan motivatif">{{ old('visi', $schoolData['visi'] ?? '') }}</textarea>
                            <div class="form-text">Tulis visi sekolah yang jelas, inspiratif, dan mencerminkan tujuan pendidikan</div>
                            @error('visi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Mission Section -->
                <div class="card card-stats mb-4 hover-card">
                    <div class="card-header bg-light border-bottom-0 py-3">
                        <h5 class="card-title mb-0 text-high-contrast fw-semibold">
                            <i class="fas fa-bullseye text-primary me-2"></i>Misi Sekolah
                        </h5>
                        <p class="text-subtle mb-0 mt-1">Langkah-langkah konkret untuk mencapai visi</p>
                    </div>
                    <div class="card-body p-4">
                        <div id="misi-container">
                            @php
                                $misiList = old('misi', $schoolData['misi'] ?? [
                                    'Menyelenggarakan pendidikan yang berkualitas dan berstandar nasional',
                                    'Mengembangkan potensi peserta didik secara optimal',
                                    'Membangun karakter yang berakhlak mulia dan berbudi pekerti luhur'
                                ]);
                                if (is_string($misiList)) {
                                    $misiList = json_decode($misiList, true) ?? [$misiList];
                                }
                            @endphp

                            @foreach($misiList as $index => $misi)
                                <div class="misi-item mb-3" data-index="{{ $index }}">
                                    <label class="form-label fw-medium text-high-contrast">
                                        Misi {{ $index + 1 }} <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <textarea name="misi[]"
                                                  class="form-control @error("misi.{$index}") is-invalid @enderror"
                                                  rows="2"
                                                  required
                                                  placeholder="Tuliskan poin misi ke-{{ $index + 1 }}">{{ $misi }}</textarea>
                                        @if($index > 0)
                                            <button type="button" class="btn btn-outline-danger" onclick="removeMisi(this)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                    @error("misi.{$index}")
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endforeach
                        </div>
                        <button type="button" class="btn btn-outline-primary" onclick="addMisi()">
                            <i class="fas fa-plus me-2"></i>Tambah Misi Baru
                        </button>
                    </div>
                </div>

                <!-- Contact Information Section -->
                <div class="card card-stats mb-4 hover-card">
                    <div class="card-header bg-light border-bottom-0 py-3">
                        <h5 class="card-title mb-0 text-high-contrast fw-semibold">
                            <i class="fas fa-address-book text-primary me-2"></i>Informasi Kontak
                        </h5>
                        <p class="text-subtle mb-0 mt-1">Alamat dan informasi kontak sekolah</p>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="alamat" class="form-label fw-medium text-high-contrast">
                                        Alamat Lengkap <span class="text-danger">*</span>
                                    </label>
                                    <textarea id="alamat"
                                              name="alamat"
                                              class="form-control @error('alamat') is-invalid @enderror"
                                              rows="3"
                                              required
                                              placeholder="Jalan, Desa/Kelurahan, Kecamatan, Kabupaten/Kota, Provinsi, Kode Pos">{{ old('alamat', $schoolData['alamat'] ?? '') }}</textarea>
                                    @error('alamat')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="telepon" class="form-label fw-medium text-high-contrast">
                                        Nomor Telepon <span class="text-danger">*</span>
                                    </label>
                                    <input type="tel"
                                           id="telepon"
                                           name="telepon"
                                           class="form-control @error('telepon') is-invalid @enderror"
                                           value="{{ old('telepon', $schoolData['telepon'] ?? '') }}"
                                           required
                                           placeholder="Contoh: (0362) 123456">
                                    @error('telepon')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label fw-medium text-high-contrast">
                                        Email <span class="text-danger">*</span>
                                    </label>
                                    <input type="email"
                                           id="email"
                                           name="email"
                                           class="form-control @error('email') is-invalid @enderror"
                                           value="{{ old('email', $schoolData['email'] ?? '') }}"
                                           required
                                           placeholder="Contoh: admin@smpn3sawan.sch.id">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Maps Coordinates Section -->
                <div class="card card-stats mb-4 hover-card">
                    <div class="card-header bg-light border-bottom-0 py-3">
                        <h5 class="card-title mb-0 text-high-contrast fw-semibold">
                            <i class="fas fa-map-marker-alt text-primary me-2"></i>Lokasi dan Koordinat
                        </h5>
                        <p class="text-subtle mb-0 mt-1">Koordinat geografis untuk menampilkan lokasi di peta</p>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4 mb-4">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="maps_latitude" class="form-label fw-medium text-high-contrast">
                                        Latitude <span class="text-danger">*</span>
                                    </label>
                                    <input type="number"
                                           id="maps_latitude"
                                           name="maps_latitude"
                                           class="form-control @error('maps_latitude') is-invalid @enderror"
                                           value="{{ old('maps_latitude', $schoolData['maps_latitude'] ?? '') }}"
                                           step="any"
                                           required
                                           placeholder="Contoh: -8.1542">
                                    <div class="form-text">Koordinat lintang (latitude) dalam format desimal</div>
                                    @error('maps_latitude')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="maps_longitude" class="form-label fw-medium text-high-contrast">
                                        Longitude <span class="text-danger">*</span>
                                    </label>
                                    <input type="number"
                                           id="maps_longitude"
                                           name="maps_longitude"
                                           class="form-control @error('maps_longitude') is-invalid @enderror"
                                           value="{{ old('maps_longitude', $schoolData['maps_longitude'] ?? '') }}"
                                           step="any"
                                           required
                                           placeholder="Contoh: 115.0956">
                                    <div class="form-text">Koordinat bujur (longitude) dalam format desimal</div>
                                    @error('maps_longitude')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-info-circle text-info me-3 mt-1 flex-shrink-0"></i>
                                <div>
                                    <h6 class="fw-bold mb-2">Cara mendapatkan koordinat:</h6>
                                    <ol class="mb-3">
                                        <li>Buka <strong>Google Maps</strong> di browser</li>
                                        <li>Cari lokasi sekolah atau klik langsung di peta</li>
                                        <li>Klik kanan pada titik lokasi</li>
                                        <li>Pilih koordinat yang muncul (contoh: -8.1542, 115.0956)</li>
                                        <li>Salin angka pertama ke field Latitude, angka kedua ke Longitude</li>
                                    </ol>
                                    <a href="https://maps.google.com" target="_blank" class="btn btn-outline-info btn-sm">
                                        <i class="fas fa-external-link-alt me-2"></i>Buka Google Maps
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="card card-stats">
                    <div class="card-body p-4">
                        <div class="d-flex flex-column flex-md-row gap-3 justify-content-md-between align-items-center">
                            <div class="d-flex flex-column flex-sm-row gap-2 w-100 w-md-auto">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-save me-2"></i>Simpan Perubahan
                                </button>
                                <button type="reset" class="btn btn-outline-secondary btn-lg">
                                    <i class="fas fa-undo me-2"></i>Reset Form
                                </button>
                            </div>
                            <div class="flex-shrink-0">
                                <a href="{{ route('school.profile') }}" class="btn btn-secondary btn-lg">
                                    <i class="fas fa-times me-2"></i>Batal
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>


<script>
// Mission Management Functions
function addMisi() {
    const container = document.getElementById('misi-container');
    const misiCount = container.children.length + 1;

    const misiItem = document.createElement('div');
    misiItem.className = 'misi-item mb-3';
    misiItem.setAttribute('data-index', misiCount - 1);

    misiItem.innerHTML = `
        <label class="form-label fw-medium text-high-contrast">Misi ${misiCount} <span class="text-danger">*</span></label>
        <div class="input-group">
            <textarea name="misi[]"
                      class="form-control"
                      rows="2"
                      required
                      placeholder="Tuliskan poin misi ke-${misiCount}"></textarea>
            <button type="button" class="btn btn-outline-danger" onclick="removeMisi(this)">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;

    container.appendChild(misiItem);
    updateMisiLabels();

    // Focus on new textarea
    const newTextarea = misiItem.querySelector('textarea');
    newTextarea.focus();
}

function removeMisi(button) {
    const misiItem = button.closest('.misi-item');
    const container = document.getElementById('misi-container');

    // Don't allow removal if only one item remains
    if (container.children.length <= 1) {
        alert('Minimal harus ada satu misi sekolah');
        return;
    }

    misiItem.remove();
    updateMisiLabels();
}

function updateMisiLabels() {
    const misiItems = document.querySelectorAll('.misi-item');
    misiItems.forEach((item, index) => {
        const label = item.querySelector('.form-label');
        label.innerHTML = `Misi ${index + 1} <span class="text-danger">*</span>`;

        const textarea = item.querySelector('textarea');
        textarea.placeholder = `Tuliskan poin misi ke-${index + 1}`;

        item.setAttribute('data-index', index);
    });
}

// Form Enhancement Functions
document.addEventListener('DOMContentLoaded', function() {
    // Auto-resize textareas
    const textareas = document.querySelectorAll('textarea');
    textareas.forEach(textarea => {
        autoResize(textarea);
        textarea.addEventListener('input', () => autoResize(textarea));
    });

    // Form validation
    const form = document.querySelector('.school-edit-form');
    form.addEventListener('submit', function(e) {
        if (!validateForm()) {
            e.preventDefault();
        }
    });

    // Auto-hide alerts
    setTimeout(() => {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            alert.style.transition = 'opacity 0.5s ease';
            alert.style.opacity = '0';
            setTimeout(() => {
                if (alert.parentNode) {
                    alert.style.display = 'none';
                }
            }, 500);
        });
    }, 8000);

    // Coordinate validation
    const latInput = document.getElementById('maps_latitude');
    const lngInput = document.getElementById('maps_longitude');

    latInput.addEventListener('input', validateCoordinates);
    lngInput.addEventListener('input', validateCoordinates);
});

function autoResize(textarea) {
    textarea.style.height = 'auto';
    textarea.style.height = textarea.scrollHeight + 'px';
}

function validateForm() {
    let isValid = true;
    const requiredFields = document.querySelectorAll('[required]');

    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.classList.add('is-invalid');
            isValid = false;
        } else {
            field.classList.remove('is-invalid');
        }
    });

    // Validate coordinates
    const lat = document.getElementById('maps_latitude').value;
    const lng = document.getElementById('maps_longitude').value;

    if (lat && (lat < -90 || lat > 90)) {
        document.getElementById('maps_latitude').classList.add('is-invalid');
        isValid = false;
    }

    if (lng && (lng < -180 || lng > 180)) {
        document.getElementById('maps_longitude').classList.add('is-invalid');
        isValid = false;
    }

    return isValid;
}

function validateCoordinates() {
    const lat = parseFloat(document.getElementById('maps_latitude').value);
    const lng = parseFloat(document.getElementById('maps_longitude').value);

    const latInput = document.getElementById('maps_latitude');
    const lngInput = document.getElementById('maps_longitude');

    if (isNaN(lat) || lat < -90 || lat > 90) {
        latInput.classList.add('is-invalid');
    } else {
        latInput.classList.remove('is-invalid');
    }

    if (isNaN(lng) || lng < -180 || lng > 180) {
        lngInput.classList.add('is-invalid');
    } else {
        lngInput.classList.remove('is-invalid');
    }
}

// Loading state for form submission
document.querySelector('.school-edit-form').addEventListener('submit', function() {
    const submitBtn = document.querySelector('.btn-primary');
    const originalText = submitBtn.innerHTML;

    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';
    submitBtn.disabled = true;

    // Re-enable after 3 seconds in case of error
    setTimeout(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }, 3000);
});
</script>
@endsection
