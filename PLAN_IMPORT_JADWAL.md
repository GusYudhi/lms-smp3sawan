# Rencana Implementasi Import Jadwal Pelajaran & Kode Guru

Untuk mempercepat penyusunan jadwal menggunakan Excel, kita akan menambahkan sistem **Import Excel** yang berbasis **Kode Mapel** dan **Kode Guru**.

## 1. Persiapan Database (Kode Guru)

Saat ini `mata_pelajarans` sudah memiliki `kode_mapel`. Namun, `guru_profiles` belum memiliki kode singkat (hanya NIP). Kita akan menambahkan kolom ini agar import lebih mudah (misal: Guru "Ahmad" kodenya "AHM", bukan NIP yang panjang).

*   **Migration:** Tambahkan kolom `kode_guru` (String, Unique, Nullable) pada tabel `guru_profiles`.
*   **Model/Controller:** Update `GuruProfile`, `AdminController` (CRUD), dan Views untuk mendukung input/edit Kode Guru.

## 2. Fitur Import Jadwal (Excel)

Kita akan menggunakan library `maatwebsite/excel` yang sudah terpasang.

### A. Format Excel Template
File Excel akan terdiri dari sheet utama untuk input dan sheet referensi.

**Kolom Sheet Utama:**
1.  **HARI**: (Senin, Selasa, dst...)
2.  **JAM KE**: (1, 2, 3...)
3.  **KELAS**: (7A, 8B, 9C...)
4.  **KODE MAPEL**: (MAT, IND, ING...)
5.  **KODE GURU**: (AHM, BDI...)

**Validasi Import:**
*   Sistem akan mencari ID Kelas berdasarkan Nama Kelas.
*   Sistem akan mencari ID Mapel berdasarkan Kode Mapel.
*   Sistem akan mencari ID Guru berdasarkan Kode Guru.
*   Jika Kode tidak ditemukan, baris tersebut akan gagal/dilewati dengan pesan error.

### B. Backend Logic (`JadwalPelajaranController`)
*   **Method `downloadTemplate`:** Mengunduh file `.xlsx` yang berisi header dan sheet referensi (Daftar Kode Guru & Kode Mapel yang valid).
*   **Method `import`:** Memproses file upload, memvalidasi data, dan menyimpan ke tabel `jadwal_pelajarans`.

## 3. Perubahan UI (Tampilan)

### A. Halaman Jadwal (`admin/jadwal-mapel/jadwal.blade.php`)
*   Menambahkan tombol **"Import Jadwal"**.
*   Menambahkan **Modal Import** yang berisi:
    *   Tombol Download Template.
    *   Form Upload File.
    *   Informasi Semester yang aktif (Jadwal akan diimport ke semester ini).

## 4. Langkah Pengerjaan

1.  [ ] **Migration:** Tambah kolom `kode_guru`.
2.  [ ] **Update Guru Module:** Edit Logic & View Guru untuk support Kode Guru.
3.  [ ] **Buat Export Template:** Class `JadwalTemplateExport`.
4.  [ ] **Buat Import Logic:** Class `JadwalImport`.
5.  [ ] **Update Controller:** Tambahkan method di `JadwalPelajaranController`.
6.  [ ] **Update View Jadwal:** Tambahkan tombol dan modal import.

---
**Status:** Draft Rencana
