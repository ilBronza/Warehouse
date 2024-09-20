<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWarehouseFieldsToProductTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(config('clients.models.client.table'), function (Blueprint $table) {
            // $table->uuid('pallettype_id')->nullable();
            // $table->foreign('pallettype_id')->references('id')->on(config('warehouse.models.pallettype.table'));
        });

        Schema::table(config('products.models.order.table'), function (Blueprint $table) {
            // $table->uuid('pallettype_id')->nullable();
            // $table->foreign('pallettype_id')->references('id')->on(config('warehouse.models.pallettype.table'));
        });

        Schema::table(config('products.models.packing.table'), function (Blueprint $table) {
            // $table->uuid('pallettype_id')->nullable();
            // $table->foreign('pallettype_id')->references('id')->on(config('warehouse.models.pallettype.table'));
        });
    }

    /**
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_tables', function (Blueprint $table) {
            //
        });
    }
}
