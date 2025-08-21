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
	        $table->timestamp('loaded_at')->after('partial')->nullable();
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
	        $table->dropColumn('loaded_at');
        });
    }
};