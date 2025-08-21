<?php

namespace IlBronza\Warehouse\Helpers\Unitloads;

use IlBronza\Warehouse\Helpers\Deliveries\ContentDeliveryUnitloadsHelper;
use IlBronza\Warehouse\Models\Unitload\Unitload;

use Illuminate\Support\Collection;

use function config;
use function count;
use function dd;

class UnitloadDeliveryCheckerHelper extends UnitloadDeliveryCheckerBaseHelper
{
	protected Unitload $unitload;

	public function __construct(Unitload $unitload)
	{
		$this->unitload = $unitload;
	}

	static function build(Unitload $unitload) : self
	{
		return static::instantiate($unitload);
	}

	public function getUnitload() : Unitload
	{
		return $this->unitload;
	}

	public function getContentDeliveriesAvailable() : array
	{
		return $this->getUnitload()->outherTwins()->distinct('content_delivery_id')->pluck('content_delivery_id')->toArray();
	}

	public function checkAttachingAvailability(array $contentDeliveriesAvailable) : bool
	{
		if(count($contentDeliveriesAvailable) == 0)
			return false;

		if(count($contentDeliveriesAvailable) == 1)
			if($contentDeliveriesAvailable[0] == null)
				return false;

		return true;
	}

	static function checkForDeliveryAutoAttaching(Collection|Unitload $unitload)
	{
		if($unitload instanceof Collection)
			return UnitloadsDeliveryCheckerHelper::checkForDeliveryAutoAttaching($unitload);

		$helper = static::build($unitload);

		$contentDeliveriesAvailable = $helper->getContentDeliveriesAvailable();

		if(! $helper->checkAttachingAvailability($contentDeliveriesAvailable))
			return $helper->responseCantAttach();

		if(count($contentDeliveriesAvailable) > 1)
		{
			dd('ragionare su più spedizioni esistenti');
		}

		ContentDeliveryUnitloadsHelper::addUnitloadToContentDelivery(
			$contentDeliveriesAvailable[0],
			$unitload
		);
	}
}