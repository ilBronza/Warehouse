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
        Schema::table(config('warehouse.models.contentDelivery.table'), function (Blueprint $table) {
	        $table->uuid('parent_id')->after('delivery_id')->nullable();
	        $table->foreign('parent_id')->references('id')->on(config('warehouse.models.contentDelivery.table'));
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
        Schema::table(config('warehouse.models.contentDelivery.table'), function (Blueprint $table) {
	        $table->dropForeign(['parent_id']);
	        $table->dropColumn('parent_id');
        });
    }
};