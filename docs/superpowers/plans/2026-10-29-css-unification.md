# CSS Unification (Pre-Material) Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Extract and unify all inline `<style>` blocks (±2,600 lines across 43 Blade files) into a structured SASS module hierarchy under `resources/sass/`, establish canonical visual tokens (blue primary `#0d47a1`), eliminate cross-layout CSS leaks via strict `<body>` scoping, preserve all existing production utilities (form focus, text contrast, photo responsive sizing), and clean unneeded CDN links while leaving 100% of runtime behavior and physical print formats (`print.blade.php`) intact.

**Architecture:** A Vite-compiled 5-module SASS hierarchy (`_tokens.scss` → `_variables.scss` → `_base.scss` → `_components.scss` → `_pages.scss` → `app.scss`). Layouts get strict body class scoping (`.layout-auth`, `.layout-guest`) to prevent global style pollution. Blade files get internal `<style>` tags deleted while keeping dynamic server HTML attributes (`style="..."`) untouched.

**Tech Stack:** Laravel 10/12, Vite 6, SASS (Dart Sass 1.56), Bootstrap 5.2.3, PHPUnit 11.

**Spec:** `docs/superpowers/specs/2026-10-29-css-unification-design.md`

## Global Constraints

- **Primary Color Anchor:** `#0d47a1` (with `#1565c0` gradient variant for hero sections).
- **Preserve Existing Production CSS:** Never drop existing utilities from `app.scss` (form focus states, buttons, text contrast helpers, alerts, and responsive photo sizing).
- **Print Exclusion:** `resources/views/guru/jurnal-mengajar/print.blade.php` MUST retain its internal `<style>` block (physical print layout).
- **Dynamic Style Attributes:** Do NOT remove HTML `style="..."` attributes containing dynamic PHP variables (e.g. `$selectedSize`, honeypot fields).
- **Cross-Platform CLI:** Commands must run safely on Windows PowerShell without relying on bash utilities (`touch`, `grep`, `rm`).
- **Verification Gate:** Every task MUST end with a passing `npm run build` command. The final task verifies both `npm run build` and `php artisan test` (73 passed).

---

## File Structure & Module Responsibilities

```text
resources/sass/
├── _tokens.scss        # Design tokens: primary colors, surface/grays, card radius & elevation
├── _variables.scss     # Bootstrap SCSS variable overrides (bridges _tokens to Bootstrap)
├── _base.scss          # Shell layout: .sidebar (solid #0d47a1), .main-content, .layout-auth (+ auth components), .layout-guest, global base utilities (form focus, text contrast, alert, table hover)
├── _components.scss    # Reusable components: pagination, calendar, cards, avatar/photo, select2, retained-chip
├── _pages.scss         # Scoped feature styles: scanner, wizard jurnal, matrix, timeline, hover-overlay/shadow
└── app.scss            # Master entrypoint importing tokens, variables, bootstrap, base, components, pages
```

---

### Task 1: Create `_tokens.scss` and Refactor `_variables.scss` + `app.scss` Entrypoint

**Files:**
- Create: `resources/sass/_tokens.scss`
- Modify: `resources/sass/_variables.scss`
- Modify: `resources/sass/app.scss`
- Create: `resources/sass/_base.scss` (stub / initial migration)
- Create: `resources/sass/_components.scss` (stub)
- Create: `resources/sass/_pages.scss` (stub)

**Interfaces:**
- Consumes: Standard SASS / Bootstrap 5 SCSS
- Produces: Design tokens (`$color-primary: #0d47a1`, `$color-primary-variant: #1565c0`, `$radius-card`, `$shadow-card`, etc.) used across all modules.

- [ ] **Step 1: Create `resources/sass/_tokens.scss`**

Write design tokens:

```scss
// Design Tokens — LMS SMPN 3 Sawan (Pre-Material)

// Color Palette
$color-primary: #0d47a1;
$color-primary-variant: #1565c0;
$color-primary-hover: #0a3d8a;
$color-surface: #f8fafc;
$color-body-text: #2c3e50;
$color-muted-text: #6c757d;
$color-border: #e9ecef;

// Status Colors
$color-success: #28a745;
$color-info: #0dcaf0;
$color-warning: #ffc107;
$color-danger: #dc3545;
$color-secondary: #6c757d;

// Cards & Elevation
$radius-card: 0.375rem;
$radius-card-lg: 12px;
$radius-auth-card: 20px;
$shadow-sm: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
$shadow-md: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
$shadow-hover: 0 0.5rem 1rem rgba(0, 0, 0, 0.25);
```

