# Fix HEIC Support di Jurnal Mengajar

## Masalah

File HEIC tidak bisa di-upload di jurnal mengajar karena browser tidak bisa langsung membaca format HEIC dengan JavaScript Canvas API.

## Solusi

Implementasi hybrid approach:

1. **JPG/PNG/WebP**: Kompresi langsung di browser (client-side)
2. **HEIC/HEIF**: Konversi di server dulu → return base64 JPG → kompresi di browser

## Flow untuk HEIC:

```
1. User pilih file HEIC
2. JavaScript deteksi extension = .heic/.heif
3. Tampilkan loading: "Memproses foto HEIC..."
4. Upload ke server: POST /convert-heic
5. Server convert HEIC → JPG (pakai maestroerror/php-heic-to-jpg)
6. Server return base64 JPG
7. JavaScript terima base64 JPG
8. Compress di browser: resize 1200px + quality 60% + WebP
9. Set ke input hidden foto_bukti
10. Tampilkan preview
```

## File yang Diubah

### 1. `resources/views/guru/jurnal-mengajar/wizard.blade.php`

**Fungsi Baru**:

-   `handleFileUpload()`: Deteksi HEIC, route ke fungsi yang tepat
-   `compressImageFile()`: Kompresi langsung untuk JPG/PNG/WebP
-   `convertAndCompressHEIC()`: AJAX konversi HEIC → kompresi

**Cara Kerja**:

```javascript
function handleFileUpload(event) {
    const file = event.target.files[0];
    const isHEIC = fileExtension === "heic" || fileExtension === "heif";

    if (isHEIC) {
        // Show loading with SweetAlert
        convertAndCompressHEIC(file);
    } else {
        // Direct browser compression
        compressImageFile(file);
    }
}
```

### 2. `app/Helpers/ImageCompressor.php`

**Method Baru**:

```php
public static function convertHeicToJpgTemp($file)
```

**Fungsi**:

-   Convert HEIC → JPG pakai HeicToJpg library
-   Simpan temporary ke `storage/app/temp/`
-   Return path file
-   Digunakan khusus untuk AJAX conversion

**Return**: `string|null` - Path ke file JPG converted, atau null jika gagal

### 3. `app/Http/Controllers/Guru/JurnalMengajarController.php`

**Method Baru**:

```php
public function convertHeic(Request $request)
```

**Fungsi**:

-   Handle POST request dari JavaScript
-   Validasi file HEIC (max 10MB)
-   Panggil `ImageCompressor::convertHeicToJpgTemp()`
-   Baca file converted
-   Return JSON dengan base64 JPG
-   Hapus file temporary

**Response JSON**:

```json
{
    "success": true,
    "image": "data:image/jpeg;base64,...",
    "message": "HEIC converted successfully"
}
```

### 4. `routes/web.php`

**Route Baru**:

```php
Route::post('/convert-heic', [JurnalMengajarController::class, 'convertHeic'])
    ->name('convert-heic');
```

**Lokasi**: Di dalam middleware guru (baris 201)

## Testing

### Test HEIC Upload:

1. Login sebagai guru
2. Buka Jurnal Mengajar → Create (Wizard)
3. Pilih foto HEIC dari iPhone
4. Harus muncul loading: "Memproses foto HEIC..."
5. Tunggu 2-5 detik (tergantung ukuran file)
6. Preview muncul dengan foto terkompresi
7. Submit form
8. Check storage: file harus WebP, ukuran ~100-200KB

### Expected Results:

| File Original         | Proses                            | Output                    |
| --------------------- | --------------------------------- | ------------------------- |
| HEIC 4.5MB, 4032x3024 | Server convert → Browser compress | WebP ~100-200KB, 1200x900 |
| JPG 3MB, 4032x3024    | Browser compress only             | WebP ~50-150KB, 1200x900  |

### Logs untuk Debug:

```bash
# Check Laravel logs
tail -f storage/logs/laravel.log

# Search for HEIC conversion
grep "Converting HEIC" storage/logs/laravel.log
grep "HEIC converted" storage/logs/laravel.log
```

## Error Handling

### Jika Konversi Gagal:

-   SweetAlert error: "Gagal mengkonversi foto HEIC"
-   Log error di `storage/logs/laravel.log`
-   User bisa retry dengan file lain

### Jika Server Error:

-   JavaScript catch error
-   SweetAlert: "Terjadi kesalahan saat mengkonversi foto HEIC"
-   Console log error untuk debugging

## Browser Console Debug:

```javascript
// Check if HEIC detected
console.log("File extension:", fileExtension);
console.log("Is HEIC:", isHEIC);

// Check conversion response
console.log("HEIC conversion response:", data);

// Check compressed output
console.log("Compressed image size:", compressedData.length);
```

## Catatan Penting

### ⚠️ Limitations:

1. **HEIC membutuhkan internet**: Harus upload ke server dulu
2. **Lebih lambat**: 2-5 detik untuk konversi + kompresi
3. **Server dependency**: Butuh HeicToJpg library dan dependencies
4. **Temp storage**: Butuh space untuk temporary files

### ✅ Advantages:

1. **Universal support**: Semua format supported (JPG, PNG, HEIC, WebP)
2. **Optimized output**: Semua foto terkompresi ke WebP ~100-200KB
3. **Good UX**: Loading indicator, error messages
4. **Efficient**: JPG/PNG langsung compress di browser (no server load)

## Dependencies

### PHP Package:

```bash
composer require maestroerror/php-heic-to-jpg
```

### System Requirements:

-   PHP 7.4+
-   libheif (untuk convert HEIC)
-   GD extension (untuk image processing di server)

### JavaScript:

-   SweetAlert2 (untuk loading & error messages)
-   Canvas API (browser built-in)

## Troubleshooting

### Problem: "Failed to convert HEIC file"

**Solution**:

1. Check HeicToJpg library installed
2. Check libheif dependencies
3. Check storage/app/temp/ folder writable
4. Check logs: `storage/logs/laravel.log`

### Problem: "Converted file not found"

**Solution**:

1. Check storage permissions (775 or 777)
2. Check temp directory exists: `storage/app/temp/`
3. Create manually: `mkdir -p storage/app/temp`

### Problem: HEIC upload lambat

**Expected**: 2-5 seconds untuk file 4-5MB
**If slower**:

-   Check server resources (CPU, memory)
-   Check internet speed (upload to server)
-   Optimize HeicToJpg library settings

## Future Improvements

### Possible Enhancements:

1. **Client-side HEIC support**: Use WASM library for browser conversion
2. **Progress bar**: Show upload & conversion progress
3. **Batch conversion**: Convert multiple HEIC files at once
4. **Cache converted**: Save converted JPG for reuse
5. **CDN upload**: Direct upload to CDN after compression

## Status

✅ **IMPLEMENTED & READY TO TEST**

-   [x] JavaScript detection HEIC
-   [x] AJAX conversion endpoint
-   [x] Server-side HEIC → JPG conversion
-   [x] Client-side compression after conversion
-   [x] Error handling & user feedback
-   [x] Cleanup temporary files
-   [x] Route & controller setup
-   [x] Documentation

**Next**: Testing dengan file HEIC dari iPhone
