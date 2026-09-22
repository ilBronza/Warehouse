<?php

namespace IlBronza\Warehouse\Helpers\Deliveries;

use IlBronza\Warehouse\Helpers\ContentDeliveries\ContentDeliveryPartialLogger;
use IlBronza\Warehouse\Models\Delivery\ContentDelivery;

use IlBronza\Warehouse\Models\Unitload\Unitload;
use Illuminate\Support\Collection;

class DeliveryDetacherHelper
{
	static function detachContentDelivery(ContentDelivery $contentDelivery)
	{
		$unitloads = $contentDelivery->getUnitloads();

		static::detachUnitloads($unitloads, $contentDelivery, false);

		$contentDelivery->delete();
	}

	static function detachUnitloads(Collection $unitloads, ContentDelivery $contentDelivery, bool $check = true)
	{
		foreach($unitloads as $unitload)
			static::detachUnitload($unitload, $contentDelivery, false);

		if($check)
			dd('occuparsi del check delle quantità');
	}

	static function detachUnitload(Unitload $unitload, ContentDelivery $contentDelivery, bool $check = true) : void
	{
		$unitload->content_delivery_id = null;

		if($check)
			dd('occuparsi del check delle quantità');

		$unitload->save();

		ContentDeliveryPartialLogger::logTransition(
			$contentDelivery,
			true,
			'unitload_detached_from_content_delivery',
			[
				'writer' => __METHOD__,
				'unitload_id' => $unitload->getKey(),
				'production_type' => $unitload->production_type,
				'production_id' => $unitload->production_id,
			]
		);

		$contentDelivery->partial = true;
		$contentDelivery->save();
	}
}
