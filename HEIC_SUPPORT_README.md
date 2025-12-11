# HEIC/HEIF Photo Upload Support

## 📱 Overview

Sistem sekarang mendukung upload foto dari iPhone dalam format HEIC/HEIF. Foto akan otomatis dikonversi ke JPG, kemudian dikompress ke format WebP untuk efisiensi penyimpanan.

## ✅ Fitur yang Ditambahkan

### 1. **Package HEIC Converter**

-   Package: `maestroerror/php-heic-to-jpg` (v1.0.8)
-   Digunakan untuk konversi HEIC/HEIF → JPG
-   Instalasi: `composer require maestroerror/php-heic-to-jpg --ignore-platform-req=ext-zip`

### 2. **ImageCompressor Helper Enhancement**

**File:** `app/Helpers/ImageCompressor.php`

**Metode Baru:** `convertHeicIfNeeded()`

-   Deteksi file HEIC/HEIF berdasarkan extension dan mime type
-   Konversi otomatis HEIC → JPG menggunakan library HeicToJpg
-   Return original file jika bukan HEIC atau konversi gagal
-   Logging lengkap untuk monitoring proses konversi

**Proses Alur:**

```
Upload HEIC → convertHeicIfNeeded() → JPG → compressWithIntervention() → WebP → Storage
```

**Mime Types yang Didukung:**

-   `image/heic`
-   `image/heif`
-   `image/heic-sequence`
-   `image/heif-sequence`

**Extensions yang Didukung:**

-   `.heic`
-   `.heif`

### 3. **ProfileController Updates**

**File:** `app/Http/Controllers/ProfileController.php`

**Validasi Diperbarui:**

-   Method `update()`: Ditambahkan `heic,heif` ke validation rules
-   Method `updateProfilePhoto()`: Ditambahkan `heic,heif` ke validation rules
-   Ukuran maksimal: **5MB (5120 KB)**
-   Format yang diterima: `jpeg,jpg,png,webp,heic,heif`

**Error Message:**

```php
'profile_photo.mimes' => 'Format foto harus JPG, PNG, WebP, atau HEIC'
```

### 4. **View/UI Updates**

**File:** `resources/views/profile/profile-section.blade.php`

**Informasi Format yang Ditampilkan:**

```
Format: JPG, PNG, WebP, HEIC (Maks. 5MB)
Catatan: Foto akan otomatis dikompress ke format WebP dan langsung tersimpan setelah dipilih.
File HEIC dari iPhone otomatis dikonversi.
```

## 🔧 Technical Details

### Compression Settings

-   **Quality:** 60% (optimal compression dengan kualitas acceptable)
-   **Max Width:** 1200px
-   **Output Format:** WebP
-   **Fallback:** Original upload jika compression gagal

### Logging

Setiap proses konversi HEIC dicatat di log dengan informasi:

-   Original filename & size
-   Mime type & extension
-   Conversion result (success/failure)
-   File sizes before & after conversion

**Log Messages:**

-   🔄 `Converting HEIC/HEIF to JPG`
-   ✅ `HEIC converted successfully`
-   ❌ `HEIC conversion failed, using original file`

### Error Handling

-   Jika konversi HEIC gagal → gunakan file original
-   Jika compression WebP gagal → store original file
-   Semua error di-log untuk debugging
-   User tetap bisa upload foto meskipun ada error

## 📋 Supported Formats Summary

| Format | Extension   | Mime Type  | Max Size | Output     |
| ------ | ----------- | ---------- | -------- | ---------- |
| JPEG   | .jpg, .jpeg | image/jpeg | 5MB      | WebP       |
| PNG    | .png        | image/png  | 5MB      | WebP       |
| WebP   | .webp       | image/webp | 5MB      | WebP       |
| HEIC   | .heic       | image/heic | 5MB      | JPG → WebP |
| HEIF   | .heif       | image/heif | 5MB      | JPG → WebP |

## 🧪 Testing Checklist

### Manual Testing

