@extends('layouts.app')

@section('title', 'Laporan Laba & Rugi Bengkel')
@section('subtitle', 'Analisis Laba Bersih, Grafik Performa Mekanik, & Jenis Pekerjaan Terlaris')

@section('content')
<!-- KPI Cards -->
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

<!-- Owner Analytics Charts Grid -->
<div class="grid-2" style="margin-bottom: 24px;">

    <!-- Chart 1: Mekanik Paling Menghasilkan -->
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <h3 style="font-size: 15px;"><i class="fa-solid fa-trophy" style="color: #f59e0b;"></i> Mekanik Paling Menghasilkan</h3>
            <span class="badge badge-working">Top Producer</span>
        </div>
        <div style="position: relative; height: 240px; width: 100%;">
            <canvas id="topMechanicChart"></canvas>
        </div>
    </div>

    <!-- Chart 2: Jenis Pekerjaan Jasa Terlaris -->
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <h3 style="font-size: 15px;"><i class="fa-solid fa-chart-pie" style="color: #8b5cf6;"></i> Pekerjaan Servis Paling Menghasilkan</h3>
            <span class="badge badge-completed">Top Services</span>
        </div>
        <div style="position: relative; height: 240px; width: 100%;">
            <canvas id="topServicesChart"></canvas>
        </div>
    </div>

</div>

<!-- Breakdown Revenue Category & Financial Summary -->
<div class="grid-2" style="margin-bottom: 24px;">

    <!-- Chart 3: Doughnut Chart Kontribusi Omzet -->
    <div class="card">
        <h3 style="font-size: 15px; margin-bottom: 16px;"><i class="fa-solid fa-chart-donut" style="color: #10b981;"></i> Kontribusi Pendapatan per Tipe Pekerjaan</h3>
        <div style="position: relative; height: 220px; width: 100%;">
            <canvas id="revenueCategoryChart"></canvas>
        </div>
    </div>

    <!-- Table Summary -->
    <div class="card">
        <h3 style="font-size: 15px; margin-bottom: 16px;"><i class="fa-solid fa-calculator"></i> Breakdown Laba Kotor & Beban Operasional</h3>
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
                    <td style="font-size: 15px; font-weight: 800;">ESTIMASI LABA KOTOR (GROSS PROFIT)</td>
                    <td style="text-align: right; font-size: 18px; font-weight: 800; color: #10b981;">
                        Rp {{ number_format($grossProfit, 0, ',', '.') }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // 1. Mechanic Chart
        const ctxMech = document.getElementById('topMechanicChart').getContext('2d');
        const mechLabels = {!! json_encode($mechanicStats->pluck('mechanic.name')->toArray()) !!};
        const mechRevenue = {!! json_encode($mechanicStats->pluck('total_revenue')->toArray()) !!};

        new Chart(ctxMech, {
            type: 'bar',
            data: {
                labels: mechLabels,
                datasets: [{
                    label: 'Omzet dihasilkan (Rp)',
                    data: mechRevenue,
                    backgroundColor: 'rgba(139, 92, 246, 0.8)',
                    borderColor: '#8b5cf6',
                    borderWidth: 1,
                    borderRadius: 8,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y', // Horizontal Bar Chart
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Omzet: ' + new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(context.parsed.x);
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        ticks: {
                            color: '#94a3b8',
                            callback: function(v) { return 'Rp ' + (v/1000) + 'k'; }
                        },
                        grid: { color: 'rgba(255,255,255,0.05)' }
                    },
                    y: { ticks: { color: '#94a3b8' }, grid: { display: false } }
                }
            }
        });

        // 2. Top Services Chart
        const ctxServ = document.getElementById('topServicesChart').getContext('2d');
        const servLabels = {!! json_encode($topServices->pluck('item_name')->toArray()) !!};
        const servRevenue = {!! json_encode($topServices->pluck('total_revenue')->toArray()) !!};

        new Chart(ctxServ, {
            type: 'bar',
            data: {
                labels: servLabels,
                datasets: [{
                    label: 'Pendapatan Jasa (Rp)',
                    data: servRevenue,
                    backgroundColor: 'rgba(59, 130, 246, 0.8)',
                    borderColor: '#3b82f6',
                    borderWidth: 1,
                    borderRadius: 8,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Pendapatan: ' + new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(context.parsed.y);
                            }
                        }
                    }
                },
                scales: {
                    x: { ticks: { color: '#94a3b8' }, grid: { display: false } },
                    y: {
                        ticks: {
                            color: '#94a3b8',
                            callback: function(v) { return 'Rp ' + (v/1000) + 'k'; }
                        },
                        grid: { color: 'rgba(255,255,255,0.05)' }
                    }
                }
            }
        });

        // 3. Doughnut Revenue Breakdown Chart
        const ctxCategory = document.getElementById('revenueCategoryChart').getContext('2d');
        new Chart(ctxCategory, {
            type: 'doughnut',
            data: {
                labels: ['Jasa Servis', 'Sparepart Gudang', 'Sparepart Toko Sebelah'],
                datasets: [{
                    data: [{{ $serviceRevenue }}, {{ $inventoryRevenue }}, {{ $directPurchaseRevenue }}],
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.85)',
                        'rgba(16, 185, 129, 0.85)',
                        'rgba(245, 158, 11, 0.85)'
                    ],
                    borderColor: ['#3b82f6', '#10b981', '#f59e0b'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { color: '#94a3b8', font: { family: 'Outfit', size: 12 } }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let val = context.parsed;
                                return context.label + ': ' + new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val);
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
