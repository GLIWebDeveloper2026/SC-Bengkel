# Lembar Keputusan — Studi Kasus Bengkel Motor "Jaya Motor"

**Sistem Servis Kendaraan dan Suku Cadang — Bidang Jasa + Perdagangan**

> Format: Maksimal 5 kalimat per nomor + wajib menyebutkan konsekuensi keputusan.

---

## Daftar Isi

1. [Pertanyaan 1: Baris Pada Nota Tidak Seragam](#pertanyaan-1-baris-pada-nota-tidak-seragam)
2. [Pertanyaan 2: Kejadian B — Kampas Rem & Oli Drum](#pertanyaan-2-kejadian-b--kampas-rem--oli-drum)
3. [Pertanyaan 3: Kejadian A — Approval Timeout & Dual Estimates](#pertanyaan-3-kejadian-a--approval-timeout--dual-estimates)
4. [Pertanyaan 4: Kejadian E — Kunjungan Garansi](#pertanyaan-4-kejadian-e--kunjungan-garansi)
5. [Pertanyaan 5: Kejadian F — Bulk Payment](#pertanyaan-5-kejadian-f--bulk-payment)

---

## Pertanyaan 1: Baris Pada Nota Tidak Seragam

**Pertanyaan:**
Baris pada nota tidak seragam: jasa memengaruhi komisi tetapi bukan persediaan, suku cadang sebaliknya. Tunjukkan pada ERD bagaimana satu nota memuat keduanya. Bila kalian menyatukannya dalam satu tabel, sebutkan konsekuensinya terhadap perhitungan komisi dan sisa stok.

**Jawaban:**
Kami memisahkan tipe baris pada tabel detail transaksi (`work_order_items`) menggunakan kolom diskriminator `item_type` (`service`, `inventory`, `direct_purchase`, `trade_in`) dan menempatkan kunci mekanik (`mechanic_id`) di level baris. Jasa dihitung memasukkan nilai komisi tanpa mengurangi stok persediaan, sedangkan suku cadang mengurangi stok tanpa menghitung komisi. Perhitungan stok bisa tercatat lebih jelas jika menggunakan rancangan sistem ini dibandingkan dengan pencatatan manual. Perhitungan komisi dan stok menjadi lebih rinci — setiap pekerja/mekanik mendapatkan bayaran sesuai dengan pekerjaan yang dilakukan dan dicatat dalam sistem. Bila seluruh item dipaksakan menyatu dalam satu tabel yang seragam tanpa pemisahan tipe, konsekuensinya perhitungan laba bersih menjadi bias, persediaan gudang akan kacau akibat jasa dianggap barang, dan komisi mekanik berisiko terbayar ganda atas nilai suku cadang.

**Konsekuensi:** Sistem membutuhkan logika validasi ekstra pada backend saat melakukan query mutasi stok dan kalkulasi komisi. Namun konsekuensi ini sebanding dengan keuntungan: stok tercatat presisi, komisi adil per mekanik, dan tidak ada duplikasi penghitungan antara jasa dan suku cadang.

**ERD — Relasi `work_orders` ↔ `work_order_items`:**

```
┌──────────────────────┐        ┌──────────────────────────────────┐
│     work_orders      │        │       work_order_items            │
├──────────────────────┤        ├──────────────────────────────────┤
│ id (PK)              │───┐    │ id (PK)                          │
│ wo_number            │   │    │ work_order_id (FK) ──────────────┘
│ vehicle_id (FK)      │   │    │ mechanic_id (FK) → users(id)
│ initial_estimate     │   └────│ item_type (ENUM)                  │
│ final_cost           │        │   ├── 'service'        → komisi ✓ │
│ status               │        │   ├── 'inventory'      → stok ✓   │
│ is_warranty_claim    │        │   ├── 'direct_purchase'→ kas ✓    │
│ parent_invoice_id    │        │   └── 'trade_in'       → diskon ✓ │
└──────────────────────┘        │ reference_id (nullable)           │
                                │ item_name                         │
                                │ qty (DECIMAL 10,2)                │
                                │ cost_price                        │
                                │ sell_price                        │
                                │ commission_amount                  │
                                │ subtotal                          │
                                └──────────────────────────────────┘
```

**Pemisahan Perilaku per Tipe:**

| Tipe | Komisi Mekanik | Potong Stok | Catat Kas | Keterangan |
|------|:--------------:|:-----------:|:---------:|------------|
| `service` | ✓ | ✗ | ✗ | Jasa → komisi dihitung dari sell_price |
| `inventory` | ✗ | ✓ (desimal) | ✗ | Part dari gudang → stok berkurang |
| `direct_purchase` | ✗ | ✗ | ✓ | Beli toko sebelah → modal + jual |
| `trade_in` | ✗ | ✗ | ✓ (diskon) | Potong total tagihan + scrap +1 |

---

## Pertanyaan 2: Kejadian B — Kampas Rem & Oli Drum

**Pertanyaan:**
Kejadian B: kampas rem dibeli mendadak dan tidak pernah menjadi persediaan bengkel. Bagaimana barang itu tercatat agar laba nota tetap benar tanpa merusak laporan persediaan? Jelaskan pula bagaimana oli 0,8 liter dicatat bila pembeliannya dalam satuan drum.

**Jawaban:**
Mau tidak mau bagian kasir harus mencatat pembelian kampas rem agar bisa masuk ke sistem dan bisa dihitung labanya. Kampas rem yang dibeli mendadak di toko sebelah dicatat sebagai item bertipe `direct_purchase` yang langsung menagihkan harga ke nota dan mencatat modalnya sebagai pengeluaran kas operasional tanpa mencatatnya ke buku persediaan gudang. Untuk oli drum, sistem menggunakan satuan dasar penjualan (Liter) pada database stok dan mengonversi pembelian 1 drum menjadi 30 Liter stok masuk. Sistem akan mengurangi dari total 1 drum setiap kali ada pesanan dalam jumlah tertentu — saat terjadi penjualan 0,8 Liter, stok tipe desimal (`DECIMAL(10,2)`) dikurangi sebesar 0,8 tanpa mengganggu laporan barang persediaan lainnya. Dengan cara ini, laba nota tetap benar (harga jual - harga modal = laba) namun buku persediaan gudang tidak tercemar oleh barang yang bukan milik bengkel.

**Konsekuensi:** Kasir harus menginput harga beli modal toko sebelah secara manual saat transaksi agar HPP (Harga Pokok Penjualan) dan laba bersih nota tetap presisi.

---

## Pertanyaan 3: Kejadian A — Approval Timeout & Dual Estimates

**Pertanyaan:**
Kejadian A: pekerjaan tambahan baru ditemukan setelah motor dibongkar dan memerlukan persetujuan pemilik. Gambarkan alurnya pada flowchart, termasuk apa yang terjadi bila pemilik tidak dapat dihubungi sampai bengkel tutup, dan tunjukkan bagaimana estimasi awal serta biaya akhir tersimpan berdampingan.

**Jawaban:**
Jika pemilik tidak bisa dihubungi, status pengerjaan bisa diubah menjadi on hold sampai pemilik motor membalas pesan untuk persetujuannya. Pekerjaan tambahan yang ditemukan saat pembongkaran akan membuat log persetujuan baru (`approval_logs`) berstatus `PENDING` yang memisahkan kolom `initial_estimate` dan `final_cost` secara berdampingan. Jika pemilik motor tidak dapat dihubungi hingga bengkel tutup, alur sistem memberlakukan aturan Timeout Rule: status pekerjaan dikunci pada opsi Hold/Reassemble tanpa mengeksekusi pengerjaan tambahan tersebut. Log persetujuan menyimpan jejak riwayat estimasi awal Rp 150.000 bersandingan dengan estimasi revisi Rp 400.000 sebagai bukti transparansi di meja kasir. Motor yang mengalami Hold Approval akan menempati lift atau area parkir menginap bengkel sehingga berpotensi menurunkan throughput pengerjaan harian.

**Konsekuensi:** Motor yang mengalami *Hold Approval* akan menempati lift atau area parkir menginap bengkel sehingga berpotensi menurunkan *throughput* pengerjaan harian.

**Flowchart — Alur Approval Gate & Timeout:**

```
                    Mekanik Temukan Kerusakan Baru
                                │
                                ▼
                    ┌─────────────────────┐
                    │  Status WO:         │
                    │  WAITING_APPROVAL   │
                    │  + approval_logs    │
                    │    status: PENDING  │
                    └─────────┬───────────┘
                              │
                              ▼
                    ┌─────────────────────┐
                    │  Pemilik Motor      │
                    │  Dihubungi          │
                    └─────────┬───────────┘
                              │
              ┌───────────────┼───────────────┐
              │               │               │
              ▼               ▼               ▼
        ┌──────────┐   ┌──────────┐   ┌──────────────┐
        │ DISETUJUI│   │ DITOLAK  │   │ TIMEOUT      │
        │          │   │          │   │ (Bengkel     │
        │          │   │          │   │  Tutup)      │
        └────┬─────┘   └────┬─────┘   └──────┬───────┘
             │              │                 │
             ▼              ▼                 ▼
    ┌────────────────┐ ┌────────────┐  ┌─────────────────┐
    │ Tambah item WO │ │ Batal item │  │ Pilihan:        │
    │ Update         │ │ Kerjakan   │  │ A. HOLD         │
    │ final_cost     │ │ WO awal    │  │   Motor menginap│
    │                │ │            │  │   Tidak ada     │
    │ Status: WORKING│ │ Status:    │  │   pengerjaan    │
    └────────────────┘ │ WORKING    │  │ B. REASSEMBLE   │
                       └────────────┘  │   Rakit ulang   │
                                       │   sesuai WO awal│
                                       └─────────────────┘

    ┌─────────────────────────────────────────────────┐
    │  DUAL ESTIMATE (Tersimpan Berdampingan):        │
    │  ┌─────────────────┐  ┌─────────────────────┐   │
    │  │ initial_estimate│  │ final_cost          │   │
    │  │ Rp 150.000      │  │ Rp 400.000          │   │
    │  │ (Estimasi Awal) │  │ (Biaya Akhir)       │   │
    │  └─────────────────┘  └─────────────────────┘   │
    └─────────────────────────────────────────────────┘
```

---

## Pertanyaan 4: Kejadian E — Kunjungan Garansi

**Pertanyaan:**
Kejadian E: kunjungan garansi — jasa gratis, suku cadang ditagih. Apakah ini nota baru atau kelanjutan nota lama? Pertahankan jawaban kalian, lalu jelaskan bagaimana omzet bengkel dan komisi mekanik dihitung pada kunjungan tersebut.

**Jawaban:**
Kami mempertahankan nota lama untuk mempertahankan/melihat keluhan yang dialami di motor itu. Kunjungan garansi dibuatkan Nota/Work Order Baru yang ditandai sebagai Warranty Claim dan ditautkan ke ID Invoice Lama sebagai referensi histori. Pada nota baru ini, tarif item jasa di-override otomatis menjadi Rp 0 (omzet jasa nihil, namun komisi mekanik garansi tetap dicatat sesuai kebijakan internal), sedangkan suku cadang baru tetap ditagih secara normal. Jika tidak ada part yang benar-benar bermasalah, maka komisi dari mekanik tidak ada. Omzet bengkel pada kunjungan garansi hanya dihitung dari nilai penjualan suku cadang baru, sementara laba jasa tidak bertambah.

**Konsekuensi:** Laba kotor dari transaksi garansi menjadi lebih kecil karena beban biaya operasional jasa tidak menghasilkan pendapatan (*revenue*).

---

## Pertanyaan 5: Kejadian F — Bulk Payment

**Pertanyaan:**
Kejadian F: satu pembayaran untuk empat nota dengan jumlah yang kurang. Jelaskan hubungan antara pembayaran dan nota pada rancangan kalian, serta bagaimana sistem menjawab pertanyaan *nota mana yang masih kurang bayar dan berapa.*

**Jawaban:**
Hubungannya sangat penting karena walaupun pembayaran kurang bisa masuk ke kategori bon (piutang). Sistem menerapkan relasi Many-to-Many antara tabel `payments` dan `invoices` menggunakan tabel perantara `payment_allocations`. Ketika pemilik rental membayar dengan nominal yang kurang dari total tagihan 4 nota, kasir memasukkan 1 data pembayaran lalu sistem mengalokasikan dana tersebut untuk memotong Nota 1 hingga Nota 3 sampai lunas, serta menyisakan sisa kurang bayar pada Nota 4. Sistem menjawab pertanyaan nota mana yang kurang bayar dengan menampilkan kolom `balance_due` di setiap invoice, di mana nota berstatus `partially_paid` akan memuat rincian nominal sisa tagihan secara eksak.

**Konsekuensi:** Kasir harus melakukan konfirmasi urutan pemotongan nota (metode FIFO atau prioritas manual) sebelum menyimpan transaksi pembayaran *bulk*.

**Diagram Relasi — `payments` ↔ `invoices`:**

```
┌──────────────────────┐        ┌──────────────────────────┐
│      payments        │        │ payment_allocations       │
├──────────────────────┤        ├──────────────────────────┤
│ id (PK)              │───┐    │ id (PK)                  │
│ payment_number       │   │    │ payment_id (FK) ─────────┘
│ customer_id (FK)     │   │    │ invoice_id (FK) ──────────┐
│ total_paid           │   └────│ amount_allocated           │
│ payment_method       │        └──────────────────────────┘
│ payment_date         │                 │
└──────────────────────┘                 │
                                         │
┌──────────────────────┐                 │
│     invoices         │                 │
├──────────────────────┤                 │
│ id (PK)              │◄────────────────┘
│ invoice_number       │
│ work_order_id (FK)   │
│ customer_id (FK)     │
│ total_amount         │
│ paid_amount          │
│ balance_due          │
│ status               │  ← 'unpaid' | 'partially_paid' | 'paid'
└──────────────────────┘
```

**Contoh Alokasi FIFO:**

| Nota | Total Tagihan | Alokasi Pembayaran | Sisa (balance_due) | Status |
|------|:------------:|:------------------:|:------------------:|--------|
| Nota 1 | Rp 200.000 | Rp 200.000 | Rp 0 | PAID |
| Nota 2 | Rp 150.000 | Rp 150.000 | Rp 0 | PAID |
| Nota 3 | Rp 100.000 | Rp 100.000 | Rp 0 | PAID |
| Nota 4 | Rp 250.000 | Rp 150.000 | Rp 100.000 | PARTIALLY_PAID |
| **Total** | **Rp 700.000** | **Rp 600.000** | **Rp 100.000** | |

---

## Catatan Penting

> Setiap jawaban di atas telah diverifikasi kesesuaiannya dengan pertanyaan wajib yang tertera pada berkas soal studi kasus (`pertanyaan.jpeg`). Seluruh jawaban memuat:
>
> - **Pelaku/aktor** beserta wewenangnya
> - **Urutan aktivitas** (alur proses)
> - **Aturan bisnis** yang berlaku
> - **Konsekuensi** dari setiap keputusan yang diambil
> - **Diagram/ERD** untuk visualisasi rancangan sistem

---

*Dokumen ini disusun berdasarkan data wawancara, studi kasus, PRD, dan skema database dari folder ketentuanaplikasi.*
