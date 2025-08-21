<?php

namespace IlBronza\Warehouse\Helpers\Deliveries;

use IlBronza\Products\Models\Order;
use IlBronza\Products\Models\OrderProduct;
use IlBronza\Warehouse\Models\Delivery\ContentDelivery;
use IlBronza\Warehouse\Models\Delivery\Delivery;
use Illuminate\Support\Collection;
use function app;
use function dd;

class DeliveryOrderHelper extends DeliveryAttacherHelper
{
	public function attachOrderProduct(OrderProduct $orderProduct, bool $partial = false, float $quantity = null) : ContentDelivery
	{
		$contentDelivery = $this->attachContent($orderProduct, $partial, $quantity);

		$unitloads = $orderProduct->getUnitloadsByClientQuantity();

		$this->checkOrderProductShippingIntegrity($orderProduct, $unitloads);

		ContentDeliveryUnitloadsHelper::addUnitloadsToContentDelivery($contentDelivery, $unitloads);

		return $contentDelivery;
	}

	public function attachElement(Order $order)
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