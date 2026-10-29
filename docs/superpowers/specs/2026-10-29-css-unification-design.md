# Design: Unifikasi CSS (Pra-Migrasi Material Design) - REVISED

Tanggal: 2026-10-29  
Status: Revised & Ready for Execution  
Arsitektur: SASS (Vite Pipeline)  

---

## 1. Konteks & Tujuan

Codebase saat ini memiliki status styling yang tersebar (terdapat blok `<style>` di puluhan file Blade, ±2.600 baris CSS) dengan duplikasi substansial antar-role (admin/kepala-sekolah/guru).

Tujuan akhir jangka menengah adalah migrasi ke Material Design. **Tahap ini adalah fondasi teknis**:
1. Satukan dan ekstrak semua style internal `<style>` ke modul-modul `resources/sass/`.
2. Hapus duplikasi CSS dan standardisasi token visual (warna primer, shadow, border radius).
3. **Tanpa perubahan visual** bagi pengguna akhir (estetika dan layout tetap presisi 100%).
4. **Proteksi kebocoran CSS (CSS Leaks)** antar layout dan komponen.

---

## 2. Keputusan Kunci & Batasan Ketat (Guardrails)

1. **Sidebar Biru Solid (`#0d47a1`)**:
   - Kembali ke biru solid `#0d47a1` (menghapus override purple gradient `#667eea→#764ba2` yang ada di tag `<style>` layout `app` dan `semester`).
   - Warna `#1565c0` dipertahankan sebagai variant pendamping (dipakai pada hero guest/welcome).
2. **Scoping Wajib untuk Tag `<body>`**:
   - DILARANG menaruh selektor global `body { ... }` dari layout spesifik langsung ke root CSS global.
   - `resources/views/layouts/auth.blade.php`: Beri atribut `<body class="layout-auth">`. Styling background biru dan flex center di-scope di bawah `.layout-auth`.
   - `resources/views/layouts/guest.blade.php`: Beri atribut `<body class="layout-guest">`. Font Poppins dan styling `.nav-link` di-scope di bawah `.layout-guest`.
   - `resources/views/layouts/app.blade.php` & `semester.blade.php`: Default body menggunakan Nunito (`$body-bg: #f8fafc`).
3. **Pengecualian Khusus (Excluded from Extraction)**:
   - **`resources/views/guru/jurnal-mengajar/print.blade.php` TETAP MEMPERTAHANKAN `<style>` INTERNAL**.  
     *Alasan:* File ini adalah dokumen cetak fisik (`window.print()`) yang terisolasi total, tidak me-load Vite/Bootstrap, dan styling tabelnya (`table, th, td { border: 1px solid #000 }`) akan merusak seluruh tabel aplikasi jika dijadikan CSS global.
4. **Atribut Inline Style Dinamis Tetap Dipertahankan**:
   - Hanya blok tag `<style>...</style>` yang dipindahkan ke SASS. Atribut inline `style="..."` yang berisi data dinamis dari server/PHP (misal `$selectedSize` pada `profile-photo.blade.php` atau honeypot anti-spam) **tidak boleh disentuh**.
5. **Eksekusi Bertahap (Phased Rollout)**:
   - DILARANG melakukan pembersihan "Big Bang" 43 file sekaligus. Eksekusi dibagi menjadi 3 fase terukur agar mudah diverifikasi dan dilacak jika terjadi regresi.

---

## 3. Struktur Modul SASS (`resources/sass/`)

```text
resources/sass/
├── _tokens.scss        # Design tokens: warna (#0d47a1, #1565c0), surface, radius, elevation
├── _variables.scss     # Bridge override variabel Bootstrap (font, grid, body-bg)
├── _base.scss          # Shell layout: .sidebar, .main-content, .layout-auth, .layout-guest
├── _components.scss    # Shared UI: pagination, calendar, cards, avatar, day-box
├── _pages.scss         # Scoped feature styles: scanner, wizard jurnal, jadwal matrix
└── app.scss            # Master entrypoint Vite
```

### Detail Modul:

*   **`_tokens.scss`**:
    Menyimpan semua CSS custom properties / SCSS variables:
    ```scss
    $color-primary: #0d47a1;
    $color-primary-variant: #1565c0;
    $color-surface: #f8f9fa;
    $radius-card: 12px;
    $shadow-card: 0 0.125rem 0.5rem rgba(0, 0, 0, 0.1);
    ```
*   **`_variables.scss`**:
    Mengatur variabel bawaan Bootstrap agar sesuai dengan token sekolah:
    ```scss
    $primary: #0d47a1;
    $body-bg: #f8fafc;
    $font-family-sans-serif: "Nunito", sans-serif;
    ```
