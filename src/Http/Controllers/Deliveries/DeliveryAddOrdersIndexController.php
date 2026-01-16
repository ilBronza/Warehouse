<?php

namespace IlBronza\Warehouse\Http\Controllers\Deliveries;

use IlBronza\CRUD\Traits\CRUDIndexTrait;
use IlBronza\CRUD\Traits\CRUDPlainIndexTrait;

use IlBronza\Products\Models\Order;

use function config;
use function request;

class DeliveryAddOrdersIndexController extends DeliveryCRUD
{
	use CRUDPlainIndexTrait;
	use CRUDIndexTrait;

	public $avoidCreateButton = true;

	public $allowedMethods = ['index'];

	public function getIndexFieldsArray()
	{
		//DeliveryPickableFieldsGroupParametersFile
		return config('warehouse.models.delivery.fieldsGroupsFiles.pickable')::getFieldsGroup();
	}

	public function getIndexElements()
	{
		return $this->getModelClass()::current()->with([
			'vehicle',
			'notes',
			'contentDeliveries.content.order.destination.address',
			'contentDeliveries.content.order.client.notes',
			'contentDeliveries.content.order.notes',
			'contentDeliveries.content.product.size',
			'contentDeliveries.content.product.notes',
			'contentDeliveries.content.product.extraFields',
			'contentDeliveries.content.product.packing',
			'contentDeliveries.unitloads.loadable.size',
			'contentDeliveries.unitloads.loadable.packing',
			'contentDeliveries.unitloads.pallettype',
			'contentDeliveries' => function($query)
			{
				$query->withCount('unitloads');
			}
		])->get();
	}

	public function shareExtraViews()
	{
		$this->table->addView('top', 'warehouse::deliveries.addOrdersTop', [
			'orders' => Order::gpc()::whereIn('id', request()->ids)->get()
		]);
	}
}
