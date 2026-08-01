@extends('layouts.app')

@section('title', 'Laporan Laba & Rugi Bengkel')
@section('subtitle', 'Analisis Laba Bersih, Beban Toko Sebelah, Komisi, & Kebocoran Kas')

@section('content')
<div class="grid-3" style="margin-bottom: 24px;">
    <div class="card" style="border-top: 4px solid #3b82f6;">
        <div style="font-size: 12px; color: var(--text-muted); text-transform: uppercase;">Total Omzet Terjual</div>
        <div style="font-size: 24px; font-weight: 700; color: #60a5fa; margin-top: 4px;">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
    </div>

    <div class="card" style="border-top: 4px solid #10b981;">
        <div style="font-size: 12px; color: var(--text-muted); text-transform: uppercase;">Total Kas Diterima (Lunas)</div>
        <div style="font-size: 24px; font-weight: 700; color: #34d399; margin-top: 4px;">Rp {{ number_format($totalPaid, 0, ',', '.') }}</div>
    </div>

    <div class="card" style="border-top: 4px solid #ef4444;">
        <div style="font-size: 12px; color: var(--text-muted); text-transform: uppercase;">Total Piutang menggantung</div>
        <div style="font-size: 24px; font-weight: 700; color: #f87171; margin-top: 4px;">Rp {{ number_format($totalOutstanding, 0, ',', '.') }}</div>
    </div>
</div>

<div class="card">
    <h3 style="font-size: 16px; margin-bottom: 16px;"><i class="fa-solid fa-calculator"></i> Breakdown Laba Kotor & Beban Operasional</h3>
    <table>
        <tbody>
            <tr>
                <td><strong>Pendapatan Jasa Servis</strong></td>
                <td style="text-align: right; color: #60a5fa; font-weight: 700;">+ Rp {{ number_format($serviceRevenue, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td><strong>Pendapatan Suku Cadang Gudang</strong></td>
                <td style="text-align: right; color: #34d399; font-weight: 700;">+ Rp {{ number_format($inventoryRevenue, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td><strong>Pendapatan Barang Toko Sebelah (Direct Purchase)</strong></td>
                <td style="text-align: right; color: #fbbf24; font-weight: 700;">+ Rp {{ number_format($directPurchaseRevenue, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td><strong>Beban Modal Barang Toko Sebelah (Cash Expense)</strong></td>
                <td style="text-align: right; color: #f87171; font-weight: 700;">- Rp {{ number_format($directPurchaseCost, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td><strong>Potongan Tukar Tambah Aki Bekas (Trade-in)</strong></td>
                <td style="text-align: right; color: #f87171; font-weight: 700;">- Rp {{ number_format(abs($tradeInDiscount), 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td><strong>Beban Komisi Mekanik Terbayar</strong></td>
                <td style="text-align: right; color: #f87171; font-weight: 700;">- Rp {{ number_format($totalCommissionsPaid, 0, ',', '.') }}</td>
            </tr>
            <tr style="border-top: 2px solid var(--border-color); background: rgba(16, 185, 129, 0.1);">
                <td style="font-size: 16px; font-weight: 800;">ESTIMASI LABA KOTOR BENGKEL (GROSS PROFIT)</td>
                <td style="text-align: right; font-size: 20px; font-weight: 800; color: #10b981;">
                    Rp {{ number_format($grossProfit, 0, ',', '.') }}
                </td>
            </tr>
        </tbody>
    </table>
</div>
@endsection
