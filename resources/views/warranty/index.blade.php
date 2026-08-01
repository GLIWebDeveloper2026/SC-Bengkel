@extends('layouts.app')

@section('title', 'Pencarian Garansi 14 Hari')
@section('subtitle', 'Lookup Histori Transaksi Pelanggan & Penerbitan WO Garansi Servis')

@section('content')
<div class="card" style="margin-bottom: 24px;">
    <h3 style="font-size: 16px; margin-bottom: 16px;"><i class="fa-solid fa-magnifying-glass"></i> Cari Plat Nomor Kendaraan Pelanggan</h3>
    <form action="{{ route('warranty.index') }}" method="GET" style="display: flex; gap: 12px;">
        <input type="text" name="plate" class="form-control" placeholder="Masukkan Plat Nomor (Misal: B 5555 TUT / B 1111 REN)" value="{{ $plate }}" required style="font-weight: 700; text-transform: uppercase;">
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-search"></i> Cek Riwayat 14 Hari</button>
    </form>
</div>

@if($plate)
<div class="card">
    <h3 style="font-size: 16px; margin-bottom: 16px;"><i class="fa-solid fa-history"></i> Hasil Pencarian Riwayat Servis (14 Hari Terakhir) untuk {{ strtoupper($plate) }}</h3>

    <table>
        <thead>
            <tr>
                <th>No. Invoice Referensi</th>
                <th>Tgl Transaksi</th>
                <th>Pelanggan</th>
                <th>Motor</th>
                <th>Total Transaksi</th>
                <th>Item Pekerjaan Sebelum</th>
                <th>Aksi Klaim</th>
            </tr>
        </thead>
        <tbody>
            @forelse($eligibleInvoices as $inv)
            <tr>
                <td><strong>{{ $inv->invoice_number }}</strong></td>
                <td>{{ $inv->created_at->format('d/m/Y H:i') }} <br><small style="color: #34d399;">({{ $inv->created_at->diffForHumans() }})</small></td>
                <td>{{ $inv->workOrder->vehicle->customer->name }}</td>
                <td><strong>{{ $inv->workOrder->vehicle->plate_number }}</strong> ({{ $inv->workOrder->vehicle->model }})</td>
                <td>Rp {{ number_format($inv->total_amount, 0, ',', '.') }}</td>
                <td>
                    <ul style="padding-left: 16px; font-size: 12px; color: var(--text-muted);">
                        @foreach($inv->workOrder->items as $it)
                            <li>{{ $it->item_name }} ({{ $it->item_type }})</li>
                        @endforeach
                    </ul>
                </td>
                <td>
                    <form action="{{ route('warranty.claim') }}" method="POST">
                        @csrf
                        <input type="hidden" name="vehicle_id" value="{{ $inv->workOrder->vehicle_id }}">
                        <input type="hidden" name="parent_invoice_id" value="{{ $inv->id }}">
                        <input type="hidden" name="initial_estimate" value="0">
                        <button type="submit" class="btn btn-warning" style="padding: 6px 12px; font-size: 12px;" onclick="return confirm('Terbitkan WO Klaim Garansi untuk motor ini? Tarif Jasa otomatis di-set Rp0.')">
                            <i class="fa-solid fa-shield-cat"></i> Klaim Garansi Baru
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 24px;">
                    Tidak ada transaksi dalam 14 hari terakhir untuk plat nomor ini.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endif
@endsection
