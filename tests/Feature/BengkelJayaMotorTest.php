<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Part;
use App\Models\ScrapItem;
use App\Models\Service;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\WorkOrder;
use App\Services\PaymentService;
use App\Services\WarrantyService;
use App\Services\WorkOrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BengkelJayaMotorTest extends TestCase
{
    use RefreshDatabase;

    protected WorkOrderService $woService;
    protected PaymentService $paymentService;
    protected WarrantyService $warrantyService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->woService = app(WorkOrderService::class);
        $this->paymentService = app(PaymentService::class);
        $this->warrantyService = app(WarrantyService::class);
    }

    /** @test */
    public function test_case_1_multi_mechanic_in_one_work_order()
    {
        $junior = User::create(['name' => 'Junior Mechanic', 'role' => 'mechanic']);
        $sarno  = User::create(['name' => 'Pak Sarno', 'role' => 'mechanic']);

        $customer = Customer::create(['name' => 'Pelanggan A']);
        $vehicle  = Vehicle::create(['customer_id' => $customer->id, 'plate_number' => 'B 1001 TEST', 'model' => 'Honda Supra X']);

        $wo = WorkOrder::create([
            'wo_number'        => 'WO-TEST-01',
            'vehicle_id'       => $vehicle->id,
            'initial_estimate' => 170000,
            'final_cost'       => 0,
            'status'           => 'working',
        ]);

        // Item 1: Jasa Ganti Oli (Junior)
        $this->woService->addLineItem($wo, [
            'item_type'         => 'service',
            'mechanic_id'       => $junior->id,
            'item_name'         => 'Jasa Ganti Oli',
            'qty'               => 1,
            'sell_price'        => 20000,
            'commission_amount' => 5000,
        ]);

        // Item 2: Jasa Kelistrikan (Pak Sarno)
        $this->woService->addLineItem($wo, [
            'item_type'         => 'service',
            'mechanic_id'       => $sarno->id,
            'item_name'         => 'Jasa Kelistrikan',
            'qty'               => 1,
            'sell_price'        => 150000,
            'commission_amount' => 35000,
        ]);

        $this->assertDatabaseHas('work_order_items', [
            'work_order_id' => $wo->id,
            'mechanic_id'   => $junior->id,
            'item_name'     => 'Jasa Ganti Oli',
            'commission_amount' => 5000,
        ]);

        $this->assertDatabaseHas('work_order_items', [
            'work_order_id' => $wo->id,
            'mechanic_id'   => $sarno->id,
            'item_name'     => 'Jasa Kelistrikan',
            'commission_amount' => 35000,
        ]);

        $wo->refresh();
        $this->assertEquals(170000, $wo->final_cost);
    }

    /** @test */
    public function test_case_2_decimal_stock_and_direct_purchase_store_next_door()
    {
        $part = Part::create([
            'code'              => 'OIL-DRUM',
            'name'              => 'Oli Engine Drum',
            'purchase_unit'     => 'Drum',
            'sell_unit'         => 'Liter',
            'conversion_factor' => 30,
            'stock_qty'         => 30.00,
            'buy_price'         => 900000,
            'sell_price'        => 45000,
        ]);

        $customer = Customer::create(['name' => 'Pelanggan B']);
        $vehicle  = Vehicle::create(['customer_id' => $customer->id, 'plate_number' => 'B 2002 TEST', 'model' => 'Yamaha Mio']);

        $wo = WorkOrder::create([
            'wo_number'        => 'WO-TEST-02',
            'vehicle_id'       => $vehicle->id,
            'initial_estimate' => 80000,
            'final_cost'       => 0,
        ]);

        // Sell 0.8 Liter Oil
        $this->woService->addLineItem($wo, [
            'item_type'    => 'inventory',
            'reference_id' => $part->id,
            'item_name'    => 'Oli Engine Drum',
            'qty'          => 0.8,
            'sell_price'   => 45000,
        ]);

        $part->refresh();
        $this->assertEquals(29.20, $part->stock_qty); // Stock decreased by 0.8

        // Direct purchase (Toko sebelah) Kampas Rem
        $this->woService->addLineItem($wo, [
            'item_type'  => 'direct_purchase',
            'item_name'  => 'Kampas Rem Beli Toko Sebelah',
            'qty'        => 1,
            'cost_price' => 30000,
            'sell_price' => 45000,
        ]);

        $this->assertDatabaseHas('work_order_items', [
            'work_order_id' => $wo->id,
            'item_type'     => 'direct_purchase',
            'cost_price'    => 30000,
            'sell_price'    => 45000,
        ]);
    }

    /** @test */
    public function test_case_3_trade_in_scrap_battery()
    {
        $customer = Customer::create(['name' => 'Pelanggan C']);
        $vehicle  = Vehicle::create(['customer_id' => $customer->id, 'plate_number' => 'B 3003 TEST', 'model' => 'Honda Vario']);

        $wo = WorkOrder::create([
            'wo_number'        => 'WO-TEST-03',
            'vehicle_id'       => $vehicle->id,
            'initial_estimate' => 200000,
            'final_cost'       => 0,
        ]);

        $this->woService->addLineItem($wo, [
            'item_type'  => 'trade_in',
            'item_name'  => 'Aki Bekas Motor',
            'qty'        => 1,
            'sell_price' => -20000,
        ]);

        $wo->refresh();
        $this->assertEquals(-20000, $wo->final_cost);

        $this->assertDatabaseHas('scrap_items', [
            'item_name' => 'Aki Bekas Motor',
            'qty'       => 1,
        ]);
    }

    /** @test */
    public function test_case_4_bulk_payment_4_invoices()
    {
        $rentalOwner = Customer::create(['name' => 'Pemilik Rental', 'is_rental_owner' => true]);

        $invoices = [];
        for ($i = 1; $i <= 4; $i++) {
            $vehicle = Vehicle::create(['customer_id' => $rentalOwner->id, 'plate_number' => "B 400{$i} REN", 'model' => 'Motor Rental']);
            $wo = WorkOrder::create(['wo_number' => "WO-REN-{$i}", 'vehicle_id' => $vehicle->id, 'initial_estimate' => 250000, 'final_cost' => 250000]);
            $invoices[] = Invoice::create([
                'invoice_number' => "INV-REN-{$i}",
                'work_order_id'  => $wo->id,
                'customer_id'    => $rentalOwner->id,
                'total_amount'   => 250000,
                'paid_amount'    => 0,
                'balance_due'    => 250000,
                'status'         => 'unpaid',
            ]);
        }

        $invoiceIds = array_map(fn($inv) => $inv->id, $invoices);

        // Process Bulk Payment of Rp 700.000
        $this->paymentService->processBulkPayment($rentalOwner->id, 700000, $invoiceIds);

        // Invoices 1, 2, 3 should be fully paid (250k x 3 = 750k -> actually 700k covers 1 & 2 fully (500k), 200k on 3)
        $invoices[0]->refresh();
        $invoices[1]->refresh();
        $invoices[2]->refresh();
        $invoices[3]->refresh();

        $this->assertEquals('paid', $invoices[0]->status);
        $this->assertEquals('paid', $invoices[1]->status);
        $this->assertEquals('partially_paid', $invoices[2]->status);
        $this->assertEquals(50000, $invoices[2]->balance_due);
        $this->assertEquals('unpaid', $invoices[3]->status);
    }

    /** @test */
    public function test_case_5_warranty_claim_within_14_days_bu_tuti()
    {
        $buTuti  = Customer::create(['name' => 'Bu Tuti']);
        $vehicle = Vehicle::create(['customer_id' => $buTuti->id, 'plate_number' => 'B 5555 TUT', 'model' => 'Honda Scoopy']);

        $oldWo = WorkOrder::create(['wo_number' => 'WO-OLD-TUTI', 'vehicle_id' => $vehicle->id, 'initial_estimate' => 150000, 'final_cost' => 150000]);
        $oldInvoice = Invoice::create([
            'invoice_number' => 'INV-OLD-TUTI',
            'work_order_id'  => $oldWo->id,
            'customer_id'    => $buTuti->id,
            'total_amount'   => 150000,
            'paid_amount'    => 150000,
            'balance_due'    => 0,
            'status'         => 'paid',
        ]);

        // Create Warranty WO
        $warrantyWo = $this->warrantyService->createWarrantyWorkOrder($vehicle->id, $oldInvoice->id);

        $this->assertTrue($warrantyWo->is_warranty_claim);
        $this->assertEquals($oldInvoice->id, $warrantyWo->parent_invoice_id);

        // Add Service Item -> Should be overridden to Rp0
        $this->woService->addLineItem($warrantyWo, [
            'item_type'  => 'service',
            'item_name'  => 'Jasa Servis Ulang Kelistrikan (Garansi)',
            'qty'        => 1,
            'sell_price' => 150000, // Will be overridden to 0
        ]);

        $warrantyWo->refresh();
        $this->assertEquals(0, $warrantyWo->items->first()->sell_price);
        $this->assertEquals(0, $warrantyWo->final_cost);
    }

    /** @test */
    public function test_bulk_payment_prevents_overpayment_via_controller()
    {
        $cashier = User::create(['name' => 'Mbak Rina', 'role' => 'cashier']);
        $customer = Customer::create(['name' => 'Pak Overpay']);
        $vehicle = Vehicle::create(['customer_id' => $customer->id, 'plate_number' => 'B 9999 OVER', 'model' => 'Motor Test']);
        $wo = WorkOrder::create(['wo_number' => 'WO-OVER-01', 'vehicle_id' => $vehicle->id, 'initial_estimate' => 100000, 'final_cost' => 100000]);
        $invoice = Invoice::create([
            'invoice_number' => 'INV-OVER-01',
            'work_order_id'  => $wo->id,
            'customer_id'    => $customer->id,
            'total_amount'   => 100000,
            'paid_amount'    => 0,
            'balance_due'    => 100000,
            'status'         => 'unpaid',
        ]);

        // Attempt to pay 200,000 for a 100,000 due -> Should be rejected by controller
        $response = $this->actingAs($cashier)->post(route('payments.bulk.process'), [
            'customer_id'    => $customer->id,
            'total_paid'     => 200000,
            'invoice_ids'    => [$invoice->id],
            'payment_method' => 'CASH',
        ]);

        $response->assertSessionHas('error');
        $invoice->refresh();
        $this->assertEquals(100000, $invoice->balance_due);
    }

    /** @test */
    public function test_inventory_item_rejects_exceeding_stock_capacity()
    {
        $part = Part::create([
            'code'              => 'OIL-TEST-CAP',
            'name'              => 'Oli Test Capacity',
            'purchase_unit'     => 'Drum',
            'sell_unit'         => 'Liter',
            'conversion_factor' => 30,
            'stock_qty'         => 5.00,
            'buy_price'         => 100000,
            'sell_price'        => 50000,
        ]);

        $customer = Customer::create(['name' => 'Pelanggan Stock Test']);
        $vehicle  = Vehicle::create(['customer_id' => $customer->id, 'plate_number' => 'B 7777 STK', 'model' => 'Motor Test']);
        $wo       = WorkOrder::create(['wo_number' => 'WO-STK-01', 'vehicle_id' => $vehicle->id, 'initial_estimate' => 50000, 'final_cost' => 0]);

        $this->expectException(\InvalidArgumentException::class);

        // Attempting to buy 10.00 Liters when stock is only 5.00 Liters -> Expect InvalidArgumentException
        $this->woService->addLineItem($wo, [
            'item_type'    => 'inventory',
            'reference_id' => $part->id,
            'item_name'    => $part->name,
            'qty'          => 10.00,
            'sell_price'   => 50000,
        ]);
    }

    /** @test */
    public function test_inventory_restock_allowed_for_owner_cashier_blocked_for_mechanic()
    {
        $part = Part::create([
            'code'              => 'OIL-RESTOCK-TEST',
            'name'              => 'Oli Restock Test',
            'purchase_unit'     => 'Drum',
            'sell_unit'         => 'Liter',
            'conversion_factor' => 30,
            'stock_qty'         => 10.00,
            'buy_price'         => 100000,
            'sell_price'        => 50000,
        ]);

        $owner = User::create(['name' => 'Pak Hendra Test', 'role' => 'owner']);
        $cashier = User::create(['name' => 'Mbak Rina Test', 'role' => 'cashier']);
        $mechanic = User::create(['name' => 'Pak Sarno Test', 'role' => 'mechanic']);

        // 1. Mechanic attempt -> Blocked (redirected to dashboard)
        $mechanicResp = $this->actingAs($mechanic)->get(route('inventory.restock'));
        $mechanicResp->assertRedirect(route('dashboard'));

        // 2. Cashier attempt -> Allowed
        $cashierResp = $this->actingAs($cashier)->get(route('inventory.restock'));
        $cashierResp->assertStatus(200);

        // 3. Owner process restock -> Stock incremented by 30.00 Liters
        $this->actingAs($owner)->post(route('inventory.restock.process'), [
            'part_id' => $part->id,
            'add_qty' => 30.00,
            'notes'   => 'Restock 1 Drum Oli',
        ])->assertRedirect(route('inventory.restock'));

        $part->refresh();
        $this->assertEquals(40.00, $part->stock_qty);
    }
}
