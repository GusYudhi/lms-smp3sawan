# Fitur Absensi Guru - SMP 3 Sawan LMS

## 📋 Deskripsi

Fitur absensi guru dengan verifikasi foto selfie dan lokasi GPS untuk memastikan guru melakukan absensi dari area sekolah.

## ✨ Fitur Utama

-   📷 **Foto Selfie**: Guru mengambil foto selfie menggunakan kamera (depan/belakang)
-   📍 **Verifikasi GPS**: Sistem otomatis mengecek lokasi guru saat absensi
-   ✅ **Validasi Radius**: Hanya dapat absen dalam radius yang ditentukan dari sekolah
-   📅 **Kalender Mingguan**: Menampilkan riwayat absensi Senin - Sabtu
-   📊 **Statistik**: Menampilkan jumlah Hadir, Terlambat, Alpha, dan Izin
-   ⏰ **Status Otomatis**: Sistem otomatis menentukan status (Hadir/Terlambat) berdasarkan waktu

## 🚀 Cara Menggunakan

### Untuk Guru:

1. Login ke sistem dengan akun guru
2. Klik menu **"Absensi Guru"** di sidebar
3. Klik tombol **"Buka Kamera"**
4. Berikan izin akses kamera dan lokasi GPS
5. Pastikan wajah terlihat jelas, lalu klik **"Ambil Foto"**
6. Sistem akan otomatis mengambil koordinat GPS
7. Jika berada di area sekolah, klik **"Konfirmasi & Absen"**
8. Status absensi akan tersimpan dan ditampilkan di kalender

### Status Absensi:

-   🟢 **Hadir**: Absen sebelum jam batas (default: 07:30)
-   🟡 **Terlambat**: Absen setelah jam batas
-   🔵 **Izin**: Tidak hadir dengan keterangan izin
-   🔴 **Alpha**: Tidak hadir tanpa keterangan

## ⚙️ Konfigurasi

### Update Koordinat Sekolah:

Edit file `.env` dan tambahkan/ubah:

```env
# Koordinat GPS Sekolah
SCHOOL_LATITUDE=-8.6705
SCHOOL_LONGITUDE=115.2126

# Radius maksimal dalam meter
SCHOOL_ATTENDANCE_RADIUS=100

# Jam batas terlambat (format: HH:MM)
SCHOOL_LATE_THRESHOLD=07:30
```

### Cara Mendapatkan Koordinat Sekolah:

1. Buka [Google Maps](https://maps.google.com)
2. Cari lokasi sekolah
3. Klik kanan pada titik lokasi
4. Pilih koordinat yang muncul (format: -8.6705, 115.2126)
5. Angka pertama adalah **latitude**, angka kedua adalah **longitude**

## 🔧 Konfigurasi Teknis

### Database Table: `guru_attendances`

Kolom:

-   `user_id` - ID guru
-   `tanggal` - Tanggal absensi
-   `waktu_absen` - Waktu absensi
-   `status` - Status (hadir/terlambat/izin/alpha)
-   `photo_path` - Path foto selfie
-   `latitude` - Koordinat latitude
-   `longitude` - Koordinat longitude
-   `distance_from_school` - Jarak dari sekolah (meter)
-   `accuracy` - Akurasi GPS

### Routes:

-   `GET /guru/absensi-guru` - Halaman absensi
-   `POST /guru/absensi-guru/store` - Simpan absensi
-   `GET /guru/absensi-guru/weekly` - Data mingguan

### Files:

-   View: `resources/views/guru/absensi-guru/absensi-guru.blade.php`
-   Controller: `app/Http/Controllers/Guru/AbsensiGuruController.php`
-   Model: `app/Models/GuruAttendance.php`
-   Config: `config/school.php`
-   Migration: `database/migrations/2025_12_01_134522_create_guru_attendances_table.php`

## 📱 Kompatibilitas Browser

-   ✅ Chrome (Desktop & Mobile)
-   ✅ Firefox (Desktop & Mobile)
-   ✅ Safari (Desktop & Mobile)
-   ✅ Edge (Desktop & Mobile)

**Catatan**: Browser harus mendukung:

-   HTML5 Camera API (getUserMedia)
-   Geolocation API
-   Canvas API

## 🔒 Keamanan

-   ✅ Validasi foto (max 2MB, format: jpeg/png/jpg)
-   ✅ Validasi koordinat GPS
-   ✅ Validasi radius area sekolah
-   ✅ Pencegahan absensi ganda (1x per hari)
-   ✅ CSRF Protection
-   ✅ Authentication & Authorization

## 🐛 Troubleshooting

### Kamera tidak muncul:

-   Pastikan browser memiliki izin akses kamera
-   Cek apakah HTTPS sudah aktif (required untuk getUserMedia)
-   Coba refresh browser

### GPS tidak akurat:

-   Pastikan GPS device aktif
-   Gunakan di area terbuka untuk sinyal lebih baik
-   Tunggu beberapa detik agar GPS lock

### Gagal absen (di luar area):

-   Pastikan koordinat sekolah di config sudah benar
-   Cek apakah radius cukup besar (default: 100m)
-   Verifikasi lokasi guru dengan Google Maps

## 📞 Support

Jika ada pertanyaan atau kendala, hubungi admin sistem.

---

**Version**: 1.0  
**Last Updated**: December 2025  
**Developed for**: SMP 3 Sawan LMS