- [ ] **Step 2: Refactor `resources/sass/_variables.scss` to use tokens**

Update `resources/sass/_variables.scss`:

```scss
@import "tokens";

// Body
$body-bg: $color-surface;

// Typography
$font-family-sans-serif: "Nunito", sans-serif;
$font-size-base: 0.9rem;
$line-height-base: 1.6;

// Primary Override
$primary: $color-primary;
```

- [ ] **Step 3: Create stub files for `_base.scss`, `_components.scss`, and `_pages.scss`**

Ensure the files exist before importing them in `app.scss`.

- [ ] **Step 4: Update `resources/sass/app.scss` to import modular hierarchy**

Update `resources/sass/app.scss`:

```scss
// Fonts
@import url("https://fonts.bunny.net/css?family=Nunito");

// Tokens & Variables
@import "tokens";
@import "variables";

// Bootstrap
@import "bootstrap/scss/bootstrap";

// Custom Modules
@import "base";
@import "components";
@import "pages";
```

- [ ] **Step 5: Verify build passes**

Run: `npm run build`  
Expected: PASS (generates `public/build/assets/app-*.css`).

- [ ] **Step 6: Commit**

```bash
git add resources/sass/_tokens.scss resources/sass/_variables.scss resources/sass/app.scss resources/sass/_base.scss resources/sass/_components.scss resources/sass/_pages.scss
git commit -m "style: add _tokens.scss and setup modular app.scss entrypoint structure"
```

---

### Task 2: Build `_base.scss` and Refactor Layout Shells (`app`, `semester`, `auth`, `guest`)

**Files:**
- Modify: `resources/sass/_base.scss`
- Modify: `resources/views/layouts/app.blade.php` (remove inline `<style>`)
- Modify: `resources/views/layouts/semester.blade.php` (remove inline `<style>`)
- Modify: `resources/views/layouts/auth.blade.php` (add `<body class="layout-auth">`, remove inline `<style>`)
- Modify: `resources/views/layouts/guest.blade.php` (add `<body class="layout-guest">`, replace Bootstrap CDN with `@vite`, remove inline `<style>`)

**Interfaces:**
- Consumes: `$color-primary`, `$color-surface`, `$font-family-sans-serif` from `_tokens.scss`
- Produces: Global shell rules (`.sidebar` solid `#0d47a1`, `.main-content`, `.content-area`, `.sidebar-brand`, `.sidebar-backdrop`, `.layout-auth` with auth components, `.layout-guest`, and preserved production utilities: form focus, button hover, table hover, text contrast helpers, alert style).

- [ ] **Step 1: Populate `resources/sass/_base.scss`**

Write `resources/sass/_base.scss` including all layout shells and preserved production utilities:

