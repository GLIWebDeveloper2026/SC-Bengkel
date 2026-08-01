<?php

namespace App\Http\Controllers;

use App\Models\ApprovalLog;
use App\Models\Part;
use App\Models\Service;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\WorkOrder;
use App\Services\WorkOrderService;
use Illuminate\Http\Request;

class WorkOrderController extends Controller
{
    protected WorkOrderService $woService;

    public function __construct(WorkOrderService $woService)
    {
        $this->woService = $woService;
    }

    public function index(Request $request)
    {
        $status = $request->query('status');

        $query = WorkOrder::with(['vehicle.customer', 'items.mechanic'])->latest();

        if ($status) {
            $query->where('status', $status);
        }

        $workOrders = $query->paginate(15);

        return view('work_orders.index', compact('workOrders', 'status'));
    }

    public function create()
    {
        $vehicles = Vehicle::with('customer')->get();
        return view('work_orders.create', compact('vehicles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id'        => 'required|exists:vehicles,id',
            'initial_estimate'  => 'required|numeric|min:0',
        ]);

        $wo = WorkOrder::create([
            'wo_number'         => 'WO-' . strtoupper(uniqid()),
            'vehicle_id'        => $validated['vehicle_id'],
            'initial_estimate'  => $validated['initial_estimate'],
            'final_cost'        => 0,
            'status'            => 'queue',
            'is_warranty_claim' => false,
        ]);

        return redirect()->route('work-orders.show', $wo->id)
            ->with('success', 'Work Order ' . $wo->wo_number . ' berhasil dibuat.');
    }

    public function show($id)
    {
        $workOrder = WorkOrder::with(['vehicle.customer', 'items.mechanic', 'approvalLogs.approvedBy', 'invoice'])
            ->findOrFail($id);

        $mechanics = User::where('role', 'mechanic')->get();
        $services = Service::all();
        $parts = Part::all();

        return view('work_orders.show', compact('workOrder', 'mechanics', 'services', 'parts'));
    }

    public function addItem(Request $request, $id)
    {
        $wo = WorkOrder::findOrFail($id);

        $validated = $request->validate([
            'item_type'         => 'required|in:service,inventory,direct_purchase,trade_in',
            'mechanic_id'       => 'nullable|exists:users,id',
            'reference_id'      => 'nullable|integer',
            'item_name'         => 'required|string|max:255',
            'qty'               => 'required|numeric|gt:0',
            'cost_price'        => 'nullable|numeric|min:0',
            'sell_price'        => 'required|numeric',
            'commission_amount' => 'nullable|numeric|min:0',
        ]);

        $this->woService->addLineItem($wo, $validated);

        return redirect()->route('work-orders.show', $wo->id)
            ->with('success', 'Item berhasil ditambahkan ke Work Order.');
    }

    public function requestApproval(Request $request, $id)
    {
        $wo = WorkOrder::findOrFail($id);

        $validated = $request->validate([
            'requested_item_name' => 'required|string|max:255',
            'estimated_cost'      => 'required|numeric|min:0',
        ]);

        $this->woService->requestApproval($wo, $validated['requested_item_name'], $validated['estimated_cost']);

        return redirect()->route('work-orders.show', $wo->id)
            ->with('warning', 'Pekerjaan tambahan dikirim untuk Approval.');
    }

    public function respondApproval(Request $request, $logId)
    {
        $log = ApprovalLog::findOrFail($logId);

        $validated = $request->validate([
            'status' => 'required|in:APPROVED,REJECTED,TIMEOUT_HOLD',
        ]);

        $this->woService->respondApproval($log, $validated['status'], auth()->id() ?? 1);

        return redirect()->route('work-orders.show', $log->work_order_id)
            ->with('info', 'Status Approval diperbarui menjadi ' . $validated['status']);
    }

    public function checkout($id)
    {
        $wo = WorkOrder::findOrFail($id);
        $invoice = $this->woService->generateInvoice($wo);

        return redirect()->route('invoices.show', $invoice->id)
            ->with('success', 'Nota / Invoice berhasil diterbitkan.');
    }
}
