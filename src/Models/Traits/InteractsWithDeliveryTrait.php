<?php

namespace IlBronza\Warehouse\Models\Traits;

use IlBronza\Warehouse\Models\Delivery\ContentDelivery;
use IlBronza\Warehouse\Models\Delivery\Delivery;

trait InteractsWithDeliveryTrait
{
	public function deliveries()
	{
		return $this->morphToMany(Delivery::gpc(), 'content', config('warehouse.models.contentDelivery.table'))
		            ->using(ContentDelivery::gpc())->withPivot([
						'quantity_required',
						'partial'
					])->withTimestamps();
	}

	public function contentDeliveries()
	{
		return $this->morphMany(ContentDelivery::gpc(), 'content');
	}
}