# Restructure Database - Guru Profiles

Dokumen ini menjelaskan perubahan struktur database yang telah dilakukan untuk memperbaiki tabel guru dan user.

## Perubahan yang Dilakukan

### 1. Tabel `teacher_profiles` → `guru_profiles`

**Perubahan Nama:** Tabel `teacher_profiles` telah diubah namanya menjadi `guru_profiles`

**Struktur Kolom Baru:**

-   `id` (Primary Key)
-   `user_id` (Foreign Key ke tabel users)
-   `nama` (String) - Nama lengkap guru
-   `nip` (String, Nullable, Unique) - Nomor Induk Pegawai
-   `foto_profil` (String, Nullable) - Path foto profil guru
-   `nomor_telepon` (String, Nullable) - Nomor telepon guru
-   `email` (String, Nullable) - Email guru
-   `jenis_kelamin` (Enum: L/P, Nullable) - Jenis kelamin
-   `tempat_lahir` (String, Nullable) - Tempat lahir
-   `tanggal_lahir` (Date, Nullable) - Tanggal lahir
-   `status_kepegawaian` (Enum: PNS/PPPK/GTT/GTY, Nullable) - Status kepegawaian
-   `golongan` (String, Nullable) - Golongan pegawai
-   `mata_pelajaran` (JSON, Nullable) - Array mata pelajaran yang diampu
-   `wali_kelas` (String, Nullable) - Kelas yang menjadi wali
-   `password` (String, Nullable, Hashed) - Password khusus guru jika diperlukan
-   `is_active` (Boolean, Default: true) - Status aktif guru
-   `created_at` (Timestamp)
-   `updated_at` (Timestamp)

**Kolom yang Dihapus:**

-   `alamat` - Alamat lengkap
-   `pendidikan_terakhir` - Pendidikan terakhir
-   `tahun_mulai_mengajar` - Tahun mulai mengajar
-   `sertifikat` - Sertifikat yang dimiliki

### 2. Tabel `users` - Penyederhanaan

**Kolom yang Dihapus dari tabel users:**

-   `nomor_induk` - Dipindahkan ke guru_profiles sebagai 'nip'
-   `profile_photo` - Dipindahkan ke guru_profiles sebagai 'foto_profil'
-   `nomor_telepon` - Dipindahkan ke guru_profiles/student_profiles
-   `jenis_kelamin` - Dipindahkan ke profile masing-masing
-   `tempat_lahir` - Dipindahkan ke profile masing-masing
-   `tanggal_lahir` - Dipindahkan ke profile masing-masing
-   `profile_photo_path` - Tidak diperlukan lagi
-   `status_kepegawaian` - Dipindahkan ke guru_profiles
-   `golongan` - Dipindahkan ke guru_profiles
-   `mata_pelajaran` - Dipindahkan ke guru_profiles
-   `wali_kelas` - Dipindahkan ke guru_profiles

**Kolom yang Tersisa di tabel users:**

-   `id` (Primary Key)
-   `name` (String) - Nama user untuk login
-   `email` (String, Unique) - Email untuk login
-   `password` (String, Hashed) - Password untuk login
-   `role` (String) - Role user (admin, kepala_sekolah, guru, siswa)
-   `email_verified_at` (Timestamp, Nullable)
-   `remember_token` (String, Nullable)
-   `created_at` (Timestamp)
-   `updated_at` (Timestamp)

### 3. Tabel `school_profiles` - Penambahan Kepala Sekolah

**Kolom Baru yang Ditambahkan:**

-   `id_kepala_sekolah` (Unsigned Big Integer, Nullable) - Foreign Key ke guru_profiles

**Foreign Key Constraint:**

-   `id_kepala_sekolah` references `guru_profiles.id` with `ON DELETE SET NULL`

## Model yang Diperbarui

### 1. Model `GuruProfile` (Baru)

File: `app/Models/GuruProfile.php`

**Fitur:**

-   Menggunakan tabel `guru_profiles`
-   Relasi dengan `User` model
-   Method `getProfilePhotoUrl()` untuk mendapatkan URL foto profil
-   Scope `active()` untuk guru yang aktif
-   Method `isKepalaSekolah()` untuk mengecek apakah guru adalah kepala sekolah
-   Method `getSubjectsStringAttribute()` untuk mendapatkan mata pelajaran dalam string

### 2. Model `User` (Diperbarui)

**Perubahan:**

-   Menghapus fillable attributes yang sudah dipindah ke profile
-   Menambah relasi `guruProfile()` sebagai pengganti `teacherProfile()`
-   Method `teacherProfile()` masih tersedia untuk backward compatibility
-   Update method `getProfilePhotoUrl()` untuk menggunakan profile yang sesuai

### 3. Model `SchoolProfile` (Diperbarui)

**Perubahan:**

-   Menambah kolom `id_kepala_sekolah` ke fillable
-   Menambah relasi `kepalaSekolahProfile()` ke GuruProfile
-   Method `getKepalaSekolahNameAttribute()` untuk mendapatkan nama kepala sekolah

## Seeder yang Diperbarui

### 1. GuruSeeder

**Perubahan:**

-   Memisahkan data user dan guru profile
-   Membuat user terlebih dahulu, kemudian guru profile
-   Mengubah Dr. Budi Santoso menjadi kepala_sekolah
-   Menggunakan array untuk mata_pelajaran

### 2. SchoolProfileSeeder

**Perubahan:**

-   Menambah logika untuk mengatur kepala sekolah
-   Mencari guru profile dengan role kepala_sekolah dan mengaturnya sebagai id_kepala_sekolah

## Migration Files

1. `2025_11_22_081442_restructure_guru_profiles_and_users_tables.php`

    - Rename teacher_profiles ke guru_profiles
    - Modify struktur guru_profiles
    - Remove kolom dari users
    - Add id_kepala_sekolah ke school_profiles

2. `2025_11_22_082129_fix_foreign_key_constraint.php`
    - Memperbaiki foreign key constraint untuk id_kepala_sekolah

## Cara Menjalankan

1. Jalankan migration:

```bash
php artisan migrate
```

2. Jalankan seeder:

```bash
php artisan db:seed --class=GuruSeeder
php artisan db:seed --class=SchoolProfileSeeder
```

## Catatan Penting

1. **Kepala Sekolah:** Role kepala_sekolah tetap termasuk dalam kategori guru, hanya saja memiliki role khusus dan tercatat sebagai kepala sekolah di tabel school_profiles.

2. **Backward Compatibility:** Method `teacherProfile()` di model User masih tersedia sebagai alias dari `guruProfile()`.

3. **Data Migration:** Jika ada data existing, perlu dilakukan migration data manual dari tabel users ke guru_profiles.

4. **Password:** Kolom password di guru_profiles adalah opsional dan bisa digunakan jika diperlukan sistem login terpisah untuk guru.

5. **File Upload:** Untuk foto profil guru, pastikan storage sudah dikonfigurasi dengan benar untuk folder `profile_photos`.
