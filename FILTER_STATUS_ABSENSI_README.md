# Filter Status Absensi

## Deskripsi

Fitur untuk memfilter data rekap absensi berdasarkan status kehadiran (Hadir, Sakit, Izin, Alpha, Terlambat) pada halaman rekap absensi siswa dan guru.

## Lokasi Fitur

### 1. Admin

-   **Rekap Absensi Siswa**: `/admin/absensi/siswa`
    -   Filter: Periode, Kelas, **Status Absensi**

### 2. Kepala Sekolah

-   **Rekap Absensi Siswa**: `/kepala-sekolah/absensi/siswa`

    -   Filter: Periode, Kelas, **Status Absensi**

-   **Rekap Absensi Guru**: `/kepala-sekolah/absensi/guru`
    -   Filter: Periode, **Status Absensi**

## Fitur Filter Status

### Dropdown "Filter Status":

-   **Semua Status** (default) - Menampilkan semua siswa/guru
-   **Hadir** - Hanya menampilkan yang pernah hadir dalam periode terpilih
-   **Sakit** - Hanya menampilkan yang pernah sakit dalam periode terpilih
-   **Izin** - Hanya menampilkan yang pernah izin dalam periode terpilih
-   **Alpha** - Hanya menampilkan yang pernah alpha dalam periode terpilih
-   **Terlambat** - Hanya menampilkan yang pernah terlambat dalam periode terpilih

## Cara Kerja

### Filter Kombinasi:

Filter status dapat dikombinasikan dengan filter lainnya:

-   **Periode** + **Status**: Contoh - "Bulan Ini" + "Hadir" = Siswa yang hadir bulan ini
-   **Kelas** + **Status**: Contoh - "Kelas 7A" + "Alpha" = Siswa kelas 7A yang alpha
-   **Periode** + **Kelas** + **Status**: Contoh - "Minggu Ini" + "Kelas 8B" + "Sakit" = Siswa kelas 8B yang sakit minggu ini

### Logic Filter:

Ketika filter status dipilih, sistem akan:

1. Mengambil data siswa/guru yang memiliki record absensi dengan status yang dipilih
2. Dalam range periode yang ditentukan
3. Tetap menampilkan **total keseluruhan** untuk semua status di kolom-kolom (Hadir, Sakit, Izin, Alpha, Terlambat)
4. Hanya **menampilkan baris data** untuk yang memenuhi kriteria filter

## Update Controller

### File yang Diubah:

#### 1. `app/Http/Controllers/Admin/AbsensiRekapController.php`

-   **Method `indexSiswa()`**:

    -   Tambah parameter `$statusFilter`
    -   Pass ke `getRekapSiswa()`
    -   Pass ke view

-   **Method `indexGuru()`**:

    -   Tambah parameter `$statusFilter`
    -   Pass ke `getRekapGuru()`
    -   Pass ke view

-   **Method `getRekapSiswa()`**:

    -   Tambah parameter `$statusFilter`
    -   Tambah `whereHas('attendance')` dengan kondisi status jika filter aktif

-   **Method `getRekapGuru()`**:
    -   Tambah parameter `$statusFilter`
    -   Tambah `whereHas('guruAttendance')` dengan kondisi status jika filter aktif

#### 2. `app/Http/Controllers/KepalaSekolah/AbsensiRekapController.php`

-   **Method `indexSiswa()`**: Override dengan tambahan `$statusFilter`
-   **Method `indexGuru()`**: Override dengan tambahan `$statusFilter`
-   Mewarisi `getRekapSiswa()` dan `getRekapGuru()` dari parent

#### 3. `app/Models/User.php`

-   Tambah relationship `attendance()` → hasMany Attendance
-   Tambah relationship `guruAttendance()` → hasMany GuruAttendance

## Update View

### File yang Diubah:

#### 1. `resources/views/admin/absensi/siswa/index.blade.php`

-   Tambah dropdown "Filter Status" di form filter
-   5 opsi status: Hadir, Sakit, Izin, Alpha, Terlambat
-   Maintain selected value dengan `{{ ($statusFilter ?? '') == 'hadir' ? 'selected' : '' }}`

#### 2. `resources/views/kepala-sekolah/absensi/siswa/index.blade.php`

-   Sama seperti admin, tambah dropdown "Filter Status"

#### 3. `resources/views/kepala-sekolah/absensi/guru/index.blade.php`

-   Tambah dropdown "Filter Status"
-   Adjust column layout dari col-md-4 → col-md-3 untuk menampung filter baru

## Contoh Penggunaan

### Skenario 1: Melihat Siswa yang Sering Alpha

1. Buka halaman "Rekap Absensi Siswa"
2. Pilih periode: "Semester Ini"
3. Pilih kelas: "Kelas 8A" (opsional)
4. Pilih status: "Alpha"
5. Klik "Tampilkan"
6. **Hasil**: Akan muncul daftar siswa yang pernah alpha di semester ini

### Skenario 2: Melihat Guru yang Izin Bulan Ini

1. Buka halaman "Rekap Absensi Guru"
2. Pilih periode: "Bulan Ini"
3. Pilih status: "Izin"
4. Klik "Tampilkan"
5. **Hasil**: Akan muncul daftar guru yang pernah izin bulan ini

### Skenario 3: Reset Filter

-   Klik tombol "Reset" untuk menghapus semua filter dan kembali ke tampilan default

## Query Database

### Untuk Siswa:

```php
User::where('role', 'siswa')
    ->whereHas('attendance', function($q) use ($statusFilter, $startDate, $endDate) {
        $q->where('status', $statusFilter)
          ->whereBetween('date', [$startDate, $endDate]);
    })
```

### Untuk Guru:

```php
User::whereIn('role', ['guru', 'kepala_sekolah'])
    ->whereHas('guruAttendance', function($q) use ($statusFilter, $startDate, $endDate) {
        $q->where('status', $statusFilter)
          ->whereBetween('tanggal', [$startDate, $endDate]);
    })
```

## Catatan Penting

1. **Filter bersifat inclusif**: Jika siswa/guru memiliki minimal 1 record dengan status yang dipilih dalam periode tersebut, mereka akan muncul dalam hasil.

2. **Total tetap menampilkan semua**: Kolom total (Hadir, Sakit, Izin, Alpha, Terlambat) tetap menampilkan count keseluruhan untuk periode yang dipilih, tidak hanya untuk status yang difilter.

3. **Pagination tetap berfungsi**: Hasil filter tetap dipaginate (20 records per page).

4. **Kombinasi filter**: Semua filter (Periode, Kelas, Status) dapat digunakan bersamaan untuk hasil yang lebih spesifik.

## Testing

Untuk testing fitur ini:

1. Login sebagai Admin atau Kepala Sekolah
2. Navigasi ke halaman Rekap Absensi Siswa/Guru
3. Pilih periode (misalnya "Bulan Ini")
4. Pilih status (misalnya "Hadir")
5. Klik "Tampilkan"
6. Verifikasi hanya siswa/guru dengan status "Hadir" yang muncul
7. Test dengan status lainnya
8. Test kombinasi dengan filter kelas (untuk siswa)
9. Klik "Reset" untuk clear filter

## Dependencies

-   Tidak ada dependency baru yang ditambahkan
-   Menggunakan Eloquent relationship yang sudah ada
-   Compatible dengan fitur export yang sudah ada sebelumnya