*   **`_base.scss`**:
    *   Layout shell utama: `.sidebar`, `.main-content`, `.sidebar-brand`, `.sidebar-backdrop`, responsive breakpoints mobile drawer.
    *   Scoped layouts:
        ```scss
        .layout-auth {
            background: #0d47a1;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            &::before { ... }
        }
        .layout-guest {
            font-family: 'Poppins', sans-serif;
            .guest-header { ... }
            .nav-link { ... }
        }
        ```
*   **`_components.scss`**:
    *   **Cards**: `.card-modern`, `.card-stats`, `.hover-card`.
    *   **Pagination shared**: `.pagination-container`, `.pagination-info`, `.pagination-controls`, `.btn-pagination`, `.pagination-current`.
    *   **Absensi calendar**: `.calendar-grid`, `.calendar-header`, `.calendar-day` (state: `.hadir`, `.alpa`, `.today`, `.empty`).
    *   **Widgets**: `.stat-item`, `.stat-mini`, `.avatar-circle`, `.profile-photo-wrapper`, `.day-box`, `.retained-chip`.
    *   **Auth components**: `.auth-card`, `.auth-header`, `.auth-body`, `.auth-footer`.
*   **`_pages.scss`**:
    Wajib menggunakan class prefix atau parent scoping agar tidak mencemari elemen global:
    *   **QR Scanner**: Dibungkus dalam `.qr-scanner-wrapper` / `.scanner-container`.
    *   **Wizard Jurnal**: Dibungkus dalam `.wizard-jurnal-container` (`.wizard-step`, `.step-circle`, `.step-connector`).
    *   **Jadwal Matrix**: Dibungkus dalam `.jadwal-matrix-container` (`.draggable-item`, `.drop-zone`).
    *   **Detail Absensi**: Style tab/tabel detail absensi guru & siswa.
*   **`app.scss`**:
    Entrypoint bersih yang me-load seluruh modul secara teratur:
    ```scss
    @import "tokens";
    @import "variables";
    @import "bootstrap/scss/bootstrap";
    @import "base";
    @import "components";
    @import "pages";
    ```

---

## 4. Rencana Eksekusi Bertahap (Implementation Roadmap)

### Fase 1: Fondasi, Desain Token & Layout Shell
1. Buat `resources/sass/_tokens.scss`.
2. Perbarui `resources/sass/_variables.scss` dan `resources/sass/app.scss`.
3. Buat `resources/sass/_base.scss`:
   - Pindahkan styling `.sidebar`, `.main-content`, dan responsive drawer dari `resources/views/layouts/app.blade.php` dan `semester.blade.php`.
   - Pastikan warna sidebar adalah biru solid `#0d47a1` (hapus gradient ungu inline).
4. Refactor `resources/views/layouts/auth.blade.php`:
   - Tambahkan class `<body class="layout-auth">`.
   - Pindahkan style background dan pattern ke `_base.scss` (di bawah `.layout-auth`).
   - Hapus blok `<style>` dari `auth.blade.php`.
5. Refactor `resources/views/layouts/guest.blade.php`:
   - Tambahkan class `<body class="layout-guest">`.
   - Masukkan `@vite(['resources/sass/app.scss', 'resources/js/app.js'])`.
   - Hapus `<link>` Bootstrap 5 CDN dari header.
   - Pindahkan styling guest ke `_base.scss` (di bawah `.layout-guest`).
6. **Verifikasi Fase 1**: Jalankan `npm run build`, buka halaman login (`/login`), halaman tamu (`/`), dan dashboard admin. Pastikan tidak ada layout yang pecah.

---

### Fase 2: Ekstraksi Komponen Bersama (Shared Components)
1. Buat `resources/sass/_components.scss`.
2. Pindahkan dan unifikasi selector umum dari Blade:
   - **Pagination** dari 10 file absensi & rekap.
   - **Calendar Grid Absensi** dari file absensi siswa & guru (admin, kepsek, guru).
   - **Cards & Stats**: `.hover-card`, `.card-stats`, `.stat-item`, `.stat-mini`.
   - **Avatar & Foto**: `.avatar-circle`, `.profile-photo-wrapper` (dari `components/profile-photo.blade.php`).
