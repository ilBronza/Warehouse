<?php

namespace IlBronza\Warehouse\Providers\RelationshipsManagers;

use IlBronza\CRUD\Providers\RelationshipsManager\RelationshipsManager;

class RoomCategoryRelationshipsManager extends RelationshipsManager
{
	public function getRelationsParameters() : array
	{
		return [
			'roomTypes' => config('warehouse.models.roomType.controller'),
		];
	}
}
