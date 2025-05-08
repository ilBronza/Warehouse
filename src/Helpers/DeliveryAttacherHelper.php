<?php

namespace IlBronza\Warehouse\Helpers;

use IlBronza\Products\Models\Order;
use IlBronza\Products\Models\OrderProduct;
use IlBronza\Warehouse\Models\Delivery\ContentDelivery;
use IlBronza\Warehouse\Models\Delivery\Delivery;
use Illuminate\Support\Collection;

use function dd;
use function route;
use function view;

class DeliveryAttacherHelper
{
	public Delivery $delivery;

	public function setDelivery(Delivery $delivery) : static
	{
		$this->delivery = $delivery;

		return $this;
	}

	//gettters
	public function getDelivery() : Delivery
	{
		return $this->delivery;
	}


	public function _attachTarget($target, bool $partial = false, float $quantity = null) : ContentDelivery
	{
		$target->deliveries()->syncWithoutDetaching([$this->getDelivery()->getKey() => [
			'partial' => $partial,
			'quantity_required' => $quantity ?? $target->getClientQuantity()
		]]);

		return $target->contentDeliveries()->byDelivery($this->getDelivery())->first();
	}

}