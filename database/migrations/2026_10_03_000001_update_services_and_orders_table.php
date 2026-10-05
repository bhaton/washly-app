<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->string('category')->default('item')->after('name'); // 'kiloan' or 'item'
            $table->string('package_type')->nullable()->after('category'); // 'ekonomis', 'premium'
            $table->decimal('express_price', 10, 2)->nullable()->after('price');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->string('service_type')->default('kiloan')->after('delivery_driver_id'); // 'kiloan' or 'per_item'
            $table->string('package_type')->nullable()->after('service_type'); // 'ekonomis', 'premium'
            $table->string('speed_type')->default('reguler')->after('package_type'); // 'reguler', 'ekspres'
            $table->decimal('estimated_weight', 8, 2)->nullable()->after('speed_type');
            $table->decimal('actual_weight', 8, 2)->nullable()->after('estimated_weight');
            $table->decimal('estimated_price', 10, 2)->nullable()->after('actual_weight');
            $table->string('custom_item_name')->nullable()->after('estimated_price');
            $table->integer('custom_item_qty')->nullable()->after('custom_item_name');
            $table->text('custom_item_notes')->nullable()->after('custom_item_qty');
            $table->timestamp('receipt_printed_at')->nullable()->after('custom_item_notes');
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['category', 'package_type', 'express_price']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'service_type',
                'package_type',
                'speed_type',
                'estimated_weight',
                'actual_weight',
                'estimated_price',
                'custom_item_name',
                'custom_item_qty',
                'custom_item_notes',
                'receipt_printed_at',
            ]);
        });
    }
};
