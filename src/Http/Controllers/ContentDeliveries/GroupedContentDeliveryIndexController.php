<?php

namespace IlBronza\Warehouse\Http\Controllers\ContentDeliveries;

use IlBronza\CRUD\Http\Controllers\Traits\StandardTraits\PackageStandardIndexTrait;
use IlBronza\Warehouse\Http\Controllers\CRUDWarehousePackageController;

class GroupedContentDeliveryIndexController extends CRUDWarehousePackageController
{
	use PackageStandardIndexTrait;

	public $configModelClassName = 'groupedContentDelivery';
	public $avoidCreateButton = true;
	public $scopes = [];
}