```scss
// Base Shell, Layout Scoping & Production Utilities

// --- Admin / App / Semester Shell ---
.sidebar {
    min-height: 100vh;
    background: $color-primary !important;
    box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
    position: fixed;
    top: 0;
    left: 0;
    bottom: 0;
    z-index: 1000;
    overflow-y: auto;

    .nav-link {
        color: rgba(255, 255, 255, 0.9);
        padding: 0.75rem 1rem;
        border-radius: 0.375rem;
        margin-bottom: 0.25rem;
        transition: all 0.3s ease;

        &:hover,
        &.active {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.2);
            transform: translateX(5px);
        }
    }
}

.main-content {
    background-color: $color-surface;
    min-height: 100vh;
    transition: margin-left 0.3s ease-in-out;
}

.sidebar-brand {
    padding: 1.5rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);

    img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
    }
}

.content-area {
    padding: 2rem;
}

.navbar-brand {
    font-weight: 600;
}

.sidebar-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    z-index: 999;
    width: 100vw;
    height: 100vh;
    background-color: rgba(0, 0, 0, 0.5);
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease-in-out;

    &.show {
        opacity: 1;
        visibility: visible;
    }
}

// Sidebar responsive drawer breakpoints
@media (max-width: 767.98px) {
    .sidebar {
        transform: translateX(-100%);
        transition: transform 0.3s ease-in-out;
        width: 280px;

        &.show {
            transform: translateX(0);
        }
    }

    .main-content {
        margin-left: 0;
        width: 100%;
    }
}

@media (min-width: 768px) {
    .main-content {
        margin-left: 25%;
    }
    .sidebar {
        width: 25%;
    }
}

@media (min-width: 992px) {
    .main-content {
        margin-left: 16.66667%;
    }
    .sidebar {
        width: 16.66667%;
    }
}

// --- Preserved Production Utilities (Form, Button, Table, Alert, Contrast) ---
.form-control:focus,
.form-select:focus {
    border-color: $color-primary;
    box-shadow: 0 0 0 0.2rem rgba(13, 71, 161, 0.25);
}

.btn-primary {
    background-color: $color-primary;
    border-color: $color-primary;

    &:hover {
        background-color: $color-primary-hover;
        border-color: $color-primary-hover;
    }
}

.table-hover tbody tr:hover {
    background-color: rgba(13, 71, 161, 0.05);
}

.alert {
    border: none;
    border-radius: 0.375rem;
}

.text-high-contrast {
    color: #2c3e50 !important;
    font-weight: 500;
}

.text-medium-contrast {
    color: #495057 !important;
}

.text-subtle {
    color: #6c757d !important;
}

// --- Scoped Layout: Auth (`/login`, passwords, register) ---
.layout-auth {
    background: $color-primary;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: 'Nunito', sans-serif;
    position: relative;

    &::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"><g fill="none" fill-rule="evenodd"><g fill="%23ffffff" fill-opacity="0.05"><circle cx="30" cy="30" r="4"/></g></svg>');
        z-index: 1;
    }

    .auth-card {
        background: white;
        border-radius: $radius-auth-card;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        overflow: hidden;
        max-width: 420px;
        width: 100%;
        margin: 2rem;
        position: relative;
        z-index: 2;
    }

    .auth-header {
        background: $color-primary;
        color: white;
        text-align: center;
        padding: 3rem 2rem 2rem;
        position: relative;

        &::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 20px;
            background: white;
            border-radius: 20px 20px 0 0;
        }

        i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.9;
        }

        h3 {
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
    }

    .auth-body {
        padding: 2.5rem 2rem 2rem;
    }

    .auth-footer {
        text-align: center;
        padding: 1rem 2rem 2.5rem;
    }

    .forgot-password {
        color: $color-primary;
        text-decoration: none;
        font-size: 0.95rem;
        font-weight: 500;
        transition: all 0.3s ease;

        &:hover {
            color: $color-primary-hover;
            text-decoration: none;
            transform: translateY(-1px);
        }
    }
}

// --- Scoped Layout: Guest (Public Pages) ---
.layout-guest {
    font-family: 'Poppins', sans-serif;
    background-color: #f8f9fa;
    min-height: 100vh;
    display: flex;
    flex-direction: column;

    .guest-header {
        background: rgba(13, 71, 161, 0.95);
        backdrop-filter: blur(10px);
        border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    }

    .nav-link {
        color: rgba(255, 255, 255, 0.8) !important;
        font-weight: 500;
        text-transform: uppercase;
        font-size: 0.9rem;
        padding: 1rem !important;

        &:hover,
        &.active {
            color: #fff !important;
        }
    }

    .dropdown-menu {
        border: none;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    .main-content {
        flex: 1;
        padding-top: 2rem;
        padding-bottom: 2rem;
        margin-left: 0 !important;
        width: 100% !important;
    }

    footer {
        margin-top: auto;
    }
}
```

- [ ] **Step 2: Clean `resources/views/layouts/app.blade.php`**

Remove the inline `<style>` block from `app.blade.php` (around lines 36-132).

- [ ] **Step 3: Clean `resources/views/layouts/semester.blade.php`**

Remove the inline `<style>` block from `semester.blade.php` (around lines 26-130).

- [ ] **Step 4: Scoping & Cleaning `resources/views/layouts/auth.blade.php`**

Update `auth.blade.php`:
1. Change `<body>` to `<body class="layout-auth">`.
2. Remove the inline `<style>` block entirely. (Styling is now safely managed under `.layout-auth` in `_base.scss`).

- [ ] **Step 5: Scoping & CDN cleanup in `resources/views/layouts/guest.blade.php`**

