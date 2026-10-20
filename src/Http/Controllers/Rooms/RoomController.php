<?php

namespace IlBronza\Warehouse\Http\Controllers\Rooms;

use IlBronza\Warehouse\Http\Controllers\CRUDWarehouseResourceController;

class RoomController extends CRUDWarehouseResourceController
{
	public $configModelClassName = 'room';
	protected array $indexRelations = ['structure', 'roomType.roomCategory', 'destination'];
}
