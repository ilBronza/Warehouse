<?php

namespace IlBronza\Warehouse\Http\Controllers\ContentDeliveries;

use IlBronza\CRUD\Http\Controllers\Traits\StandardTraits\PackageStandardIndexTrait;

class ContentDeliveryIndexController extends ContentDeliveriesCRUDController
{
	public $avoidCreateButton = true;

	use PackageStandardIndexTrait;

	public $scopes = [];

}
