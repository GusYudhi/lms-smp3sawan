# Design: Unifikasi CSS (Pra-Migrasi Material Design)

Tanggal: 2026-10-29
Status: Approved

## Konteks

Codebase berstatus "vibe coded" secara visual: tidak ada design system, style tersebar inline di 43 file blade (±2.600 baris CSS), dengan duplikasi substansial antar role (admin/kepala-sekolah/guru) untuk halaman yang sama. Tujuan akhir jangka menengah adalah migrasi ke Material Design. Tahap ini adalah persiapan: satukan dan ekstrak semua style ke resources/sass sebagai fondasi, tanpa perubahan visual maupun migrasi Material.

## Keputusan yang sudah disetujui user

1. **Sidebar biru solid `#0d47a1`** (kembali dari purple gradient `#667eea→#764ba2` yang saat ini menang runtime karena inline style layout lebih spesifik). Inline sidebar style dihapus dari layouts.
2. **Layout `guest.blade.php` diikutkan** ke pipeline `app.scss` via `@vite` (hapus Bootstrap CSS CDN). JS Bootstrap CDN tetap.
3. **File dipecah per fungsi**: `_tokens`, `_base`, `_components`, `_pages`, entrypoint `app.scss`.
4. **Bersihkan semua inline style sekarang** — bukan bertahap.
5. **Visual tetap sama** (restore/restruktorisasi saja), tidak ada perubahan estetika.
6. Warna primary tetap biru `#0d47a1` (dengan `#1565c0` sebagai variant gradient yang dipakai welcome hero) — jangan diganti, jadi anchor saat migrasi Material nanti.

## Inventaris temuan eksplorasi

- 43 file blade punya blok `<style>`, total ±2.600 baris.
- Duplikasi utama:
  - Absensi (index siswa/guru, detail siswa/guru) identik byte-per-byte antara admin vs kepala-sekolah; versi guru juga kembar (hanya beda `box-shadow` `6px` vs `8px` di satu tempat).
  - Pagination (`pagination-container/info/controls/current`, `btn-pagination`) — ±10 file.
  - Calendar absensi (`calendar-grid/header/day` + state hadir/alpa/today/empty) — ±6 file.
  - `hover-card` (14 file), `card-stats` (10 file), `stat-item`/`stat-mini`.
- Selector unik terinventarisasi: ~75 kelas + beberapa ID (kamera/scanner) + keyframes (`fadeIn`, `scan`) + media queries standard.
- Layout `app.blade.php` & `semester.blade.php` punya CSS inline sidebar (purple) yang menang atas biru di app.scss karena urutan muat/spesifisitas.
- Layout `guest.blade.php` tidak memuat `app.scss` — Bootstrap disalurkan lewat CDN.
- `resources/css/app.css` **kosong** — tidak dipakai, dihapus.
- `resources/sass/app.scss` memakai Bootstrap sass, sehingga sudah satu pipeline sah.

## Struktur file baru (resources/sass/)

- **`_tokens.scss`** — Semua variable design system: warna primary (`#0d47a1`), variant (`#1565c0`), surface/grayscale (`#f8f9fa`, `#495057`, `#6c757d`, `#dee2e6`), token radius, shadow. Ini satu-satunya file yang akan berubah besar saat migrasi Material nanti.
- **`_variables.scss`** (update) — bridge ke Bootstrap variable (sudah ada, di-refactor pemakainya supaya consume token).
- **`_base.scss`** — Layout shell: `.sidebar` (biru solid `#0d47a1` — inline purple dihapus dari layouts `app`, `semester`, `guest`), `.main-content`, `.content-area`, `.sidebar-brand`, `.sidebar-backdrop`, responsive breakpoints sidebar. Card umum: `.card`, `.hover-card`, `.card-stats`, `.card-modern`.
- **`_components.scss`** — Style shared/reusable antar halaman:
  - Pagination: `.pagination-container`, `.pagination-info`, `.pagination-controls`, `.btn-pagination`, `.pagination-current`.
  - Calendar absensi: `.calendar-grid`, `.calendar-header`, `.calendar-day` + state (`hadir`, `alpa`, `today`, `empty`, dsb).
  - `.attendance-list`, `.stat-item`, `.stat-mini`, `.auth-*`, `.avatar-circle`, `.profile-photo-wrapper`, `.retained-chip`, `.day-box`.
  - Form/focus tweak, select2 bootstrap-5 overrides (yang di-copy di beberapa halaman admin).
- **`_pages.scss`** — Style page-specific (tak bisa atau tak worth di-generalisasi):
  - QR scanner: `.qr-scanner`, `.scanner-container`, `.scan-line`, `.camera-*`, `.scan-success-overlay`, `.scanning-indicator`, `#camera-*`, `#photo-canvas`, `#scanner-placeholder`, override `#html5-qr-code-*`.
  - Wizard jurnal: `.wizard-step`, `.step-indicator`, `.step-circle`, `.step-connector`, `.step-label`, `.timeline-marker`.
  - Drag-drop jadwal: `.draggable-item`, `.drop-zone`, `.jadwal-table`.
  - `.no-print` + `@media print` rules.
  - `.semester-info-card`, keyframes (`fadeIn`, `scan`).
- **`app.scss`** (refactor) — entrypoint saja: font import, tokens, bootstrap, lalu `@import` ke 4 part file.

Semua file `.scss`, dicompile Vite bersama Bootstrap — **tidak menambah dependency**.

## Perubahan blade

- Hapus seluruh blok `<style>` inline di 43 file.
- Hapus style sidebar inline dari layouts `app.blade.php`, `semester.blade.php`, `guest.blade.php`.
- `guest.blade.php`: ganti `<link>` Bootstrap CDN dengan `@vite`.
- Hapus `resources/css/app.css` (kosong, tidak dipakai).
- Markup/HTML blade tidak berubah — hanya CSS style block yang dipindah.
- Konflik satu-off (mis. `box-shadow 6px vs 8px` antar copy absensi) diambil satu versi, tanpa turun estetika.

## Risiko & mitigasi

- **Class sama, visual beda antar halaman**: satu definisi untuk shared class; perbedaan dicatat sebagai modifier, atau class dipindah ke `_pages.scss`. Tidak lengkung visual, hanya placement.
- **Urutan muat layou**: baik app/guru/admin/kepsek siswa pakai `app.scss` via `@vite`; guest diikutin. Sekarang urutan jadi deterministik, bukan inline-style-wins.
- **Verifikasi**: `npm run build` compile sass tanpa error; visual check manual di halaman kunci: login, admin/kepsek dashboard, absensi detail (3 role), siswa/jadwal, jurnal wizard, scan QR, guest welcome/berita/galeri/prestasi, berita print.

## Out of scope (tahap ini)

Migrasi Material Design, tambah fitur, tambah paket, perubahan JS, rombak markup HTML. Ini hanya ekstraksi + unifikasi CSS sebagai fondasi Material.
