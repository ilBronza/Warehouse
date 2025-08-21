<?php

namespace IlBronza\Warehouse\Helpers\Unitloads;

use IlBronza\Ukn\Ukn;
use IlBronza\Warehouse\Helpers\Deliveries\ContentDeliveryUnitloadsHelper;
use IlBronza\Warehouse\Models\Unitload\Unitload;

use Illuminate\Support\Collection;

use function count;
use function dd;

class UnitloadsDeliveryCheckerHelper extends UnitloadDeliveryCheckerBaseHelper
{
	protected Collection $unitloads;

	public function __construct(Collection $unitloads)
	{
		$this->unitloads = $unitloads;
	}

	static function build(Collection $unitloads) : self
	{
		return static::instantiate($unitloads);
	}

	public function getUnitloads() : Collection
	{
		return $this->unitloads;
	}

	public function getContentDeliveriesAvailable() : array
	{
		return $this->getUnitloads()->outherTwins()->distinct('content_delivery_id')->pluck('content_delivery_id')->toArray();
	}

	static function checkForDeliveryAutoAttaching(Collection|Unitload $unitloads)
	{
		if($unitloads instanceof Unitload)
			return UnitloadDeliveryCheckerHelper::checkForDeliveryAutoAttaching($unitloads);

		$helper = static::build($unitloads);

		$distinctProductions = $helper->getUnitloads()
		                            ->unique('production_id')->pluck('production_id');

		foreach($distinctProductions as $distinctProduction)
		{
			$unitloads = $helper->getUnitloads()
			                  ->where('production_id', $distinctProduction);

			$placeholderUnitload = $unitloads->first();

			$contentDeliveriesAvailable = $placeholderUnitload->twins()->whereNotIn('id', $unitloads->pluck('id'))->distinct('content_delivery_id')->pluck('content_delivery_id')->toArray();

			if(! $contentDelivery = $helper->pickRightContentDeliveryAvailble($contentDeliveriesAvailable))
			{
				Ukn::e('Nessuna spedizione associabile ai bindelli creati');

				continue;
			}

			ContentDeliveryUnitloadsHelper::addUnitloadsToContentDelivery(
				$contentDelivery,
				$unitloads
			);
		}
	}
}