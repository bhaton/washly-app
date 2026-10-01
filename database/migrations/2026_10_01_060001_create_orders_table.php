<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('pickup_driver_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('delivery_driver_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status')->default('PENDING_PAYMENT');
            
            $table->decimal('subtotal', 10, 2)->default(0.00);
            $table->decimal('shipping_fee', 10, 2)->default(0.00);
            $table->decimal('total', 10, 2)->default(0.00);
            
            // Pickup Details
            $table->string('pickup_name');
            $table->string('pickup_phone');
            $table->text('pickup_address');
            $table->date('pickup_date');
            $table->string('pickup_time');
            $table->text('pickup_notes')->nullable();

            // Delivery Details
            $table->string('delivery_name');
            $table->string('delivery_phone');
            $table->text('delivery_address');
            $table->text('delivery_notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
