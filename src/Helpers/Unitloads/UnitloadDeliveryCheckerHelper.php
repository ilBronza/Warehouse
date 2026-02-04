<?php

namespace IlBronza\Warehouse\Helpers\Unitloads;

use IlBronza\Ukn\Ukn;
use IlBronza\Warehouse\Helpers\Deliveries\ContentDeliveryUnitloadsHelper;
use IlBronza\Warehouse\Models\Unitload\Unitload;
use Illuminate\Support\Collection;

class UnitloadDeliveryCheckerHelper extends UnitloadDeliveryCheckerBaseHelper
{
	static function checkForDeliveryAutoAttaching(Collection|Unitload $unitload)
	{
		if($unitload instanceof Collection)
			return UnitloadsDeliveryCheckerHelper::gpc()::checkForDeliveryAutoAttaching($unitload);

		if(! $contentDelivery = $unitload->getProduction()->getOrderProduct()->getFirstVacantContentDelivery())
		{
			if(! $contentDelivery = $unitload->getProduction()->getOrderProduct()->getForcedAssignedContentDelivery())
			{
				Ukn::w(trans('warehouse::contentDeliveries.no_vacant_content_delivery_found_for_unitload_auto_attaching', [
					'unitloadCode' => $unitload->code,
					'orderProductCode' => $unitload->getProduction()->getOrderProduct()->code
				]));

				return ;
			}
		}

		ContentDeliveryUnitloadsHelper::addUnitloadToContentDelivery(
			$contentDelivery,
			$unitload
		);
	}
}