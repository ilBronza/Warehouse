<?php

namespace IlBronza\Warehouse\Providers\RelationshipsManagers;

use IlBronza\CRUD\Providers\RelationshipsManager\RelationshipsManager;

class StructureRelationshipsManager extends RelationshipsManager
{
	public function getRelationsParameters() : array
	{
		return [
			'rooms' => config('warehouse.models.room.controller'),
		];
	}
}
