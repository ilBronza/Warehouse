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
	        $table->uuid('finishing_id')->nullable();
	        $table->foreign('finishing_id')->references('id')->on(config('products.models.finishing.table'));
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
	        $table->dropForeign(['finishing_id']);
	        $table->dropColumn('finishing_id');
        });
    }
};