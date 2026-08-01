<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Part;
use App\Models\WorkOrder;
use App\Models\WorkOrderItem;

class DashboardController extends Controller
{
    public function index()
    {
        $activeWorkOrdersCount = WorkOrder::whereIn('status', ['queue', 'diagnosing', 'waiting_approval', 'working'])->count();
        $todayRevenue = Invoice::whereDate('created_at', now()->today())->sum('total_amount');
        $unpaidInvoicesCount = Invoice::where('status', '!=', 'paid')->count();
        $totalOutstandingBalance = Invoice::where('status', '!=', 'paid')->sum('balance_due');

        $recentWorkOrders = WorkOrder::with(['vehicle.customer'])->latest()->take(5)->get();
        $lowStockParts = Part::whereColumn('stock_qty', '<=', 'conversion_factor')->get();
        $parts = Part::all();

        // Top mechanic generator
        $topMechanic = WorkOrderItem::whereNotNull('mechanic_id')
            ->select('mechanic_id', \DB::raw('SUM(subtotal) as total_revenue'))
            ->groupBy('mechanic_id')
            ->orderByDesc('total_revenue')
            ->with('mechanic')
            ->first();

        return view('dashboard', compact(
            'activeWorkOrdersCount',
            'todayRevenue',
            'unpaidInvoicesCount',
            'totalOutstandingBalance',
            'recentWorkOrders',
            'lowStockParts',
            'parts',
            'topMechanic'
        ));
    }
}
