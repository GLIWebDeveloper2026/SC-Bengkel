@extends('layouts.app')

@section('title', 'Daftar Work Order')
@section('subtitle', 'Kelola Antrean Pengerjaan & Transaksi Kendaraan')

@section('content')
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div style="display: flex; gap: 8px;">
            <a href="{{ route('work-orders.index') }}" class="btn {{ !$status ? 'btn-primary' : 'btn-secondary' }}" style="padding: 6px 12px; font-size: 12px;">Semua</a>
            <a href="{{ route('work-orders.index', ['status' => 'queue']) }}" class="btn {{ $status == 'queue' ? 'btn-primary' : 'btn-secondary' }}" style="padding: 6px 12px; font-size: 12px;">Antrean</a>
            <a href="{{ route('work-orders.index', ['status' => 'waiting_approval']) }}" class="btn {{ $status == 'waiting_approval' ? 'btn-primary' : 'btn-secondary' }}" style="padding: 6px 12px; font-size: 12px;">Need Approval</a>
            <a href="{{ route('work-orders.index', ['status' => 'working']) }}" class="btn {{ $status == 'working' ? 'btn-primary' : 'btn-secondary' }}" style="padding: 6px 12px; font-size: 12px;">Pengerjaan</a>
            <a href="{{ route('work-orders.index', ['status' => 'completed']) }}" class="btn {{ $status == 'completed' ? 'btn-primary' : 'btn-secondary' }}" style="padding: 6px 12px; font-size: 12px;">Selesai</a>
        </div>
        <a href="{{ route('work-orders.create') }}" class="btn btn-success"><i class="fa-solid fa-plus"></i> Work Order Baru</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>No. WO</th>
                <th>Tgl Masuk</th>
                <th>Kendaraan / Plat</th>
                <th>Pemilik</th>
                <th>Estimasi Awal</th>
                <th>Biaya Akhir</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($workOrders as $wo)
            <tr>
                <td>
                    <strong>{{ $wo->wo_number }}</strong>
                    @if($wo->is_warranty_claim)
                        <span class="badge" style="background: rgba(139, 92, 246, 0.3); color: #c084fc;">GARANSI</span>
                    @endif
                </td>
                <td>{{ $wo->created_at->format('d/m/Y H:i') }}</td>
                <td><strong>{{ $wo->vehicle->plate_number }}</strong> <br><small style="color: var(--text-muted);">{{ $wo->vehicle->model }}</small></td>
                <td>
                    {{ $wo->vehicle->customer->name }}
                    @if($wo->vehicle->customer->is_rental_owner)
                        <span class="badge" style="background: rgba(59, 130, 246, 0.2); color: #60a5fa;">RENTAL</span>
                    @endif
                </td>
                <td>Rp {{ number_format($wo->initial_estimate, 0, ',', '.') }}</td>
                <td style="font-weight: 700; color: #34d399;">Rp {{ number_format($wo->final_cost, 0, ',', '.') }}</td>
                <td><span class="badge badge-{{ $wo->status }}">{{ strtoupper(str_replace('_', ' ', $wo->status)) }}</span></td>
                <td>
                    <a href="{{ route('work-orders.show', $wo->id) }}" class="btn btn-primary" style="padding: 4px 10px; font-size: 12px;"><i class="fa-solid fa-folder-open"></i> Buka</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="8" style="text-align: center; color: var(--text-muted); padding: 24px;">Tidak ada Work Order ditemukan.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 16px;">
        {{ $workOrders->links() }}
    </div>
</div>
@endsection
