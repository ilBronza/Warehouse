<?php

namespace IlBronza\Warehouse\Http\Controllers\Rooms;

use IlBronza\Warehouse\Http\Controllers\CRUDWarehouseResourceController;

class RoomTypeController extends CRUDWarehouseResourceController
{
	public $configModelClassName = 'roomType';
	protected array $indexRelations = ['roomCategory'];
}
