@extends('layouts.app')

@section('title', 'Restock Bahan & Sparepart Gudang')
@section('subtitle', 'Penambahan Stok Persediaan Gudang (Khusus Kasir & Owner)')

@section('content')
<div class="grid-3">
    <!-- Left Column: Restock Input Form -->
    <div class="card" style="border-top: 4px solid #10b981;">
        <h3 style="font-size: 16px; margin-bottom: 16px;"><i class="fa-solid fa-boxes-packing" style="color: #10b981;"></i> Form Input Restock Barang</h3>
        
        <form action="{{ route('inventory.restock.process') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label for="part_id">Pilih Barang / Sparepart Gudang</label>
                <select name="part_id" id="part_id" class="form-control" required>
                    <option value="">-- Pilih Barang --</option>
                    @foreach($parts as $p)
                        <option value="{{ $p->id }}">
                            {{ $p->name }} (Sisa Stok: {{ number_format($p->stock_qty, 2) }} {{ $p->sell_unit }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="add_qty">Jumlah Kuantitas Tambah (Desimal)</label>
                <input type="number" step="0.01" name="add_qty" id="add_qty" class="form-control" placeholder="Contoh: 30.00 / 10.00" required>
                <small style="color: var(--text-muted);">Masukkan angka desimal kuantitas yang baru masuk ke gudang (misal 30.00 Liter untuk 1 Drum Oli).</small>
            </div>

            <div class="form-group">
                <label for="notes">Catatan Pembelian / Supplier (Opsional)</label>
                <input type="text" name="notes" id="notes" class="form-control" placeholder="Misal: Pembelian Grosir Distributor Resmi">
            </div>

            <button type="submit" class="btn btn-success" style="width: 100%; justify-content: center; margin-top: 10px;">
                <i class="fa-solid fa-plus-circle"></i> Eksekusi Restock Gudang
            </button>
        </form>
    </div>

    <!-- Right 2 Columns: Current Inventory Stock Table -->
    <div style="grid-column: span 2;" class="card">
        <h3 style="font-size: 16px; margin-bottom: 16px;"><i class="fa-solid fa-warehouse" style="color: #60a5fa;"></i> Status Stok Persediaan Gudang Terkini</h3>
        
        <table>
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama Barang / Sparepart</th>
                    <th>Satuan Grosir / Ecer</th>
                    <th>Faktor Konversi</th>
                    <th>Harga Modal / Jual</th>
                    <th>Sisa Stok (Desimal)</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($parts as $part)
                <tr>
                    <td><code>{{ $part->code }}</code></td>
                    <td><strong>{{ $part->name }}</strong></td>
                    <td>{{ $part->purchase_unit }} / {{ $part->sell_unit }}</td>
                    <td>1 {{ $part->purchase_unit }} = {{ number_format($part->conversion_factor, 0) }} {{ $part->sell_unit }}</td>
                    <td>
                        <small style="color: var(--text-muted);">Beli: Rp {{ number_format($part->buy_price, 0, ',', '.') }}</small><br>
                        <strong style="color: #60a5fa;">Jual: Rp {{ number_format($part->sell_price, 0, ',', '.') }}</strong>
                    </td>
                    <td style="font-size: 16px; font-weight: 800; color: {{ $part->stock_qty <= $part->conversion_factor ? '#f87171' : '#34d399' }};">
                        {{ number_format($part->stock_qty, 2) }} {{ $part->sell_unit }}
                    </td>
                    <td>
                        @if($part->stock_qty <= 0)
                            <span class="badge" style="background: rgba(239, 68, 68, 0.2); color: #f87171;">STOK HABIS</span>
                        @elseif($part->stock_qty <= $part->conversion_factor)
                            <span class="badge" style="background: rgba(245, 158, 11, 0.2); color: #fbbf24;">KRITIS / REFILL</span>
                        @else
                            <span class="badge" style="background: rgba(16, 185, 129, 0.2); color: #34d399;">AMAN</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align: center; color: var(--text-muted); padding: 24px;">Belum ada data barang persediaan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