-   [ ] Upload foto JPEG dari Android/PC
-   [ ] Upload foto PNG dengan transparansi
-   [ ] Upload foto WebP
-   [ ] Upload foto HEIC dari iPhone
-   [ ] Upload foto HEIF
-   [ ] Test dengan file size mendekati 5MB
-   [ ] Test dengan file size melebihi 5MB (harus ditolak)
-   [ ] Verify foto tersimpan sebagai WebP
-   [ ] Check logs untuk proses konversi
-   [ ] Verify preview foto setelah upload

### Error Scenarios

-   [ ] Upload file non-image (harus ditolak)
-   [ ] Upload file corrupt (harus ditolak)
-   [ ] Server dengan GD tanpa WebP support (fallback ke original)
-   [ ] Disk space penuh (error handling)

## 🚀 Deployment Notes

### Server Requirements

-   PHP 8.2+ with GD extension
-   WebP support in GD (recommended)
-   Sufficient storage space
-   Write permissions on `storage/app/public/profile_photos`

### Post-Deployment Verification

1. Check composer packages installed:

    ```bash
    composer show maestroerror/php-heic-to-jpg
    ```

2. Verify storage permissions:

    ```bash
    php artisan storage:link
    ```

3. Test upload dengan foto HEIC dari iPhone

4. Monitor logs untuk error:
    ```bash
    tail -f storage/logs/laravel.log
    ```

## 📱 User Benefits

### Untuk Pengguna iPhone

-   ✅ Upload foto langsung dari iPhone tanpa konversi manual
-   ✅ Format native iPhone (HEIC) didukung penuh
-   ✅ File size lebih kecil (HEIC lebih efisien dari JPEG)
-   ✅ Kualitas foto tetap terjaga

### Untuk Pengguna Android/PC

-   ✅ Tetap support format lama (JPEG, PNG)
-   ✅ WebP compression untuk file size lebih kecil
-   ✅ Upload lebih cepat (max 5MB)

### Untuk Server/Admin

-   ✅ Storage lebih efisien (WebP format)
-   ✅ Bandwidth lebih hemat
-   ✅ Logging lengkap untuk monitoring
-   ✅ Error handling yang robust

## 🔍 Troubleshooting

### Problem: "HEIC conversion failed"

**Possible Causes:**

-   File corrupt atau tidak valid
-   Insufficient memory
-   Library tidak ter-install dengan benar

**Solution:**

-   Check logs untuk detail error
-   Verify package installed: `composer show maestroerror/php-heic-to-jpg`
-   Increase PHP memory limit jika perlu

### Problem: "Format foto harus JPG, PNG, WebP, atau HEIC"

**Causes:**

-   File extension tidak sesuai
-   Mime type tidak dikenali

**Solution:**

-   Pastikan file benar-benar HEIC/HEIF
-   Check mime type di log file
-   Coba export dari iPhone dengan format "Most Compatible"

### Problem: File size terlalu besar

**Solution:**

-   Resize foto di iPhone sebelum upload
-   Gunakan aplikasi kompressor
-   Max size: 5MB (5120KB)

## 📝 Code Examples

### Example: Manual HEIC Conversion Test

```php
use Maestroerror\HeicToJpg;

$heicPath = '/path/to/photo.heic';
$jpgPath = '/path/to/output.jpg';

$converter = new HeicToJpg();
$result = $converter->convert($heicPath)->saveAs($jpgPath);

if ($result) {
    echo "Conversion successful!";
}
```

### Example: Check Supported Formats

```php
use App\Helpers\ImageCompressor;

$formats = ImageCompressor::getSupportedFormats();
// Returns: ['jpeg', 'jpg', 'png', 'webp', 'heic', 'heif']
```

## 🔗 References

-   **Package Documentation:** https://github.com/maestroerror/php-heic-to-jpg
-   **HEIC Format Spec:** https://nokiatech.github.io/heif/
-   **WebP Documentation:** https://developers.google.com/speed/webp

## 📅 Change Log

### Version 1.0 (Current)

-   ✅ Installed maestroerror/php-heic-to-jpg package
-   ✅ Added convertHeicIfNeeded() method to ImageCompressor
-   ✅ Updated ProfileController validation rules
-   ✅ Updated profile view with HEIC support information
-   ✅ Comprehensive logging for HEIC conversion
-   ✅ Error handling and fallback mechanisms

---

**Last Updated:** 2024
**Status:** ✅ Implemented & Ready for Testing
