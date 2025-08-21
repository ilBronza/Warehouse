<?php

namespace IlBronza\Warehouse\Http\Controllers\Deliveries;

use IlBronza\CRUD\Http\Controllers\Traits\StandardTraits\PackageStandardIndexTrait;
use IlBronza\CRUD\Traits\CRUDIndexTrait;
use IlBronza\CRUD\Traits\CRUDPlainIndexTrait;

use function config;

class DeliveryIndexController extends DeliveryCRUD
{
	public $scopes = ['current'];

	use PackageStandardIndexTrait;

	function getIndexElementsRelationsArray() : array
	{
		return [
			'contentDeliveries.content',
			'contentDeliveries' => function($query)
			{
				$query->withCount('unitloads');
			}
		];
	}

}
