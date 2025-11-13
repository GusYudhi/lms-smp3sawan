@extends('layouts.app')

@section('content')
<div class="school-edit-management">
    <!-- Page Header -->
    <div class="page-header">
        <div class="header-content">
            <h1><i class="fas fa-school"></i> Edit Data Sekolah</h1>
            <p class="page-subtitle">Perbarui informasi profil dan data sekolah</p>
        </div>
        <div class="header-actions">
            <a href="{{ route('school.profile') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali ke Profil
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    @if($errors->any())
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle"></i>
            <strong>Terdapat kesalahan input:</strong>
            <ul class="error-list">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="alert-close" onclick="this.parentElement.style.display='none';">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
            <button type="button" class="alert-close" onclick="this.parentElement.style.display='none';">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    <!-- Main Edit Form -->
    <div class="edit-form-container">
        <form action="{{ route('school.update') }}" method="POST" class="school-edit-form" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- School Identity Section -->
            <div class="form-section">
                <div class="section-header">
                    <h3><i class="fas fa-id-card"></i> Identitas Sekolah</h3>
                    <p class="section-description">Informasi dasar dan identitas sekolah</p>
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="name" class="form-label required">Nama Sekolah</label>
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

                    <div class="form-group">
                        <label for="kepala_sekolah" class="form-label required">Kepala Sekolah</label>
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

                    <div class="form-group">
                        <label for="npsn" class="form-label required">NPSN</label>
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

                    <div class="form-group">
                        <label for="tahun_berdiri" class="form-label required">Tahun Berdiri</label>
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

                    <div class="form-group">
                        <label for="akreditasi" class="form-label required">Akreditasi</label>
                        <select id="akreditasi"
                                name="akreditasi"
                                class="form-control @error('akreditasi') is-invalid @enderror"
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

                    <div class="form-group">
                        <label for="website" class="form-label">Website</label>
                        <input type="url"
                               id="website"
                               name="website"
                               class="form-control @error('website') is-invalid @enderror"
                               value="{{ old('website', $schoolData['website'] ?? '') }}"
                               placeholder="Contoh: www.smpn3sawan.sch.id">
                        <small class="form-text">Opsional - kosongkan jika tidak ada</small>
                        @error('website')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Vision Section -->
            <div class="form-section">
                <div class="section-header">
                    <h3><i class="fas fa-eye"></i> Visi Sekolah</h3>
                    <p class="section-description">Visi dan pandangan masa depan sekolah</p>
                </div>
                <div class="form-group">
                    <label for="visi" class="form-label required">Visi Sekolah</label>
                    <textarea id="visi"
                              name="visi"
                              class="form-control @error('visi') is-invalid @enderror"
                              rows="4"
                              required
                              placeholder="Tuliskan visi sekolah yang inspiratif dan motivatif">{{ old('visi', $schoolData['visi'] ?? '') }}</textarea>
                    <small class="form-text">Tulis visi sekolah yang jelas, inspiratif, dan mencerminkan tujuan pendidikan</small>
                    @error('visi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Mission Section -->
            <div class="form-section">
                <div class="section-header">
                    <h3><i class="fas fa-bullseye"></i> Misi Sekolah</h3>
                    <p class="section-description">Langkah-langkah konkret untuk mencapai visi</p>
                </div>
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
                        <div class="form-group misi-item" data-index="{{ $index }}">
                            <label class="form-label required">Misi {{ $index + 1 }}</label>
                            <div class="misi-input-group">
                                <textarea name="misi[]"
                                          class="form-control @error("misi.{$index}") is-invalid @enderror"
                                          rows="2"
                                          required
                                          placeholder="Tuliskan poin misi ke-{{ $index + 1 }}">{{ $misi }}</textarea>
                                @if($index > 0)
                                    <button type="button" class="btn btn-danger btn-remove-misi" onclick="removeMisi(this)">
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
                <button type="button" class="btn btn-outline-primary btn-add-misi" onclick="addMisi()">
                    <i class="fas fa-plus"></i> Tambah Misi Baru
                </button>
            </div>

            <!-- Contact Information Section -->
            <div class="form-section">
                <div class="section-header">
                    <h3><i class="fas fa-address-book"></i> Informasi Kontak</h3>
                    <p class="section-description">Alamat dan informasi kontak sekolah</p>
                </div>
                <div class="form-grid">
                    <div class="form-group full-width">
                        <label for="alamat" class="form-label required">Alamat Lengkap</label>
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

                    <div class="form-group">
                        <label for="telepon" class="form-label required">Nomor Telepon</label>
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

                    <div class="form-group">
                        <label for="email" class="form-label required">Email</label>
                        <input type="email"
                               id="email"
                               name="email"
                               class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email', $schoolData['email'] ?? '') }}"
                               required
                               placeholder="Contoh: info@smpn3sawan.sch.id">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Maps Coordinates Section -->
            <div class="form-section">
                <div class="section-header">
                    <h3><i class="fas fa-map-marker-alt"></i> Lokasi dan Koordinat</h3>
                    <p class="section-description">Koordinat geografis untuk menampilkan lokasi di peta</p>
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="maps_latitude" class="form-label required">Latitude</label>
                        <input type="number"
                               id="maps_latitude"
                               name="maps_latitude"
                               class="form-control @error('maps_latitude') is-invalid @enderror"
                               value="{{ old('maps_latitude', $schoolData['maps_latitude'] ?? '') }}"
                               step="any"
                               required
                               placeholder="Contoh: -8.1542">
                        <small class="form-text">Koordinat lintang (latitude) dalam format desimal</small>
                        @error('maps_latitude')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="maps_longitude" class="form-label required">Longitude</label>
                        <input type="number"
                               id="maps_longitude"
                               name="maps_longitude"
                               class="form-control @error('maps_longitude') is-invalid @enderror"
                               value="{{ old('maps_longitude', $schoolData['maps_longitude'] ?? '') }}"
                               step="any"
                               required
                               placeholder="Contoh: 115.0956">
                        <small class="form-text">Koordinat bujur (longitude) dalam format desimal</small>
                        @error('maps_longitude')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="coordinate-helper">
                    <div class="helper-card">
                        <div class="helper-icon">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <div class="helper-content">
                            <h4>Cara mendapatkan koordinat:</h4>
                            <ol>
                                <li>Buka <strong>Google Maps</strong> di browser</li>
                                <li>Cari lokasi sekolah atau klik langsung di peta</li>
                                <li>Klik kanan pada titik lokasi</li>
                                <li>Pilih koordinat yang muncul (contoh: -8.1542, 115.0956)</li>
                                <li>Salin angka pertama ke field Latitude, angka kedua ke Longitude</li>
                            </ol>
                            <a href="https://maps.google.com" target="_blank" class="helper-link">
                                <i class="fas fa-external-link-alt"></i> Buka Google Maps
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <button type="submit" class="btn btn-primary btn-large">
                    <i class="fas fa-save"></i>
                    Simpan Perubahan
                </button>
                <a href="{{ route('school.profile') }}" class="btn btn-secondary btn-large">
                    <i class="fas fa-times"></i>
                    Batal
                </a>
                <button type="reset" class="btn btn-outline-secondary btn-large">
                    <i class="fas fa-undo"></i>
                    Reset Form
                </button>
            </div>
        </form>
    </div>
</div>

<style>
/* School Edit Management Styling */
.school-edit-management {
    min-height: 100vh;
    background: #f8f9fa;
    padding: 2rem 0;
}

.page-header {
    background: white;
    border-radius: 15px;
    padding: 2rem;
    margin: 0 2rem 2rem;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.header-content h1 {
    margin: 0 0 0.5rem;
    color: var(--primary-color);
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 1.8rem;
    font-weight: 700;
}

.page-subtitle {
    margin: 0;
    color: #6c757d;
    font-size: 1rem;
}

.header-actions .btn {
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    border-radius: 8px;
    transition: all 0.3s ease;
}

/* Alert Styling */
.alert {
    margin: 0 2rem 2rem;
    padding: 1.25rem 1.5rem;
    border-radius: 10px;
    border: none;
    position: relative;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.alert-danger {
    background: #f8d7da;
    color: #721c24;
    border-left: 4px solid #dc3545;
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border-left: 4px solid #28a745;
}

.error-list {
    margin: 0.5rem 0 0;
    padding-left: 1.5rem;
}

.error-list li {
    margin-bottom: 0.25rem;
}

.alert-close {
    position: absolute;
    top: 1rem;
    right: 1rem;
    background: none;
    border: none;
    color: inherit;
    cursor: pointer;
    opacity: 0.7;
    font-size: 1.1rem;
}

.alert-close:hover {
    opacity: 1;
}

/* Form Container */
.edit-form-container {
    max-width: 1000px;
    margin: 0 auto;
    padding: 0 2rem;
}

.school-edit-form {
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    overflow: hidden;
}

/* Form Sections */
.form-section {
    padding: 2rem;
    border-bottom: 1px solid #e9ecef;
}

.form-section:last-child {
    border-bottom: none;
}

.section-header {
    margin-bottom: 2rem;
    text-align: center;
}

.section-header h3 {
    margin: 0 0 0.5rem;
    color: var(--primary-color);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    font-size: 1.4rem;
    font-weight: 600;
}

.section-description {
    margin: 0;
    color: #6c757d;
    font-size: 0.95rem;
}

/* Form Grid */
.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
    align-items: start;
}

.form-group.full-width {
    grid-column: 1 / -1;
}

/* Form Groups */
.form-group {
    margin-bottom: 1.5rem;
}

.form-label {
    display: block;
    margin-bottom: 0.5rem;
    color: #495057;
    font-weight: 600;
    font-size: 0.95rem;
}

.form-label.required::after {
    content: " *";
    color: #dc3545;
}

.form-control {
    width: 100%;
    padding: 0.75rem;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    background: #fff;
}

.form-control:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
}

.form-control.is-invalid {
    border-color: #dc3545;
}

.form-text {
    display: block;
    margin-top: 0.5rem;
    color: #6c757d;
    font-size: 0.875rem;
}

.invalid-feedback {
    display: block;
    width: 100%;
    margin-top: 0.5rem;
    font-size: 0.875rem;
    color: #dc3545;
}

/* Mission Section */
.misi-item {
    position: relative;
}

.misi-input-group {
    display: flex;
    gap: 0.75rem;
    align-items: flex-start;
}

.misi-input-group textarea {
    flex: 1;
}

.btn-remove-misi {
    padding: 0.5rem;
    border-radius: 6px;
    background: #dc3545;
    border: none;
    color: white;
    cursor: pointer;
    transition: all 0.3s ease;
    height: fit-content;
    margin-top: 0.125rem;
}

.btn-remove-misi:hover {
    background: #c82333;
    transform: scale(1.05);
}

.btn-add-misi {
    margin-top: 1rem;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    border: 2px solid var(--primary-color);
    background: transparent;
    color: var(--primary-color);
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-add-misi:hover {
    background: var(--primary-color);
    color: white;
}

/* Coordinate Helper */
.coordinate-helper {
    margin-top: 2rem;
}

.helper-card {
    background: #f8f9ff;
    border: 1px solid #e3f2fd;
    border-radius: 10px;
    padding: 1.5rem;
    display: flex;
    gap: 1rem;
}

.helper-icon {
    color: var(--primary-color);
    font-size: 1.5rem;
    flex-shrink: 0;
}

.helper-content h4 {
    margin: 0 0 1rem;
    color: var(--primary-color);
    font-size: 1.1rem;
}

.helper-content ol {
    margin: 0 0 1rem;
    padding-left: 1.25rem;
}

.helper-content li {
    margin-bottom: 0.5rem;
    line-height: 1.5;
}

.helper-link {
    color: var(--primary-color);
    text-decoration: none;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border: 1px solid var(--primary-color);
    border-radius: 6px;
    transition: all 0.3s ease;
}

.helper-link:hover {
    background: var(--primary-color);
    color: white;
    text-decoration: none;
}

/* Form Actions */
.form-actions {
    padding: 2rem;
    background: #f8f9fa;
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
}

.btn-large {
    padding: 1rem 2rem;
    font-size: 1rem;
    font-weight: 600;
    border-radius: 8px;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
}

.btn-primary {
    background: var(--primary-color);
    color: white;
}

.btn-primary:hover {
    background: #0056b3;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
}

.btn-secondary {
    background: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background: #5a6268;
    transform: translateY(-2px);
}

.btn-outline-secondary {
    background: transparent;
    color: #6c757d;
    border: 2px solid #6c757d;
}

.btn-outline-secondary:hover {
    background: #6c757d;
    color: white;
}

/* Responsive Design */
@media (max-width: 992px) {
    .form-grid {
        grid-template-columns: 1fr;
    }

    .page-header {
        flex-direction: column;
        text-align: center;
    }

    .helper-card {
        flex-direction: column;
    }
}

@media (max-width: 768px) {
    .school-edit-management {
        padding: 1rem 0;
    }

    .page-header,
    .edit-form-container {
        margin: 0 1rem;
    }

    .page-header {
        padding: 1.5rem;
        flex-direction: column;
        text-align: center;
    }

    .form-section {
        padding: 1.5rem;
    }

    .form-actions {
        flex-direction: column;
        align-items: stretch;
    }

    .misi-input-group {
        flex-direction: column;
    }

    .btn-remove-misi {
        align-self: flex-end;
        margin-top: 0.5rem;
    }

    .helper-card {
        flex-direction: column;
    }
}@media (max-width: 480px) {
    .header-content h1 {
        font-size: 1.5rem;
    }

    .section-header h3 {
        font-size: 1.2rem;
        flex-direction: column;
        gap: 0.5rem;
    }

    .form-actions {
        padding: 1.5rem;
    }

    .btn-large {
        padding: 0.875rem 1.5rem;
    }
}
</style>

<script>
// Mission Management Functions
function addMisi() {
    const container = document.getElementById('misi-container');
    const misiCount = container.children.length + 1;

    const misiItem = document.createElement('div');
    misiItem.className = 'form-group misi-item';
    misiItem.setAttribute('data-index', misiCount - 1);

    misiItem.innerHTML = `
        <label class="form-label required">Misi ${misiCount}</label>
        <div class="misi-input-group">
            <textarea name="misi[]"
                      class="form-control"
                      rows="2"
                      required
                      placeholder="Tuliskan poin misi ke-${misiCount}"></textarea>
            <button type="button" class="btn btn-danger btn-remove-misi" onclick="removeMisi(this)">
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
        label.textContent = `Misi ${index + 1}`;

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

    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
    submitBtn.disabled = true;

    // Re-enable after 3 seconds in case of error
    setTimeout(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }, 3000);
});
</script>
@endsection
