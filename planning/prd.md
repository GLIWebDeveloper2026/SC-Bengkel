# PRD — Sistem Operasional & Kasir Bengkel Motor "Jaya Motor"

## 1. Ringkasan Produk
Aplikasi Web Full-Stack Manajemen Bengkel Motor "Jaya Motor" dirancang untuk menangani operasional bengkel secara menyeluruh. Aplikasi ini menyelesaikan masalah pencatatan nota manual yang tidak seragam, kebocoran laba, perhitungan komisi multi-mekanik, konversi stok desimal, barang beli mendadak (toko sebelah), pembayaran parsial/bulk (pemilik rental), hingga klaim garansi servis.

## 2. Latar Belakang & Problem Statement
* **Pak Hendra (Pemilik):** Laba bengkel tidak sebanding dengan keramaian. Komisi mekanik sulit dihitung konsisten. Perlu melacak mekanik paling produktif dan pekerjaan yang paling sering dikomplain.
* **Mbak Rina (Kasir):** Bingung mencatat nota campuran, motor menginap, barang beli mendadak di toko sebelah, pencatatan pecahan oli desimal (0.8 Liter dari drum 30 Liter), dan pelunasan bertahap/bulk payment untuk 4-5 nota pemilik rental.
* **Pak Sarno (Mekanik Senior):** Kerusakan baru sering ditemukan setelah mesin dibongkar. Estimasi biaya awal sering melesat dari biaya akhir.
* **Bu Tuti (Pelanggan):** Riwayat servis tidak tercatat rapi, garansi 2 minggu membingungkan antara suku cadang dan jasa.

## 3. Sasaran Pengguna & Hak Akses (RBAC)
1. **Admin / Kasir (Mbak Rina):** Input Work Order, registrasi customer/motor, transaksi kasir, pembelian mendadak, alokasi pembayaran parsial/bulk, cetak nota, klaim garansi.
2. **Mekanik (Pak Sarno & Junior):** Dashboard pekerjaan, update status pengerjaan per item, request sparepart, input temuan kerusakan baru.
3. **Pemilik Bengkel (Pak Hendra):** Approval pekerjaan tambahan, laporan omzet & laba bersih, kontrol stok gudang, perhitungan komisi otomatis, laporan audit scrap/aki bekas.

## 4. Aturan Bisnis Utama (Business Rules)
1. **Pemisahan Perilaku Item:** Baris Nota wajib dibedakan berdasarkan tipe:
   - `service`: Menghasilkan komisi mekanik, TIDAK memotong stok persediaan.
   - `inventory`: Memotong stok persediaan desimal, TIDAK menghasilkan komisi.
   - `direct_purchase`: Barang dibeli mendadak (toko sebelah). Masuk nota & pengeluaran kas, TIDAK memotong stok gudang.
   - `trade_in`: Potongan harga nota (contoh: aki bekas). Mengurangi total tagihan & menambah persediaan scrap.
2. **Multi-Mekanik per Nota:** Penugasan mekanik dilakukan di level *Item Pekerjaan*, bukan di level *Header Nota*. 1 Nota bisa memuat komisi untuk 2 mekanik berbeda.
3. **Konversi Satuan Desimal:** Pembelian grosir (contoh: Drum 30 Liter) disimpan dalam satuan terkecil (Liter), dan penjualan bisa menggunakan angka desimal (contoh: 0.8 Liter).
4. **Approval Gate & Dual-Estimate:** Estimasi Awal dan Biaya Akhir disimpan berdampingan. Pekerjaan tambahan wajib menunggu persetujuan (approval).
5. **Matrix Alokasi Pembayaran:** 1 Pembayaran dapat dialokasikan memotong banyak Nota sekaligus (*Bulk Payment*), dan 1 Nota dapat dibayar beberapa kali (*Partial Payment*).
6. **Garansi 14 Hari:** Servis ulang dalam periode garansi menggratiskan Jasa (Rp0) namun tetap menagih Suku Cadang baru.