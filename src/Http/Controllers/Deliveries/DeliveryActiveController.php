<?php

namespace IlBronza\Warehouse\Http\Controllers\Deliveries;

use App\Delivery;
use IlBronza\Warehouse\Http\Controllers\Deliveries\DeliveryIndexController;

class DeliveryActiveController extends DeliveryIndexController
{
	public array $scopes = ['current'];
	protected string $indexFieldsArraySuffix = 'active';

	public function addIndexButtons()
	{
		$this->table->addButton(
			Delivery::gpc()::automaticallySortOrderProductPhasesButton()
		);
	}


	function getIndexElementsRelationsArray() : array
	{
		return [
			'vehicle',
			'notes',
			'contentDeliveries.content.order.destination.address',
			'contentDeliveries.content.order.client.notes',
			'contentDeliveries.content.order.notes',
			'contentDeliveries.content.product.size',
			'contentDeliveries.content.product.notes',
			'contentDeliveries.content.product.extraFields',
			'contentDeliveries.content.product.packing',
			'contentDeliveries.unitloads.loadable.size',
			'contentDeliveries.unitloads.loadable.packing',
			'contentDeliveries.unitloads.pallettype',
			'contentDeliveries' => function($query)
			{
				$query->withCount('unitloads');
			}
		];
	}

}
