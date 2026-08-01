<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\WarrantyController;
use App\Http\Controllers\WorkOrderController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Work Orders
Route::get('/work-orders', [WorkOrderController::class, 'index'])->name('work-orders.index');
Route::get('/work-orders/create', [WorkOrderController::class, 'create'])->name('work-orders.create');
Route::post('/work-orders', [WorkOrderController::class, 'store'])->name('work-orders.store');
Route::get('/work-orders/{id}', [WorkOrderController::class, 'show'])->name('work-orders.show');
Route::post('/work-orders/{id}/items', [WorkOrderController::class, 'addItem'])->name('work-orders.items.store');
Route::post('/work-orders/{id}/request-approval', [WorkOrderController::class, 'requestApproval'])->name('work-orders.approval.request');
Route::post('/approvals/{logId}/respond', [WorkOrderController::class, 'respondApproval'])->name('approvals.respond');
Route::post('/work-orders/{id}/checkout', [WorkOrderController::class, 'checkout'])->name('work-orders.checkout');

// Invoices & Payments
Route::get('/invoices/{id}', [PaymentController::class, 'showInvoice'])->name('invoices.show');
Route::get('/payments/bulk', [PaymentController::class, 'bulkForm'])->name('payments.bulk');
Route::post('/payments/bulk', [PaymentController::class, 'processBulk'])->name('payments.bulk.process');

// Warranty Claims
Route::get('/warranty', [WarrantyController::class, 'index'])->name('warranty.index');
Route::post('/warranty/claim', [WarrantyController::class, 'claim'])->name('warranty.claim');

// Reports
Route::get('/reports/commissions', [ReportController::class, 'commissions'])->name('reports.commissions');
Route::get('/reports/profit-loss', [ReportController::class, 'profitLoss'])->name('reports.profit-loss');
Route::get('/reports/scrap', [ReportController::class, 'scrapInventory'])->name('reports.scrap');
