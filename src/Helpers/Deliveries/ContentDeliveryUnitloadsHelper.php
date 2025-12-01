<?php

namespace IlBronza\Warehouse\Helpers\Deliveries;

use IlBronza\Warehouse\Models\Delivery\ContentDelivery;
use IlBronza\Warehouse\Models\Unitload\Unitload;
use Illuminate\Support\Collection;

use function is_string;

class ContentDeliveryUnitloadsHelper
{
	static function removeContentDeliveriesIfEmpty(Collection $contentDeliveries)
	{
		foreach($contentDeliveries as $contentDelivery)
		{
			if(is_string($contentDelivery))
				$contentDelivery = ContentDelivery::gpc()::find($contentDelivery);

			if($contentDelivery)
				if($contentDelivery->unitloads()->count() == 0)
					$contentDelivery->delete();
		}
	}

	static function addUnitloadsToContentDelivery(string|ContentDelivery $contentDelivery, Collection $unitloads)
	{
		if(is_string($contentDelivery))
			$contentDelivery = ContentDelivery::gpc()::findOrFail($contentDelivery);

		foreach($unitloads as $unitload)
			static::_addUnitloadToContentDelivery($contentDelivery, $unitload);

		$content = $contentDelivery->getContent();

		foreach($content->getDeliveries() as $delivery)
			ContentDeliveryIntegrityHelper::checkContentDeliveryIntegrity($delivery->pivot);
	}

	static function addUnitloadToContentDelivery(string|ContentDelivery $contentDelivery, Unitload $unitload)
	{
		if(is_string($contentDelivery))
			$contentDelivery = ContentDelivery::gpc()::findOrFail($contentDelivery);

		static::_addUnitloadToContentDelivery($contentDelivery, $unitload);

		$content = $contentDelivery->getContent();

		foreach($content->getDeliveries() as $delivery)
			ContentDeliveryIntegrityHelper::checkContentDeliveryIntegrity($delivery->pivot);
	}

	static function _addUnitloadToContentDelivery(ContentDelivery $contentDelivery, Unitload $unitload)
	{
		$unitload->contentDelivery()->associate($contentDelivery);
		$unitload->save();
	}
}