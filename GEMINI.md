# GEMINI.md - Context & Documentation

## 🌍 Project Overview

**Project Name:** SMP 3 Sawan LMS (Learning Management System)
**Framework:** Laravel 10 (PHP 8.1+)
**Type:** Web Application (Monolith)
**Purpose:** Comprehensive school management system for SMP 3 Sawan, handling student/teacher data, attendance, teaching journals, scheduling, and academic records.

## 🛠 Tech Stack

*   **Backend:** PHP 8.1+, Laravel 10.x
*   **Frontend:** Blade Templates, Bootstrap 5, SASS, JavaScript
*   **Asset Bundling:** Vite
*   **Database:** MySQL (assumed standard)
*   **Key Dependencies:**
    *   `intervention/image`: Image processing (resizing, compression).
    *   `maestroerror/php-heic-to-jpg`: Support for iPhone HEIC/HEIF image uploads.
    *   `maatwebsite/excel`: Excel export/import functionality.
    *   `laravel/ui`: Authentication scaffolding.

## 🏗 Architecture & Structure

The project follows the standard Laravel MVC architecture with role-based segregation.

### 📂 Key Directories

*   `app/Http/Controllers/`: Controllers are grouped by user role:
    *   `Admin/`: Administrative tasks (User management, Master data).
    *   `Guru/`: Teacher-specific tasks (Attendance, Teaching Journals, Grades).
    *   `Siswa/`: Student portal (View grades, schedules).
    *   `KepalaSekolah/`: Principal's view (Monitoring, Reporting).
*   `routes/web.php`: Routes are strictly grouped by middleware roles (`admin`, `guru`, `siswa`, `kepala_sekolah`).
*   `resources/views/`: Views mirror the controller structure.
*   `database/migrations/`: Extensive database schema definition.

### 🔐 User Roles

1.  **Admin:** Full system access, master data management.
2.  **Kepala Sekolah (Principal):** Monitoring, reporting, and oversight.
3.  **Guru (Teacher):** Daily operations, attendance, teaching journals, grading.
4.  **Siswa (Student):** View schedule, attendance, and academic results.

## ✨ Key Features

### 1. Teacher Attendance (Absensi Guru)
*   **Mechanism:** Selfie + GPS Location Verification.
*   **Constraints:** Must be within a configured radius of the school.
*   **Configuration:** School coordinates and radius set in `.env` (`SCHOOL_LATITUDE`, `SCHOOL_LONGITUDE`).
*   **Docs:** See `ABSENSI_GURU_README.md`.

### 2. HEIC Image Support
*   **Problem:** iPhones save images in HEIC format, which web browsers don't natively support.
*   **Solution:** Automatic backend conversion of HEIC/HEIF to JPG/WebP on upload.
*   **Implementation:** Used in Profile photos and Teaching Journals (`JurnalMengajar`).
*   **Docs:** See `HEIC_SUPPORT_README.md`.

### 3. Teaching Journal (Jurnal Mengajar)
*   Teachers record daily teaching activities.
*   Supports photo evidence uploads (with HEIC support).
*   Linked to class schedules.

## 🚀 Building & Running

### Prerequisites
*   PHP >= 8.1
*   Composer
*   Node.js & NPM

### Setup Commands

```bash
# Install PHP dependencies
composer install

# Install JS dependencies
npm install

# Setup Environment
cp .env.example .env
php artisan key:generate

# Database Migration
php artisan migrate

# Storage Link (Crucial for image uploads)
php artisan storage:link
```

### Development Commands

```bash
# Start local development server
php artisan serve

# Watch assets (Vite)
npm run dev
```

### Production Build

```bash
# Build frontend assets
npm run build
```

## 📝 Conventions & Standards

*   **Role-Based Access:** Always ensure new routes/features are protected by the appropriate role middleware.
*   **Image Handling:** Use the `ImageCompressor` helper for handling uploads to ensure consistency and HEIC support.
*   **Formatting:** Follow PSR-12 for PHP.
*   **Routes:** Named routes are preferred (e.g., `route('guru.jurnal-mengajar.index')`).