Update `guest.blade.php`:
1. Change `<body>` to `<body class="layout-guest">`.
2. Replace Bootstrap 5 CDN `<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">` with `@vite(['resources/sass/app.scss', 'resources/js/app.js'])`.
3. Remove the inline `<style>` block.

- [ ] **Step 6: Run build verification**

Run: `npm run build`  
Expected: PASS.

- [ ] **Step 7: Commit**

```bash
git add resources/sass/_base.scss resources/views/layouts/app.blade.php resources/views/layouts/semester.blade.php resources/views/layouts/auth.blade.php resources/views/layouts/guest.blade.php
git commit -m "refactor(layouts): extract base shell to _base.scss and apply body scoping for auth and guest"
```

---

### Task 3: Extract Shared UI Components to `_components.scss` & Clean Component Blade Files

**Files:**
- Modify: `resources/sass/_components.scss`
- Modify: 26 Blade files (strip `<style>` blocks):
  - `resources/views/components/profile-photo.blade.php`
  - `resources/views/welcome.blade.php`
  - `resources/views/admin/dashboard.blade.php`
  - `resources/views/guru/dashboard.blade.php`
  - `resources/views/kepala-sekolah/dashboard.blade.php`
  - `resources/views/admin/absensi/index.blade.php`
  - `resources/views/admin/absensi/guru/index.blade.php`
  - `resources/views/admin/absensi/siswa/index.blade.php`
  - `resources/views/admin/absensi/guru/detail.blade.php`
  - `resources/views/admin/absensi/siswa/detail.blade.php`
  - `resources/views/guru/absensi/siswa/detail.blade.php`
  - `resources/views/guru/absensi-guru/absensi-guru.blade.php`
  - `resources/views/kepala-sekolah/absensi/index.blade.php`
  - `resources/views/kepala-sekolah/absensi/guru/index.blade.php`
  - `resources/views/kepala-sekolah/absensi/guru/detail.blade.php`
  - `resources/views/kepala-sekolah/absensi/siswa/index.blade.php`
  - `resources/views/kepala-sekolah/absensi/siswa/detail.blade.php`
  - `resources/views/admin/semester/dashboard.blade.php`
  - `resources/views/admin/data-sekolah/index.blade.php`
  - `resources/views/kepala-sekolah/agenda-guru/index.blade.php`
  - `resources/views/kepala-sekolah/jurnal-mengajar/index.blade.php`
  - `resources/views/kepala-sekolah/data/guru/index.blade.php`
  - `resources/views/kepala-sekolah/data/siswa/index.blade.php`
  - `resources/views/admin/guru/create.blade.php`
  - `resources/views/admin/guru/edit.blade.php`
  - `resources/views/admin/siswa/index.blade.php`

**Interfaces:**
- Consumes: `$color-primary`, `$color-success`, `$color-info`, `$color-warning`, `$color-danger`, `$color-secondary` from `_tokens.scss`
- Produces: Reusable component classes (`.card`, `.hover-card`, `.card-stats`, `.card-modern`, `.pagination-container`, `.btn-pagination`, `.calendar-grid`, `.calendar-day` + state classes, `.avatar-circle`, `.profile-photo-wrapper`, responsive `.teacher-photo` & `.student-photo`, `.stat-mini`, `.select2-container--bootstrap-5`, `.retained-chip`).

- [ ] **Step 1: Write `resources/sass/_components.scss`**

Write `resources/sass/_components.scss` with complete responsive rules:

