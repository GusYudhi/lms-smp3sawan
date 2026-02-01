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
    *   **Structure:** One-to-Many (Strict). A teacher (`guru_profiles`) teaches exactly **one** main subject (`mata_pelajaran_id`).
    *   **Reason:** Simplifies scheduling logic and data integrity. Legacy pivot tables or string-based columns have been removed.
    *   **Code:** `GuruProfile` belongsTo `MataPelajaran`. `MataPelajaran` hasMany `GuruProfile`.

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

### 📍 Attendance (Absensi)
*   **Guru:** Selfie + GPS Geofencing. Real-time updates via Ajax polling on Principal's dashboard.
*   **Siswa:** Daily and per-subject attendance.

### 📸 Media Handling
*   **HEIC Support:** Native iPhone images are auto-converted to JPG/WebP on upload.
*   **Compression:** Server-side resizing using `Intervention\Image`.

---

## 💻 Technical Standards

### 🎨 UI/UX Guidelines
1.  **Confirmations:** **NEVER** use native `confirm()`. Use **SweetAlert2** modals.
    *   **Pattern:** Use `<form class="delete-form" data-message="...">`. A global handler in `app.js`/`layout` intercepts this and shows the SweetAlert.
2.  **Dropdowns:** Use **Select2** (Bootstrap 5 Theme) for all select inputs.
    *   **Behavior:** Auto-focus search field on open.
    *   **Sorting:** Alphanumeric sorting (7A, 7B, 8A...).
3.  **Alerts:** Use Bootstrap utility classes (`bg-warning bg-opacity-10`) for persistent messages within modals to avoid conflict with auto-dismiss scripts.

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