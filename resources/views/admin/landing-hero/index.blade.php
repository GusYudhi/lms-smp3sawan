@extends('layouts.app')

@section('title', 'Manajemen Hero Wallpaper & Slideshow')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                        <div>
                            <h1 class="h3 mb-2 text-dark">
                                <i class="fas fa-images text-primary me-2"></i>Wallpaper Hero Landing Page
                            </h1>
                            <p class="text-muted mb-0">Kelola gambar latar belakang beranda utama portal sekolah. Mendukung mode wallpaper statis maupun tayangan slide (slideshow).</p>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadHeroModal">
                                <i class="fas fa-cloud-upload-alt me-1"></i> Upload Wallpaper
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Gagal menyimpan:</strong>
            <ul class="mb-0 mt-1">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Mode Configuration Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                        <div>
                            <h5 class="fw-bold mb-1 text-dark"><i class="fas fa-sliders-h text-primary me-2"></i>Mode Tampilan Hero Latar</h5>
                            <p class="text-muted small mb-0">Pilih apakah halaman utama menampilkan satu gambar tetap (statis) atau berganti-ganti secara otomatis (slideshow).</p>
                        </div>
                        <div class="btn-group" role="group" id="heroModeSelector">
                            <input type="radio" class="btn-check" name="hero_mode_opt" id="modeSlideshow" value="slideshow" {{ $heroMode === 'slideshow' ? 'checked' : '' }}>
                            <label class="btn btn-outline-primary px-3 py-2" for="modeSlideshow">
                                <i class="fas fa-play me-1"></i> Slide Show (Berganti)
                            </label>

                            <input type="radio" class="btn-check" name="hero_mode_opt" id="modeStatic" value="static" {{ $heroMode === 'static' ? 'checked' : '' }}>
                            <label class="btn btn-outline-primary px-3 py-2" for="modeStatic">
                                <i class="fas fa-image me-1"></i> Tetap (1 Wallpaper Statis)
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hero Wallpapers Grid -->
    <div class="row g-4">
        @forelse($heroes as $hero)
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm h-100 overflow-hidden">
                    <div class="position-relative">
                        <img src="{{ asset('storage/' . $hero->gambar) }}" class="card-img-top object-fit-cover" alt="Hero Wallpaper" style="height: 200px;">
                        <span class="position-absolute top-0 start-0 m-2 badge bg-dark bg-opacity-75">
                            Urutan: {{ $hero->urutan }}
                        </span>
                        <div class="position-absolute top-0 end-0 m-2">
                            <div class="form-check form-switch bg-white px-2 py-1 rounded shadow-sm">
                                <input class="form-check-input toggle-hero-status" type="checkbox" role="switch" data-id="{{ $hero->id }}" {{ $hero->is_active ? 'checked' : '' }}>
                                <label class="form-check-label small fw-semibold ms-1">{{ $hero->is_active ? 'Aktif' : 'Nonaktif' }}</label>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-3 d-flex flex-column">
                        <h6 class="fw-bold mb-1 text-truncate">{{ $hero->judul ?: 'Tanpa Judul' }}</h6>
                        <p class="text-muted small mb-3 text-truncate-2 flex-grow-1">{{ $hero->subjudul ?: 'Tanpa keterangan tambahan.' }}</p>

                        <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                            <small class="text-muted">{{ $hero->created_at ? $hero->created_at->format('d M Y') : '' }}</small>
                            <div class="d-flex gap-1">
                                <button type="button" class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#editHeroModal{{ $hero->id }}">
                                    <i class="fas fa-edit me-1"></i> Edit
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteHero({{ $hero->id }})">
                                    <i class="fas fa-trash me-1"></i> Hapus
                                </button>
                                <form id="delete-hero-{{ $hero->id }}" action="{{ route('admin.landing-hero.destroy', $hero->id) }}" method="POST" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Edit Hero -->
            <div class="modal fade" id="editHeroModal{{ $hero->id }}" tabindex="-1" aria-labelledby="editHeroModalLabel{{ $hero->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow">
                        <form action="{{ route('admin.landing-hero.update', $hero->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="modal-header">
                                <h5 class="modal-title fw-bold" id="editHeroModalLabel{{ $hero->id }}">
                                    <i class="fas fa-edit text-primary me-2"></i>Edit Wallpaper
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-4">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Ganti Gambar (Opsional)</label>
                                    <input type="file" name="gambar" class="form-control" accept="image/*">
                                    <small class="text-muted d-block mt-1">Biarkan kosong jika tidak ingin mengganti gambar. Otomatis dikompres WebP.</small>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Judul / Caption (Opsional)</label>
                                    <input type="text" name="judul" class="form-control" value="{{ $hero->judul }}" placeholder="Contoh: Suasana Belajar Interaktif">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Subjudul / Deskripsi Singkat (Opsional)</label>
                                    <input type="text" name="subjudul" class="form-control" value="{{ $hero->subjudul }}" placeholder="Keterangan singkat wallpaper...">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Nomor Urutan</label>
                                    <input type="number" name="urutan" class="form-control" value="{{ $hero->urutan }}" min="0">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card border-0 shadow-sm p-5 text-center">
                    <i class="fas fa-images fa-4x text-muted opacity-50 mb-3"></i>
                    <h5 class="fw-bold text-muted">Belum ada wallpaper yang diunggah</h5>
                    <p class="text-muted small">Klik tombol "Upload Wallpaper" di atas untuk menambahkan gambar latar beranda.</p>
                </div>
            </div>
        @endforelse
    </div>
