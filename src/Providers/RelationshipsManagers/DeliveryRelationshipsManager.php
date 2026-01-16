<?php

namespace IlBronza\Warehouse\Providers\RelationshipsManagers;

use IlBronza\CRUD\Providers\RelationshipsManager\RelationshipsManager;

use IlBronza\Products\Models\Quotations\Quotationrow;

use IlBronza\Warehouse\Models\Delivery\GroupedContentDelivery;

use function config;

class DeliveryRelationshipsManager Extends RelationshipsManager
{
	public  function getAllRelationsParameters() : array
	{
//		$relations['contentDeliveries'] = [
//			'controller' => config('warehouse.models.contentDelivery.controllers.index'),
//			'elementGetterMethod' => 'getRelatedContentDeliveries',
//		];

		$relations['contentDeliveries'] = [
			'controller' => config('warehouse.models.groupedContentDelivery.controllers.index'),
			'elementGetterMethod' => 'getGroupedClientsContentDeliveriesModels',
		];

		//		$relations['vehicle'] = config('vehicles.models.vehicle.controllers.show');

		return [
			'show' => [
				'relations' => $relations
			]
		];
	}
}