```scss
// Shared UI Components

// --- Card Variants ---
.card {
    border: 1px solid rgba(0, 0, 0, 0.125);
    box-shadow: 0 0.125rem 0.5rem rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;

    &:hover {
        border-color: rgba(13, 71, 161, 0.15);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
}

.hover-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;

    &:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }
}

.card-stats {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    transition: all 0.3s ease;

    &:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        transform: translateY(-5px);
    }
}

.card-modern {
    border: none;
    border-left: 4px solid var(--bs-primary);
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);

    &:hover {
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
        transform: translateY(-2px);
    }
}

// --- Pagination Controls ---
.pagination-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.pagination-info {
    color: #6c757d;
    font-size: 0.9rem;
}

.pagination-controls {
    display: flex;
    gap: 0.5rem;
    align-items: center;
}

.btn-pagination {
    padding: 0.5rem 1rem;
    border: 1px solid #dee2e6;
    background-color: #fff;
    color: #495057;
    text-decoration: none;
    border-radius: 0.375rem;
    font-size: 0.9rem;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;

    &:hover:not(:disabled) {
        background-color: #0d6efd;
        color: #fff;
        border-color: #0d6efd;
    }

    &:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
}

.pagination-current {
    padding: 0.5rem 1rem;
    background-color: #0d6efd;
    color: #fff;
    border-radius: 0.375rem;
    font-weight: 600;
    min-width: 2.5rem;
    text-align: center;
}

@media (max-width: 576px) {
    .pagination-container {
        flex-direction: column;
        text-align: center;
    }

    .pagination-controls {
        width: 100%;
        justify-content: center;
    }
}

// --- Attendance Monthly Calendar ---
.calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 3px;
    margin-bottom: 1rem;
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
}

.calendar-header {
    text-align: center;
    font-weight: 600;
    font-size: 0.7rem;
    padding: 0.4rem 0.2rem;
    background-color: #f8f9fa;
    color: #495057;
    border-radius: 0.25rem;
}

.calendar-day {
    aspect-ratio: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    border-radius: 0.25rem;
    background-color: #fff;
    border: 1px solid #e9ecef;
    cursor: pointer;
    transition: all 0.2s ease;
    position: relative;
    min-height: 35px;
    max-height: 50px;

    &:hover:not(.empty) {
        transform: scale(1.05);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        z-index: 1;
    }

    &.empty {
        background-color: #f8f9fa;
        border: none;
        cursor: default;

        &:hover {
            transform: none;
            box-shadow: none;
        }
    }

    &.today {
        border: 2px solid #0d6efd;
        font-weight: bold;
    }

    &.hadir {
        background-color: $color-success;
        color: white;
        border-color: $color-success;
    }

    &.izin {
        background-color: $color-info;
        color: #000;
        border-color: $color-info;
    }

    &.sakit {
        background-color: $color-warning;
        color: #000;
        border-color: $color-warning;
    }

    &.alpha {
        background-color: $color-danger;
        color: white;
        border-color: $color-danger;
    }

    &.terlambat,
    &.libur {
        background-color: $color-secondary;
        color: white;
        border-color: $color-secondary;
    }

    .day-number {
        position: relative;
        z-index: 1;
        font-weight: 600;
    }
}

@media (max-width: 768px) {
    .calendar-grid {
        max-width: 100%;
    }

    .calendar-day {
        min-height: 32px;
        max-height: 40px;
        font-size: 0.7rem;
    }

    .calendar-header {
        font-size: 0.65rem;
        padding: 0.3rem 0.15rem;
    }
}

// --- Stat Mini Item ---
.stat-mini {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.4rem 0.6rem;
    border-radius: 0.25rem;
    font-size: 0.75rem;
    margin-bottom: 0.4rem;

    &:last-child {
        margin-bottom: 0;
    }

    &.hadir {
        background-color: rgba(40, 167, 69, 0.1);
        color: #28a745;
    }

    &.izin {
        background-color: rgba(255, 193, 7, 0.1);
        color: #856404;
    }

    &.sakit {
        background-color: rgba(13, 202, 240, 0.1);
        color: #055160;
    }

    &.alpha {
        background-color: rgba(220, 53, 69, 0.1);
        color: #dc3545;
    }
}

// --- Avatar & Profile Photo ---
.profile-photo-wrapper {
    display: inline-block;
    position: relative;
    overflow: hidden;
    border-radius: 50%;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s ease, box-shadow 0.2s ease;

    &.cursor-pointer {
        cursor: pointer;
    }

    &:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
}

.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 1.2rem;
}

.teacher-photo,
.student-photo {
    width: 60px;
    height: 60px;
    border: 2px solid var(--bs-border-color);

    img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .default-avatar {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }
}

// Responsive Photo Sizes (Preserved from legacy app.scss)
@media (max-width: 768px) {
    .teacher-photo,
    .student-photo {
        width: 45px;
        height: 45px;

        .default-avatar {
            font-size: 1rem;
        }
    }
}

@media (max-width: 576px) {
    .teacher-photo,
    .student-photo {
        width: 35px;
        height: 35px;

        .default-avatar {
            font-size: 0.9rem;
        }
    }
}

// --- Select2 Overrides (Bootstrap 5 theme) ---
.select2-container--bootstrap-5 {
    font-size: 0.875rem !important;

    .select2-selection {
        font-size: 0.875rem !important;
        min-height: 38px !important;
        border-color: #dee2e6 !important;
        border-radius: 0.375rem !important;
        padding: 3px 6px !important;
    }

    .select2-selection--multiple .select2-selection__rendered {
        padding: 0 !important;
        margin: 0 !important;
    }

    .select2-selection--multiple .select2-selection__choice {
        font-size: 0.8125rem !important;
        font-weight: 500 !important;
        padding: 2px 8px !important;
        margin: 2px 4px 2px 0 !important;
        background-color: #e0e7ff !important;
        border: 1px solid #c7d2fe !important;
        color: #3730a3 !important;
        border-radius: 4px !important;
        line-height: 1.4 !important;
        display: inline-flex !important;
        align-items: center !important;
    }

    .select2-selection--multiple .select2-selection__choice__remove {
        color: #4338ca !important;
        margin-right: 5px !important;
        font-weight: bold !important;
        font-size: 0.875rem !important;

        &:hover {
            color: #dc2626 !important;
        }
    }

    .select2-search--inline .select2-search__field {
        font-size: 0.875rem !important;
        margin-top: 4px !important;
        height: 24px !important;
        line-height: 1.4 !important;
    }

    .select2-dropdown {
        font-size: 0.875rem !important;
        border-color: #86b7fe !important;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08) !important;
    }

    .select2-results__option {
        padding: 6px 12px !important;
        font-size: 0.875rem !important;

        &--highlighted {
            background-color: #0d6efd !important;
        }
    }
}

// --- Retained Student Chip ---
.retained-chip {
    background-color: #f8d7da;
    color: #842029;
    border: 1px solid #f5c2c7;
    border-radius: 20px;
    padding: 5px 12px;
    font-size: 0.85rem;
    display: inline-flex;
    align-items: center;
    gap: 8px;

    .btn-remove {
        cursor: pointer;
        color: #842029;
        font-size: 0.95rem;

        &:hover {
            color: #b02a37;
        }
    }
}
```

