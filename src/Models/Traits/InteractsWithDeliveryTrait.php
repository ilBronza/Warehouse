<?php

namespace IlBronza\Warehouse\Models\Traits;

use IlBronza\Warehouse\Models\Delivery\ContentDelivery;
use IlBronza\Warehouse\Models\Delivery\Delivery;
use Illuminate\Support\Collection;

trait InteractsWithDeliveryTrait
{
	abstract public function getProductionStatusList() : ? string;

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

		if($this->relationLoaded('unitloads'))
			return !! $this->unitloads->whereNull('content_delivery_id')->first();

		if ($this->unitloads()->notDelivering()->first())
			return true;

		return false;
	}

	public function hasBeenCompletelyDelivered() : bool
	{
		if(! count($this->contentDeliveries))
			return false;

		foreach($this->contentDeliveries as $contentDelivery)
			if(! $contentDelivery->hasBeenShipped())
				return false;

		if($this->getDeliveryingQuantity() < $this->getQuantityRequired())
			return false;

		if ($this->hasUndeliveringUnitloads())
			return false;

		foreach($this->getDeliveries() as $delivery)
			if(! $delivery->pivot->loaded_at)
				return false;

		return true;
	}

	public function isBeingDelivered() : bool
	{
		if(! count($this->contentDeliveries))
			return false;

		return true;
	}

	public function getDeliveryingQuantity() : float
	{
		return $this->contentDeliveries->sum(function($contentDelivery)
		{
			return $contentDelivery->getQuantityRequired();
		}) ?? 0;
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
		return $this->getOrderrows();
	}

	public function getDeliveriesNamesArray() : array
	{
		$result = [];

		foreach ($this->getDeliveringChildren() as $child)
			$result = array_merge($child->getDeliveriesNamesArray());

		foreach ($deliveries = $this->getDeliveries() as $delivery)
			$result[] = $delivery->getShortName();

		return array_unique($result);
	}

}