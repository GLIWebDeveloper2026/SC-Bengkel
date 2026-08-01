<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_orders', function (Blueprint $table) {
            $table->id();
            $table->string('wo_number')->unique();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->decimal('initial_estimate', 12, 2);
            $table->decimal('final_cost', 12, 2)->default(0);
            $table->enum('status', ['queue', 'diagnosing', 'waiting_approval', 'working', 'completed', 'cancelled'])->default('queue');
            $table->boolean('is_warranty_claim')->default(false);
            $table->unsignedBigInteger('parent_invoice_id')->nullable();
            $table->timestamps();
        });

        Schema::create('approval_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_order_id')->constrained()->cascadeOnDelete();
            $table->string('requested_item_name');
            $table->decimal('estimated_cost', 12, 2);
            $table->string('status')->default('PENDING'); // PENDING, APPROVED, REJECTED, TIMEOUT_HOLD
            $table->foreignId('approved_by_user_id')->nullable()->constrained('users');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('work_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('mechanic_id')->nullable()->constrained('users'); // Multi-mekanik per baris
            $table->enum('item_type', ['service', 'inventory', 'direct_purchase', 'trade_in']);
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('item_name');
            $table->decimal('qty', 10, 2); // Mendukung desimal (0.8 L)
            $table->decimal('cost_price', 12, 2)->default(0);
            $table->decimal('sell_price', 12, 2);
            $table->decimal('commission_amount', 12, 2)->default(0);
            $table->decimal('subtotal', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_order_items');
        Schema::dropIfExists('approval_logs');
        Schema::dropIfExists('work_orders');
    }
};
