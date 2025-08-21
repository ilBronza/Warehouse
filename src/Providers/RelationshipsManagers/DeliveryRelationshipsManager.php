<?php

namespace IlBronza\Warehouse\Providers\RelationshipsManagers;

use IlBronza\CRUD\Providers\RelationshipsManager\RelationshipsManager;

use function config;

class DeliveryRelationshipsManager Extends RelationshipsManager
{
	public  function getAllRelationsParameters() : array
	{
		$relations['contentDeliveries'] = config('warehouse.models.contentDelivery.controllers.index');
//		$relations['vehicle'] = config('vehicles.models.vehicle.controllers.show');

		return [
			'show' => [
				'relations' => $relations
			]
		];
	}
}