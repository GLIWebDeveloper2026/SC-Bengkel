# Lembar Keputusan — Solusi Studi Kasus Bengkel Motor "Jaya Motor"
*(Disusun sesuai format aturan lomba: maksimal 5 kalimat per nomor + wajib menyebutkan konsekuensi keputusan).*

### 1. Baris pada Nota Tidak Seragam
**Jawaban:**
Kami memisahkan tipe baris pada tabel detail transaksi (`work_order_items`) menggunakan kolom diskriminator `item_type` (`service`, `inventory`, `direct_purchase`, `trade_in`) dan menempatkan kunci mekanik (`mechanic_id`) di level baris. Jasa dihitung memasukkan nilai komisi tanpa mengurangi stok persediaan, sedangkan suku cadang mengurangi stok tanpa menghitung komisi mekanik. Bila seluruh item dipaksakan menyatu dalam satu tabel yang seragam tanpa pemisahan tipe, konsekuensinya perhitungan laba bersih menjadi bias, persediaan gudang akan kacau akibat jasa dianggap barang, dan komisi mekanik berisiko terbayar ganda atas nilai suku cadang.
* **Konsekuensi:** Sistem membutuhkan logika validasi ekstra pada backend saat melempar query mutasi stok dan kalkulasi komisi.

### 2. Kejadian B: Kampas Rem Beli Mendadak & Oli Drum Desimal
**Jawaban:**
Kampas rem beli mendadak dicatat sebagai item bertipe `direct_purchase` yang langsung menagihkan harga ke nota dan mencatat modalnya sebagai pengeluaran kas operasional tanpa mencatatnya ke buku persediaan gudang. Untuk oli drum, sistem menggunakan satuan dasar penjualan (Liter) pada database stok dan mengonversi pembelian 1 drum menjadi 30 Liter stok masuk. Saat terjadi penjualan $0.8\text{ Liter}$, stok tipe desimal (`DECIMAL(10,2)`) dikurangi sebesar $0.8$ tanpa mengganggu laporan barang persediaan lainnya.
* **Konsekuensi:** Kasir harus menginput harga beli modal toko sebelah secara manual saat transaksi agar HPP dan laba bersih nota tetap presisi.

### 3. Kejadian A: Pekerjaan Tambahan, Approval Timeout & Dual Estimates
**Jawaban:**
Pekerjaan tambahan yang ditemukan saat pembongkaran akan membuat log persetujuan baru (`approval_logs`) berstatus `PENDING` yang memisahkan kolom `initial_estimate` dan `final_cost` secara berdampingan. Jika pemilik motor tidak dapat dihubungi hingga bengkel tutup, alur sistem memberlakukan aturan *Timeout Rule*: status pekerjaan dikunci pada opsi *Hold/Reassemble* tanpa mengeksekusi pengerjaan tambahan tersebut. Log persetujuan menyimpan jejak riwayat estimasi awal Rp150.000 bersandingan dengan estimasi revisi Rp400.000 sebagai bukti transparansi di meja kasir.
* **Konsekuensi:** Motor yang mengalami *Hold Approval* akan menempati *lift* atau area parkir menginap bengkel sehingga berpotensi menurunkan *throughput* pengerjaan harian.

### 4. Kejadian E: Kunjungan Garansi (Jasa Gratis, Suku Cadang Ditagih)
**Jawaban:**
Kunjungan garansi dibuatkan Nota/Work Order Baru yang ditandai sebagai `Warranty Claim` dan ditautkan ke ID Invoice Lama sebagai referensi histori. Pada nota baru ini, tarif item jasa di-override otomatis menjadi Rp0 (omzet jasa nihil, namun komisi mekanik garansi tetap dicatat sesuai kebijakan internal), sedangkan suku cadang baru tetap ditagih secara normal. Omzet bengkel pada kunjungan garansi hanya dihitung dari nilai penjualan suku cadang baru, sementara laba jasa tidak bertambah.
* **Konsekuensi:** Laba kotor dari transaksi garansi menjadi lebih kecil karena beban biaya operasional jasa tidak menghasilkan pendapatan (*revenue*).

### 5. Kejadian F: Satu Pembayaran untuk Empat Nota Kurang Bayar
**Jawaban:**
Sistem menerapkan relasi *Many-to-Many* antara tabel `payments` dan `invoices` menggunakan tabel perantara `payment_allocations`. Ketika pemilik rental membayar dengan nominal yang kurang dari total tagihan 4 nota, kasir memasukkan 1 data pembayaran lalu sistem mengalokasikan dana tersebut untuk memotong Nota 1 hingga Nota 3 sampai lunas, serta menyisakan sisa kurang bayar pada Nota 4. Sistem menjawab pertanyaan nota mana yang kurang bayar dengan menampilkan kolom `balance_due` di setiap invoice, di mana nota berstatus `partially_paid` akan memuat rincian nominal sisa tagihan secara eksak.
* **Konsekuensi:** Kasir harus melakukan konfirmasi urutan pemotongan nota (metode FIFO atau prioritas manual) sebelum menyimpan transaksi pembayaran *bulk*.