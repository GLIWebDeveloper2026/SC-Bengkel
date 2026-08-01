@extends('layouts.app')

@section('title', 'Nota Pembayaran ' . $invoice->invoice_number)
@section('subtitle', 'Cetak Nota Thermal / Printable Receipt View')

@section('content')
<div style="display: flex; justify-content: flex-end; gap: 12px; margin-bottom: 16px;">
    <button onclick="window.print()" class="btn btn-primary"><i class="fa-solid fa-print"></i> Cetak Nota Thermal</button>
    <a href="{{ route('work-orders.show', $invoice->work_order_id) }}" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Kembali ke WO</a>
</div>

<div class="card" style="max-width: 700px; margin: 0 auto; background: #fff; color: #1e293b; font-family: monospace; padding: 32px; border-radius: 8px;" id="printable-receipt">
    <!-- Receipt Header -->
    <div style="text-align: center; border-bottom: 2px dashed #94a3b8; padding-bottom: 16px; margin-bottom: 16px;">
        <h2 style="font-size: 22px; font-weight: 800; color: #0f172a; margin-bottom: 4px;">BENGKEL "JAYA MOTOR"</h2>
        <p style="font-size: 12px; color: #475569;">Jl. Raya Kabupaten No. 45, Bengkel Umum & Sparepart</p>
        <p style="font-size: 12px; color: #475569;">Telp: 0812-3456-7890</p>
    </div>

    <!-- Meta Info -->
    <div style="display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 16px; border-bottom: 1px solid #cbd5e1; padding-bottom: 12px;">
        <div>
            <div><strong>No. Nota:</strong> {{ $invoice->invoice_number }}</div>
            <div><strong>No. WO:</strong> {{ $invoice->workOrder->wo_number }}</div>
            <div><strong>Tanggal:</strong> {{ $invoice->created_at->format('d/m/Y H:i') }}</div>
        </div>
        <div style="text-align: right;">
            <div><strong>Pelanggan:</strong> {{ $invoice->customer->name }}</div>
            <div><strong>Plat Nomor:</strong> {{ $invoice->workOrder->vehicle->plate_number }}</div>
            <div><strong>Motor:</strong> {{ $invoice->workOrder->vehicle->model }}</div>
        </div>
    </div>

    <!-- Items Section Breakdown -->
    <table style="width: 100%; font-size: 13px; margin-bottom: 20px; border-collapse: collapse;">
        <thead>
            <tr style="border-bottom: 1px solid #0f172a; text-align: left;">
                <th style="padding: 6px 0; color: #0f172a;">Deskripsi Item</th>
                <th style="padding: 6px 0; color: #0f172a;">Mekanik</th>
                <th style="padding: 6px 0; color: #0f172a; text-align: right;">Qty</th>
                <th style="padding: 6px 0; color: #0f172a; text-align: right;">Harga</th>
                <th style="padding: 6px 0; color: #0f172a; text-align: right;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->workOrder->items as $item)
            <tr style="border-bottom: 1px dashed #e2e8f0;">
                <td style="padding: 8px 0; color: #0f172a;">
                    <strong>{{ $item->item_name }}</strong>
                    @if($item->item_type == 'service')
                        <br><small style="color: #64748b;">[JASA SERVIS]</small>
                    @elseif($item->item_type == 'direct_purchase')
                        <br><small style="color: #64748b;">[BARANG TOKO SEBELAH]</small>
                    @elseif($item->item_type == 'trade_in')
                        <br><small style="color: #ef4444;">[DISKON AKI BEKAS]</small>
                    @endif
                </td>
                <td style="padding: 8px 0; color: #475569;">
                    {{ $item->mechanic->name ?? '-' }}
                </td>
                <td style="padding: 8px 0; text-align: right; color: #0f172a;">
                    {{ number_format($item->qty, 2) }}
                </td>
                <td style="padding: 8px 0; text-align: right; color: #0f172a;">
                    Rp {{ number_format($item->sell_price, 0, ',', '.') }}
                </td>
                <td style="padding: 8px 0; text-align: right; font-weight: 700; color: {{ $item->subtotal < 0 ? '#ef4444' : '#0f172a' }};">
                    Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Receipt Footer Summary -->
    <div style="border-top: 2px dashed #94a3b8; padding-top: 16px; font-size: 14px;">
        <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
            <span>TOTAL TAGIHAN:</span>
            <strong style="font-size: 16px; color: #0f172a;">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</strong>
        </div>
        <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
            <span>TOTAL DIBAYAR:</span>
            <strong style="color: #16a34a;">Rp {{ number_format($invoice->paid_amount, 0, ',', '.') }}</strong>
        </div>
        <div style="display: flex; justify-content: space-between; border-top: 1px solid #cbd5e1; padding-top: 6px; margin-top: 6px;">
            <span>SISA KURANG BAYAR (BALANCE DUE):</span>
            <strong style="font-size: 16px; color: {{ $invoice->balance_due > 0 ? '#dc2626' : '#16a34a' }};">
                Rp {{ number_format($invoice->balance_due, 0, ',', '.') }}
            </strong>
        </div>
    </div>

    <!-- Status Stamp -->
    <div style="text-align: center; margin-top: 24px;">
        <span style="display: inline-block; padding: 6px 20px; border: 2px solid {{ $invoice->status == 'paid' ? '#16a34a' : ($invoice->status == 'partially_paid' ? '#d97706' : '#dc2626') }}; color: {{ $invoice->status == 'paid' ? '#16a34a' : ($invoice->status == 'partially_paid' ? '#d97706' : '#dc2626') }}; font-weight: 800; font-size: 16px; letter-spacing: 2px; text-transform: uppercase;">
            STATUS: {{ $invoice->status }}
        </span>
    </div>

    <div style="text-align: center; font-size: 11px; color: #64748b; margin-top: 24px;">
        *** Terima kasih atas kunjungan Anda. Garansi servis 14 hari ***
    </div>
</div>

<style>
    @media print {
        body * { visibility: hidden; }
        #printable-receipt, #printable-receipt * { visibility: visible; }
        #printable-receipt { position: absolute; left: 0; top: 0; width: 100%; }
    }
</style>
@endsection
