<?php

namespace IlBronza\Warehouse\Helpers\Unitloads;

use IlBronza\Warehouse\Helpers\Deliveries\ContentDeliveryUnitloadsHelper;
use IlBronza\Warehouse\Models\Unitload\Unitload;

use Illuminate\Support\Collection;

use function class_basename;
use function class_exists;
use function config;
use function count;
use function dd;
use function lcfirst;

class UnitloadDeliveryCheckerBaseHelper
{
	public static function gpc() : string
	{
		return config(
			'warehouse.models.unitload.helpers.' . lcfirst(class_basename(static::class))
		);
	}

	static function instantiate($content)
	{
		$helperClass = static::gpc();

		if(! class_exists($helperClass))
			throw new \Exception("UnitloadDeliveryHelper class not found: {$helperClass}");

		return new $helperClass($content);
	}

	public function pickRightContentDeliveryAvailble(array $contentDeliveriesAvailables) : ? ContentDelivery
	{
		if(count($contentDeliveriesAvailables) == 0)
			return null;

		if(count($contentDeliveriesAvailables) == 1)
		{
			if($contentDeliveriesAvailables[0] == null)
				return null;

			return ContentDelivery::gpc()::find($contentDeliveriesAvailables[0]);
		}

		return null;
	}
}