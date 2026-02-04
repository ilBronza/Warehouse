<?php

namespace IlBronza\Warehouse\Helpers\Unitloads;

use IlBronza\Ukn\Ukn;
use IlBronza\Warehouse\Helpers\Deliveries\ContentDeliveryUnitloadsHelper;
use IlBronza\Warehouse\Models\Unitload\Unitload;
use Illuminate\Support\Collection;

class UnitloadsDeliveryCheckerHelper extends UnitloadDeliveryCheckerBaseHelper
{
	static function checkForDeliveryAutoAttaching(Collection|Unitload $unitloads)
	{
		if($unitloads instanceof Unitload)
			return UnitloadDeliveryCheckerHelper::gpc()::checkForDeliveryAutoAttaching($unitloads);

		foreach($unitloads as $unitload)
			UnitloadDeliveryCheckerHelper::gpc()::checkForDeliveryAutoAttaching($unitloads);

		return;
	}
}