@extends('layouts.app')

@section('title', 'Dashboard Operasional')
@section('subtitle', 'Ringkasan Antrean Servis, Omzet, & Alert Stok')

@section('content')
<div class="grid-4">
    <div class="card" style="border-left: 4px solid #3b82f6;">
        <div style="font-size: 12px; color: var(--text-muted); text-transform: uppercase;">WO Aktif</div>
        <div style="font-size: 28px; font-weight: 700; margin-top: 4px;">{{ $activeWorkOrdersCount }}</div>
        <div style="font-size: 12px; color: #60a5fa; margin-top: 4px;"><i class="fa-solid fa-list-check"></i> Dalam antrean & pengerjaan</div>
    </div>

    <div class="card" style="border-left: 4px solid #10b981;">
        <div style="font-size: 12px; color: var(--text-muted); text-transform: uppercase;">Omzet Hari Ini</div>
        <div style="font-size: 28px; font-weight: 700; margin-top: 4px;">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</div>
        <div style="font-size: 12px; color: #34d399; margin-top: 4px;"><i class="fa-solid fa-cash-register"></i> Transaksi kasir</div>
    </div>

    <div class="card" style="border-left: 4px solid #f59e0b;">
        <div style="font-size: 12px; color: var(--text-muted); text-transform: uppercase;">Piutang menggantung</div>
        <div style="font-size: 28px; font-weight: 700; margin-top: 4px;">Rp {{ number_format($totalOutstandingBalance, 0, ',', '.') }}</div>
        <div style="font-size: 12px; color: #fbbf24; margin-top: 4px;"><i class="fa-solid fa-clock-rotate-left"></i> {{ $unpaidInvoicesCount }} Invoice belum lunas</div>
    </div>

    <div class="card" style="border-left: 4px solid #8b5cf6;">
        <div style="font-size: 12px; color: var(--text-muted); text-transform: uppercase;">Top Mechanic Generator</div>
        <div style="font-size: 20px; font-weight: 700; margin-top: 4px;">{{ $topMechanic->mechanic->name ?? 'Belum ada data' }}</div>
        <div style="font-size: 12px; color: #a78bfa; margin-top: 4px;">
            @if($topMechanic)
                Produksi: Rp {{ number_format($topMechanic->total_revenue, 0, ',', '.') }}
            @else
                Belum ada transaksi
            @endif
        </div>
    </div>
</div>

<div class="grid-2">
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <h3 style="font-size: 16px;"><i class="fa-solid fa-motorcycle"></i> Work Order Terbaru</h3>
            <a href="{{ route('work-orders.create') }}" class="btn btn-primary" style="padding: 6px 12px; font-size: 12px;"><i class="fa-solid fa-plus"></i> WO Baru</a>
        </div>
        <table>
            <thead>
                <tr>
                    <th>No. WO</th>
                    <th>Kendaraan / Plat</th>
                    <th>Pelanggan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentWorkOrders as $wo)
                <tr>
                    <td><strong>{{ $wo->wo_number }}</strong></td>
                    <td>{{ $wo->vehicle->model }} ({{ $wo->vehicle->plate_number }})</td>
                    <td>{{ $wo->vehicle->customer->name }}</td>
                    <td><span class="badge badge-{{ $wo->status }}">{{ strtoupper($wo->status) }}</span></td>
                    <td><a href="{{ route('work-orders.show', $wo->id) }}" class="btn btn-secondary" style="padding: 4px 8px; font-size: 12px;">Detail</a></td>
                </tr>
                @empty
                <tr><td colspan="5" style="text-align: center; color: var(--text-muted);">Belum ada Work Order.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="card">
        <h3 style="font-size: 16px; margin-bottom: 16px;"><i class="fa-solid fa-boxes-stacked" style="color: #60a5fa;"></i> Monitoring Stok Persediaan Gudang (Real-time)</h3>
        <table>
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama Barang</th>
                    <th>Satuan Grosir / Ecer</th>
                    <th>Sisa Stok (Desimal)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($parts as $part)
                <tr>
                    <td><code>{{ $part->code }}</code></td>
                    <td><strong>{{ $part->name }}</strong></td>
                    <td>{{ $part->purchase_unit }} / {{ $part->sell_unit }}</td>
                    <td style="color: {{ $part->stock_qty <= $part->conversion_factor ? '#f87171' : '#34d399' }}; font-weight: 700;">
                        {{ number_format($part->stock_qty, 2) }} {{ $part->sell_unit }}
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" style="text-align: center; color: var(--text-muted);">Belum ada barang persediaan di gudang.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
