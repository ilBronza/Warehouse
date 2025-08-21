<?php

namespace IlBronza\Warehouse\Http\Controllers\Deliveries;

use IlBronza\Products\Models\Order;

class DeliveryByOrderController extends DeliveryCRUD
{
	public $allowedMethods = ['popup'];

	public function popup(string $order)
	{
		$model = Order::gpc()::find($order);

		$children = $model->getDeliveringChildren();

		return view('warehouse::deliveries.allDeliveriesBy', compact('model', 'children'));
	}
}