- [ ] **Step 2: Delete inline `<style>` tags from all 26 shared component Blade files**

Remove `<style>...</style>` blocks from:
- `resources/views/components/profile-photo.blade.php`
- `resources/views/welcome.blade.php`
- `resources/views/admin/dashboard.blade.php`
- `resources/views/guru/dashboard.blade.php`
- `resources/views/kepala-sekolah/dashboard.blade.php`
- `resources/views/admin/absensi/index.blade.php`
- `resources/views/admin/absensi/guru/index.blade.php`
- `resources/views/admin/absensi/siswa/index.blade.php`
- `resources/views/admin/absensi/guru/detail.blade.php`
- `resources/views/admin/absensi/siswa/detail.blade.php`
- `resources/views/guru/absensi/siswa/detail.blade.php`
- `resources/views/guru/absensi-guru/absensi-guru.blade.php`
- `resources/views/kepala-sekolah/absensi/index.blade.php`
- `resources/views/kepala-sekolah/absensi/guru/index.blade.php`
- `resources/views/kepala-sekolah/absensi/guru/detail.blade.php`
- `resources/views/kepala-sekolah/absensi/siswa/index.blade.php`
- `resources/views/kepala-sekolah/absensi/siswa/detail.blade.php`
- `resources/views/admin/semester/dashboard.blade.php`
- `resources/views/admin/data-sekolah/index.blade.php`
- `resources/views/kepala-sekolah/agenda-guru/index.blade.php`
- `resources/views/kepala-sekolah/jurnal-mengajar/index.blade.php`
- `resources/views/kepala-sekolah/data/guru/index.blade.php`
- `resources/views/kepala-sekolah/data/siswa/index.blade.php`
- `resources/views/admin/guru/create.blade.php`
- `resources/views/admin/guru/edit.blade.php`
- `resources/views/admin/siswa/index.blade.php`

- [ ] **Step 3: Run build verification**

Run: `npm run build`  
Expected: PASS.

- [ ] **Step 4: Commit**

```bash
git add resources/sass/_components.scss resources/views/
git commit -m "refactor(components): extract shared UI components to _components.scss and strip inline styles"
```

---

### Task 4: Extract Feature-Specific Styles to `_pages.scss` & Clean Remaining Page Blade Files

