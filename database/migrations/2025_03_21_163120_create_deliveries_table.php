<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('warehouse.models.delivery.table'), function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('name');
            $table->string('slug', 32);

            $table->datetime('datetime')->nullable();
            $table->datetime('loaded_at')->nullable();
            $table->datetime('shipped_at')->nullable();

            $table->uuid('vehicle_id')->nullable();
            $table->foreign('vehicle_id')->references('id')->on(config('vehicles.models.vehicle.table'));

            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create(config('warehouse.models.contentDelivery.table'), function (Blueprint $table) {
            $table->uuid('id')->primary();

            // $table->uuid('order_id')->nullable();
            // $table->foreign('order_id')->references('id')->on(config('products.models.order.table'));

            $table->uuid('delivery_id');
            $table->foreign('delivery_id')->references('id')->on(config('warehouse.models.delivery.table'));

            $table->nullableUuidMorphs('content');

            $table->decimal('quantity_required', 8, 2)->nullable();
	        $table->boolean('partial')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
	    Schema::dropIfExists(config('warehouse.models.contentDelivery.table'));
	    Schema::dropIfExists(config('warehouse.models.delivery.table'));
    }
};
