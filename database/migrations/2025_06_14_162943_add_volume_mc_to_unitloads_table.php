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
	        $table->decimal('volume_mc', 3, 2)->after('height_mm')->nullable();
	        $table->renameColumn('weigth_kg', 'weight_kg');
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
	        $table->dropColumn('volume_mc');
	        $table->renameColumn('weight_kg', 'weigth_kg');
        });
    }
};