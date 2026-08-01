# Uraian Proses Bisnis Bengkel Motor "Jaya Motor"

**Sistem Servis Kendaraan dan Suku Cadang — Bidang Jasa + Perdagangan**

---

## Daftar Isi

1. [Identitas Dokumen](#1-identitas-dokumen)
2. [Pelaku/Aktor & Wewenang](#2-pelakuaktor--wewenang)
3. [Proses Bisnis AS-IS (Berjalan)](#3-proses-bisnis-as-is-berjalan)
4. [Proses Bisnis TO-BE (Usulan)](#4-proses-bisnis-to-be-usulan)
5. [Aturan Bisnis](#5-aturan-bisnis)
6. [Daftar Asumsi](#6-daftar-asumsi)

---

## 1. Identitas Dokumen

| Field | Keterangan |
|-------|------------|
| Nama Proyek | Sistem Operasional & Kasir Bengkel Motor "Jaya Motor" |
| Jenis Dokumen | Uraian Proses Bisnis AS-IS & TO-BE |
| Sumber Data | Wawancara pemangku kepentingan, studi kasus, dokumen PRD |
| Pemangku Kepentingan | Pak Hendra (Pemilik), Mbak Rina (Kasir), Pak Sarno (Mekanik Senior), Pelanggan |
| Lingkup | Operasional bengkel: intake, diagnosa, approval, eksekusi, kasir, garansi, scrap |

---

## 2. Pelaku/Aktor & Wewenang

### 2.1 Daftar Pelaku

| No | Nama | Peran | Atribut |
|----|------|-------|---------|
| 1 | Pak Hendra | Pemilik Bengkel | 5 mekanik, pemilik usaha, pengambil keputusan strategis |
| 2 | Mbak Rina | Kasir & Administrasi | Mengelola nota, transaksi kasir, registrasi pelanggan |
| 3 | Pak Sarno | Mekanik Senior | Spesialis kelistrikan, 1 dari 5 mekanik |
| 4 | Mekanik Junior | Mekanik | Mengerjakan servis ringan, penggantian oli |
| 5 | Bu Tuti | Pelanggan (Individu) | Memiliki 3 motor, pelanggan rutin |
| 6 | Pemilik Rental | Pelanggan (Bulk) | Mengelola >20 unit motor, multi-nota |
| 7 | Pengepul Aki | Pihak Ketiga | Membeli aki bekas sebulan sekali |

### 2.2 Wewenang per Aktor

#### Pak Hendra — Pemilik Bengkel

| Wewenang | Deskripsi |
|----------|-----------|
| Approval pekerjaan tambahan | Menyetujui/menolak pekerjaan tambahan yang ditemukan mekanik setelah pembongkaran |
| Laporan omzet & laba bersih | Melihat grafik omzet vs laba bersih, pekerjaan terbanyak |
| Kontrol stok gudang | Memantau stok persediaan desimal, konversi drum, rekap aki bekas |
| Perhitungan komisi otomatis | Melihat laporan komisi per mekanik berdasarkan periode |
| Audit scrap/aki bekas | Mengecek rekap aki bekas dan penjualan ke pengepul |

#### Mbak Rina — Kasir & Administrasi

| Wewenang | Deskripsi |
|----------|-----------|
| Input Work Order | Membuat work order baru saat motor masuk, input keluhan awal |
| Registrasi customer/motor | Mendaftarkan pelanggan baru dan data kendaraan |
| Transaksi kasir | Melakukan check-out, input diskon aki bekas, cetak nota |
| Pembelian mendadak | Mencatat barang beli di toko sebelah (direct purchase) |
| Alokasi pembayaran parsial/bulk | Mengalokasikan 1 pembayaran ke beberapa nota (FIFO/custom) |
| Klaim garansi | Mencari histori plat nomor dan menerbitkan klaim garansi |

#### Pak Sarno — Mekanik Senior (Kelistrikan)

| Wewenang | Deskripsi |
|----------|-----------|
| Update status pengerjaan | Memperbarui status item pekerjaan di kanban board |
| Input temuan kerusakan baru | Melaporkan kerusakan tambahan yang ditemukan saat pembongkaran |
| Request sparepart | Meminta suku cadang yang dibutuhkan |
| Assign ke item kelistrikan | Mengerjakan item jasa kelistrikan (bidang keahlian) |

#### Mekanik Junior — Mekanik

| Wewenang | Deskripsi |
|----------|-----------|
| Update status pengerjaan | Memperbarui status item pekerjaan di kanban board |
| Request sparepart | Meminta suku cadang yang dibutuhkan |
| Assign ke item servis ringan | Mengerjakan item jasa servis ringan, penggantian oli |

#### Bu Tuti — Pelanggan (Individu)

| Wewenang | Deskripsi |
|----------|-----------|
| Menyampaikan keluhan | Memberikan informasi gejala kerusakan motor |
| Menyetujui/tolak pekerjaan tambahan | Merespon approval request untuk pekerjaan tambahan |
| Membayar tagihan | Melunasi nota servis |

#### Pemilik Rental — Pelanggan (Bulk)

| Wewenang | Deskripsi |
|----------|-----------|
| Mengelola multi-unit | Mengelola >20 unit motor dengan 1 akun |
| Pembayaran bulk | Melunasi 4-5 nota sekaligus dengan 1 pembayaran |
| Menyetujui/tolak pekerjaan tambahan | Merespon approval request untuk unit-unit rental |

---

## 3. Proses Bisnis AS-IS (Berjalan)

> Kondisi operasional bengkel saat ini berdasarkan hasil wawancara dan studi kasus.
> Seluruh pencatatan masih memakai nota rangkap tiga dan buku stok yang ditulis tangan.

### 3.1 Alur Intake & Diagnosa (AS-IS)

```
┌─────────────────────────────────────────────────────────────────┐
│  STAGE 1: INTAKE & DIAGNOSA (AS-IS)                            │
└─────────────────────────────────────────────────────────────────┘

  Motor Masuk ke Bengkel
        │
        ▼
  Kasir (Mbak Rina) menyapa pelanggan
        │
        ▼
  Kasir MENULIS NOTA RANGKAP 3 secara manual
  ├── Identitas pelanggan (nama, alamat)
  ├── Data motor (plat nomor, merek)
  ├── Keluhan awal dari pelanggan
  └── Estimasi biaya awal (diperkirakan kasir/mekanik)
        │
        ▼
  Kasir MENCARI di buku catatan apakah pelanggan sudah ada
  (Jika belum → tulis data baru di buku)
        │
        ▼
  Motor diteruskan ke mekanik (Pak Sarno / Junior)
        │
        ▼
  Mekanik MEMBONGKAR mesin untuk diagnosa
        │
        ▼
  Ditemukan kerusakan tambahan?
  ├── Ya → Mekanik TELEPON pemilik motor
  │        ├── Disetujui → Kerjakan semua
  │        └── Ditolak → Kerjakan yang awal saja
  └── Tidak → Kerjakan sesuai keluhan awal
```

**Masalah yang teridentifikasi pada stage ini:**
- Estimasi awal sering meleset dari biaya akhir (Pak Sarno: "Di depan saya bilang seratus lima puluh ribu, ternyata jadi empat ratus")
- Tidak ada catatan digital; pelanggan harus menunggu ditelepon
- Pencarian data pelanggan manual di buku tulis

### 3.2 Alur Pekerjaan & Item (AS-IS)

```
┌─────────────────────────────────────────────────────────────────┐
│  STAGE 2: PEKERJAAN & ITEM (AS-IS)                             │
└─────────────────────────────────────────────────────────────────┘

  Mekanik mengerjakan servis
        │
        ▼
  NOTA berisi CAMPURAN semua baris:
  ├── Jasa servis ringan (misal: Rp 50.000)
  ├── Oli 0,8 liter dari gudang bengkel
  ├── Kampas rem beli di toko sebelah (stok kosong)
  └── (Semua ditulis dalam satu kolom yang sama)
        │
        ▼
  Komisi mekanik dihitung MANUAL oleh kasir
  (Bingung: berapa komisi Pak Sarno vs Junior?)
        │
        ▼
  Stok oli dicatat MANUAL di buku stok
  (Kasir bingung menuliskan 0,8 liter dari drum 30 liter)
        │
        ▼
  Kampas rem (beli toko sebelah) TIDAK masuk stok gudang
  (Tapi tercatat di nota sebagai pengeluaran)
```

**Masalah yang teridentifikasi pada stage ini:**
- Baris nota tidak seragam: jasa mempengaruhi komisi tetapi bukan persediaan, suku cadang sebaliknya
- Komisi mekanik sulit dihitung konsisten (Pak Hendra: "Komisi mekanik sulit dihitung konsisten")
- Stok oli desimal bingung dicatat (Mbak Rina: "Kalau ada pelanggan minta nol koma delapan liter, saya bingung menuliskannya")
- Barang beli mendadak masuk nota tapi tidak pernah masuk stok (Mbak Rina: "Kalau saya catat masuk lalu keluar, laporan stok saya jadi aneh")

### 3.3 Alur Multi-Mekanik (AS-IS)

```
┌─────────────────────────────────────────────────────────────────┐
│  STAGE 3: MULTI-MEKANIK (AS-IS)                                │
└─────────────────────────────────────────────────────────────────┘

  Satu motor dikerjakan dua mekanik pada hari yang sama:
  ├── Penggantian oli → Mekanik Junior
  └── Perbaikan kelistrikan → Pak Sarno
        │
        ▼
  Keduanya berhak atas komisi
        │
        ▼
  Kasir MENCATAT MANUAL di nota:
  "Servis oli + kelistrikan"
  (Tidak ada pemisahan siapa mengerjakan apa)
        │
        ▼
  Komisi dihitung campur aduk
  (Pak Hendra harus menebak siapa yang lebih produktif)
```

**Masalah yang teridentifikasi:**
- Tidak ada pencatatan siapa mengerjakan item mana
- Komisi tidak bisa dipisah per mekanik
- Tidak bisa melacak mekanik paling produktif

### 3.4 Alur Pekerjaan Tambahan & Approval (AS-IS)

```
┌─────────────────────────────────────────────────────────────────┐
│  STAGE 4: PEKERJAAN TAMBAHAN & APPROVAL (AS-IS)               │
└─────────────────────────────────────────────────────────────────┘

  Mekanik menemukan kerusakan baru setelah pembongkaran
        │
        ▼
  Mekanik TELEPON pemilik motor
        │
        ├── Pemilik SETUJUI
        │   └── Kerjakan, tambah biaya di nota
        │
        ├── Pemilik TOLAK
        │   └── Batal, kerjakan yang awal saja
        │
        └── Pemilik TIDAK DIHUBUNGI sampai bengkel tutup
            └── Motor menginap tanpa kejelasan
                (Pak Sarno: "Motor menginap seminggu,
                 akhirnya diambil tanpa dikerjakan apa-apa")
```

**Masalah yang teridentifikasi:**
- Tidak ada catatan approval yang terstruktur
- Estimasi awal vs biaya akhir tidak transparan di kasir
- Motor menginap memakan tempat tanpa pengerjaan (throughput turun)
- Tidak ada mekanisme timeout yang jelas

### 3.5 Alur Pembayaran & Piutang (AS-IS)

```
┌─────────────────────────────────────────────────────────────────┐
│  STAGE 5: PEMBAYARAN & PIUTANG (AS-IS)                         │
└─────────────────────────────────────────────────────────────────┘

  Nota selesai → Kasir tulis "LUNAS" atau "BON" di nota
        │
        ├── Bayar lunas → Selesai
        │
        └── Bon (piutang) → Dicatat di buku piutang
            │
            ▼
        Akhir bulan: Pemilik rental datang bayar
        ├── 4-5 nota sekaligus dengan 1 kali pembayaran
        ├── Jumlahnya KURANG dari total tagihan
        └── Kasir HITUNG MANUAL berapa potong nota mana
            │
            ▼
        Kasir coret-coret di buku:
        "Nota 1 lunas, Nota 2 lunas, Nota 3 kurang Rp 50.000,
         Nota 4 kurang Rp 120.000"
```

**Masalah yang teridentifikasi:**
- Pencatatan piutang manual, rawan salah hitung
- Tidak ada jejak digital pembayaran
- Sulit melihat nota mana yang masih kurang bayar
- Bulk payment membingungkan kasir

### 3.6 Alur Garansi (AS-IS)

```
┌─────────────────────────────────────────────────────────────────┐
│  STAGE 6: GARANSI (AS-IS)                                      │
└─────────────────────────────────────────────────────────────────┘

  Bu Tuti servis motor → Bayar lunas
        │
        ▼
  Seminggu kemudian: motor bunyi lagi
        │
        ▼
  Bu Tuti datang → "Katanya masih garansi"
        │
        ▼
  Kasir BOLAK-BALIK BUKU cari nota lama
  (Bu Tuti: "Mereka harus membolak-balik buku dulu")
        │
        ▼
  Kasir BINGUNG: garansi jasa atau part?
  (Bu Tuti: "Saya tetap disuruh bayar kampas rem baru.
             Saya bingung, garansinya di sebelah mana")
        │
        ▼
  Keputusan kasir: kadang digratiskan, kadang tetap ditagih
  (Tidak konsisten)
```

**Masalah yang teridentifikasi:**
- Riwayat servis tidak tercatat rapi
- Garansi 2 minggu membingungkan antara suku cadang dan jasa
- Pencarian nota lama sangat lambat
- Keputusan garansi tidak konsisten

### 3.7 Alur Scrap / Aki Bekas (AS-IS)

```
┌─────────────────────────────────────────────────────────────────┐
│  STAGE 7: SCRAP / AKI BEKAS (AS-IS)                            │
└─────────────────────────────────────────────────────────────────┘

  Pelanggan tukar tambah aki bekas
        │
        ▼
  Mekanik (Pak Sarno) KUMPULKAN aki bekas di pojok bengkel
  ("Aki bekasnya saya kumpulkan, sebulan sekali ada pengepul datang")
        │
        ▼
  Pengepul datang 1x sebulan
        │
        ▼
  Transaksi CASH, tidak ada catatan digital
  (Pak Hendra ingin tahu rekap aki bekas tapi tidak ada datanya)
```

**Masalah yang teridentifikasi:**
- Tidak ada pencatatan jumlah aki bekas
- Tidak ada jejak transaksi penjualan ke pengepul
- Potongan harga aki bekas di nota tidak terstruktur

---

## 4. Proses Bisnis TO-BE (Usulan)

> Rancangan sistem operasional digital berbasis web untuk menyelesaikan seluruh masalah AS-IS.

### 4.1 Alur Intake & Diagnosa (TO-BE)

```
┌─────────────────────────────────────────────────────────────────┐
│  STAGE 1: INTAKE & DIAGNOSA (TO-BE)                            │
└─────────────────────────────────────────────────────────────────┘

  Motor Masuk ke Bengkel
        │
        ▼
  Kasir (Mbak Rina) membuka halaman /work-orders/create
        │
        ▼
  Sistem: Fast Search Pelanggan/Rental
  ├── Input plat nomor → Sistem temukan data pelanggan
  ├── Input nama → Sistem temukan data pelanggan
  └── Jika baru → Form registrasi customer & motor baru
        │
        ▼
  Sistem: Buat Work Order Baru
  ├── Nomor WO otomatis (WO-YYYYMMDD-XXX)
  ├── Input keluhan awal
  ├── Input initial_estimate (misal: Rp 150.000)
  └── Status: QUEUE
        │
        ▼
  Motor diteruskan ke mekanik via kanban board
        │
        ▼
  Mekanik (Pak Sarno / Junior) terima WO di /mechanic/board
        │
        ▼
  Mekanik MEMBONGKAR mesin & melakukan diagnosa
        │
        ▼
  Status WO berubah: QUEUE → DIAGNOSING
```

**Peningkatan dari AS-IS:**
- Pencarian pelanggan instan berdasarkan plat/nama
- Data pelanggan tersimpan digital, tidak perlu buka buku
- Multi-kendaraan per pemilik rental terelasi otomatis
- Estimasi awal tercatat transparan di sistem

### 4.2 Alur Approval Gate & Timeout (TO-BE)

```
┌─────────────────────────────────────────────────────────────────┐
│  STAGE 2: GATE APPROVAL & TIMEOUT (TO-BE)                      │
└─────────────────────────────────────────────────────────────────┘

  Mekanik menemukan kerusakan baru setelah pembongkaran
        │
        ▼
  Ada kerusakan tambahan?
  ├── TIDAK → Status: WORKING → Lanjut pekerjaan awal
  │
  └── YA → Mekanik input temuan di /mechanic/job/[item_id]
            │
            ▼
          Sistem: Status WO → WAITING_APPROVAL
          Sistem: Generate Approval Log
          ├── requested_item_name: "Ganti Kampas Rem"
          ├── estimated_cost: Rp 200.000
          └── status: PENDING
            │
            ▼
          Pemilik motor (Bu Tuti / Pemilik Rental) respon:
            │
            ├── DISETUJUI
            │   ├── Approval Log → APPROVED
            │   ├── Tambah item ke WO
            │   ├── Update final_cost
            │   └── Status WO → WORKING
            │
            ├── DITOLAK
            │   ├── Approval Log → REJECTED
            │   ├── Batal item tambahan
            │   └── Status WO → WORKING (kerjakan awal)
            │
            └── TIMEOUT (bengkel tutup, tidak ada respon)
                ├── Pilihan A: HOLD
                │   ├── Approval Log → TIMEOUT_HOLD
                │   ├── Motor menginap di bengkel
                │   ├── Tidak ada pengerjaan/biaya tambahan
                │   └── Status WO → WAITING_APPROVAL (dijaga)
                │
                └── Pilihan B: REASSEMBLE
                    ├── Approval Log → TIMEOUT_HOLD
                    ├── Rakit kembali sesuai WO awal
                    ├── Biaya = initial_estimate
                    └── Status WO → COMPLETED
```

**Peningkatan dari AS-IS:**
- Approval tercatat digital dengan jejak audit
- Dual estimate (initial_estimate & final_cost) tersimpan berdampingan
- Timeout engine memberikan kejelasan keputusan
- Tidak ada motor yang "menginap tanpa kejelasan"

### 4.3 Alur Eksekusi & Klasifikasi Item (TO-BE)

```
┌─────────────────────────────────────────────────────────────────┐
│  STAGE 3: EKSEKUSI & KLASIFIKASI ITEM (TO-BE)                  │
└─────────────────────────────────────────────────────────────────┘

  Work Order dalam status WORKING
        │
        ▼
  Mekanik menambahkan item pekerjaan:
  /work-orders/[id]/items
        │
        ▼
  SETIAP ITEM WAJIB dikelompokkan ke tipe:

  ┌──────────────────────────────────────────────────────────┐
  │ TIPE 1: SERVICE (Jasa)                                  │
  │ ├── Item: Servis ringan, perbaikan kelistrikan           │
  │ ├── Assign mechanic_id (Pak Sarno / Junior)             │
  │ ├── Komisi otomatis dihitung                            │
  │ ├── TIDAK memotong stok persediaan                      │
  │ └── Contoh: Servis kelistrikan → Pak Sarno, Rp 80.000  │
  └──────────────────────────────────────────────────────────┘
        │
  ┌──────────────────────────────────────────────────────────┐
  │ TIPE 2: INVENTORY (Suku Cadang dari Gudang)             │
  │ ├── Item: Oli, sparepart dari gudang                    │
  │ ├── Potong stok desimal (0,8 Liter dari 30 Liter drum)  │
  │ ├── TIDAK menghasilkan komisi mekanik                   │
  │ └── Contoh: Oli 0,8 Liter → stok -0,8                  │
  └──────────────────────────────────────────────────────────┘
        │
  ┌──────────────────────────────────────────────────────────┐
  │ TIPE 3: DIRECT PURCHASE (Beli Mendadak / Toko Sebelah)  │
  │ ├── Item: Kampas rem, aksesoris mendadak                │
  │ ├── Catat harga modal (beban kas operasional)           │
  │ ├── Catat harga jual ke pelanggan                       │
  │ ├── flag: is_inventory = FALSE                           │
  │ ├── TIDAK memotong stok gudang                          │
  │ └── Contoh: Kampas rem → modal Rp 25.000, jual Rp 50.000│
  └──────────────────────────────────────────────────────────┘
        │
  ┌──────────────────────────────────────────────────────────┐
  │ TIPE 4: TRADE-IN (Tukar Tambah / Aki Bekas)             │
  │ ├── Item: Aki bekas                                     │
  │ ├── Potong total tagihan (diskon negatif)               │
  │ ├── Otomatis +1 di scrap inventory                      │
  │ └── Contoh: Aki bekas → -Rp 20.000 dari total nota      │
  └──────────────────────────────────────────────────────────┘
```

**Peningkatan dari AS-IS:**
- Pemisahan tipe item otomatis, menghitung komisi dan stok secara benar
- Multi-mekanik per baris: 1 WO bisa punya komisi untuk 2 mekanik berbeda
- Stok oli desimal tercatat presisi di database
- Direct purchase tidak merusak laporan persediaan gudang

### 4.4 Alur Checkout & Pembayaran (TO-BE)

```
┌─────────────────────────────────────────────────────────────────┐
│  STAGE 4: CHECKOUT & PAYMENT MATRIX (TO-BE)                    │
└─────────────────────────────────────────────────────────────────┘

  Semua item WO selesai dikerjakan
        │
        ▼
  Status WO → COMPLETED
        │
        ▼
  Sistem generate INVOICE otomatis:
  ├── invoice_number: INV-YYYYMMDD-XXX
  ├── total_amount: jumlah semua item subtotal
  ├── paid_amount: 0
  ├── balance_due: total_amount
  └── status: UNPAID
        │
        ▼
  Kasir buka /cashier/checkout/[wo_id]
  ├── Preview nota lengkap
  ├── Input diskon aki bekas (jika ada)
  └── Cetak nota
        │
        ▼
  SKENARIO PEMBAYARAN:

  ┌──────────────────────────────────────────────────────────┐
  │ Skenario A: BAYAR LUNAS                                  │
  │ ├── Kasir input nominal = total_amount                   │
  │ ├── Invoice status → PAID                                │
  │ ├── balance_due → 0                                      │
  │ └── Selesai                                              │
  └──────────────────────────────────────────────────────────┘
        │
  ┌──────────────────────────────────────────────────────────┐
  │ Skenario B: BAYAR PARSIAL (BON)                          │
  │ ├── Kasir input nominal < total_amount                   │
  │ ├── Invoice status → PARTIALLY_PAID                      │
  │ ├── balance_due = total_amount - paid_amount             │
  │ └── Sisa tagihan tercatat di sistem                      │
  └──────────────────────────────────────────────────────────┘
        │
  ┌──────────────────────────────────────────────────────────┐
  │ Skenario C: BULK PAYMENT (Pemilik Rental)                │
  │ ├── Pemilik rental bayar 1 kali untuk 4-5 nota          │
  │ ├── Kasir buka /payments/bulk                            │
  │ ├── Pilih nota-nota yang akan dilunasi                   │
  │ ├── Input 1 nominal pembayaran                           │
  │ ├── Sistem alokasikan via payment_allocations (FIFO)     │
  │ │   ├── Nota 1 (Rp 200.000) → Lunas                     │
  │ │   ├── Nota 2 (Rp 150.000) → Lunas                     │
  │ │   ├── Nota 3 (Rp 100.000) → Lunas                     │
  │ │   └── Nota 4 (Rp 250.000) → PARTIALLY (sisa Rp 100k) │
  │ └── Setiap invoice update balance_due masing-masing      │
  └──────────────────────────────────────────────────────────┘
```

**Peningkatan dari AS-IS:**
- Invoice tercatat digital dengan status jelas
- Bulk payment terstruktur, tidak perlu coret-coret buku
- Outstanding balances tracker: lihat sisa piutang per nota secara real-time
- Jejak pembayaran lengkap untuk audit

### 4.5 Alur Garansi (TO-BE)

```
┌─────────────────────────────────────────────────────────────────┐
│  STAGE 5: WARRANTY / KUNJUNGAN ULANG (TO-BE)                   │
└─────────────────────────────────────────────────────────────────┘

  Pelanggan (Bu Tuti) kembali dengan motor yang sama
        │
        ▼
  Kasir buka /warranty
  ├── Input plat nomor motor
  └── Sistem: Auto-Warranty Lookup
      ├── Cari semua invoice 14 hari terakhir
      ├── Jika ditemukan → KLAIM GARANSI
      └── Jika tidak → Nota servis baru biasa
        │
        ▼
  Klaim Garansi: Buat Work Order GARANSI BARU
  ├── flag: is_warranty_claim = TRUE
  ├── parent_invoice_id → ditaut ke invoice lama
  ├── Status WO: COMPLETED
        │
        ▼
  Generate Invoice Garansi:
  ├── Item Jasa → OVERRIDE ke Rp 0 (GRATIS)
  │   (Ongkos jasa digratiskan)
  ├── Item Suku Cadang BARU → Ditagih NORMAL
  │   (Satu suku cadang tetap ditagih)
  └── Omzet hanya dari penjualan suku cadang baru
        │
        ▼
  Kasir cetak nota garansi
  ├── Bu Tuti bayar hanya untuk suku cadang
  └── Jasa = Rp 0 (beban garansi internal)
```

**Peningkatan dari AS-IS:**
- Auto-warranty lookup: tidak perlu bolak-balik buku
- Keputusan garansi konsisten: Jasa = Rp 0, Part = ditagih
- Jejak histori servis lengkap per plat nomor
- Omzet garansi tercatat benar (hanya dari part)

### 4.6 Alur Scrap Inventory (TO-BE)

```
┌─────────────────────────────────────────────────────────────────┐
│  STAGE 6: SCRAP INVENTORY / AKI BEKAS (TO-BE)                  │
└─────────────────────────────────────────────────────────────────┘

  Pelanggan tukar tambah aki bekas
        │
        ▼
  Mekanik input item tipe TRADE-IN di WO
        │
        ▼
  Sistem OTOMATIS:
  ├── Kurangi total tagihan (diskon -Rp 20.000)
  └── Tambah +1 di tabel scrap_items
      ├── item_name: "Aki Bekas"
      ├── qty: +1
      └── collected_date: NOW()
        │
        ▼
  Pengepul datang (sebulan sekali)
        │
        ▼
  Pak Hendra buka /owner/inventory
  ├── Lihat rekap aki bekas (total qty)
  ├── Catat penjualan ke pengepul:
  │   ├── sold_date: NOW()
  │   └── sale_amount: Rp XXX.XXX
  └── Laporan audit scrap otomatis
```

**Peningkatan dari AS-IS:**
- Pencatatan otomatis saat trade-in
- Rekap jumlah aki bekas real-time
- Jejak transaksi penjualan ke pengepul
- Laporan audit untuk Pak Hendra

---

## 5. Aturan Bisnis

| No | Kode | Aturan | Deskripsi | Konsekuensi |
|----|------|--------|-----------|-------------|
| 1 | BR-01 | Pemisahan Tipe Item | Baris nota wajib dibedakan berdasarkan tipe: `service`, `inventory`, `direct_purchase`, `trade_in` | Backend membutuhkan logika validasi ekstra pada query mutasi stok dan kalkulasi komisi |
| 2 | BR-02 | Komisi = Service Only | Komisi mekanik hanya dihitung dari item tipe `service`, tidak dari part/inventory | Laba bersih menjadi lebih akurat karena tidak ada duplikasi komisi atas nilai suku cadang |
| 3 | BR-03 | Multi-Mekanik per Item | Penugasan mekanik dilakukan di level *Item Pekerjaan*, bukan *Header Work Order*. 1 WO bisa memuat komisi untuk 2+ mekanik berbeda | Sistem harus menyimpan `mechanic_id` di tabel `work_order_items`, bukan di `work_orders` |
| 4 | BR-04 | Dual Estimate Tracking | Simpan `initial_estimate` dan `final_cost` secara berdampingan untuk audit transparansi | Kolom terpisah di tabel `work_orders` untuk estimasi awal dan biaya akhir |
| 5 | BR-05 | Approval Gate | Pekerjaan tambahan yang ditemukan setelah pembongkaran wajib menunggu persetujuan pemilik kendaraan | Status WO berubah ke `WAITING_APPROVAL` sampai ada respon `APPROVED` atau `REJECTED` |
| 6 | BR-06 | Timeout Decision Engine | Jika pemilik tidak memberikan respon sampai bengkel tutup: pilihan **Hold** (motor menginap, tidak ada pengerjaan) atau **Reassemble** (rakit ulang sesuai WO awal) | Motor yang Hold memakan tempat di bengkel, berpotensi menurunkan throughput harian |
| 7 | BR-07 | Konversi Stok Desimal | Pembelian grosir (Drum 30 Liter) dikonversi ke satuan terkecil (Liter). Penjualan bisa menggunakan desimal (0,8 Liter) | Database menggunakan `DECIMAL(10,2)` untuk `stock_qty`. 1 Drum → +30 Liter, penjualan 0,8 Liter → -0,8 |
| 8 | BR-08 | Direct Purchase (Toko Sebelah) | Barang dibeli mendadak: masuk nota + catat harga modal sebagai pengeluaran kas. `is_inventory = FALSE` agar tidak merusak laporan persediaan | Kasir harus menginput harga beli modal toko sebelah secara manual agar HPP dan laba bersih tetap presisi |
| 9 | BR-09 | Trade-in / Scrap Ledger | Potongan tukar tambah aki bekas mengurangi total tagihan dan otomatis menambah kuantitas di scrap inventory | Sistem harus update 2 tabel sekaligus: kurangi `total_amount` invoice dan tambah `qty` di `scrap_items` |
| 10 | BR-10 | Garansi 14 Hari | Kunjungan ulang dalam 14 hari kalender: Jasa = Rp 0 (gratis), Suku Cadang baru tetap ditagih normal | Nota garansi adalah nota BARU yang ditaut ke invoice lama (`parent_invoice_id`). Omzet hanya dari part |
| 11 | BR-11 | Matrix Alokasi Pembayaran | 1 Pembayaran dapat dialokasikan memotong banyak Invoice sekaligus (Bulk Payment), dan 1 Invoice dapat dibayar beberapa kali (Partial Payment) | Relasi Many-to-Many: `payments` ↔ `invoices` via `payment_allocations`. FIFO atau manual |
| 12 | BR-12 | Outstanding Balance Tracker | Setiap invoice memiliki `balance_due` yang menunjukkan sisa tagihan secara akurat | Status invoice: `unpaid` → `partially_paid` → `paid` berdasarkan `paid_amount` vs `total_amount` |

---

## 6. Daftar Asumsi

> Berikut adalah asumsi yang diambil oleh tim untuk menutupi informasi yang tidak dijelaskan secara eksplisit di dalam soal studi kasus.

| No | Kode | Asumsi | Sumber / Alasan |
|----|------|--------|-----------------|
| 1 | A-01 | Bengkel beroperasi jam **08.00 – 17.00** WIB | Tidak disebutkan jam operasional. Diasumsikan jam kerja normal bengkel umum |
| 2 | A-02 | **Timeout approval** = saat bengkel tutup (pukul 17.00) | Dari wawancara Pak Sarno: "Saya harus telepon dulu, kalau setuju baru dikerjakan" + lembar keputusan: "sampai bengkel tutup" |
| 3 | A-03 | **Komisi mekanik** = proporsi dari harga jasa `service` | Tidak disebutkan angka pasti %. Diasumsikan berdasarkan nilai item jasa yang dikerjakan |
| 4 | A-04 | **Metode pembayaran** yang diterima: **cash** dan **transfer bank** | Tidak disebutkan spesifik. Diasumsikan metode umum bengkel kecil |
| 5 | A-05 | **Jumlah mekanik aktif** = 5 orang (termasuk Pak Sarno) | Dari wawancara Pak Hendra: "Mekanik saya lima" |
| 6 | A-06 | **Pemilik rental** memiliki **>20 unit motor** | Dari core feature: "1 pemilik rental bisa mengelola >20 unit motor" |
| 7 | A-07 | **Pengepul aki bekas** datang **1x sebulan** | Dari wawancara Pak Sarno: "sebulan sekali ada pengepul datang" |
| 8 | A-08 | **Masa garansi** = **14 hari kalender** dari tanggal servis | Dari PRD: "garansi 2 minggu" |
| 9 | A-09 | **Pilihan timeout** (Hold/Reassemble) dipilih oleh **kasir** berdasarkan kebijakan bengkel | Dari lembar keputusan: "motor yang mengalami Hold Approval akan menempati lift" |
| 10 | A-10 | **Nota garansi** adalah **nota baru** yang ditaut ke invoice lama | Dari lembar keputusan: "membuat Nota/Work Order Baru yang ditandai sebagai Warranty Claim" |
| 11 | A-11 | **Metode alokasi bulk payment** = **FIFO** (First In First Out) atau prioritas manual | Dari lembar keputusan: "metode FIFO atau prioritas manual" |
| 12 | A-12 | **Konversi oli** = 1 Drum = **30 Liter** | Diasumsikan standar industri oli motor. Tidak disebutkan angka pasti di soal |
| 13 | A-13 | **Tidak ada fitur reservasi/booking online** | Tidak disebutkan di soal. Motor datang langsung (walk-in) |
| 14 | A-14 | **Tidak ada fitur cetak struk thermal POS** | Hanya cetak nota rangkap 3 (AS-IS) / cetak nota digital (TO-BE) |
| 15 | A-15 | **Pencatatan langsung di tempat** (real-time) tanpa mode offline | Tidak ada kebutuhan sinkronasi offline yang disebutkan |
| 16 | A-16 | **Satu Work Order** = **satu motor** pada satu kunjungan | Tidak ada kasus 1 nota untuk beberapa motor sekaligus |
| 17 | A-17 | **Pemilik rental** adalah pelanggan yang sudah terdaftar dengan `is_rental_owner = TRUE` | Dari schema: tabel `customers` memiliki kolom `is_rental_owner` |
| 18 | A-18 | **Approval request** dikirim via notifikasi dalam aplikasi (bukan telepon/SMS) | TO-BE menggunakan sistem digital; mekanisme notifikasi diasumsikan in-app |
| 19 | A-19 | **Harga jual oli per liter** sudah diatur di master `parts` sebelumnya | Sistem memerlukan data harga jual untuk menghitung subtotal item inventory |
| 20 | A-20 | **Laporan komisi** dapat dilihat oleh Pak Hendra per periode (harian/mingguan/bulanan) | Dari PRD: "Pak Hendra ingin tahu mekanik mana yang paling menghasilkan" |

---

## Lampiran: Perbandingan AS-IS vs TO-BE

| Aspek | AS-IS (Berjalan) | TO-BE (Usulan) |
|-------|-------------------|-----------------|
| **Pencatatan** | Nota rangkap 3 manual + buku stok tulis tangan | Aplikasi web full-stack (Laravel + Filament) |
| **Pencarian Pelanggan** | Bolak-balik buku tulis | Fast search berdasarkan plat/nama |
| **Pemisahan Item** | Campur aduk dalam satu kolom | 4 tipe: service, inventory, direct_purchase, trade_in |
| **Multi-Mekanik** | Tidak ada pencatatan per mekanik | Assign per item, komisi terpisah |
| **Estimasi** | Hanya 1 estimasi, sering meleset | Dual estimate: initial + final berdampingan |
| **Approval** | Telepon manual, tidak ada jejak | Digital approval gate + timeout engine |
| **Stok Oli Desimal** | Bingung menulis 0,8 liter | Otomatis dengan DECIMAL(10,2) |
| **Beli Mendadak** | Masuk nota tapi tidak masuk stok | Flag is_inventory = FALSE, modal tercatat |
| **Scrap/Aki Bekas** | Dikumpulkan物理, tidak ada catatan | Otomatis +1 di scrap_items |
| **Pembayaran** | Coret-coret buku piutang | Matrix alokasi: bulk + partial payment |
| **Garansi** | Bolak-balik buku, keputusan tidak konsisten | Auto-warranty lookup 14 hari, Jasa = Rp 0 |
| **Laporan** | Tidak ada | Omzet, komisi, laba bersih, scrap audit |

---

*Dokumen ini disusun berdasarkan data wawancara, studi kasus, PRD, dan skema database dari folder ketentuanaplikasi.*
