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

				// $this->table->addButton(Delivery::gpc()::getAddOrdersToDeliveryButton());


		$relations['contentDeliveries'] = [
			'controller' => config('warehouse.models.groupedContentDelivery.controllers.index'),
			'elementGetterMethod' => 'getGroupedClientsContentDeliveriesModels',
			'selectRowCheckboxes' => true,
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