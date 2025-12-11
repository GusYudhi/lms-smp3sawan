# Fitur Unduh Rekap Absensi

## Deskripsi

Fitur untuk mengunduh rekap absensi siswa dan guru dalam format Excel (.xlsx) dengan berbagai pilihan periode dan tingkat kelas.

## Lokasi Fitur

### 1. Admin

-   **Rekap Absensi Siswa**: `/admin/absensi/siswa`
    -   Tombol: "Unduh Rekap Absensi" (hijau)
    -   Route export: `admin.absensi.siswa.export` (POST)

### 2. Kepala Sekolah

-   **Rekap Absensi Siswa**: `/kepala-sekolah/absensi/siswa`

    -   Tombol: "Unduh Rekap Absensi" (hijau)
    -   Route export: `kepala-sekolah.absensi.siswa.export` (POST)

-   **Rekap Absensi Guru**: `/kepala-sekolah/absensi/guru`
    -   Tombol: "Unduh Rekap Absensi" (hijau)
    -   Route export: `kepala-sekolah.absensi.guru.export` (POST)

## Fitur Modal

### Untuk Absensi Siswa:

1. **Dropdown Periode**:

    - Bulan individual (Juli, Agustus, ..., Desember) - untuk Semester Ganjil
    - Bulan individual (Januari, Februari, ..., Juni) - untuk Semester Genap
    - Opsi "Selama 1 Semester"

2. **Checkbox Tingkat Kelas**:

    - Kelas 7
    - Kelas 8
    - Kelas 9
    - Minimal harus memilih 1 tingkat

3. **Validasi**: Akan muncul error jika tidak ada tingkat kelas yang dipilih

### Untuk Absensi Guru:

1. **Dropdown Periode**:

    - Sama seperti siswa (bulan individual atau 1 semester)

2. **Tidak ada pilihan kelas** karena semua guru ditampilkan dalam 1 sheet

## Format File Excel

### Rekap Absensi Siswa:

-   **Multiple Sheets**: Setiap kelas memiliki sheet terpisah
    -   Contoh: "Kelas 7A", "Kelas 7B", "Kelas 8A", dst.
-   **Kolom**:
    -   NOMOR (urutan)
    -   NIS (diurutkan dari terkecil)
    -   NAMA SISWA
    -   L/P (Laki-laki/Perempuan)
    -   Tanggal-tanggal (horizontal): 7/11, 14/11, 21/11, dst.
-   **Header**:
    -   Baris 1: Nama bulan (merged, font besar 14pt, background hijau)
    -   Baris 2: Label kolom utama
    -   Baris 3: Tanggal (dd/mm)
-   **Status**:
    -   H = Hadir
    -   S = Sakit
    -   I = Izin
    -   A = Alpha
    -   -   = Tidak ada data
-   **Hari**: Hanya hari efektif sekolah (Senin-Sabtu), Minggu di-skip

### Rekap Absensi Guru:

-   **Single Sheet**: "Rekap Absensi Guru"
-   **Kolom**:
    -   NOMOR (urutan)
    -   NIP/NIK
    -   NAMA GURU
    -   L/P (Laki-laki/Perempuan)
    -   MATA PELAJARAN (bisa multiple, dipisah koma)
    -   Tanggal-tanggal (horizontal)
-   **Format sama dengan siswa** untuk header, status, dan hari

## Nama File

-   Format single bulan: `Rekap_Absensi_Siswa_November_2025.xlsx`
-   Format semester: `Rekap_Absensi_Siswa_Semester_Ganjil_2024-2025.xlsx`
-   Format guru: `Rekap_Absensi_Guru_November_2025.xlsx`

## Export Classes

-   **StudentsAttendanceExport.php**: Handler utama untuk export siswa (multiple sheets)
-   **StudentsAttendanceSheet.php**: Handler per sheet kelas siswa
-   **TeachersAttendanceExport.php**: Handler export guru (single sheet)

## Dependencies

-   `maatwebsite/excel` (sudah terinstall)
-   PhpSpreadsheet (dependency dari maatwebsite/excel)

## Controller Methods

### Admin\AbsensiRekapController

-   `exportSiswa(Request $request)`: Export rekap absensi siswa

### KepalaSekolah\AbsensiRekapController

-   `exportSiswa(Request $request)`: Export rekap absensi siswa
-   `exportGuru(Request $request)`: Export rekap absensi guru

## Styling Excel

-   **Header bulan**: Background hijau (#4CAF50), font putih, size 14pt, bold
-   **Header tabel**: Background hijau muda (#E8F5E9), font bold size 11pt
-   **Border**: Thin border untuk semua cell
-   **Alignment**:
    -   Center untuk semua cell kecuali nama
    -   Left untuk kolom nama dan mata pelajaran
-   **Column Width**:
    -   NOMOR: 8
    -   NIS/NIP: 15
    -   NAMA: 30
    -   L/P: 6
    -   MATA PELAJARAN: 25
    -   Tanggal: 8

## Testing

Untuk testing fitur ini:

1. Login sebagai Admin atau Kepala Sekolah
2. Navigasi ke halaman Rekap Absensi
3. Klik tombol "Unduh Rekap Absensi"
4. Pilih periode dan tingkat kelas (untuk siswa)
5. Klik "Unduh"
6. File Excel akan terdownload otomatis
