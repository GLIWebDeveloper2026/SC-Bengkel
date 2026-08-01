@extends('layouts.app')

@section('title', 'Laporan Komisi Mekanik')
@section('subtitle', 'Rekapitulasi Produksi & Komisi per Mekanik')

@section('content')
<!-- Chart Section -->
<div class="card" style="margin-bottom: 24px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
        <h3 style="font-size: 16px;"><i class="fa-solid fa-chart-column" style="color: #60a5fa;"></i> Grafik Produksi Omzet & Komisi Mekanik</h3>
        <span class="badge badge-working">Produktivitas Mekanik</span>
    </div>
    <div style="position: relative; height: 260px; width: 100%;">
        <canvas id="mechanicCommissionChart"></canvas>
    </div>
</div>

<!-- Table Section -->
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h3 style="font-size: 16px;"><i class="fa-solid fa-users-gear"></i> Ringkasan Tabel Produksi Mekanik</h3>
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
                <td><strong>{{ $cd->mechanic->name ?? 'Mekanik' }}</strong></td>
                <td><span class="badge badge-working">{{ strtoupper($cd->mechanic->role ?? 'MECHANIC') }}</span></td>
                <td><strong>{{ $cd->total_jobs }} Pekerjaan</strong></td>
                <td style="color: #60a5fa; font-weight: 700;">Rp {{ number_format($cd->total_revenue, 0, ',', '.') }}</td>
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

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const ctx = document.getElementById('mechanicCommissionChart').getContext('2d');
        
        const labels = {!! json_encode($commissionData->pluck('mechanic.name')->toArray()) !!};
        const revenueData = {!! json_encode($commissionData->pluck('total_revenue')->toArray()) !!};
        const commissionData = {!! json_encode($commissionData->pluck('total_commission')->toArray()) !!};

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Total Produksi Jasa (Omzet)',
                        data: revenueData,
                        backgroundColor: 'rgba(59, 130, 246, 0.75)',
                        borderColor: '#3b82f6',
                        borderWidth: 1,
                        borderRadius: 8,
                    },
                    {
                        label: 'Total Hak Komisi (Rp)',
                        data: commissionData,
                        backgroundColor: 'rgba(16, 185, 129, 0.75)',
                        borderColor: '#10b981',
                        borderWidth: 1,
                        borderRadius: 8,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: { color: '#94a3b8', font: { family: 'Outfit' } }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) label += ': ';
                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        ticks: { color: '#94a3b8' },
                        grid: { color: 'rgba(255, 255, 255, 0.05)' }
                    },
                    y: {
                        ticks: {
                            color: '#94a3b8',
                            callback: function(value) {
                                return 'Rp ' + (value / 1000).toLocaleString('id-ID') + 'k';
                            }
                        },
                        grid: { color: 'rgba(255, 255, 255, 0.05)' }
                    }
                }
            }
        });
    });
</script>
@endsection
