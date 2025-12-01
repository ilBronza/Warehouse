<?php

namespace IlBronza\Warehouse\Http\Controllers\Deliveries;

use IlBronza\Products\Models\Order;

use function request;
use function session;

class DeliveryByOrderController extends DeliveryCRUD
{
	public $allowedMethods = ['popup'];

	public function popup(string $order)
	{
		if($callertablename = request()->callertablename)
			session()->put('callertablename', $callertablename);

		if($callerrowid = request()->callerrowid)
			session()->put('callerrowid', $callerrowid);

		$model = Order::gpc()::find($order);

		$children = $model->getDeliveringChildren();

		return view('warehouse::deliveries.allDeliveriesBy', compact('model', 'children'));
	}
}