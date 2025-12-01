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
			'prefix' => 'content-deliveries',
			'as' => 'contentDeliveries.'
		], function()
		{
			//ContentDeliveryPopupController
			Route::get('{contentDelivery}/deliveries-popup', [Warehouse::getController('contentDelivery', 'popup'), 'popup'])->name('popup');

			//ContentDeliveryLoadCumulativeController
			Route::get('{contentDelivery}/load-cumulative-by-client', [Warehouse::getController('contentDelivery', 'loadCumulative'), 'loadCumulative'])->name('loadCumulative');
			Route::get('{contentDelivery}/unload-cumulative-by-client', [Warehouse::getController('contentDelivery', 'unloadCumulative'), 'unloadCumulative'])->name('unloadCumulative');

			//ContentDeliveryLoadController
			Route::get('{contentDelivery}/load', [Warehouse::getController('contentDelivery', 'load'), 'load'])->name('load');
			Route::get('{contentDelivery}/unload', [Warehouse::getController('contentDelivery', 'unload'), 'unload'])->name('unload');

			//ContentDeliveryDetachController
			Route::get('{contentDelivery}/detach', [Warehouse::getController('contentDelivery', 'detach'), 'detach'])->name('detach');

			Route::get('{contentDelivery}/edit', [Warehouse::getController('contentDelivery', 'edit'), 'edit'])->name('edit');
			Route::put('{contentDelivery}', [Warehouse::getController('contentDelivery', 'edit'), 'update'])->name('update');

			Route::get('', [Warehouse::getController('contentDelivery', 'index'), 'index'])->name('index');
		});

		Route::group([
			'prefix' => 'deliveries-management',
			'as' => 'deliveries.'
		], function()
		{

			Route::delete('{delivery}/delete', [Warehouse::getController('delivery', 'destroy'), 'destroy'])->name('destroy');

			Route::group([
				'prefix' => 'orders',
				'as' => 'orders.'
			], function()
			{
				//DeliveryByOrderController
				Route::get('{order}/deliveries-popup', [Warehouse::getController('delivery', 'orderDeliveriesPopup'), 'popup'])->name('popup');
			});

			//DeliveryIndexController
			Route::get('', [Warehouse::getController('delivery', 'index'), 'index'])->name('index');

			//DeliveryCreateStoreController
			Route::get('create', [Warehouse::getController('delivery', 'create'), 'create'])->name('create');
			Route::post('', [Warehouse::getController('delivery', 'store'), 'store'])->name('store');

			//DeliveryAddOrdersIndexController
			Route::post('add-orders-index', [Warehouse::getController('delivery', 'addOrdersIndex'), 'index'])->name('addOrdersIndex');

			//DeliveryAddOrdersController
			Route::post('{delivery}/add-orders', [Warehouse::getController('delivery', 'addOrders'), 'addOrders'])->name('addOrders');

			//DeliveryAddUnitloadsController
			Route::post('{delivery}/add-unitloads', [Warehouse::getController('delivery', 'addUnitloads'), 'addUnitloads'])->name('addUnitloads');

			Route::get('{delivery}', [Warehouse::getController('delivery', 'show'), 'show'])->name('show');
			Route::get('{delivery}/edit', [Warehouse::getController('delivery', 'edit'), 'edit'])->name('edit');
			Route::put('{delivery}', [Warehouse::getController('delivery', 'update'), 'update'])->name('update');

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


		//UnitloadsBulkCreateController
		Route::get('bulk-create-unit-loads/by-order-product-phase/{orderProductPhase}', [UnitloadsBulkCreateController::class, 'bulkCreate'])->name('bulk.create');
		Route::post('bulk-store-unit-loads/by-order-product-phase/{orderProductPhase}', [UnitloadsBulkCreateController::class, 'bulkStore'])->name('bulk.store');

		Route::group(['prefix' => 'unitloads'], function()
		{
			//UnitloadAssociateToDeliveryController
			Route::post('associate-to-delivery-table', [Warehouse::getController('unitload', 'associateToDelivery'), 'index'])->name('unitloads.associateToDeliveryTable');
			Route::post('associate-to-delivery', [Warehouse::getController('unitload', 'associateToDelivery'), 'store'])->name('unitloads.associateToDelivery');


			Route::get('', [Warehouse::getController('unitload', 'index'), 'index'])->name('unitloads.index');

			//UnitloadEditUpdateController
			Route::get('{unitload}/edit', [Warehouse::getController('unitload', 'edit'), 'edit'])->name('unitloads.edit');
			Route::put('{unitload}', [Warehouse::getController('unitload', 'edit'), 'update'])->name('unitloads.update');

			//UnitloadSplitController
			Route::get('{unitload}/split', [Warehouse::getController('unitload', 'split'), 'edit'])->name('unitloads.splitForm');
			Route::put('{unitload}/split', [Warehouse::getController('unitload', 'split'), 'update'])->name('unitloads.split');

			// UnitloadPrintController
			Route::get('{unitload}/print', [Warehouse::getController('unitload', 'print'), 'print'])->name('unitloads.print');
			Route::get('{unitload}/reset-printed-at', [Warehouse::getController('unitload', 'resetPrintedAt'), 'resetPrintedAt'])->name('unitloads.resetPrintedAt');

			Route::get('{unitload}/delete', [Warehouse::getController('unitload', 'destroy'), 'destroy'])->name('unitloads.destroy');
		});

	});