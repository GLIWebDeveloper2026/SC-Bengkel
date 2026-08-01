# Matrix Halaman Frontend & Endpoint API Backend

## 1. Structure Halaman Frontend (Next.js / React / Vue)

### A. Role Admin / Kasir (Mbak Rina)
1. `/dashboard` — Ringkasan WO aktif, antrean kasir, alert stok kritis.
2. `/work-orders` — Daftar Work Order (Filter: Queue, Working, Pending Approval, Done).
3. `/work-orders/create` — Form Check-in Motor & Input Keluhan Awal.
4. `/work-orders/[id]` — Detail WO, tambah item jasa/part, input barang toko sebelah, set multi-mekanik.
5. `/cashier/checkout/[wo_id]` — Preview Nota, input diskon aki bekas, cetak nota.
6. `/payments/bulk` — Matrix pelunasan multi-nota pemilik rental.
7. `/warranty` — Pencarian histori plat nomor & penerbitan klaim garansi.

### B. Role Mekanik (Pak Sarno & Junior)
1. `/mechanic/board` — Kanban board daftar pekerjaan per mekanik.
2. `/mechanic/job/[item_id]` — Update status pengerjaan item & input temuan kerusakan baru.

### C. Role Pemilik Bengkel (Pak Hendra)
1. `/owner/dashboard` — Grafik Omzet vs Laba Bersih, Pekerjaan Terbanyak.
2. `/owner/approvals` — Halaman persetujuan pekerjaan tambahan mendesak.
3. `/owner/commissions` — Laporan komisi per mekanik berdasarkan periode.
4. `/owner/inventory` — Kontrol stok persediaan desimal, konversi drum, dan rekap aki bekas (scrap).

---

## 2. API Endpoints Backend (Laravel / Node.js Express / Supabase REST)

### Auth & Master Data
- `POST /api/auth/login`
- `GET  /api/customers?search={query}`
- `POST /api/customers`
- `GET  /api/vehicles?customer_id={id}`

### Work Order & Operations
- `GET  /api/work-orders`
- `POST /api/work-orders` (Input WO baru + initial estimate)
- `GET  /api/work-orders/{id}`
- `POST /api/work-orders/{id}/items` (Tambah jasa/part/direct_purchase/trade_in + assign mechanic_id)
- `POST /api/work-orders/{id}/request-approval` (Trigger pekerjaan tambahan)
- `PATCH /api/approvals/{id}/respond` (Approve / Reject / Timeout)

### Billing, Payments & Warranty
- `POST /api/invoices/generate` (Konversi WO selesai menjadi Invoice)
- `POST /api/payments/bulk` (Satu pembayaran untuk memotong banyak Invoice ID)
- `GET  /api/customers/{id}/outstanding-invoices` (Cek nota yang masih kurang bayar / bon)
- `POST /api/warranty/claim` (Membuat WO Garansi baru bertaut ke Invoice Lama)

### Reports & Analytics
- `GET  /api/reports/commissions?mechanic_id={id}&month={m}`
- `GET  /api/reports/profit-loss` (Pemisahan Laba Jasa vs Laba Part vs Beban Direct Purchase)
- `GET  /api/reports/scrap-inventory` (Rekap Aki Bekas)