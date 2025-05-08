<?php

namespace IlBronza\Warehouse\Http\Controllers\Deliveries;

use IlBronza\Products\Models\Order;
use Illuminate\Http\Request;

class DeliveryAddOrdersController extends DeliveryCRUD
{
    public $allowedMethods = ['addOrders'];

	public function addOrders(Request $request, string $delivery)
	{
		$request->validate([
			'ids' => 'required|array|exists:' . config('products.models.order.table') . ',id',
		]);

		$delivery = $this->findModel($delivery);

		$orders = Order::gpc()::with('orderProducts')->whereIn('id', $request->input('ids'))->get();

		$helper = app('warehouse')->getOrderDeliveryHelper();

		$helper->setDelivery($delivery);
		$helper->setOrders($orders);
		$helper->attach();

		return $helper->getResponse();
	}
}