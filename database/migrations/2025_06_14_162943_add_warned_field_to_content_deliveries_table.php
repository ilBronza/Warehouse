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
	        $table->smallInteger('warned')->nullable()->after('sorting_index');
	        $table->timestamp('warned_at')->nullable()->after('sorting_index');
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
	        $table->dropColumn('warned_at');
	        $table->dropColumn('warned');
        });
    }
};