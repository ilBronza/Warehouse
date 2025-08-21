<?php

namespace IlBronza\Warehouse\Http\Controllers\Deliveries;

use IlBronza\CRUD\Traits\CRUDCreateStoreTrait;
use IlBronza\CRUD\Traits\CRUDDeleteTrait;
use IlBronza\CRUD\Traits\CRUDRelationshipTrait;

class DeliveryDestroyController extends DeliveryCRUD
{    use CRUDDeleteTrait;

	public $allowedMethods = ['destroy'];

	public function destroy($delivery)
	{
		$delivery = $this->findModel($delivery);

		return $this->_destroy($delivery);
	}
}