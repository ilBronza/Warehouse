<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUnitloadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('warehouse.models.pallettype.table'), function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('name');
            $table->string('slug', 32);

            $table->unsignedBigInteger('width_mm')->nullable();
            $table->unsignedBigInteger('height_mm')->nullable();
            $table->unsignedBigInteger('length_mm')->nullable();
            $table->unsignedBigInteger('weigth_kg')->nullable();

            $table->unsignedBigInteger('loadable_width_mm')->nullable();
            $table->unsignedBigInteger('loadable_length_mm')->nullable();
            $table->unsignedBigInteger('loadable_height_mm')->nullable();
            $table->unsignedBigInteger('loadable_weigth_kg')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create(config('warehouse.models.pallet.table'), function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('pallettype_slug', 32)
                ->references('slug')->on(config('warehouse.models.pallettype.table'))
                ->nullable();

            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create(config('warehouse.models.unitload.table'), function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->nullableUuidMorphs('loadable');
            $table->nullableUuidMorphs('production');

            $table->unsignedBigInteger('quantity_capacity')->nullable();
            $table->unsignedBigInteger('quantity_expected')->nullable();
            $table->unsignedBigInteger('quantity')->nullable();

            $table->unsignedSmallInteger('sequence')->nullable();

            $table->unsignedBigInteger('width_mm')->nullable();
            $table->unsignedBigInteger('length_mm')->nullable();
            $table->unsignedBigInteger('height_mm')->nullable();

            $table->unsignedBigInteger('weigth_kg')->nullable();

            $table->uuid('processing_id')
                ->references('id')
                ->on('processings')
                ->nullable();

            $table->uuid('destination_id')
                ->references('id')
                ->on(config('clients.models.destination.table'))
                ->nullable();

            $table->uuid('user_id')
                ->references('id')
                ->on('users')
                ->nullable();

            $table->uuid('product_id')
                ->references('id')
                ->on(config('products.models.product.table'))
                ->nullable();

            $table->uuid('order_product_id')
                ->references('id')
                ->on(config('products.models.orderProduct.table'))
                ->nullable();

            $table->uuid('order_product_phase_id')
                ->references('id')
                ->on(config('products.models.orderProductPhase.table'))
                ->nullable();

            $table->uuid('parent_id')
                ->references('id')->on(config('warehouse.models.unitload.table'))
                ->nullable();

            $table->string('pallettype_slug', 32)
                ->references('slug')->on(config('warehouse.models.pallettype.table'))
                ->nullable();

            $table->uuid('pallettype_id')
                ->references('id')->on(config('warehouse.models.pallettype.table'))
                ->nullable();

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
        Schema::dropIfExists(config('warehouse.models.unitload.table'));
        Schema::dropIfExists(config('warehouse.models.pallet.table'));
        Schema::dropIfExists(config('warehouse.models.pallettype.table'));
    }
}
