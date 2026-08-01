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

        $commissionData = WorkOrderItem::where('item_type', 'service')
            ->whereNotNull('mechanic_id')
            ->select('mechanic_id', DB::raw('SUM(commission_amount) as total_commission'), DB::raw('COUNT(*) as total_jobs'), DB::raw('SUM(subtotal) as total_revenue'))
            ->groupBy('mechanic_id')
            ->with('mechanic')
            ->get();

        return view('reports.commissions', compact('mechanics', 'commissionData'));
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

        return view('reports.profit_loss', compact(
            'totalRevenue', 'totalPaid', 'totalOutstanding',
            'serviceRevenue', 'inventoryRevenue', 'directPurchaseRevenue',
            'directPurchaseCost', 'tradeInDiscount', 'totalCommissionsPaid', 'grossProfit'
        ));
    }

    public function scrapInventory()
    {
        $scrapItems = ScrapItem::latest()->get();
        $totalQty = $scrapItems->sum('qty');

        return view('reports.scrap', compact('scrapItems', 'totalQty'));
    }
}
