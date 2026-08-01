@extends('layouts.app')

@section('title', 'Work Order ' . $workOrder->wo_number)
@section('subtitle', 'Detail Item Pengerjaan, Multi-Mekanik, & Dual Estimate')

@section('content')
<!-- Header Banner / Dual Estimate Panel -->
<div class="card" style="background: linear-gradient(135deg, #1e293b, #0f172a); border-left: 4px solid #3b82f6;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <span class="badge badge-{{ $workOrder->status }}" style="font-size: 13px; margin-bottom: 8px;">
                STATUS: {{ strtoupper(str_replace('_', ' ', $workOrder->status)) }}
            </span>
            @if($workOrder->is_warranty_claim)
                <span class="badge" style="background: rgba(139, 92, 246, 0.3); color: #c084fc; font-size: 13px; margin-bottom: 8px;">
                    KLAIM GARANSI 14 HARI (JASA RP0)
                </span>
            @endif
            <h2 style="font-size: 22px;">{{ $workOrder->vehicle->model }} — <span style="color: #60a5fa;">{{ $workOrder->vehicle->plate_number }}</span></h2>
            <p style="color: var(--text-muted); font-size: 13px;">
                Pelanggan: <strong>{{ $workOrder->vehicle->customer->name }}</strong> {{ $workOrder->vehicle->customer->is_rental_owner ? '(Akun Rental Motor)' : '' }} |
                Tgl Masuk: {{ $workOrder->created_at->format('d M Y H:i') }}
            </p>
        </div>

        <div style="display: flex; gap: 24px; background: rgba(15, 23, 42, 0.6); padding: 12px 20px; border-radius: 12px; border: 1px solid var(--border-color);">
            <div>
                <div style="font-size: 11px; color: var(--text-muted); text-transform: uppercase;">Estimasi Awal Biaya</div>
                <div style="font-size: 18px; font-weight: 700; color: #cbd5e1;">Rp {{ number_format($workOrder->initial_estimate, 0, ',', '.') }}</div>
            </div>
            <div style="border-left: 1px solid var(--border-color); padding-left: 20px;">
                <div style="font-size: 11px; color: var(--text-muted); text-transform: uppercase;">Biaya Akhir Realisasi</div>
                <div style="font-size: 22px; font-weight: 800; color: #34d399;">Rp {{ number_format($workOrder->final_cost, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Pending Approval Banners (If Any) -->
@foreach($workOrder->approvalLogs as $log)
    @if($log->status === 'PENDING')
    <div class="alert alert-warning" style="flex-direction: column; align-items: flex-start; gap: 8px;">
        <div style="font-weight: 700;"><i class="fa-solid fa-bell"></i> MINTA PERSETUJUAN PEKERJAAN TAMBAHAN (Kejadian A)</div>
        <div>Mekanik menemukan kerusakan tambahan: <strong>{{ $log->requested_item_name }}</strong> dengan perkiraan biaya <strong>Rp {{ number_format($log->estimated_cost, 0, ',', '.') }}</strong>.</div>
        @if((auth()->user()->role ?? '') === 'owner')
        <div style="display: flex; gap: 8px; margin-top: 6px;">
            <form action="{{ route('approvals.respond', $log->id) }}" method="POST" style="display: inline;">
                @csrf
                <input type="hidden" name="status" value="APPROVED">
                <button type="submit" class="btn btn-success" style="padding: 4px 10px; font-size: 12px;"><i class="fa-solid fa-check"></i> Setujui (Approved)</button>
            </form>
            <form action="{{ route('approvals.respond', $log->id) }}" method="POST" style="display: inline;">
                @csrf
                <input type="hidden" name="status" value="REJECTED">
                <button type="submit" class="btn btn-danger" style="padding: 4px 10px; font-size: 12px;"><i class="fa-solid fa-xmark"></i> Tolak (Rejected)</button>
            </form>
            <form action="{{ route('approvals.respond', $log->id) }}" method="POST" style="display: inline;">
                @csrf
                <input type="hidden" name="status" value="TIMEOUT_HOLD">
                <button type="submit" class="btn btn-secondary" style="padding: 4px 10px; font-size: 12px;"><i class="fa-solid fa-hourglass-end"></i> Timeout Bengkel Tutup (Hold)</button>
            </form>
        </div>
        @else
        <div style="margin-top: 6px; font-size: 12px; color: var(--text-muted);">
            <i class="fa-solid fa-lock"></i> Persetujuan hanya dapat diproses oleh Owner (Pak Hendra).
        </div>
        @endif
    </div>
    @elseif($log->status === 'TIMEOUT_HOLD')
    <div class="alert alert-danger">
        <i class="fa-solid fa-ban"></i> <strong>TIMEOUT RULE HOLD:</strong> Pemilik motor belum merespon hingga bengkel tutup. Pengerjaan tambahan ditahan & motor menginap.
    </div>
    @endif
@endforeach

<div class="grid-3">
    <!-- Left 2 Columns: Line Items Table -->
    <div style="grid-column: span 2;" class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <h3 style="font-size: 16px;"><i class="fa-solid fa-list-ol"></i> Baris Item Pekerjaan & Sparepart</h3>
            @if($workOrder->invoice)
                <a href="{{ route('invoices.show', $workOrder->invoice->id) }}" class="btn btn-success" style="padding: 6px 12px; font-size: 12px;">
                    <i class="fa-solid fa-print"></i> Lihat Nota / Invoice
                </a>
            @elseif(in_array(auth()->user()->role ?? '', ['owner', 'cashier']))
                <form action="{{ route('work-orders.checkout', $workOrder->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary" style="padding: 6px 12px; font-size: 12px;" onclick="return confirm('Selesaikan Work Order & Terbitkan Invoice?')">
                        <i class="fa-solid fa-cash-register"></i> Checkout & Terbitkan Nota
                    </button>
                </form>
            @else
                <span class="badge badge-working" style="padding: 6px 12px; font-size: 12px;">
                    <i class="fa-solid fa-wrench"></i> Mode Mekanik (Proses Pengerjaan)
                </span>
            @endif
        </div>

        <table>
            <thead>
                <tr>
                    <th>Tipe Baris</th>
                    <th>Deskripsi Item</th>
                    <th>Mekanik Penanggung Jawab</th>
                    <th>Qty (Desimal)</th>
                    <th>Harga Jual</th>
                    <th>Komisi</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($workOrder->items as $item)
                <tr>
                    <td>
                        @if($item->item_type == 'service')
                            <span class="badge" style="background: rgba(59, 130, 246, 0.2); color: #60a5fa;">JASA</span>
                        @elseif($item->item_type == 'inventory')
                            <span class="badge" style="background: rgba(16, 185, 129, 0.2); color: #34d399;">GUDANG</span>
                        @elseif($item->item_type == 'direct_purchase')
                            <span class="badge" style="background: rgba(245, 158, 11, 0.2); color: #fbbf24;">TOKO SEBELAH</span>
                        @elseif($item->item_type == 'trade_in')
                            <span class="badge" style="background: rgba(239, 68, 68, 0.2); color: #f87171;">TUKAR TAMBAH</span>
                        @endif
                    </td>
                    <td>
                        <strong>{{ $item->item_name }}</strong>
                        @if($item->item_type == 'direct_purchase')
                            <br><small style="color: var(--text-muted);">Modal: Rp {{ number_format($item->cost_price, 0, ',', '.') }}</small>
                        @endif
                    </td>
                    <td>
                        @if($item->mechanic)
                            <strong style="color: #a78bfa;"><i class="fa-solid fa-user-gear"></i> {{ $item->mechanic->name }}</strong>
                        @else
                            <span style="color: var(--text-muted);">-</span>
                        @endif
                    </td>
                    <td><strong>{{ number_format($item->qty, 2) }}</strong></td>
                    <td>Rp {{ number_format($item->sell_price, 0, ',', '.') }}</td>
                    <td style="color: #60a5fa;">Rp {{ number_format($item->commission_amount, 0, ',', '.') }}</td>
                    <td style="font-weight: 700; {{ $item->subtotal < 0 ? 'color: #f87171;' : '' }}">
                        Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align: center; color: var(--text-muted); padding: 24px;">Belum ada item ditambahkan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Right Column: Add Item Form & Request Approval -->
    <div style="display: flex; flex-direction: column; gap: 16px;">
        <!-- Add Item Form -->
        <div class="card">
            <h3 style="font-size: 15px; margin-bottom: 14px;"><i class="fa-solid fa-plus-circle"></i> Tambah Baris Item Pekerjaan</h3>
            <form action="{{ route('work-orders.items.store', $workOrder->id) }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="item_type">Tipe Classifier Baris</label>
                    <select name="item_type" id="item_type" class="form-control" required onchange="toggleItemTypeForm(this.value)">
                        <option value="service">Jasa Servis (Berkomisi, No-Stok)</option>
                        <option value="inventory">Suku Cadang Gudang (Potong Stok Desimal & Komisi)</option>
                        <option value="direct_purchase">Beli Langsung / Toko Sebelah (Kejadian B)</option>
                        <option value="trade_in">Tukar Tambah Aki Bekas (Diskon Minus & Scrap +1)</option>
                    </select>
                </div>

                <div class="form-group" id="part_select_group" style="display: none;">
                    <label for="reference_id">Pilih Master Sparepart Gudang</label>
                    <select name="reference_id" id="reference_id" class="form-control" onchange="onPartSelected(this)">
                        <option value="">-- Manual / Pilih Barang Gudang --</option>
                        @foreach($parts as $p)
                            <option value="{{ $p->id }}" data-name="{{ $p->name }}" data-price="{{ $p->sell_price }}" data-stock="{{ $p->stock_qty }}" data-unit="{{ $p->sell_unit }}">
                                {{ $p->name }} (Sisa Stok: {{ number_format($p->stock_qty, 2) }} {{ $p->sell_unit }}) — Rp {{ number_format($p->sell_price, 0, ',', '.') }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group" id="mechanic_group">
                    <label for="mechanic_id">Mekanik Penanggung Jawab (Multi-Mekanik)</label>
                    <select name="mechanic_id" id="mechanic_id" class="form-control">
                        <option value="">-- Pilih Mekanik --</option>
                        @foreach($mechanics as $m)
                            <option value="{{ $m->id }}">{{ $m->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="item_name">Nama Pekerjaan / Barang</label>
                    <input type="text" name="item_name" id="item_name" class="form-control" placeholder="Misal: Perbaikan Kelistrikan / Oli Drum 0.8L" required>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label for="qty">Kuantitas (Qty Desimal)</label>
                        <input type="number" step="0.01" name="qty" id="qty" class="form-control" value="1.00" required oninput="validateStockCapacity()">
                        <div id="stock-overdraft-warning" style="color: #f87171; font-size: 11px; margin-top: 4px; display: none;">
                            <i class="fa-solid fa-circle-xmark"></i> <span id="stock-warning-text">Qty melebihi sisa stok gudang!</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="sell_price">Harga Jual (Rp)</label>
                        <input type="number" name="sell_price" id="sell_price" class="form-control" placeholder="150000" required>
                    </div>
                </div>

                <div class="grid-2" id="extra_price_group">
                    <div class="form-group" id="cost_price_group" style="display: none;">
                        <label for="cost_price">Harga Modal Toko Sebelah</label>
                        <input type="number" name="cost_price" id="cost_price" class="form-control" placeholder="30000">
                    </div>
                    <div class="form-group" id="commission_group">
                        <label for="commission_amount">Komisi Mekanik (Rp)</label>
                        <input type="number" name="commission_amount" id="commission_amount" class="form-control" placeholder="35000">
                    </div>
                </div>

                <button type="submit" id="btn-add-item" class="btn btn-primary" style="width: 100%; justify-content: center; margin-top: 10px;">
                    <i class="fa-solid fa-check"></i> Tambahkan Baris
                </button>
            </form>
        </div>

        <!-- Request Approval Form (Kejadian A) -->
        <div class="card" style="border: 1px dashed var(--accent-warning);">
            <h3 style="font-size: 14px; color: #fbbf24; margin-bottom: 10px;"><i class="fa-solid fa-triangle-exclamation"></i> Minta Approval Kerusakan Baru</h3>
            <form action="{{ route('work-orders.approval.request', $workOrder->id) }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="requested_item_name">Temuan Kerusakan Baru</label>
                    <input type="text" name="requested_item_name" class="form-control" placeholder="Misal: Gear Rasio Aus" required>
                </div>
                <div class="form-group">
                    <label for="estimated_cost">Estimasi Biaya Tambahan (Rp)</label>
                    <input type="number" name="estimated_cost" class="form-control" placeholder="250000" required>
                </div>
                <button type="submit" class="btn btn-warning" style="width: 100%; justify-content: center; font-size: 13px;">
                    <i class="fa-solid fa-paper-plane"></i> Kirim Request Approval
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    function toggleItemTypeForm(type) {
        const mechanicGroup = document.getElementById('mechanic_group');
        const costGroup = document.getElementById('cost_price_group');
        const commissionGroup = document.getElementById('commission_group');
        const partSelectGroup = document.getElementById('part_select_group');
        const sellPrice = document.getElementById('sell_price');

        if (type === 'service') {
            mechanicGroup.style.display = 'block';
            costGroup.style.display = 'none';
            commissionGroup.style.display = 'block';
            partSelectGroup.style.display = 'none';
        } else if (type === 'inventory') {
            mechanicGroup.style.display = 'block';
            costGroup.style.display = 'none';
            commissionGroup.style.display = 'block';
            partSelectGroup.style.display = 'block';
        } else if (type === 'direct_purchase') {
            mechanicGroup.style.display = 'none';
            costGroup.style.display = 'block';
            commissionGroup.style.display = 'none';
            partSelectGroup.style.display = 'none';
        } else if (type === 'trade_in') {
            mechanicGroup.style.display = 'none';
            costGroup.style.display = 'none';
            commissionGroup.style.display = 'none';
            partSelectGroup.style.display = 'none';
            sellPrice.value = '-20000'; // Default minus trade-in discount
        } else {
            mechanicGroup.style.display = 'none';
            costGroup.style.display = 'none';
            commissionGroup.style.display = 'none';
            partSelectGroup.style.display = 'none';
        }
        validateStockCapacity();
    }

    function validateStockCapacity() {
        const itemType = document.getElementById('item_type').value;
        const partSelect = document.getElementById('reference_id');
        const qtyInput = parseFloat(document.getElementById('qty').value || 0);
        const warningDiv = document.getElementById('stock-overdraft-warning');
        const warningText = document.getElementById('stock-warning-text');
        const submitBtn = document.getElementById('btn-add-item');

        if (itemType === 'inventory' && partSelect && partSelect.selectedIndex > 0) {
            const option = partSelect.options[partSelect.selectedIndex];
            const availableStock = parseFloat(option.dataset.stock || 0);
            const unit = option.dataset.unit || '';

            if (qtyInput > availableStock) {
                warningText.innerText = 'Qty (' + qtyInput + ') melebihi stok gudang (' + availableStock + ' ' + unit + ')!';
                warningDiv.style.display = 'block';
                if (submitBtn) submitBtn.disabled = true;
                return;
            }
        }

        warningDiv.style.display = 'none';
        if (submitBtn) submitBtn.disabled = false;
    }

    function onPartSelected(select) {
        const option = select.options[select.selectedIndex];
        if (option && option.value) {
            document.getElementById('item_name').value = option.dataset.name || '';
            document.getElementById('sell_price').value = option.dataset.price || '';
        }
        validateStockCapacity();
    }
</script>
@endsection
