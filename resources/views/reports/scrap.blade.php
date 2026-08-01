@extends('layouts.app')

@section('title', 'Laporan Audit Aki Bekas (Scrap)')
@section('subtitle', 'Pencatatan Penampungan Aki Bekas Hasil Tukar Tambah Pak Sarno')

@section('content')
<div class="card" style="margin-bottom: 24px; border-left: 4px solid #ef4444;">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <div style="font-size: 12px; color: var(--text-muted); text-transform: uppercase;">Total Stok Aki Bekas Terkumpul</div>
            <div style="font-size: 28px; font-weight: 700; color: #f87171;">{{ $totalQty }} Unit</div>
        </div>
        <div style="color: var(--text-muted); font-size: 13px;">
            <i class="fa-solid fa-circle-info"></i> Siap dijual ke pengepul barang bekas
        </div>
    </div>
</div>

<div class="card">
    <h3 style="font-size: 16px; margin-bottom: 16px;"><i class="fa-solid fa-recycle"></i> Log Riwayat Penampungan Aki Bekas</h3>
    <table>
        <thead>
            <tr>
                <th>ID Log</th>
                <th>Nama Barang Scrap</th>
                <th>Kuantitas</th>
                <th>Tgl Terkumpul</th>
                <th>Status Penjualan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($scrapItems as $item)
            <tr>
                <td>#{{ $item->id }}</td>
                <td><strong>{{ $item->item_name }}</strong></td>
                <td><span class="badge" style="background: rgba(239, 68, 68, 0.2); color: #f87171; font-size: 13px;">+{{ $item->qty }} Unit</span></td>
                <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
                <td>
                    @if($item->sold_date)
                        <span class="badge badge-paid">TERJUAL (Rp {{ number_format($item->sale_amount, 0, ',', '.') }})</span>
                    @else
                        <span class="badge badge-unpaid">DALAM PENAMPUNGAN</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="5" style="text-align: center; color: var(--text-muted); padding: 24px;">Belum ada aki bekas terkumpul.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
