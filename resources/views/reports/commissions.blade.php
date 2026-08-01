@extends('layouts.app')

@section('title', 'Laporan Komisi Mekanik')
@section('subtitle', 'Rekapitulasi Produksi & Komisi per Mekanik (Pak Sarno & Junior)')

@section('content')
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h3 style="font-size: 16px;"><i class="fa-solid fa-users-gear"></i> Ringkasan Komisi & Produksi Mekanik</h3>
    </div>

    <table>
        <thead>
            <tr>
                <th>Mekanik</th>
                <th>Spesialisasi / Role</th>
                <th>Total Pekerjaan Dikerjakan</th>
                <th>Total Produksi Jasa (Omzet)</th>
                <th>Total Hak Komisi (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($commissionData as $cd)
            <tr>
                <td><strong>{{ $cd->mechanic->name }}</strong></td>
                <td><span class="badge badge-working">{{ $cd->mechanic->role }}</span></td>
                <td><strong>{{ $cd->total_jobs }} Pekerjaan</strong></td>
                <td>Rp {{ number_format($cd->total_revenue, 0, ',', '.') }}</td>
                <td style="font-weight: 800; color: #34d399; font-size: 16px;">
                    Rp {{ number_format($cd->total_commission, 0, ',', '.') }}
                </td>
            </tr>
            @empty
            <tr><td colspan="5" style="text-align: center; color: var(--text-muted); padding: 24px;">Belum ada komisi tercatat.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
