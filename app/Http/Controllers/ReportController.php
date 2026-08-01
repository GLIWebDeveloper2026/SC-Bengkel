<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\ScrapItem;
use App\Models\User;
use App\Models\WorkOrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function commissions(Request $request)
    {
        $mechanics = User::where('role', 'mechanic')->get();

        $commissionData = WorkOrderItem::whereNotNull('mechanic_id')
            ->select('mechanic_id', DB::raw('SUM(commission_amount) as total_commission'), DB::raw('COUNT(*) as total_jobs'), DB::raw('SUM(subtotal) as total_revenue'))
            ->groupBy('mechanic_id')
            ->with('mechanic')
            ->orderByDesc('total_revenue')
            ->get();

        $commissionLogs = WorkOrderItem::whereNotNull('mechanic_id')
            ->where('commission_amount', '>', 0)
            ->with(['mechanic', 'workOrder.vehicle.customer'])
            ->latest()
            ->get();

        return view('reports.commissions', compact('mechanics', 'commissionData', 'commissionLogs'));
    }

    public function profitLoss()
    {
        $totalRevenue = Invoice::sum('total_amount');
        $totalPaid = Invoice::sum('paid_amount');
        $totalOutstanding = Invoice::sum('balance_due');

        $items = WorkOrderItem::all();

        $serviceRevenue = $items->where('item_type', 'service')->sum('subtotal');
        $inventoryRevenue = $items->where('item_type', 'inventory')->sum('subtotal');
        $directPurchaseRevenue = $items->where('item_type', 'direct_purchase')->sum('subtotal');
        $directPurchaseCost = $items->where('item_type', 'direct_purchase')->sum('cost_price');
        $tradeInDiscount = $items->where('item_type', 'trade_in')->sum('subtotal'); // Negative
        $totalCommissionsPaid = $items->sum('commission_amount');

        $grossProfit = $serviceRevenue + $inventoryRevenue + ($directPurchaseRevenue - $directPurchaseCost) - $totalCommissionsPaid;

        // Top Mechanics Data for Analytics Chart
        $mechanicStats = WorkOrderItem::where('item_type', 'service')
            ->whereNotNull('mechanic_id')
            ->select('mechanic_id', DB::raw('SUM(subtotal) as total_revenue'), DB::raw('SUM(commission_amount) as total_commission'))
            ->groupBy('mechanic_id')
            ->with('mechanic')
            ->orderByDesc('total_revenue')
            ->get();

        // Top Specific Services Data for Analytics Chart
        $topServices = WorkOrderItem::where('item_type', 'service')
            ->select('item_name', DB::raw('SUM(subtotal) as total_revenue'), DB::raw('COUNT(*) as total_count'))
            ->groupBy('item_name')
            ->orderByDesc('total_revenue')
            ->take(5)
            ->get();

        return view('reports.profit_loss', compact(
            'totalRevenue', 'totalPaid', 'totalOutstanding',
            'serviceRevenue', 'inventoryRevenue', 'directPurchaseRevenue',
            'directPurchaseCost', 'tradeInDiscount', 'totalCommissionsPaid', 'grossProfit',
            'mechanicStats', 'topServices'
        ));
    }

    public function scrapInventory()
    {
        $scrapItems = ScrapItem::latest()->get();
        $totalQty = $scrapItems->sum('qty');

        return view('reports.scrap', compact('scrapItems', 'totalQty'));
    }
}