3. Hapus blok `<style>` terkait dari file-file tersebut:
   - `resources/views/welcome.blade.php` (hapus blok `.hover-card`).
   - `resources/views/admin/dashboard.blade.php` & `kepala-sekolah/dashboard.blade.php`.
   - `resources/views/admin/absensi/index.blade.php`, `guru/index.blade.php`, `siswa/index.blade.php`.
   - `resources/views/kepala-sekolah/absensi/index.blade.php`, `guru/index.blade.php`, `siswa/index.blade.php`.
   - `resources/views/guru/absensi/absensi-siswa.blade.php`, `absensi-guru/absensi-guru.blade.php`.
   - `resources/views/components/profile-photo.blade.php`.
   - `resources/views/admin/semester/dashboard.blade.php` (`.hover-shadow`).
   - `resources/views/admin/data-sekolah/index.blade.php` (`.hover-overlay`).
   - `resources/views/guru/dashboard.blade.php` (`.hover-card`).
   - `resources/views/kepala-sekolah/agenda-guru/index.blade.php` & `kepala-sekolah/jurnal-mengajar/index.blade.php` (`.card-stats`).
   - `resources/views/kepala-sekolah/data/guru/index.blade.php` & `kepala-sekolah/data/siswa/index.blade.php` (pagination + `.avatar-circle`).
   - `resources/views/admin/guru/create.blade.php` & `admin/guru/edit.blade.php` (`.select2-container--bootstrap-5`).
   - `resources/views/admin/siswa/index.blade.php` (`.retained-chip`).
4. **Verifikasi Fase 2**: Jalankan `npm run build`, cek visual halaman absensi dan dashboard di ketiga role (Admin, Kepala Sekolah, Guru).

---

### Fase 3: Ekstraksi Fitur Spesifik Halaman (Page-Specific Styles)
1. Buat `resources/sass/_pages.scss`.
2. Pindahkan style spesifik fitur dengan scoping ketat:
   - **Scanner QR Absensi**: `resources/views/siswa/absen/index.blade.php` (`.scanner-container`, `#camera-*`, keyframes `scan`).
   - **Wizard Jurnal Mengajar**: `resources/views/guru/jurnal-mengajar/wizard.blade.php` (`.wizard-step`, `.step-indicator`, dll).
   - **Matriks Jadwal Pelajaran**: `resources/views/admin/jadwal-mapel/jadwal.blade.php` & `guru/jadwal-mengajar/jadwal-mengajar.blade.php` & `siswa/jadwal-pelajaran/jadwal-mapel.blade.php`.
   - **Galeri, Berita & Prestasi Guest**: `resources/views/guest/galeri.blade.php`, `guest/berita/index.blade.php`, `guest/prestasi/index.blade.php`.
   - **Agenda Guru / Kepsek**: `resources/views/guru/agenda-guru/agenda-guru.blade.php`, `resources/views/kepala-sekolah/agenda/agenda-kepsek.blade.php`.
   - **Timeline Semester**: `resources/views/kepala-sekolah/data/semester/show.blade.php` (`.timeline-marker` — selector sudah ada di grup wizard, pastikan tidak bentrok scope).
3. Hapus blok `<style>` di halaman-halaman tersebut setelah CSS terdaftar di `_pages.scss`.
4. **Ingat**: JANGAN menyentuh atau menghapus `<style>` di `guru/jurnal-mengajar/print.blade.php`.
5. **Verifikasi Fase 3**: Jalankan `npm run build`, uji fungsi kamera scanner QR, wizard jurnal guru, dan tabel jadwal drag-and-drop.

---

### Fase 4: Finalisasi & Housekeeping
1. Hapus file `resources/css/app.css` (karena kosong dan tidak terdaftar di pipeline Vite).
2. Jalankan `npm run build` untuk memastikan tidak ada error kompilasi SASS.
3. Jalankan suite pengujian: `php artisan test` untuk memastikan seluruh fitur fungsional tetap 100% lulus.

---

## 5. Checklist Verifikasi Manual untuk Pelaksana (GLM)

Sebelum menandai pekerjaan selesai, pelaksana wajib memverifikasi visual di browser pada rute berikut:

* [ ] **/login**: Tampilan card login tetap rapi di tengah dengan background biru `#0d47a1` & pola SVG (tidak merembet ke halaman lain).
* [ ] **/** (Welcome/Guest): Navbar Poppins tampil presisi, hero biru konsisten, link hover berfungsi normal.
* [ ] **/admin/dashboard**: Sidebar biru solid `#0d47a1`, kartu statistik rapi, responsive mobile drawer berfungsi (bisa dibuka-tutup).
* [ ] **/admin/absensi/guru** & **/admin/absensi/siswa**: Kalender kehadiran grid dan kontrol pagination utuh.
* [ ] **/guru/jurnal-mengajar/wizard**: Indikator langkah (steps) dan konektor garis wizard jurnal mengajar tampil normal.
* [ ] **/guru/jurnal-mengajar/.../print**: Buka halaman cetak jurnal, pastikan tabel bergaris hitam tegas siap cetak (tidak rusak menjadi tanpa garis).
* [ ] **/siswa/absen**: Overlay kamera dan animasi garis laser pemindai QR aktif.
* [ ] **/admin/jadwal-mapel**: Grid jadwal drag-and-drop tetap presisi.
* [ ] **Terminal**: `npm run build` berhasil menghasilkan bundle tanpa error syntax.
* [ ] **Terminal**: `php artisan test` 100% lulus (73 passed).