**Files:**
- Modify: `resources/sass/_pages.scss`
- Modify: 12 Blade files (strip `<style>` blocks):
  - `resources/views/guru/absensi/absensi-siswa.blade.php` (remove 3 inline `<style>` blocks)
  - `resources/views/guru/jurnal-mengajar/wizard.blade.php`
  - `resources/views/admin/jadwal-mapel/jadwal.blade.php`
  - `resources/views/guru/jadwal-mengajar/jadwal-mengajar.blade.php`
  - `resources/views/siswa/jadwal-pelajaran/jadwal-mapel.blade.php`
  - `resources/views/siswa/absen/index.blade.php`
  - `resources/views/guest/galeri.blade.php`
  - `resources/views/guest/berita/index.blade.php`
  - `resources/views/guest/prestasi/index.blade.php`
  - `resources/views/kepala-sekolah/data/semester/show.blade.php`
  - `resources/views/guru/agenda-guru/agenda-guru.blade.php`
  - `resources/views/kepala-sekolah/agenda/agenda-kepsek.blade.php`

**Interfaces:**
- Consumes: SCSS animation keyframes & grid properties
- Produces: Scoped page rules (`.scanner-container`, `#scanner-placeholder`, `.scan-line`, `.scan-success-overlay`, `.wizard-step`, `.step-indicator`, `.step-circle`, `.step-connector`, `.draggable-item`, `.drop-zone`, `.jadwal-table`, `.timeline-marker`, `@keyframes scan`, `@keyframes fadeIn`).

- [ ] **Step 1: Populate `resources/sass/_pages.scss`**

Write `resources/sass/_pages.scss`:

```scss
// Scoped Feature & Page Styles

// --- Animations ---
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes scan {
    0% {
        transform: translateY(0);
        opacity: 1;
    }
    100% {
        transform: translateY(400px);
        opacity: 0;
    }
}

// --- QR Scanner (Guru Absensi Siswa & Siswa Absen) ---
.scan-line {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, transparent, #28a745, transparent);
    animation: scan 0.5s ease-out;
    z-index: 10;
}

.scan-success-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(40, 167, 69, 0.9);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 0.375rem;
    z-index: 20;
    animation: fadeIn 0.3s ease-out;
}

.scanner-container {
    position: relative;
}

#scanner-placeholder {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 400px;
    border: 2px dashed #0d6efd;
    border-radius: 0.375rem;
    background: #f8f9fa;
    overflow: hidden;

    > div {
        border-radius: 0.375rem !important;
    }

    video {
        width: 100% !important;
        height: auto !important;
        max-width: 400px;
        border-radius: 0.375rem !important;
        background: #000;
    }

    canvas {
        border-radius: 0.375rem !important;
    }
}

.scanning-indicator {
    position: absolute;
    bottom: 10px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 10;
}

.html5-qrcode-element {
    border: 2px dashed #007bff !important;
    border-radius: 0.375rem !important;
}

#html5-qr-code-select-camera,
#html5-qr-code-start-button,
#html5-qr-code-stop-button {
    display: none !important;
}

.attendance-list {
    &::-webkit-scrollbar {
        width: 6px;
    }
    &::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 3px;
    }
    &::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 3px;
        &:hover {
            background: #a8a8a8;
        }
    }
}

@media (max-width: 768px) {
    #scanner-placeholder {
        min-height: 300px !important;
        padding: 0 !important;
        border: none !important;
        background: #000;

        video {
            max-width: 100% !important;
            height: 100% !important;
            object-fit: cover;
        }
    }

    .scanner-container {
        margin: -1rem -1rem 1rem -1rem;
    }
}

// --- Jurnal Mengajar Wizard ---
.wizard-step {
    animation: fadeIn 0.3s;
}

.step-indicator {
    position: relative;

    &.active {
        .step-circle {
            background-color: #0d6efd;
            color: white;
        }
        .step-label {
            color: #0d6efd;
            font-weight: 600;
        }
    }

    &.completed .step-circle {
        background-color: #198754;
        color: white;
    }
}

.step-circle {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background-color: #e9ecef;
    color: #6c757d;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    font-weight: bold;
    margin: 0 auto 10px;
    transition: all 0.3s;
}

.step-label {
    font-size: 14px;
    font-weight: 500;
    color: #6c757d;
}

.step-connector {
    height: 2px;
    background-color: #e9ecef;
    margin: 25px 20px 0;
}

// --- Schedule Drag-and-Drop Matrix ---
.draggable-item {
    cursor: grab;
    transition: transform 0.1s ease, box-shadow 0.1s ease;

    &:active {
        cursor: grabbing;
    }

    &.dragging {
        opacity: 0.5;
        transform: scale(0.95);
    }
}

.drop-zone {
    transition: background-color 0.2s ease;

    &.drag-over {
        background-color: #e9ecef !important;
        border: 2px dashed #0d6efd !important;
    }
}

// --- Full Schedule Table (Siswa Jadwal Mapel) ---
.jadwal-table {
    table-layout: fixed;
    width: 1000px;
    min-width: 1000px;

    th:nth-child(1), td:nth-child(1) { width: 70px; min-width: 70px; }
    th:nth-child(2), td:nth-child(2) { width: 90px; min-width: 90px; }
    th:nth-child(3), td:nth-child(3),
    th:nth-child(4), td:nth-child(4),
    th:nth-child(5), td:nth-child(5),
    th:nth-child(6), td:nth-child(6),
    th:nth-child(7), td:nth-child(7),
    th:nth-child(8), td:nth-child(8) { width: 140px; min-width: 140px; }
}

// --- Miscellaneous Utilities ---
.hover-overlay {
    transition: opacity 0.3s ease;
}

.position-relative:hover .hover-overlay {
    opacity: 1 !important;
}

.hover-shadow {
    transition: all 0.3s ease;
    &:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }
}

.timeline-marker {
    width: 15px;
    height: 15px;
    border-radius: 50%;
    flex-shrink: 0;
    margin-top: 5px;
}
```

