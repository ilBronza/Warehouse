<?php

namespace IlBronza\Warehouse\Http\Controllers\Deliveries;

use IlBronza\Products\Providers\Helpers\OrderProductPhases\OrderProductPhaseCompletionHelper;
use IlBronza\Ukn\Ukn;
use IlBronza\Warehouse\Helpers\ContentDeliveries\ContentDeliveryLoaderHelper;
use IlBronza\Warehouse\Http\Controllers\ContentDeliveries\ContentDeliveriesCRUDController;
use IlBronza\Warehouse\Models\Delivery\Delivery;

class DeliveryMapController extends ContentDeliveriesCRUDController
{
	public $allowedMethods = ['renderMap'];

	public function renderMap(string $delivery)
	{
		$delivery = Delivery::gpc()::find($delivery);

		$stops = [
			$delivery->getStartingPairs()
		];

		foreach($delivery->getGroupedClientsContentDeliveriesModels() as $groupedContentDelivery)
		{
			$destination = $groupedContentDelivery->getDestinationFromKey($groupedContentDelivery->getKey());

			$pairs = $destination->getCoordinatesPair();

			$pairs['label'] = $destination->getClient()?->getName();

			$stops[] = $pairs;
		}

		$stops[] = $delivery->getEndingPairs();

		return view('addresses::maps.points', compact('stops'));
	}
}
