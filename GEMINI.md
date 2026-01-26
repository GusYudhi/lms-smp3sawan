# Panduan Proyek & Konteks - LMS SMP 3 Sawan

## 1. Ringkasan Proyek

Proyek ini adalah sistem **Learning Management System (LMS)** dan **Sistem Informasi Sekolah** untuk **SMP Negeri 3 Sawan**. Aplikasi ini dibangun menggunakan Laravel 10 dan berfungsi untuk mengelola data akademik, absensi, dan aktivitas sekolah.

### Fitur Utama

- **Manajemen Pengguna**: Siswa, Guru, dan Staff.
- **Akademik**: Jadwal, Kelas, Mapel, Tahun Pelajaran.
- **Absensi & Jurnal**: Absensi QR Code, Jurnal Mengajar, Agenda Guru.
- **Tugas & Kegiatan**: Pengumpulan Tugas, Kokurikuler, Prestasi.
- **Laporan**: Ekspor/Impor Excel, Rekapitulasi Jurnal.

## 2. Stack Teknologi

### Backend

- **Framework**: Laravel 10.x (PHP ^8.1)
- **Database**: MySQL
- **Library Utama**:
    - `maatwebsite/excel`: Ekspor/Impor Excel.
    - `intervention/image`: Manipulasi gambar.
    - `maestroerror/php-heic-to-jpg`: Support upload HEIC.
    - `laravel/sanctum` & `laravel/ui`.

### Frontend

- **Build Tool**: Vite
- **CSS Framework**: Bootstrap 5.3 (via Sass)
- **Icons**: Font Awesome 6
- **JS**: Vanilla JS + jQuery (legacy support) + Axios

## 3. Struktur Project (Architecture)

### Pola Direktori

Struktur folder mengikuti pola **Role-Based Separation** untuk memudahkan manajemen hak akses.

#### Controllers (`app/Http/Controllers/`)

Dipisah berdasarkan Role Pengguna:

- `Admin/`: Logika untuk Administrator (misal: `SiswaController`, `GuruController`).
- `Guru/`: Logika untuk Guru (misal: `JurnalMengajarController`).
- `KepalaSekolah/`: Logika Read-only/Monitoring untuk Kepsek.
- `Siswa/`: Logika untuk dashboard Siswa.
- _Root_: Controller umum/publik (`HomeController`, `ProfileController`).

#### Views (`resources/views/`)

Mengikuti struktur Controller:

- `admin/`, `guru/`, `kepala-sekolah/`, `siswa/`.
- `layouts/`: Template utama (`app.blade.php`).
- `auth/`: Halaman login/register.
- `components/`: Blade components reusable.

#### Services (`app/Services/`)

Gunakan Service Class untuk logika bisnis yang kompleks atau dipakai di banyak tempat.

- Contoh: `UserManagementService.php` (menangani pembuatan User + Profile sekaligus).

#### Validasi

- Saat ini dominan menggunakan **Inline Validation** (`$request->validate([...])`) di dalam Controller.
- Untuk validasi kompleks, disarankan beralih ke **Form Request** (`app/Http/Requests`).

## 4. Frontend Guidelines (UI/UX Patterns)

Setiap halaman baru **WAJIB** mengikuti pola desain yang sudah ada agar konsisten.

### A. Card & Container

Gunakan style "Modern Card" untuk membungkus konten utama.

```html
<div class="card card-modern border-0 shadow-sm">
    <div class="card-header bg-light border-bottom-0 py-3">
        <h6 class="card-title mb-0 text-high-contrast fw-semibold">
            <i class="fas fa-icon text-primary me-2"></i>Judul Section
        </h6>
    </div>
    <div class="card-body">
        <!-- Content -->
    </div>
</div>
```

- **Stats Card**: Gunakan class `.card-stats.hover-card` untuk widget statistik.

### B. Filter & Pencarian

Form filter diletakkan dalam card tersendiri di atas tabel data.

- Gunakan **Grid System** (`row g-3`) untuk layout input.
- Tambahkan class `auto-submit` pada dropdown (`select`) jika ingin submit otomatis saat diganti.
- **Search Input**: Gunakan Input Group dengan icon search.

```html
<div class="input-group">
    <span class="input-group-text bg-light border-end-0"
        ><i class="fas fa-search text-muted"></i
    ></span>
    <input
        type="text"
        class="form-control border-start-0"
        placeholder="Cari..."
    />
</div>
```

### C. Tombol (Buttons)

- **Primary Action (Tambah/Simpan)**: `btn btn-primary` + Icon.
    - `<a href="..." class="btn btn-primary"><i class="fas fa-plus me-1"></i> Tambah</a>`
- **Secondary Action (Export/Print)**: `btn btn-outline-secondary`.
- **Import/Excel**: `btn btn-outline-success`.
- **Danger (Hapus)**: `btn btn-danger`.

### D. Tabel Data

- Bungkus tabel dengan `div.table-responsive`.
- Table Classes: `table table-hover table-striped align-middle`.
- Header Table: `table-light`.
- Jika menggunakan AJAX search, pisahkan tabel ke file partial (misal: `partials/table.blade.php`).

### E. Badge Status

Gunakan helper class Bootstrap standar:

- Aktif/Hadir: `<span class="badge bg-success">Aktif</span>`
- Non-Aktif/Alpha: `<span class="badge bg-danger">...</span>`
- Warning/Sakit: `<span class="badge bg-warning text-dark">...</span>`
- Info/Izin: `<span class="badge bg-info">...</span>`

## 5. Aturan Coding (Best Practices)

1.  **Naming Convention**:
    - Controller: `PascalCase` + `Controller` (e.g., `SiswaController`).
    - Model: `PascalCase`, Singular (e.g., `StudentProfile`).
    - Table: `snake_case`, Plural (e.g., `student_profiles`).
    - View Folder: `kebab-case` (e.g., `rekap-jurnal`).
2.  **Eager Loading**:
    - Selalu gunakan `with()` saat query model berelasi untuk menghindari N+1 Problem.
    - Contoh: `User::with('studentProfile')->get()`.
3.  **Clean Code**:
    - Hindari logic berat di Blade (@php ... @endphp). Pindahkan ke Controller atau Model Accessor.
    - Gunakan Accessor untuk format data tampilan (misal: `getProfilePhotoUrl()`).
4.  **Security**:
    - Upload file wajib divalidasi (Mime types, Size).
    - Gunakan `ImageCompressor` helper untuk upload gambar.

## 6. User Feedback (UX)

Gunakan standar feedback berikut untuk interaksi pengguna:

1.  **Konfirmasi Hapus Data**:
    - **WAJIB** menggunakan **SweetAlert2** untuk setiap aksi penghapusan.
    - Jangan gunakan `window.confirm` bawaan browser.
    - Tampilkan pesan "Are you sure?" yang jelas beserta konsekuensinya (misal: "Data yang dihapus tidak bisa dikembalikan").

2.  **Notifikasi (Success/Error)**:
    - Gunakan **Toastr** atau **Flash Message** berbasis Session Laravel.
    - Untuk form submission, tampilkan alert di bagian atas halaman (biasanya sudah di-handle di `layouts/app.blade.php`).
    - Success: Warna Hijau (`alert-success` / Toastr Success).
    - Error/Gagal: Warna Merah (`alert-danger` / Toastr Error).
