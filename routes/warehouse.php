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
		Route::group([
			'prefix' => 'deliveries-management',
			'as' => 'deliveries.'
		], function()
		{
			Route::get('{delivery}/edit', [Warehouse::getController('delivery', 'edit'), 'edit'])->name('edit');
			Route::put('{delivery}', [Warehouse::getController('delivery', 'update'), 'update'])->name('update');

			//DeliveryIndexController
			Route::get('', [Warehouse::getController('delivery', 'index'), 'index'])->name('index');
			Route::get('create', [Warehouse::getController('delivery', 'create'), 'create'])->name('create');
			Route::post('', [Warehouse::getController('delivery', 'store'), 'store'])->name('store');

			//DeliveryAddOrdersIndexController
			Route::post('add-orders-index', [Warehouse::getController('delivery', 'addOrdersIndex'), 'index'])->name('addOrdersIndex');

			Route::post('{delivery}/add-orders', [Warehouse::getController('delivery', 'addOrders'), 'addOrders'])->name('addOrders');
		});


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
			Route::get('', [Warehouse::getController('unitload', 'index'), 'index'])->name('unitloads.index');

			//UnitloadEditUpdateController
			Route::get('{unitload}/edit', [Warehouse::getController('unitload', 'edit'), 'edit'])->name('unitloads.edit');
			Route::put('{unitload}', [Warehouse::getController('unitload', 'edit'), 'update'])->name('unitloads.update');

			// UnitloadPrintController
			Route::get('{unitload}/print', [Warehouse::getController('unitload', 'print'), 'print'])->name('unitloads.print');
			Route::get('{unitload}/reset-printed-at', [Warehouse::getController('unitload', 'resetPrintedAt'), 'resetPrintedAt'])->name('unitloads.resetPrintedAt');

			Route::get('{unitload}/delete', [Warehouse::getController('unitload', 'destroy'), 'destroy'])->name('unitloads.destroy');
		});

	});