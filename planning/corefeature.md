# Spesifikasi Fitur Inti & Alur Kerja Aplikasi (Core Features & App Flow)

## 1. Fitur-Fitur Inti (Core Features)

### A. Modul Work Order & Multi-Mekanik
- **Check-in & Fast Search Pelanggan/Rental:** Pencarian cepat berdasarkan plat nomor atau nama pemilik. Mendukung pengelolaan multi-kendaraan (1 pemilik rental bisa mengelola >20 unit motor).
- **Multi-Mechanic Item Assignment:** Penugasan mekanik dilakukan di level *Item Pekerjaan*, bukan *Header Work Order*. Contoh: *Item Jasa Kelistrikan* ditugaskan ke Pak Sarno, sedangkan *Item Jasa Ganti Oli* ditugaskan ke Mekanik Junior.
- **Dual Estimate Tracking:** Menyimpan nilai `initial_estimate` (misal Rp150.000) dan `final_cost` (misal Rp400.000) secara transparan bersandingan untuk kebutuhan audit dan kejelasan saat di kasir.

### B. Modul Inventory & Direct Purchase (Toko Sebelah)
- **Decimal Stock Ledger:** Mendukung pengurangan dan pencatatan stok dalam angka desimal (contoh: pengeluaran `0.8 Liter` dari persediaan `1 Drum = 30 Liter`).
- **Direct Purchase Direct-to-Invoice:** Fitur pencatatan barang yang dibeli mendadak di toko sebelah (seperti kampas rem). Mencatat harga modal (beban kas operasional) + harga jual ke pelanggan. Membawa flag `is_inventory = FALSE` agar tidak merusak laporan persediaan gudang.
- **Trade-in / Scrap Ledger:** Pencatatan potongan tukar tambah aki bekas (-Rp20.000 pada total nota) dan otomatis menambah kuantitas di penampungan *Scrap Inventory* sebelum dijual ke pengepul.

### C. Modul Approval Gate & Timeout Handling
- **Real-time Approval Request:** Memicu status `WAITING_APPROVAL` saat mekanik menemukan kerusakan baru ketika membongkar mesin.
- **Timeout Decision Engine:** Aturan otomatis jika pemilik kendaraan tidak memberikan kepastian/tidak dapat dihubungi hingga bengkel tutup:
  - *Status Hold:* Motor menginap di bengkel tanpa ada pengerjaan/biaya tambahan.
  - *Status Reassemble:* Motor dirakit kembali sesuai kesepakatan estimasi awal.

### D. Modul Billing, Bulk Payment & Piutang
- **Multi-Invoice Settlement Matrix:** Memungkinkan kasir memilih beberapa nota menggantung (bon/piutang) milik 1 pelanggan, lalu memasukkan 1 nominal pembayaran parsial yang memotong nota-nota tersebut secara berurutan (FIFO atau Custom).
- **Outstanding Balances Tracker:** Menampilkan sisa piutang/kurang bayar (`balance_due`) per nota secara akurat.

### E. Modul Garansi & History Servis
- **Auto-Warranty Lookup:** Menarik data nota 14 hari terakhir secara otomatis saat motor kembali datang.
- **Warranty Claim Bill Breakdown:** Otomatis mengubah komponen Jasa = Rp0 (beban garansi internal) namun tetap menagih Suku Cadang baru secara normal.

---

## 2. Diagram Alur Kerja Operasional (Mermaid.js Flowchart)

```mermaid
flowchart TD
    %% SUBGRAPH: CHECK-IN & DIAGNOSA
    subgraph STAGE1 ["1. INTAKE & DIAGNOSA"]
        A[Motor Masuk / Pelanggan Datang] --> B[Cari/Input Customer & Motor]
        B --> C[Buat Work Order WO\nSave initial_estimate]
        C --> D[Mekanik Pembongkaran & Diagnosa]
    end

    %% SUBGRAPH: APPROVAL GATE & TIMEOUT
    subgraph STAGE2 ["2. GATE APPROVAL & TIMEOUT"]
        D --> E{Ada Kerusakan\nTambahan?}
        E -- Tidak --> F[Status: WORKING\nLanjut Pekerjaan Awal]
        E -- Ya --> G[Status: WAITING_APPROVAL\nGenerate Request Approval]
        
        G --> H{Respon Pemilik Motor}
        H -- Disetujui --> I[Update final_cost & Tambah Item WO] --> F
        H -- Ditolak --> J[Batal Item Tambahan, Kerjakan Awal] --> F
        H -- Timeout / Bengkel Tutup --> K{Kebijakan Timeout Engine}
        K -- Option A: Hold --> L[Motor Menginap, Pekerjaan Ditiadakan]
        K -- Option B: Reassemble --> M[Rakit Kembali Sesuai WO Awal]
    end

    %% SUBGRAPH: EKSEKUSI & TIPE ITEM
    subgraph STAGE3 ["3. EKSEKUSI & KLASIFIKASI ITEM"]
        F --> N[Proses Pengerjaan & Penagihan Baris Item]
        
        N --> O1["[Item Jasa] Services"]
        O1 --> P1[Assign to specific mechanic_id\nKalkulasi Komisi Mekanik]
        
        N --> O2["[Item Part] Inventory Stock"]
        O2 --> P2[Potong Stok Gudang Desimal\nContoh: 0.8 Liter Oli]
        
        N --> O3["[Item Part] Direct Purchase / Toko Sebelah"]
        O3 --> P3[Catat Cash Expense Modal\nis_inventory = FALSE]
        
        N --> O4["[Item Discount] Trade-in Aki Bekas"]
        O4 --> P4[Potong Total Tagihan\nScrap Inventory Aki +1 Unit]
    end

    %% SUBGRAPH: CHECKOUT & PAYMENT
    subgraph STAGE4 ["4. CHECKOUT & PAYMENT MATRIX"]
        P1 & P2 & P3 & P4 --> Q[WO Status: COMPLETED\nGenerate Invoice Final]
        Q --> R{Skenario Pembayaran Kasir}
        
        R -- Bayar Lunas --> S1[Invoice Status: PAID]
        R -- Bayar Parsial / Bon --> S2[Invoice Status: PARTIALLY_PAID\nCatat balance_due]
        R -- Bulk Payment / Rental 4 Nota --> S3[Entry 1 Payment\nMap via payment_allocations FIFO]
    end

    %% SUBGRAPH: GARANSI
    subgraph STAGE5 ["5. WARRANTY / KUNJUNGAN ULANG"]
        S1 & S2 & S3 --> T[Masuk Database Histori Servis]
        T -. Kunjungan Ulang < 14 Hari .-> U[Cek Garansi via Plat Nomor]
        U --> V[Buat WO Garansi Baru\nLinked to parent_invoice_id]
        V --> W[Rule: Jasa = Rp0 Gratis\nPart Baru = Ditagih Normal]
    end