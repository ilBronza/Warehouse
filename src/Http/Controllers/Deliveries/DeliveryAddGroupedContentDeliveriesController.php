<?php

namespace IlBronza\Warehouse\Http\Controllers\Deliveries;

use IlBronza\Warehouse\Helpers\Deliveries\DeliveryOrderProductHelper;
use IlBronza\Warehouse\Models\Delivery\Delivery;
use Illuminate\Http\Request;

class DeliveryAddGroupedContentDeliveriesController extends DeliveryCRUD
{
	public $allowedMethods = ['addGroupedContentDeliveries'];

	public function addGroupedContentDeliveries(Request $request, string $delivery)
	{
		$delivery = $this->findModel($delivery);

		$orderProducts = collect();

		foreach($request->ids as $id)
		{
			$pieces = explode("_", $id);
			$deliveryId = $pieces[0];

			$_delivery = Delivery::gpc()::find($deliveryId);

			$groupedContentDeliveries = $_delivery->getGroupedClientsContentDeliveriesModels()->firstWhere(function($item) use($id)
				{
					return $item->client_destination_key == $id;
				});

			foreach($groupedContentDeliveries->contentDeliveries as $contentDelivery)
				$orderProducts->push(
					$contentDelivery->getContent()
				);
		}

		return DeliveryOrderProductHelper::attachOrderProductsToDelivery($delivery, $orderProducts)->getResponse();
	}
}