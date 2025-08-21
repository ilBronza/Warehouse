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
        Schema::table(config('warehouse.models.delivery.table'), function (Blueprint $table) {

            $table->uuid('zone_id')->nullable();
            $table->uuid('status_id')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(config('warehouse.models.delivery.table'), function (Blueprint $table) {
            $table->dropColumn('zone_id');
            $table->dropColumn('status_id');
        });
    }
};
