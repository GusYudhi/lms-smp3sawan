# Rencana Perubahan Input Jadwal Pelajaran

Tujuannya adalah untuk mempermudah proses input jadwal pelajaran agar lebih cepat dan intuitif.

## 1. Perubahan UI/UX pada Modal Input

Saat ini, modal input masih standar. Kita akan meningkatkannya dengan:

*   **Pencarian Cerdas (Select2/TomSelect):** Mengganti dropdown standar `Mata Pelajaran` dan `Guru Pengampu` dengan dropdown yang bisa dicari (searchable). Ini sangat membantu jika daftarnya panjang.
*   **Indikator Konflik Jadwal (Real-time):** Saat memilih Guru dan Jam, sistem secara otomatis mengecek apakah guru tersebut sudah mengajar di kelas lain pada jam yang sama. Jika ya, tampilkan peringatan langsung di modal sebelum disubmit.
*   **Preset Jadwal (Opsional):** Menambahkan opsi untuk menduplikasi jadwal dari hari lain atau kelas lain (fitur lanjutan).

## 2. Fitur "Drag & Drop" (Jangka Panjang/Kompleksitas Tinggi)

*   Memungkinkan pengguna untuk memindahkan jadwal antar slot waktu hanya dengan menarik (drag) kotak jadwal.
*   Membutuhkan library seperti `FullCalendar` atau implementasi custom JavaScript drag-and-drop.

## 3. Bulk Action (Input Masal)

*   **Mode Edit Cepat:** Alih-alih membuka modal satu per satu, buat mode di mana pengguna bisa mengklik slot kosong dan langsung memilih mapel+guru dari dropdown kecil (popover) tanpa menutup tampilan grid.
*   **Copy-Paste Jadwal:** Fitur untuk menyalin jadwal hari Senin ke hari Selasa, dll.

## 4. Perbaikan Teknis pada `jadwal.blade.php`

### A. Update Modal Form
*   Menambahkan library `Select2` atau sejenisnya untuk dropdown.
*   Menambahkan AJAX request saat dropdown Guru berubah untuk mengecek ketersediaan.

### B. Validasi Frontend
*   Pastikan input `jumlah_jam` tidak melebihi sisa slot yang tersedia di hari itu.

## Implementasi Tahap 1 (Fokus Utama)

Kita akan fokus pada **Poin 1 (UI Modal)** dan **Poin 3 (Mode Edit Cepat)** karena ini memberikan dampak paling signifikan dengan usaha yang wajar.

### Rincian Perubahan Kode:

1.  **Include Library Select2:** Tambahkan CSS dan JS Select2 di layout atau stack scripts.
2.  **Update Modal HTML:** Ubah `<select>` menjadi compatible dengan Select2.
3.  **AJAX Check:** Tambahkan route baru di backend untuk cek ketersediaan guru (`check-guru-availability`).
4.  **JavaScript Logic:** Update script untuk handle inisialisasi Select2 dan event listener.

---

**Status:** Draft Rencana
**Target File:** `resources/views/admin/jadwal-mapel/jadwal.blade.php`
