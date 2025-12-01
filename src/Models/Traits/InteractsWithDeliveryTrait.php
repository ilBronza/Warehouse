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
			'partial',
			'loaded_at'
		])->withTimestamps();
	}

	public function hasUndeliveringUnitloads() : bool
	{
		if(isset($this->attributes['unitloads_without_delivery_count']))
		{
			if($this->unitloads_without_delivery_count)
				return true;
		}

		if ($this->unitloads()->notDelivering()->first())
			return true;

		return false;
	}

	public function hasBeenCompletelyDelivered() : bool
	{
		if ($this->hasUndeliveringUnitloads())
			return false;

		foreach($this->getDeliveries() as $delivery)
			if(! $delivery->pivot->loaded_at)
				return false;

		return true;
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