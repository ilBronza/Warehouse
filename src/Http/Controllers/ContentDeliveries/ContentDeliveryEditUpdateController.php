<?php

namespace IlBronza\Warehouse\Http\Controllers\ContentDeliveries;

use IlBronza\CRUD\Http\Controllers\Traits\StandardTraits\PackageStandardEditUpdateTrait;

class ContentDeliveryEditUpdateController extends ContentDeliveriesCRUDController
{
	public $returnBack = true;

	use PackageStandardEditUpdateTrait;
}
