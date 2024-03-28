<?php

use IlBronza\Warehouse\Http\Controllers\Pallettypes\PallettypeCRUDController;
use IlBronza\Warehouse\Http\Controllers\Unitloads\UnitloadsBulkCreateController;

Route::group([
	'middleware' => ['web', 'auth'],
	'prefix' => 'warehouse-management',
	'as' => config('warehouse.routePrefix')
	],
	function()
	{
		Route::group(['prefix' => 'pallettypes'], function()
		{
			Route::get('', [Warehouse::getController('pallettype', 'index'), 'index'])->name('pallettypes.index');
			Route::get('create', [Warehouse::getController('pallettype', 'create'), 'create'])->name('pallettypes.create');
			Route::post('', [Warehouse::getController('pallettype', 'store'), 'store'])->name('pallettypes.store');
			Route::get('{pallettype}', [Warehouse::getController('pallettype', 'show'), 'show'])->name('pallettypes.show');
			Route::get('{pallettype}/edit', [Warehouse::getController('pallettype', 'edit'), 'edit'])->name('pallettypes.edit');
			Route::put('{pallettype}', [Warehouse::getController('pallettype', 'edit'), 'update'])->name('pallettypes.update');

			Route::delete('{pallettype}/delete', [Warehouse::getController('pallettype', 'destroy'), 'destroy'])->name('pallettypes.destroy');
		});


		Route::get('bulk-create-unit-loads/by-order-product-phase/{orderProductPhase}', [UnitloadsBulkCreateController::class, 'bulkCreate'])->name('bulk.create');

		Route::post('bulk-store-unit-loads/by-order-product-phase/{orderProductPhase}', [UnitloadsBulkCreateController::class, 'bulkStore'])->name('bulk.store');

		Route::group(['prefix' => 'unitloads'], function()
		{
			Route::get('{unitload}/edit', [Warehouse::getController('unitload', 'edit'), 'edit'])->name('unitloads.edit');
			Route::put('{unitload}', [Warehouse::getController('unitload', 'edit'), 'update'])->name('unitloads.update');
		});

	});