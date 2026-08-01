<?php

namespace App\Http\Controllers;

use App\Services\WarrantyService;
use Illuminate\Http\Request;

class WarrantyController extends Controller
{
    protected WarrantyService $warrantyService;

    public function __construct(WarrantyService $warrantyService)
    {
        $this->warrantyService = $warrantyService;
    }

    public function index(Request $request)
    {
        $plate = $request->query('plate');
        $eligibleInvoices = collect();

        if ($plate) {
            $eligibleInvoices = $this->warrantyService->findEligibleWarrantyInvoices(trim($plate));
        }

        return view('warranty.index', compact('plate', 'eligibleInvoices'));
    }

    public function claim(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id'        => 'required|exists:vehicles,id',
            'parent_invoice_id' => 'required|exists:invoices,id',
            'initial_estimate'  => 'nullable|numeric|min:0',
        ]);

        $wo = $this->warrantyService->createWarrantyWorkOrder(
            $validated['vehicle_id'],
            $validated['parent_invoice_id'],
            $validated['initial_estimate'] ?? 0
        );

        return redirect()->route('work-orders.show', $wo->id)
            ->with('success', 'Klaim Garansi Servis berhasil diterbitkan untuk Work Order ' . $wo->wo_number . '. Tarif Jasa otomatis di-set Rp0.');
    }
}
