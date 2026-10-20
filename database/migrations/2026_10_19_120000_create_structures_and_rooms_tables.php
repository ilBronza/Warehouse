<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up() : void
	{
		foreach (['structure', 'roomCategory'] as $model) {
			Schema::create(config("warehouse.models.{$model}.table"), function (Blueprint $table) {
				$table->uuid('id')->primary();
				$table->string('name');
				$table->string('slug');
				$table->text('description')->nullable();
				$table->softDeletes();
				$table->timestamps();
			});
		}

		Schema::create(config('warehouse.models.roomType.table'), function (Blueprint $table) {
			$table->uuid('id')->primary();
			$table->string('name');
			$table->string('slug');
			$table->text('description')->nullable();
			$table->foreignUuid('room_category_id')->constrained(config('warehouse.models.roomCategory.table'))->restrictOnDelete();
			$table->softDeletes();
			$table->timestamps();
		});

		Schema::create(config('warehouse.models.room.table'), function (Blueprint $table) {
			$table->uuid('id')->primary();
			$table->string('name');
			$table->string('slug');
			$table->string('code')->nullable();
			$table->string('floor')->nullable();
			$table->decimal('surface_sqm', 12, 2)->nullable();
			$table->text('notes')->nullable();
			$table->foreignUuid('structure_id')->constrained(config('warehouse.models.structure.table'))->restrictOnDelete();
			$table->foreignUuid('room_type_id')->constrained(config('warehouse.models.roomType.table'))->restrictOnDelete();
			$table->foreignUuid('destination_id')->nullable()->constrained(config('clients.models.destination.table'))->nullOnDelete();
			$table->softDeletes();
			$table->timestamps();
		});
	}

	public function down() : void
	{
		foreach (['room', 'roomType', 'roomCategory', 'structure'] as $model)
			Schema::dropIfExists(config("warehouse.models.{$model}.table"));
	}
};
