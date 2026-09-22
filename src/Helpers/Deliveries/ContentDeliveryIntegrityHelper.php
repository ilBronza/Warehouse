<?php

namespace IlBronza\Warehouse\Helpers\Deliveries;

use IlBronza\Warehouse\Helpers\ContentDeliveries\ContentDeliveryPartialLogger;
use IlBronza\Warehouse\Models\Delivery\ContentDelivery;

class ContentDeliveryIntegrityHelper
{
	static function checkContentDeliveryIntegrity(ContentDelivery $contentDelivery)
	{
		$unitloads = $contentDelivery->unitloads;
		$firstUnitload = $unitloads->first();
		$unitloadsCount = $unitloads->count();
		$brotherCount = $firstUnitload?->getBrotherNumbers();

		if($unitloadsCount != $brotherCount)
		{
			ContentDeliveryPartialLogger::logTransition(
				$contentDelivery,
				true,
				'unitloads_count_does_not_match_first_production_brothers',
				[
					'writer' => __METHOD__,
					'unitloads_count' => $unitloadsCount,
					'brother_count' => $brotherCount,
					'first_unitload_id' => $firstUnitload?->getKey(),
					'first_production_type' => $firstUnitload?->production_type,
					'first_production_id' => $firstUnitload?->production_id,
					'production_groups' => $unitloads
						->groupBy(fn($unitload) => "{$unitload->production_type}:{$unitload->production_id}")
						->map(fn($group, $production) => [
							'production' => $production,
							'unitloads_count' => $group->count(),
							'unitload_ids' => $group->pluck('id')->values()->all(),
							'quantity' => $group->sum('quantity'),
						])
						->values()
						->all(),
				]
			);

			$contentDelivery->partial = true;
		}
		else
		{
			ContentDeliveryPartialLogger::logTransition(
				$contentDelivery,
				false,
				$firstUnitload
					? 'unitloads_count_matches_first_production_brothers'
					: 'no_unitloads_attached',
				[
					'writer' => __METHOD__,
					'unitloads_count' => $unitloadsCount,
					'brother_count' => $brotherCount,
					'first_unitload_id' => $firstUnitload?->getKey(),
					'first_production_type' => $firstUnitload?->production_type,
					'first_production_id' => $firstUnitload?->production_id,
				]
			);

			$contentDelivery->partial = false;
		}

		$total = $unitloads->sum('quantity');

		$contentDelivery->quantity = $total;

		if($contentDelivery->quantity_required === null)
			$contentDelivery->quantity_required = $total;

		$contentDelivery->save();
	}

}
