# GEMINI.md - System Architecture & Documentation

## 🌍 Project Overview

**Project Name:** SMP 3 Sawan LMS (Learning Management System)
**Framework:** Laravel 10 (PHP 8.1+)
**Type:** Web Application (Monolith)
**Purpose:** Comprehensive school management system handling student/teacher data, attendance (GPS+Selfie), teaching journals, scheduling, and reporting.

---

## 🏗 Core Architecture

### 1. Database Relationships
*   **Teacher - Subject (Guru - Mapel):** 
    *   **Structure:** Hybrid (Many-to-Many via pivot `guru_mata_pelajaran` + Primary Subject on `guru_profiles.mata_pelajaran_id`).
    *   **Reason:** Memungkinkan guru mengajar lebih dari 1 mata pelajaran (Mapel Utama + Mapel Tambahan) dengan proteksi anti-duplikasi, sekaligus menjaga *backward compatibility* 100% pada rekap, export/import, dan profil guru.
    *   **Code:** `GuruProfile` belongsTo `MataPelajaran` (`mata_pelajaran_id`, Utama) & belongsToMany `MataPelajaran` via `guru_mata_pelajaran` (`mataPelajarans`, Seluruh Mapel).

### 2. User Roles & Access
*   **Admin:** Master data, User management, Scheduling.
*   **Kepala Sekolah (Principal):** Read-only monitoring (Dashboards, Attendance Tables), Reporting.
*   **Guru:** Operations (Journal, Attendance, Grading).
*   **Siswa:** Read-only (Schedule, Grades).

---

## 🚀 Key Features

### 📅 Schedule Management (Jadwal Pelajaran)
A robust system for managing class schedules.

*   **Matrix Import/Export:**
    *   **Format:** Excel Grid (Rows: Time/Day, Columns: Classes).
    *   **Cell Data:** `[Kode Mapel] [Kode Guru]` (e.g., "MAT AHM").
    *   **Smart Import:** 
        *   Auto-creates/updates Subjects from `KODE_MAPEL` sheet.
        *   Auto-updates Teacher Codes from `KODE_GURU` sheet.
        *   "Steals" codes: If a Teacher Code is taken, it's reassigned to the new teacher during import.
        *   **Merged Cells:** Handles "Day" column merges via "Fill Down" logic.
        *   **Header Row:** Uses Row 2 (Row 1 is Title).
*   **UI/UX:**
    *   **Drag & Drop:** Move schedules interactively.
    *   **Linked Dropdowns:** Selecting a Subject filters Teachers; Selecting a Teacher auto-selects their Subject.
    *   **Conflict Detection:** Real-time checking for:
        1.  Teacher double-booking.
        2.  Class slot occupancy.
        3.  Fixed Schedules (Upacara/Istirahat).
    *   **Export & Reset:** Dedicated buttons to backup or clear class schedules.

### 📍 Attendance & Reporting (Absensi & Rekap)
*   **Guru Attendance:**
    *   Selfie capture (front/back camera) + GPS geofencing against coordinates in `config/school.php` (`SCHOOL_LATITUDE`, `SCHOOL_LONGITUDE`, `SCHOOL_ATTENDANCE_RADIUS`, `SCHOOL_LATE_THRESHOLD`).
    *   Automated status assignment (`hadir` vs `terlambat` based on threshold time, `izin`, `alpha`).
    *   Real-time monitoring via Ajax polling on Principal's dashboard.
*   **Siswa Attendance:** Daily class attendance and per-subject attendance.
*   **Rekap & Filter:**
    *   Multi-criteria filtering by Period (Daily, Monthly, Semester) and Status (`hadir`, `sakit`, `izin`, `alpha`, `terlambat`) in Admin & Kepala Sekolah rekap tables (`AbsensiRekapController`).
*   **Excel Export (`maatwebsite/excel`):**
    *   **Siswa:** Multi-sheet workbook (`StudentsAttendanceExport` -> `StudentsAttendanceSheet` per class), sorted by NIS with daily attendance matrix and total counts.
    *   **Guru:** Single-sheet workbook (`TeachersAttendanceExport`) detailing teacher NIP, name, subjects, and daily attendance matrix.

### 📸 Media Handling & Image Compression
*   **Client-Side Compression:**
    *   Uses HTML5 Canvas API before upload in Jurnal Mengajar (`wizard.blade.php`), Absensi Selfie (`absensi-guru.blade.php`), and Profile Photo preview (`profile-section.blade.php`).
    *   Resizes images to max 1200px width at 60% quality WebP format (fallback JPEG), minimizing upload payload and server processing.
*   **HEIC / iPhone Support:**
    *   Native iOS HEIC/HEIF files handled via `maestroerror/php-heic-to-jpg`.
    *   Dedicated conversion route `/guru/jurnal-mengajar/convert-heic` enables asynchronous server-side HEIC-to-JPG conversion returning base64 for client canvas compression.
*   **Server-Side Processing & Fallback:**
    *   Managed by `app/Helpers/ImageCompressor.php` with WebP encoding and `Intervention\Image` resizing when client-side compression is bypassed or during direct profile updates.

---

## 💻 Technical Standards

### 🎨 UI/UX Guidelines
1.  **Styling & Architecture (Zero Inline `<style>`):**
    *   **NEVER** write `<style>` blocks or `@push('styles')` in Blade views.
    *   All custom styles MUST be placed in the centralized Sass architecture (`resources/sass/_tokens.scss`, `_variables.scss`, `_base.scss`, `_components.scss`, `_pages.scss`) using Material Design 3 tokens and compiled via `npm run build`.
2.  **Confirmations:** **NEVER** use native `confirm()` or `alert()`. Use **SweetAlert2** modals.
    *   **Pattern:** Use `<form class="delete-form" data-message="...">`. A global handler in `app.js`/`layout` intercepts this and shows the SweetAlert.
3.  **Dropdowns:** Use **Select2** (Bootstrap 5 Theme) for all select inputs.
    *   **Behavior:** Auto-focus search field on open.
    *   **Sorting:** Alphanumeric sorting (7A, 7B, 8A...).
4.  **Alerts:** Use Bootstrap utility classes (`bg-warning bg-opacity-10`) for persistent messages within modals to avoid conflict with auto-dismiss scripts.

### 💾 Database Conventions
*   **Codes:**
    *   `kode_guru` (Unique, String): Short code for import (e.g., "AHM").
    *   `kode_mapel` (Unique, String): Short code for import (e.g., "MAT").
*   **Eager Loading:** Always use `with()` in Controllers (e.g., `with(['guruProfile.mataPelajaran'])`) to prevent N+1 query performance issues.

### 🛠 Tools & Helpers
*   **Import/Export:** `maatwebsite/excel`.
    *   Exports must implement `WithMultipleSheets`.
    *   Imports must handle `headingRow` configuration carefully.
*   **PDF:** `barryvdh/laravel-dompdf`.

---

## 📂 Setup & Maintenance

### Commands
```bash
# Install
composer install && npm install

# Build Assets
npm run build

# Run
php artisan serve
```

### Troubleshooting
*   **Import fails silently?** Check `headingRow()` in the Import class. It likely defaults to 1 when headers are on 2.
*   **Duplicate Entry '1'?** The import logic now handles code collision by setting the previous owner's code to NULL.