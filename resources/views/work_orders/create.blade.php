@extends('layouts.app')

@section('title', 'Buat Work Order Baru')
@section('subtitle', 'Check-in Kendaraan Masuk & Input Estimasi Biaya Awal')

@section('content')
<div class="card" style="max-width: 650px; margin: 0 auto;">
    <form action="{{ route('work-orders.store') }}" method="POST">
        @csrf
        
        <div class="form-group">
            <label for="vehicle_id">Pilih Kendaraan / Plat Nomor Pelanggan</label>
            <select name="vehicle_id" id="vehicle_id" class="form-control" required>
                <option value="">-- Pilih Kendaraan --</option>
                @foreach($vehicles as $v)
                    <option value="{{ $v->id }}">
                        {{ $v->plate_number }} — {{ $v->model }} ({{ $v->customer->name }} {{ $v->customer->is_rental_owner ? '[RENTAL OWNER]' : '' }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="initial_estimate">Estimasi Biaya Awal (Rp)</label>
            <input type="number" name="initial_estimate" id="initial_estimate" class="form-control" placeholder="Misal: 150000" required>
            <small style="color: var(--text-muted);">Estimasi ini akan disimpan berdampingan dengan Biaya Akhir Realisasi untuk transparansi.</small>
        </div>

        <div style="display: flex; gap: 12px; margin-top: 24px;">
            <button type="submit" class="btn btn-success"><i class="fa-solid fa-save"></i> Simpan & Lanjut Input Item</button>
            <a href="{{ route('work-orders.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection
