# Rencana Implementasi Sistem Peringatan Perubahan Kode Guru

## Latar Belakang
User menginginkan peringatan jika proses import akan mengubah `kode_guru` yang sudah ada di database. Hal ini penting karena perubahan kode guru yang tidak disengaja dapat merusak konsistensi data saat melakukan export/import di masa depan atau di semester yang berbeda.

## Masalah Teknis
Proses import saat ini bersifat **langsung (atomic)**. Begitu file diupload, data langsung diperbarui. Kita memerlukan mekanisme untuk memberi tahu user *sebelum* atau *saat* data sensitif seperti kode guru diubah.

---

## Solusi A: Konfirmasi "Ceklis" di Modal (Rekomendasi)
Menambahkan pengaman di awal sebelum user menekan tombol import.

1.  **UI Update:** Di dalam Modal Import, tambahkan checkbox wajib:
    *   `[ ] Saya menyadari bahwa import ini dapat mengubah Kode Guru di database. Hal ini mungkin mempengaruhi pencarian jadwal pada semester lain.`
2.  **Logic Update:** Tombol "Import" hanya aktif jika checkbox ini dicentang.
3.  **Kelebihan:** Sangat mudah diimplementasikan dan memaksa user untuk sadar akan risiko sebelum memulai.

## Solusi B: Sistem "Pre-Validation" (Dua Langkah)
Proses import dibagi menjadi dua tahap.

1.  **Langkah 1 (Upload & Validasi):** Sistem membaca file Excel dan membandingkan kode di Excel dengan database.
2.  **Langkah 2 (Tampilan Review):** Jika ditemukan perbedaan kode guru, tampilkan tabel perbandingan:
    *   Guru Ahmad: Kode Lama (7) -> Kode Baru (07).
    *   Guru Budi: Kode Lama (NULL) -> Kode Baru (08).
3.  **Langkah 3 (Eksekusi):** User menekan tombol "Ya, Terapkan Perubahan" atau "Batalkan".
4.  **Kelebihan:** Paling aman dan informatif.
5.  **Kekurangan:** Memerlukan perubahan arsitektur yang cukup besar pada controller dan frontend.

---

## Rekomendasi Langkah Pengerjaan (Bertahap)

### Tahap 1: Proteksi Modal (Cepat & Efektif)
*   Menambahkan checkbox konfirmasi di `jadwal.blade.php`.
*   User tidak bisa meng-import tanpa menyetujui pernyataan risiko perubahan kode guru.

### Tahap 2: Laporan Perubahan Detail (Informasi Pasca-Import)
*   Memperbaiki response JSON di `JadwalPelajaranController`.
*   Jika kode guru berubah, kembalikan daftar guru yang berubah kodenya agar muncul di SweetAlert sukses/warning.
*   Pesan: *"Import Berhasil! Catatan: Kode Guru untuk Antoni berubah dari 7 menjadi 07."*

---

**Status:** Menunggu Persetujuan User untuk memilih Solusi A atau B.
