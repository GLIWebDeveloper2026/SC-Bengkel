# Sistem Operasional & Kasir Bengkel Motor "Jaya Motor"

Aplikasi Web Full-Stack Manajemen Operasional Bengkel Motor "Jaya Motor" berbasis Laravel Engine dengan UI modern glassmorphism. Aplikasi ini menyelesaikan masalah pencatatan nota manual yang tidak seragam, kebocoran laba, komisi multi-mekanik, konversi stok desimal, barang beli mendadak (toko sebelah), alokasi *bulk payment* (pemilik rental), garansi servis 14 hari, manajemen restock gudang, dan pembatasan validasi ketat (RBAC & Stok).

---

## 👥 Nama Tim
- Daffa Dermawan
- M. Raffi Ar-Rasyid
- Raka Erlangga
- M. Satria NP

---

## 🚀 Fitur Utama & Penyelesaian Studi Kasus

1. **Line Item Classifier (Baris Nota Tidak Seragam):**
   - `service`: Menghitung `commission_amount` mekanik, tanpa memotong stok gudang.
   - `inventory`: Memotong stok desimal (`stock_qty` pada `parts`), komisi opsional didukung, serta dilengkapi validasi ketat kapasitas stok gudang (mencegah *negative stock*).
   - `direct_purchase` (Toko Sebelah): Mencatat `cost_price` (modal kasir) & `sell_price` (harga ke pelanggan) tanpa memotong stok gudang.
   - `trade_in` (Aki Bekas): Memasang harga negatif (`sell_price < 0`) dan menambah kuantitas di penampungan `scrap_items`.

2. **Multi-Mekanik 1 Work Order (Kejadian C / Pak Sarno & Junior Mechanic):**
   - Penugasan `mechanic_id` berada di level baris pekerjaan (`work_order_items`). 1 Motor dapat dikerjakan bersama oleh Pak Sarno (Mekanik Utama) dan Junior Mechanic dengan komisi terpisah secara eksak.

3. **Restock Gudang & Konversi Satuan (Oli 0,8L dari Drum 30L / Kejadian B):**
   - `parts` menyimpan `conversion_factor` (1 Drum = 30 Liter) dan `stock_qty` bertipe `DECIMAL(10,2)`.
   - Modul **Restock Gudang** khusus untuk Kasir & Owner menambah stok persediaan secara aman.

4. **Dual Estimate & Approval Timeout Gate (Kejadian A):**
   - Menyimpan `initial_estimate` dan `final_cost` berdampingan.
   - Merekam request temuan kerusakan tambahan via `approval_logs` dengan penanganan aturan `TIMEOUT_HOLD` saat pemilik motor tidak dapat dihubungi hingga bengkel tutup.

5. **Bulk Payment Matrix & Overpayment Protection (Pemilik Rental 4 Nota / Kejadian F):**
   - Relasi *Many-to-Many* `payments` <-> `invoices` via pivot `payment_allocations`.
   - 1 Kali Pembayaran memotong N Invoice menggantung secara berurutan (FIFO) dan memperbarui sisa `balance_due`.
   - **Proteksi Overpayment (Tiga Lapis):** Mencegah sistem dan kasir dari kelebihan bayar, menjamin data `total_paid` konsisten mutlak dengan piutang.

6. **Garansi 14 Hari & Breakdown Billing (Bu Tuti / Kejadian E):**
   - Lookup riwayat plat nomor $< 14\text{ hari}$.
   - Work Order Garansi baru ditautkan ke `parent_invoice_id` dengan rule otomatis: **Jasa = Rp0 (Gratis Garansi)**, Suku Cadang baru ditagih normal.

7. **Role-Based Access Control (RBAC):**
   - Proteksi ketat hak akses 4 Aktor (Owner, Kasir, Mekanik Senior, Mekanik Junior).
   - *Quick Login* tersedia lengkap di halaman login, Mekanik otomatis diblokir jika masuk ke menu Keuangan/Owner.

---

## 🛠️ Cara Menjalankan Aplikasi di Lokal

1. **Persyaratan Sistem:**
   - PHP >= 8.2 (dengan ekstensi `pdo_sqlite` & `sqlite3` aktif)
   - Composer

2. **Jalankan Migrasi & Database Seeder:**
   ```bash
   php artisan migrate:fresh --seed
   ```

3. **Menjalankan Dev Server:**
   ```bash
   php artisan serve
   ```
   Akses aplikasi di browser pada `http://127.0.0.1:8000`.

4. **Menjalankan Automated Test Suite (18 Test Cases, 100% Lulus):**
   ```bash
   php artisan test
   ```

---

## 📁 Struktur Folder Utama
- `app/Models/` — Model Eloquent (`WorkOrder`, `WorkOrderItem`, `Invoice`, `Payment`, `PaymentAllocation`, `Part`, `Service`, `Customer`, `Vehicle`, `ScrapItem`, `ApprovalLog`)
- `app/Services/` — Business Logic Engine (`WorkOrderService`, `PaymentService`, `WarrantyService`)
- `app/Http/Controllers/` — Application Controllers (`WorkOrderController`, `PaymentController`, `InventoryController`, `WarrantyController`, `ReportController`, `DashboardController`)
- `database/migrations/` — Migration Schema Laravel Engine
- `database/seeders/` — Seeder Mandatory (Pak Hendra, Mbak Rina, Pak Sarno, Junior Mechanic, Pemilik Rental, dsb)
- `resources/views/` — Views UI responsive dark glassmorphic (`layouts/app.blade.php`, `work_orders/`, `inventory/`, `invoices/`, `payments/`, `warranty/`, `reports/`)
- `tests/Feature/` — Test Suite Komprehensif (18 Skenario Operasional)
