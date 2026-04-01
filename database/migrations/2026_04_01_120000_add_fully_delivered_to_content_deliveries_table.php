<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table(config('warehouse.models.contentDelivery.table'), function (Blueprint $table) {
            $table->boolean('fully_delivered')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table(config('warehouse.models.contentDelivery.table'), function (Blueprint $table) {
            $table->dropColumn('fully_delivered');
        });
    }
};
