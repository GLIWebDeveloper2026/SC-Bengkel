-- SKEMA DATABASE BENGKEL JAYA MOTOR (POSTGRESQL / SUPABASE / LARAVEL MIGRATIONS)

-- 1. USERS & ROLES
CREATE TYPE user_role AS ENUM ('owner', 'cashier', 'mechanic');
CREATE TABLE users (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(250) NOT NULL,
    role user_role NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. CUSTOMERS & VEHICLES
CREATE TABLE customers (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(250) NOT NULL,
    phone VARCHAR(50),
    is_rental_owner BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE vehicles (
    id BIGSERIAL PRIMARY KEY,
    customer_id BIGINT REFERENCES customers(id),
    plate_number VARCHAR(20) UNIQUE NOT NULL,
    model VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. SERVICES & PARTS MASTER
CREATE TABLE services (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(250) NOT NULL,
    price DECIMAL(12,2) NOT NULL,
    default_commission_amount DECIMAL(12,2) DEFAULT 0
);

CREATE TABLE parts (
    id BIGSERIAL PRIMARY KEY,
    code VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(250) NOT NULL,
    purchase_unit VARCHAR(20) NOT NULL, -- Contoh: 'Drum', 'Dus', 'Pcs'
    sell_unit VARCHAR(20) NOT NULL,     -- Contoh: 'Liter', 'Pcs'
    conversion_factor DECIMAL(10,2) DEFAULT 1.00, -- 1 Drum = 30 Liter
    stock_qty DECIMAL(10,2) DEFAULT 0.00, -- Menggunakan desimal (0.8 Liter)
    min_stock DECIMAL(10,2) DEFAULT 0.00,
    buy_price DECIMAL(12,2) NOT NULL,
    sell_price DECIMAL(12,2) NOT NULL
);

-- 4. WORK ORDERS & APPROVAL LOGS
CREATE TYPE wo_status AS ENUM ('queue', 'diagnosing', 'waiting_approval', 'working', 'completed', 'cancelled');
CREATE TABLE work_orders (
    id BIGSERIAL PRIMARY KEY,
    wo_number VARCHAR(50) UNIQUE NOT NULL,
    vehicle_id BIGINT REFERENCES vehicles(id),
    initial_estimate DECIMAL(12,2) NOT NULL,
    final_cost DECIMAL(12,2) DEFAULT 0,
    status wo_status DEFAULT 'queue',
    is_warranty_claim BOOLEAN DEFAULT FALSE,
    parent_invoice_id BIGINT, -- Terhubung ke invoice lama jika ini garansi
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE approval_logs (
    id BIGSERIAL PRIMARY KEY,
    work_order_id BIGINT REFERENCES work_orders(id),
    requested_item_name VARCHAR(250) NOT NULL,
    estimated_cost DECIMAL(12,2) NOT NULL,
    status VARCHAR(20) DEFAULT 'PENDING', -- 'APPROVED', 'REJECTED', 'TIMEOUT_HOLD'
    approved_by_user_id BIGINT REFERENCES users(id),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 5. WORK ORDER ITEMS (MULTI-MEKANIK & TIPE BARIS NOTA)
CREATE TYPE item_type AS ENUM ('service', 'inventory', 'direct_purchase', 'trade_in');
CREATE TABLE work_order_items (
    id BIGSERIAL PRIMARY KEY,
    work_order_id BIGINT REFERENCES work_orders(id),
    mechanic_id BIGINT REFERENCES users(id), -- Multi-mekanik per baris
    item_type item_type NOT NULL,
    reference_id BIGINT, -- ID dari tabel services atau parts (nullable jika direct purchase)
    item_name VARCHAR(250) NOT NULL,
    qty DECIMAL(10,2) NOT NULL, -- Mengakomodasi 0.8 L
    cost_price DECIMAL(12,2) DEFAULT 0, -- Modal (penting untuk direct_purchase)
    sell_price DECIMAL(12,2) NOT NULL,
    commission_amount DECIMAL(12,2) DEFAULT 0, -- Komisi mekanik terhitung
    subtotal DECIMAL(12,2) NOT NULL
);

-- 6. INVOICES, PAYMENTS & ALLOCATIONS (BULK / PARSIAL)
CREATE TYPE invoice_status AS ENUM ('unpaid', 'partially_paid', 'paid');
CREATE TABLE invoices (
    id BIGSERIAL PRIMARY KEY,
    invoice_number VARCHAR(50) UNIQUE NOT NULL,
    work_order_id BIGINT REFERENCES work_orders(id),
    customer_id BIGINT REFERENCES customers(id),
    total_amount DECIMAL(12,2) NOT NULL,
    paid_amount DECIMAL(12,2) DEFAULT 0,
    balance_due DECIMAL(12,2) NOT NULL,
    status invoice_status DEFAULT 'unpaid',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE payments (
    id BIGSERIAL PRIMARY KEY,
    payment_number VARCHAR(50) UNIQUE NOT NULL,
    customer_id BIGINT REFERENCES customers(id),
    total_paid DECIMAL(12,2) NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Pivot Table: 1 Payment memotong N Invoices (Bulk/Partial Matrix)
CREATE TABLE payment_allocations (
    id BIGSERIAL PRIMARY KEY,
    payment_id BIGINT REFERENCES payments(id),
    invoice_id BIGINT REFERENCES invoices(id),
    amount_allocated DECIMAL(12,2) NOT NULL
);

-- 7. SCRAP INVENTORY (AKI BEKAS)
CREATE TABLE scrap_items (
    id BIGSERIAL PRIMARY KEY,
    item_name VARCHAR(100) DEFAULT 'Aki Bekas',
    qty INT DEFAULT 0,
    collected_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    sold_date TIMESTAMP,
    sale_amount DECIMAL(12,2)
);