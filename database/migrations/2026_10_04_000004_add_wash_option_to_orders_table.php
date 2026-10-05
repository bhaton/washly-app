<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('orders', 'wash_option')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->string('wash_option')->default('cuci_setrika')->after('speed_type');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('orders', 'wash_option')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('wash_option');
            });
        }
    }
};
