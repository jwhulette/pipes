<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// @codingStandardsIgnoreLine
class CreatePackageLargeTables extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sales_data', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->string('region');
            $table->string('country');
            $table->string('item_type');
            $table->string('sales_channel');
            $table->string('order_priority', 1);
            $table->date('order_date');
            $table->string('order_id', 15);
            $table->date('ship_date');
            $table->integer('units_sold');
            $table->float('unit_price', 8, 2);
            $table->float('unit_cost', 8, 2);
            $table->float('total_revenue', 8, 2);
            $table->float('total_cost', 8, 2);
            $table->float('total_profit', 8, 2);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('sales_data');
    }
}
