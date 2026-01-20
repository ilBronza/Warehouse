<?php

namespace IlBronza\Warehouse\Http\Controllers\Deliveries;

use IlBronza\Products\Models\Order;
use IlBronza\Warehouse\Http\Controllers\Deliveries\DeliveryAddOrdersIndexController;
use IlBronza\Warehouse\Models\Delivery\Delivery;

class DeliveryAddGroupedContentDeliveriesIndexController extends DeliveryAddOrdersIndexController
{
	public function getIndexFieldsArray()
	{
		$result = config('warehouse.models.delivery.fieldsGroupsFiles.pickable')::getFieldsGroup();

		$result['fields']['mySelfAddToDelivery'] = 'warehouse::groupedClientsContentDeliveries.addGroupedContentDeliveries';

		return $result;
	}

	public function shareExtraViews()
	{
		$ids = request()->ids;
	
		$firstId = $ids[0];

		$pieces = explode("_", $firstId);

		$deliveryId = $pieces[0];

		$delivery = Delivery::gpc()::find($deliveryId);

		$filteredGroupedContentDeliveries = $delivery->getGroupedClientsContentDeliveriesModels()->filter(function($item) use($ids)
			{
				return in_array($item->client_destination_key, $ids);
			});

		$this->table->addView('top', 'warehouse::deliveries.addGroupedContentDeliveriesTop', [
			'filteredGroupedContentDeliveries' => $filteredGroupedContentDeliveries
		]);
	}
}
