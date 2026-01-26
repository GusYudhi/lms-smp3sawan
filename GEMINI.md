# Panduan Proyek & Konteks - LMS SMP 3 Sawan

## 1. Ringkasan Proyek
Proyek ini adalah sistem **Learning Management System (LMS)** dan **Sistem Informasi Sekolah** untuk **SMP Negeri 3 Sawan**. Aplikasi ini dibangun menggunakan Laravel 10 dan berfungsi untuk mengelola data akademik, absensi, dan aktivitas sekolah.

### Fitur Utama (Berdasarkan Struktur File)
*   **Manajemen Pengguna**: Siswa, Guru, dan Staff (menggunakan `User`, `StudentProfile`, `GuruProfile`).
*   **Akademik**:
    *   Data Kelas, Mata Pelajaran, Tahun Pelajaran, Semester.
    *   Jadwal Pelajaran (`JadwalPelajaran`, `FixedSchedule`, `JamPelajaran`).
*   **Absensi & Jurnal**:
    *   Absensi Siswa & Guru (`Attendance`, `GuruAttendance`).
    *   Jurnal Mengajar Guru (`JurnalMengajar`, `JurnalAttendance`).
    *   Agenda Harian Guru (`AgendaGuru`).
*   **Tugas & Kegiatan**:
    *   Pengumpulan Tugas (`TugasGuru`, `TugasGuruSubmission`).
    *   Kegiatan Kokurikuler & Prestasi.
*   **Laporan**: Ekspor/Impor data siswa dan absensi via Excel.

## 2. Stack Teknologi

### Backend
*   **Framework**: Laravel 10.x
*   **Bahasa**: PHP ^8.1
*   **Database**: MySQL (diasumsikan standar Laravel)
*   **Library Kunci**:
    *   `maatwebsite/excel`: Untuk ekspor/impor Excel (Siswa, Absensi).
    *   `intervention/image`: Manipulasi gambar (v3).
    *   `maestroerror/php-heic-to-jpg`: Dukungan upload format HEIC (iPhone).
    *   `laravel/sanctum`: Otentikasi API.
    *   `laravel/ui`: Scaffolding Auth (Bootstrap).

### Frontend
*   **Build Tool**: Vite
*   **Framework CSS**: Bootstrap 5.2.3
*   **Preprocessor**: SASS/SCSS
*   **HTTP Client**: Axios

## 3. Struktur & Pola Arsitektur

### Konvensi Direktori
*   **Models**: Terletak di `app/Models/`. Menggunakan Eloquent ORM. Model cenderung memiliki relasi yang kompleks (User -> Profile -> Kelas).
*   **Services**: Logika bisnis kompleks dipisahkan ke `app/Services/` (contoh: `UserManagementService.php`) untuk menjaga Controller tetap ramping (*Thin Controller*).
*   **Helpers**: Fungsi bantu khusus di `app/Helpers/` (contoh: `ImageCompressor.php`).
*   **Exports/Imports**: Logika Excel terpisah di `app/Exports` dan `app/Imports`.

### Aturan Coding & Best Practices (Recommended)

1.  **Service Pattern**:
    *   Hindari logika bisnis yang berat di Controller. Gunakan Service Class jika logika melibatkan beberapa model atau transaksi kompleks.
    *   Contoh: Pembuatan user baru yang juga membuat `StudentProfile` sebaiknya ada di Service.

2.  **Type Hinting & Return Types**:
    *   Gunakan fitur PHP 8.1+. Selalu definisikan tipe parameter dan return type pada method function.
    *   Contoh: `public function getAttendance(User $user): Collection`

3.  **Pengelolaan Aset (Gambar)**:
    *   Gunakan `ImageCompressor` helper yang sudah ada saat upload gambar untuk menghemat storage.
    *   Perhatikan konversi HEIC untuk kompatibilitas mobile.

4.  **Validasi**:
    *   Gunakan Form Request (`php artisan make:request`) untuk validasi input, jangan validasi langsung di Controller.

5.  **Penamaan**:
    *   Model: PascalCase, Singular (`StudentProfile`).
    *   Table: Snake_case, Plural (`student_profiles`).
    *   Controller: PascalCase (`StudentController`).

6.  **Git & Versioning**:
    *   Pesan commit harus deskriptif.
    *   Jangan commit file `.env` atau folder `vendor/` dan `node_modules/`.

## 4. Catatan Khusus Pengembangan
*   **Kompresi Sisi Klien**: Proyek ini memiliki dokumentasi `CLIENT_SIDE_COMPRESSION_IMPLEMENTATION.md`, pastikan fitur upload gambar mengikuti panduan tersebut.
*   **HEIC Support**: Baca `HEIC_SUPPORT_README.md` saat menangani fitur upload foto dari kamera HP.
*   **Absensi**: Logika absensi cukup kompleks dengan validasi jarak/lokasi atau waktu (lihat `Attendance` model dan migration terkait).
