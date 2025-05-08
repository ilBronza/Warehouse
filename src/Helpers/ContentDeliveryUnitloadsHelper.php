<?php

namespace IlBronza\Warehouse\Helpers;

use IlBronza\Warehouse\Models\Delivery\ContentDelivery;
use IlBronza\Warehouse\Models\Unitload\Unitload;
use Illuminate\Support\Collection;

class ContentDeliveryUnitloadsHelper
{
	static function addUnitloadsToContentDelivery(ContentDelivery $contentDelivery, Collection $unitloads)
	{
		foreach($unitloads as $unitload)
			static::addUnitloadToContentDelivery($contentDelivery, $unitload);
	}

	static function addUnitloadToContentDelivery(ContentDelivery $contentDelivery, Unitload $unitload)
	{
		$unitload->contentDelivery()->associate($contentDelivery);
		$unitload->save();
	}
}