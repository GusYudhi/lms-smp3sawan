@extends('layouts.app')

@section('title', 'Tambah Tahun Pelajaran')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body py-4">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                        <div class="mb-3 mb-md-0">
                            <h1 class="h3 mb-2 text-primary">
                                <i class="fas fa-plus-circle me-2"></i>Tambah Tahun Pelajaran Baru
                            </h1>
                            <p class="text-muted mb-0">Buat tahun pelajaran baru dengan semester</p>
                        </div>
                        <div>
                            <a href="{{ route('admin.tahun-pelajaran.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Card -->
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0 fw-semibold">
                        <i class="fas fa-edit me-2"></i>Form Tahun Pelajaran
                    </h6>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.tahun-pelajaran.store') }}" method="POST">
                        @csrf

                        <!-- Nama Tahun Pelajaran -->
                        <div class="mb-4">
                            <label for="nama" class="form-label fw-medium">
                                Nama Tahun Pelajaran <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   class="form-control @error('nama') is-invalid @enderror"
                                   id="nama"
                                   name="nama"
                                   value="{{ old('nama') }}"
                                   placeholder="Contoh: 2024/2025"
                                   required>
                            <div class="form-text">Format: YYYY/YYYY (contoh: 2024/2025)</div>
                            @error('nama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Tahun Mulai & Selesai -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="tahun_mulai" class="form-label fw-medium">
                                    Tahun Mulai <span class="text-danger">*</span>
                                </label>
                                <input type="number"
                                       class="form-control @error('tahun_mulai') is-invalid @enderror"
                                       id="tahun_mulai"
                                       name="tahun_mulai"
                                       value="{{ old('tahun_mulai', date('Y')) }}"
                                       min="2000"
                                       max="2100"
                                       required>
                                @error('tahun_mulai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="tahun_selesai" class="form-label fw-medium">
                                    Tahun Selesai <span class="text-danger">*</span>
                                </label>
                                <input type="number"
                                       class="form-control @error('tahun_selesai') is-invalid @enderror"
                                       id="tahun_selesai"
                                       name="tahun_selesai"
                                       value="{{ old('tahun_selesai', date('Y') + 1) }}"
                                       min="2000"
                                       max="2100"
                                       required>
                                @error('tahun_selesai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Tanggal Mulai & Selesai -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="tanggal_mulai" class="form-label fw-medium">Tanggal Mulai</label>
                                <input type="date"
                                       class="form-control @error('tanggal_mulai') is-invalid @enderror"
                                       id="tanggal_mulai"
                                       name="tanggal_mulai"
                                       value="{{ old('tanggal_mulai', old('tahun_mulai', date('Y')) . '-07-01') }}">
                                @error('tanggal_mulai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="tanggal_selesai" class="form-label fw-medium">Tanggal Selesai</label>
                                <input type="date"
                                       class="form-control @error('tanggal_selesai') is-invalid @enderror"
                                       id="tanggal_selesai"
                                       name="tanggal_selesai"
                                       value="{{ old('tanggal_selesai', old('tahun_selesai', date('Y') + 1) . '-06-30') }}">
                                @error('tanggal_selesai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Keterangan -->
                        <div class="mb-4">
                            <label for="keterangan" class="form-label fw-medium">Keterangan</label>
                            <textarea class="form-control @error('keterangan') is-invalid @enderror"
                                      id="keterangan"
                                      name="keterangan"
                                      rows="3"
                                      placeholder="Catatan atau keterangan tambahan...">{{ old('keterangan') }}</textarea>
                            @error('keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Status Aktif -->
                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input"
                                       type="checkbox"
                                       id="is_active"
                                       name="is_active"
                                       value="1"
                                       {{ old('is_active') ? 'checked' : '' }}>
                                <label class="form-check-label fw-medium" for="is_active">
                                    Aktifkan Tahun Pelajaran Ini
                                </label>
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Jika diaktifkan, tahun pelajaran lain akan otomatis dinonaktifkan
                                </div>
                            </div>
                        </div>

                        <!-- Auto Create Semesters -->
                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input"
                                       type="checkbox"
                                       id="create_semesters"
                                       name="create_semesters"
                                       value="1"
                                       checked>
                                <label class="form-check-label fw-medium" for="create_semesters">
                                    Buat 2 Semester Otomatis (Ganjil & Genap)
                                </label>
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Otomatis membuat Semester Ganjil dan Genap untuk tahun pelajaran ini
                                </div>
                            </div>
                        </div>

                        <!-- Copy Data dari Tahun Sebelumnya -->
                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input"
                                       type="checkbox"
                                       id="copy_from_previous"
                                       name="copy_from_previous"
                                       value="1">
                                <label class="form-check-label fw-medium" for="copy_from_previous">
                                    Copy Data dari Tahun Pelajaran Sebelumnya
                                </label>
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Copy Mata Pelajaran, Jam Pelajaran, dan Jadwal Tetap dari semester terakhir tahun sebelumnya ke Semester Ganjil tahun ini
                                </div>
                            </div>
                        </div>

                        <!-- Kenaikan Kelas Otomatis -->
                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input"
                                       type="checkbox"
                                       id="auto_promote_students"
                                       name="auto_promote_students"
                                       value="1">
                                <label class="form-check-label fw-medium" for="auto_promote_students">
                                    Kenaikan Kelas Otomatis
                                </label>
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Otomatis menaikkan kelas siswa: Tingkat 7 → 8, Tingkat 8 → 9.
                                    Siswa Tingkat 9 akan diubah statusnya menjadi <strong>LULUS</strong> dan tidak aktif.
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-between pt-3 border-top">
                            <a href="{{ route('admin.tahun-pelajaran.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Simpan Tahun Pelajaran
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Auto-update tahun_selesai when tahun_mulai changes
document.getElementById('tahun_mulai').addEventListener('change', function() {
    const tahunMulai = parseInt(this.value);
    if (!isNaN(tahunMulai)) {
        document.getElementById('tahun_selesai').value = tahunMulai + 1;

        // Auto-generate nama if empty
        const namaInput = document.getElementById('nama');
        if (!namaInput.value || namaInput.value.match(/^\d{4}\/\d{4}$/)) {
            namaInput.value = tahunMulai + '/' + (tahunMulai + 1);
        }
    }
});
</script>
@endpush
@endsection
