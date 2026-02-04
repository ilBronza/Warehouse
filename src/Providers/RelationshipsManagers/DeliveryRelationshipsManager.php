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
		$relations = [];

		$relations['groupedContentDeliveries'] = [
			'controller' => config('warehouse.models.groupedContentDelivery.controllers.index'),
			'elementGetterMethod' => 'getGroupedClientsContentDeliveriesModels',
			'selectRowCheckboxes' => true,
			'sorting' => true,
			'buttonsMethods' => [
				'getAddGroupedContentDeliveriesToDeliveryButton',
			]
		];

		//		$relations['vehicle'] = config('vehicles.models.vehicle.controllers.show');

		return [
			'show' => [
				'relations' => $relations
			]
		];
	}
}