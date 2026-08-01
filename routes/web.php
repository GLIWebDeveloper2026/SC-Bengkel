<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\WarrantyController;
use App\Http\Controllers\WorkOrderController;
use Illuminate\Support\Facades\Route;

// Guest Auth Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Authenticated Routes
Route::middleware('auth')->group(function () {

    // All roles can access dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Work Orders - Common Operational Routes (Owner, Cashier, Mechanic)
    Route::middleware('role:owner,cashier,mechanic')->group(function () {
        Route::get('/work-orders', [WorkOrderController::class, 'index'])->name('work-orders.index');
        Route::get('/work-orders/create', [WorkOrderController::class, 'create'])->name('work-orders.create');
        Route::post('/work-orders', [WorkOrderController::class, 'store'])->name('work-orders.store');
        Route::get('/work-orders/{id}', [WorkOrderController::class, 'show'])->name('work-orders.show');
        Route::post('/work-orders/{id}/items', [WorkOrderController::class, 'addItem'])->name('work-orders.items.store');
        Route::post('/work-orders/{id}/request-approval', [WorkOrderController::class, 'requestApproval'])->name('work-orders.approval.request');
    });

    // Work Order Approval Response - Owner Only
    Route::post('/approvals/{logId}/respond', [WorkOrderController::class, 'respondApproval'])
        ->middleware('role:owner')
        ->name('approvals.respond');

    // Work Order Checkout - Owner & Cashier
    Route::post('/work-orders/{id}/checkout', [WorkOrderController::class, 'checkout'])
        ->middleware('role:owner,cashier')
        ->name('work-orders.checkout');

    // Invoices & Bulk Payments - Owner & Cashier
    Route::middleware('role:owner,cashier')->group(function () {
        Route::get('/invoices/{id}', [PaymentController::class, 'showInvoice'])->name('invoices.show');
        Route::get('/payments/bulk', [PaymentController::class, 'bulkForm'])->name('payments.bulk');
        Route::post('/payments/bulk', [PaymentController::class, 'processBulk'])->name('payments.bulk.process');
    });

    // Warranty Claims & Inventory Restock - Owner & Cashier
    Route::middleware('role:owner,cashier')->group(function () {
        Route::get('/warranty', [WarrantyController::class, 'index'])->name('warranty.index');
        Route::post('/warranty/claim', [WarrantyController::class, 'claim'])->name('warranty.claim');
        Route::get('/inventory/restock', [InventoryController::class, 'restockForm'])->name('inventory.restock');
        Route::post('/inventory/restock', [InventoryController::class, 'processRestock'])->name('inventory.restock.process');
    });

    // Owner Reports - Owner Only
    Route::middleware('role:owner')->prefix('reports')->name('reports.')->group(function () {
        Route::get('/commissions', [ReportController::class, 'commissions'])->name('commissions');
        Route::get('/profit-loss', [ReportController::class, 'profitLoss'])->name('profit-loss');
        Route::get('/scrap', [ReportController::class, 'scrapInventory'])->name('scrap');
    });

});
