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
        Schema::table(config('warehouse.models.unitload.table'), function (Blueprint $table) {
            $table->boolean('placeholder')->nullable();
            $table->uuid('delivery_id')->nullable();
	        $table->uuid('content_delivery_id')->after('delivery_id')->nullable();
	        $table->foreign('content_delivery_id')->references('id')->on(config('warehouse.models.contentDelivery.table'));
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
        Schema::table(config('warehouse.models.unitload.table'), function (Blueprint $table) {
            $table->dropColumn('placeholder');
            $table->dropColumn('delivery_id');
	        $table->dropForeign(['content_delivery_id']);
	        $table->dropColumn('content_delivery_id');
        });
    }
};