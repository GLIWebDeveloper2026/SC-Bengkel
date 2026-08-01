<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('price', 12, 2);
            $table->decimal('default_commission_amount', 12, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('parts', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('purchase_unit'); // e.g. Drum
            $table->string('sell_unit');     // e.g. Liter
            $table->decimal('conversion_factor', 10, 2)->default(1.00);
            $table->decimal('stock_qty', 10, 2)->default(0.00); // Mendukung 0.8 L
            $table->decimal('buy_price', 12, 2);
            $table->decimal('sell_price', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parts');
        Schema::dropIfExists('services');
    }
};
