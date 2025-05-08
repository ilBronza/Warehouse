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

	public $allowedMethods = ['index'];

	public function getIndexFieldsArray()
	{
		//DeliveryPickableFieldsGroupParametersFile
		return config('warehouse.models.delivery.fieldsGroupsFiles.pickable')::getFieldsGroup();
	}

	public function getIndexElements()
	{
		return $this->getModelClass()::pickables()->get();
	}

	public function shareExtraViews()
	{
		$this->table->addView('top', 'warehouse::deliveries.addOrdersTop', [
			'orders' => Order::gpc()::whereIn('id', request()->ids)->get()
		]);
	}
}
