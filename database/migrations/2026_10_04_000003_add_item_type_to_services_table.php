<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('services', 'item_type')) {
            Schema::table('services', function (Blueprint $table) {
                $table->string('item_type')->default('both')->after('category');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('services', 'item_type')) {
            Schema::table('services', function (Blueprint $table) {
                $table->dropColumn('item_type');
            });
        }
    }
};
