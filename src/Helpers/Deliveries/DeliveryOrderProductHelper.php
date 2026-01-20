<?php

namespace IlBronza\Warehouse\Helpers\Deliveries;

use IlBronza\Products\Models\OrderProduct;
use IlBronza\Warehouse\Models\Delivery\ContentDelivery;
use IlBronza\Warehouse\Models\Delivery\Delivery;
use Illuminate\Support\Collection;

class DeliveryOrderProductHelper extends DeliveryAttacherHelper
{
	public function attachOrderProduct(OrderProduct $orderProduct, bool $partial = false, float $quantity = null) : ContentDelivery
	{
		$contentDelivery = $this->attachContent($orderProduct, $partial, $quantity);

		$unitloads = $orderProduct->getUnitloadsByClientQuantity();

		$this->checkOrderProductShippingIntegrity($orderProduct, $unitloads);

		ContentDeliveryUnitloadsHelper::addUnitloadsToContentDelivery($contentDelivery, $unitloads);

		return $contentDelivery;
	}

	public function attachElement($orderProduct)
	{
		$this->attachOrderProduct($orderProduct);
	}

	static function attachOrderProductsToDelivery(
		Delivery $delivery,
		array|Collection $orderProducts
	) : self
	{
		return app('warehouse')->getOrderProductDeliveryHelper()
			->setDelivery($delivery)
			->setElements($orderProducts)
			->attach();
	}
}