- [ ] **Step 2: Strip inline `<style>` tags from remaining 12 feature Blade files**

Remove `<style>...</style>` blocks from:
- `resources/views/guru/absensi/absensi-siswa.blade.php` (remove all 3 style tags)
- `resources/views/guru/jurnal-mengajar/wizard.blade.php`
- `resources/views/admin/jadwal-mapel/jadwal.blade.php`
- `resources/views/guru/jadwal-mengajar/jadwal-mengajar.blade.php`
- `resources/views/siswa/jadwal-pelajaran/jadwal-mapel.blade.php`
- `resources/views/siswa/absen/index.blade.php`
- `resources/views/guest/galeri.blade.php`
- `resources/views/guest/berita/index.blade.php`
- `resources/views/guest/prestasi/index.blade.php`
- `resources/views/kepala-sekolah/data/semester/show.blade.php`
- `resources/views/guru/agenda-guru/agenda-guru.blade.php`
- `resources/views/kepala-sekolah/agenda/agenda-kepsek.blade.php`

*REMINDER:* Do NOT touch `resources/views/guru/jurnal-mengajar/print.blade.php`!

- [ ] **Step 3: Run build verification**

Run: `npm run build`  
Expected: PASS.

- [ ] **Step 4: Commit**

```bash
git add resources/sass/_pages.scss resources/views/
git commit -m "refactor(pages): extract feature-specific styles to _pages.scss and clean remaining Blade files"
```

---

### Task 5: Finalization, Housekeeping, and Full Test Suite Verification

**Files:**
- Delete: `resources/css/app.css`

**Interfaces:**
- Consumes: Complete project state
- Produces: Clean codebase with 0 unused legacy CSS, 100% passing build and PHP test suite.

- [ ] **Step 1: Delete unused empty file `resources/css/app.css`**

Delete `resources/css/app.css` using `git rm resources/css/app.css`.

- [ ] **Step 2: Verify zero inline `<style>` blocks remain (except `print.blade.php`)**

Check remaining style tags using PowerShell:
```powershell
Get-ChildItem -Path resources/views -Recurse -Filter *.blade.php | Select-String -Pattern "<style" | Select-Object -ExpandProperty Path -Unique
```
Expected output: Exactly ONE line — `resources\views\guru\jurnal-mengajar\print.blade.php`.

- [ ] **Step 3: Run full Vite build**

Run: `npm run build`  
Expected: PASS with single compiled asset bundle (`public/build/assets/app-*.css`).

- [ ] **Step 4: Run full PHP test suite**

Run: `php artisan test`  
Expected: PASS (73 passed).

- [ ] **Step 5: Commit finalization**

```bash
git add -A
git commit -m "chore: remove unused resources/css/app.css and finalize CSS unification"
```
