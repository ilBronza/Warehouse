<?php

namespace IlBronza\Warehouse\Helpers\Deliveries;

use IlBronza\Warehouse\Helpers\ContentDeliveries\ContentDeliveryPartialLogger;
use IlBronza\Warehouse\Models\Delivery\Delivery;
use IlBronza\Warehouse\Models\Unitload\Unitload;
use Illuminate\Support\Collection;

use function dd;

class DeliveryUnitloadHelper extends DeliveryAttacherHelper
{
	public function attachElement(Unitload $unitload)
	{
		return $this->attachUnitload($unitload);
	}

	public function attach() : static
	{
		$distinctProductions = $this->getElements()
			->unique('production_id')->pluck('production_id');

		$contentDeliveries = $this->getElements()->pluck('content_delivery_id')->unique()->values();

		foreach($this->getElements() as $unitload)
			if(! ($contentDelivery = $unitload->getcontentDelivery())?->isPartial())
				if($contentDelivery)
				{
					ContentDeliveryPartialLogger::logTransition(
						$contentDelivery,
						true,
						'unitload_moved_from_content_delivery',
						[
							'writer' => __METHOD__,
							'unitload_id' => $unitload->getKey(),
							'production_type' => $unitload->production_type,
							'production_id' => $unitload->production_id,
							'source_delivery_id' => $contentDelivery->delivery_id,
							'target_delivery_id' => $this->getDelivery()->getKey(),
						]
					);

					$contentDelivery->setPartial(true);
				}

		foreach($distinctProductions as $distinctProduction)
		{
			$unitloads = $this->getElements()
				->where('production_id', $distinctProduction);

			$placeholderUnitload = $unitloads->first();

			$contentDelivery = $this->provideContentDeliveryByUnitload($placeholderUnitload);

			ContentDeliveryUnitloadsHelper::addUnitloadsToContentDelivery(
				$contentDelivery,
				$unitloads
			);
		}

		ContentDeliveryUnitloadsHelper::removeContentDeliveriesIfEmpty(
			$contentDeliveries
		);

		return $this;
	}

	static function attachUnitloadsToDelivery(
		Delivery $delivery,
		array|Collection $unitloads
	)
	{
		$helper = app('warehouse')->getUnitloadDeliveryHelper();

		$helper->setDelivery($delivery);

		$helper->setElements($unitloads);

		$helper->attach();

		return $helper->getResponse();
	}
}
