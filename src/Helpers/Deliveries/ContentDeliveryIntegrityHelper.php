<?php

namespace IlBronza\Warehouse\Helpers\Deliveries;

use IlBronza\Warehouse\Models\Delivery\ContentDelivery;
use IlBronza\Warehouse\Models\Unitload\Unitload;
use Illuminate\Support\Collection;

use function dd;

class ContentDeliveryIntegrityHelper
{
	static function checkContentDeliveryIntegrity(ContentDelivery $contentDelivery)
	{
		$unitloads = $contentDelivery->unitloads;

		$unitloadsCount = $unitloads->unique('production_id')->count();

//		if($unitloads->unique('production_id')->count() != 1)
//			dd('content delivery has multiple productions, whats wrong?');

		if(count($unitloads) != $unitloads->first()?->getBrotherNumbers())
			$contentDelivery->partial = true;

		$total = $unitloads->sum('quantity');

		$contentDelivery->quantity = $total;

		if($contentDelivery->quantity_required === null)
			$contentDelivery->quantity_required = $total;

		$contentDelivery->save();
	}

}