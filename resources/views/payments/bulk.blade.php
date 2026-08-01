@extends('layouts.app')

@section('title', 'Pelunasan Bulk (Akun Rental Motor)')
@section('subtitle', 'Matrix Pelunasan Multi-Nota dengan 1 Kali Pembayaran Parsial')

@section('content')
<div class="card" style="margin-bottom: 24px;">
    <h3 style="font-size: 16px; margin-bottom: 16px;"><i class="fa-solid fa-filter"></i> Pilih Pelanggan / Pemilik Rental</h3>
    <form action="{{ route('payments.bulk') }}" method="GET" style="display: flex; gap: 12px; align-items: flex-end;">
        <div class="form-group" style="flex: 1; margin-bottom: 0;">
            <label for="customer_id">Daftar Akun Pelanggan (Rental Owner / Piutang)</label>
            <select name="customer_id" id="customer_id" class="form-control" onchange="this.form.submit()">
                <option value="">-- Pilih Pelanggan --</option>
                @foreach($customers as $c)
                    <option value="{{ $c->id }}" {{ $selectedCustomerId == $c->id ? 'selected' : '' }}>
                        {{ $c->name }} {{ $c->is_rental_owner ? '[RENTAL OWNER]' : '' }}
                    </option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-search"></i> Tampilkan Nota</button>
    </form>
</div>

@if($selectedCustomerId)
<form action="{{ route('payments.bulk.process') }}" method="POST">
    @csrf
    <input type="hidden" name="customer_id" value="{{ $selectedCustomerId }}">

    <div class="grid-3">
        <div style="grid-column: span 2;" class="card">
            <h3 style="font-size: 16px; margin-bottom: 16px;"><i class="fa-solid fa-receipt"></i> Daftar Nota menggantung (Unpaid / Partially Paid)</h3>
            <table>
                <thead>
                    <tr>
                        <th style="width: 40px;"><input type="checkbox" id="select-all" onclick="toggleSelectAll(this)" checked></th>
                        <th>No. Invoice</th>
                        <th>No. WO & Plat Motor</th>
                        <th>Total Tagihan</th>
                        <th>Sudah Dibayar</th>
                        <th>Sisa Kurang Bayar (Due)</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($unpaidInvoices as $inv)
                    <tr>
                        <td>
                            <input type="checkbox" name="invoice_ids[]" value="{{ $inv->id }}" class="inv-checkbox" checked data-due="{{ $inv->balance_due }}">
                        </td>
                        <td><strong>{{ $inv->invoice_number }}</strong></td>
                        <td>
                            {{ $inv->workOrder->wo_number }} <br>
                            <small style="color: #60a5fa;">{{ $inv->workOrder->vehicle->plate_number }} ({{ $inv->workOrder->vehicle->model }})</small>
                        </td>
                        <td>Rp {{ number_format($inv->total_amount, 0, ',', '.') }}</td>
                        <td style="color: #34d399;">Rp {{ number_format($inv->paid_amount, 0, ',', '.') }}</td>
                        <td style="font-weight: 700; color: #f87171;">Rp {{ number_format($inv->balance_due, 0, ',', '.') }}</td>
                        <td><span class="badge badge-{{ $inv->status }}">{{ strtoupper($inv->status) }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="7" style="text-align: center; color: #34d399; padding: 24px;">Tidak ada nota menggantung untuk pelanggan ini. Semua lunas!</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card" style="border-top: 4px solid #10b981;">
            <h3 style="font-size: 16px; margin-bottom: 16px;"><i class="fa-solid fa-calculator"></i> Matrix Alokasi Dana</h3>

            <div class="form-group">
                <label for="total_paid">Total Uang Diterima (Rp)</label>
                <input type="number" name="total_paid" id="total_paid" class="form-control" placeholder="Contoh: 700000" required oninput="calculateAllocation()">
                <div id="overpay-warning" style="color: #f87171; font-size: 12px; margin-top: 6px; display: none;">
                    <i class="fa-solid fa-circle-xmark"></i> Nominal pembayaran melebihi total sisa tagihan terpilih!
                </div>
            </div>

            <div class="form-group">
                <label for="payment_method">Metode Pembayaran</label>
                <select name="payment_method" id="payment_method" class="form-control">
                    <option value="CASH">CASH / TUNAI</option>
                    <option value="TRANSFER">TRANSFER BANK</option>
                    <option value="QRIS">QRIS</option>
                </select>
            </div>

            <div style="background: var(--bg-dark); padding: 14px; border-radius: 10px; border: 1px solid var(--border-color); margin-top: 16px;">
                <div style="display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 6px;">
                    <span>Total Piutang Terpilih:</span>
                    <strong id="total-selected-due">Rp 0</strong>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 6px;">
                    <span>Alokasi Memotong:</span>
                    <strong id="allocated-money" style="color: #34d399;">Rp 0</strong>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 14px; border-top: 1px solid var(--border-color); padding-top: 6px; margin-top: 6px;">
                    <span>Sisa Kurang Bayar Akun:</span>
                    <strong id="remaining-account-due" style="color: #f87171;">Rp 0</strong>
                </div>
            </div>

            <button type="submit" id="btn-submit-bulk" class="btn btn-success" style="width: 100%; justify-content: center; margin-top: 20px;" {{ $unpaidInvoices->isEmpty() ? 'disabled' : '' }}>
                <i class="fa-solid fa-floppy-disk"></i> Eksekusi Bulk Payment
            </button>
        </div>
    </div>
</form>

<script>
    function toggleSelectAll(master) {
        const checkboxes = document.querySelectorAll('.inv-checkbox');
        checkboxes.forEach(cb => cb.checked = master.checked);
        calculateAllocation();
    }

    function calculateAllocation() {
        const checkboxes = document.querySelectorAll('.inv-checkbox:checked');
        let totalDue = 0;
        checkboxes.forEach(cb => {
            totalDue += parseFloat(cb.dataset.due || 0);
        });

        const paidInput = parseFloat(document.getElementById('total_paid').value || 0);
        const overpayWarning = document.getElementById('overpay-warning');
        const submitBtn = document.getElementById('btn-submit-bulk');

        if (paidInput > totalDue && totalDue > 0) {
            overpayWarning.style.display = 'block';
            if (submitBtn) submitBtn.disabled = true;
        } else {
            overpayWarning.style.display = 'none';
            if (submitBtn) submitBtn.disabled = (checkboxes.length === 0);
        }

        const allocated = Math.min(paidInput, totalDue);
        const remaining = Math.max(0, totalDue - allocated);

        document.getElementById('total-selected-due').innerText = 'Rp ' + totalDue.toLocaleString('id-ID');
        document.getElementById('allocated-money').innerText = 'Rp ' + allocated.toLocaleString('id-ID');
        document.getElementById('remaining-account-due').innerText = 'Rp ' + remaining.toLocaleString('id-ID');
    }

    document.addEventListener('DOMContentLoaded', calculateAllocation);
</script>
@endif
@endsection
