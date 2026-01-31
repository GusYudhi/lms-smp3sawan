# Rencana Perubahan Input Jadwal Pelajaran

Tujuannya adalah untuk mempermudah proses input jadwal pelajaran agar lebih cepat dan intuitif.

## 1. Perubahan UI/UX pada Modal Input [SELESAI]

*   **[Γ£ô] Pencarian Cerdas (Select2):** Mengganti dropdown standar `Mata Pelajaran` dan `Guru Pengampu` dengan dropdown searchable menggunakan Select2 (Bootstrap 5 theme).
*   **[Γ£ô] Indikator Konflik Jadwal (Real-time):** Sistem mengecek ketersediaan Guru, bentrokan slot Kelas, dan Jadwal Tetap (Upacara/Istirahat) secara real-time via Ajax.
*   **[Γ£ô] Integrasi SweetAlert2:** Menggunakan standar notifikasi proyek untuk feedback yang lebih modern.

## 2. Fitur "Drag & Drop" [SELESAI]

*   **[Γ£ô] Interaksi Visual:** Memungkinkan pengguna untuk memindahkan jadwal antar slot waktu atau menukar (swap) jadwal antar guru/mapel hanya dengan menarik (drag) kotak jadwal.
*   **[Γ£ô] Validasi Backend:** Setiap perpindahan divalidasi ulang untuk memastikan tidak ada konflik di slot tujuan.

## 3. Bulk Action (Input Masal) [DALAM PROSES/IDEP]

*   **Mode Edit Cepat:** Ide untuk menggunakan popover/inline editor agar tidak perlu membuka modal besar terus-menerus.
*   **Copy-Paste Jadwal:** (Dibatalkan/Revert karena jadwal antar kelas unik).

## 4. Perbaikan Teknis pada `jadwal.blade.php` [SELESAI]

*   **[Γ£ô] Update Modal Form:** Inisialisasi Select2 dengan `dropdownParent` yang benar.
*   **[Γ£ô] AJAX Check:** Route khusus `check-schedule-conflict` dan `move-schedule`.
*   **[Γ£ô] Bypass Global Auto-Dismiss:** Menggunakan utility classes Bootstrap untuk peringatan persisten di modal.

---

**Status:** Tahap 1 & 2 Selesai (UI Modal & Drag-Drop)
**Target Berikutnya:** Optimasi performa grid atau Mode Edit Cepat (Inline).
**Last Updated:** Sabtu, 31 Januari 2026