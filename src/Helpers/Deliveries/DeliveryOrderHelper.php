<?php

namespace IlBronza\Warehouse\Helpers\Deliveries;

use IlBronza\Warehouse\Models\Delivery\Delivery;
use Illuminate\Support\Collection;

class DeliveryOrderHelper extends DeliveryOrderProductHelper
{
	public function attachElement($order)
	{
		foreach($order->getOrderProducts() as $orderProduct)
			$this->attachOrderProduct($orderProduct);
	}

	static function attachOrdersToDelivery(
		Delivery $delivery,
		array|Collection $orders
	) : self
	{
		return app('warehouse')->getOrderDeliveryHelper()
			->setDelivery($delivery)
			->setElements($orders)
			->attach();
	}
}