</div>

<!-- Modal Upload Hero Baru -->
<div class="modal fade" id="uploadHeroModal" tabindex="-1" aria-labelledby="uploadHeroModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('admin.landing-hero.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="uploadHeroModalLabel">
                        <i class="fas fa-cloud-upload-alt text-primary me-2"></i>Upload Wallpaper Hero
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">File Gambar <span class="text-danger">*</span></label>
                        <input type="file" name="gambar" class="form-control" accept="image/*" required>
                        <small class="text-muted d-block mt-1">Format: JPG, PNG, WEBP, HEIC (Maks. 12 MB). Disarankan rasio landscape 16:9.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Judul / Caption (Opsional)</label>
                        <input type="text" name="judul" class="form-control" placeholder="Contoh: Gerbang Utama SMPN 3 Sawan">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Subjudul / Deskripsi Singkat (Opsional)</label>
                        <input type="text" name="subjudul" class="form-control" placeholder="Keterangan singkat...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nomor Urutan</label>
                        <input type="number" name="urutan" class="form-control" value="0" min="0">
                        <small class="text-muted">Urutan menentukan foto mana yang tampil lebih dulu pada slide show atau foto utama pada mode tetap.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-upload me-1"></i> Upload & Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Delete Confirmation via SweetAlert2
    function deleteHero(id) {
        Swal.fire({
            title: 'Hapus Wallpaper?',
            text: 'Wallpaper ini akan dihapus permanen dari sistem.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-hero-' + id).submit();
            }
        });
    }

    // Toggle Hero Mode (Slideshow vs Static)
    document.querySelectorAll('input[name="hero_mode_opt"]').forEach(elem => {
        elem.addEventListener('change', function() {
            const selectedMode = this.value;
            fetch('{{ route("admin.landing-hero.toggle-mode") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ mode: selectedMode })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Mode Diperbarui',
                        text: data.message,
                        timer: 1800,
                        showConfirmButton: false
                    });
                }
            })
            .catch(err => {
                Swal.fire({ icon: 'error', title: 'Gagal', text: 'Gagal memperbarui mode hero.' });
            });
        });
    });

    // Toggle Active Status
    document.querySelectorAll('.toggle-hero-status').forEach(elem => {
        elem.addEventListener('change', function() {
            const heroId = this.dataset.id;
            fetch(`/admin/landing-hero/${heroId}/toggle-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const label = this.parentElement.querySelector('label');
                    if (label) label.textContent = data.is_active ? 'Aktif' : 'Nonaktif';
                }
            });
        });
    });
</script>
@endpush

