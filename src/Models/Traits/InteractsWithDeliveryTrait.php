<?php

namespace IlBronza\Warehouse\Models\Traits;

use IlBronza\Warehouse\Models\Delivery\ContentDelivery;
use IlBronza\Warehouse\Models\Delivery\Delivery;
use Illuminate\Support\Collection;

trait InteractsWithDeliveryTrait
{
	public function deliveries()
	{
		return $this->morphToMany(Delivery::gpc(), 'content', config('warehouse.models.contentDelivery.table'))->using(ContentDelivery::gpc())->withPivot([
			'id',
			'quantity',
			'quantity_required',
			'partial'
		])->withTimestamps();
	}

	public function contentDeliveries()
	{
		return $this->morphMany(ContentDelivery::gpc(), 'content');
	}

	public function getDeliveries() : Collection
	{
		return $this->deliveries;
	}

	public function getDeliveringChildren() : Collection
	{
		return collect();

		return $this->getOrderrows();
	}

	public function getDeliveriesNamesArray() : array
	{
		$result = [];

		foreach ($this->getDeliveringChildren() as $child)
			$result = array_merge($child->getDeliveriesNamesArray());

		foreach ($deliveries = $this->getDeliveries() as $delivery)
			$result[] = $delivery->getName();

		return array_unique($result);
	}

}