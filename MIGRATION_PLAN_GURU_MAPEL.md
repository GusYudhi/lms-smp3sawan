# Dokumentasi Migrasi Relasi Guru & Mata Pelajaran (Final)

Berdasarkan keputusan final, relasi antara Guru dan Mata Pelajaran adalah **One-to-Many**.
Artinya: **Satu Guru hanya mengampu Satu Mata Pelajaran Utama**.

## 1. Perubahan Skema Database

### Tabel `guru_profiles`
*   **Hapus:** Kolom `mata_pelajaran` (tipe string/json lama).
*   **Tambah:** Kolom `mata_pelajaran_id` (Foreign Key ke tabel `mata_pelajarans`).

**Migration File:**
`2026_02_01_000001_add_mata_pelajaran_id_to_guru_profiles.php`

## 2. Struktur Relasi Model

### `App\Models\GuruProfile`
Menggunakan relasi `belongsTo` karena profile guru menyimpan ID mapel.

```php
public function mataPelajaran()
{
    return $this->belongsTo(MataPelajaran::class, 'mata_pelajaran_id');
}
```

### `App\Models\MataPelajaran`
Menggunakan relasi `hasMany` karena satu mapel bisa diampu oleh banyak guru.

```php
public function guruProfiles()
{
    return $this->hasMany(GuruProfile::class, 'mata_pelajaran_id');
}
```

## 3. Daftar File yang Disesuaikan

Berikut adalah file-file yang telah diperbarui untuk mendukung perubahan ini:

### A. Controllers (Logika Bisnis)

1.  **`app/Http/Controllers/Admin/AdminController.php`**
    *   **Filter/Search:** Menggunakan `whereHas('mataPelajaran', ...)` untuk memfilter guru berdasarkan nama mapel.
    *   **Store/Update:** Menyimpan `mata_pelajaran_id` langsung ke tabel `guru_profiles`. Validasi menggunakan `exists:mata_pelajarans,id`.

2.  **`app/Http/Controllers/ProfileController.php`**
    *   **Update Profile:** Saat guru mengupdate profil sendiri, sistem menyimpan `mata_pelajaran_id`.

### B. Views (Tampilan)

1.  **`resources/views/admin/guru/create.blade.php`**
    *   Input Mata Pelajaran menggunakan dropdown single select (dengan Select2).
    *   `name="mata_pelajaran"`.

2.  **`resources/views/admin/guru/edit.blade.php`**
    *   Dropdown single select, nilai terpilih diambil dari `$teacher->guruProfile->mata_pelajaran_id`.

3.  **`resources/views/admin/guru/index.blade.php`** (dan partials)
    *   Menampilkan satu label nama mapel di tabel.

4.  **`resources/views/admin/guru/show.blade.php`**
    *   Menampilkan satu detail nama mapel di halaman detail guru.

5.  **`resources/views/profile/profile-section.blade.php`**
    *   Form edit profil guru menggunakan single select.

---
**Status:** Selesai & Terimplementasi
**Notes:** Sistem sekarang mencegah input multiple mapel untuk satu guru di level database dan aplikasi.
