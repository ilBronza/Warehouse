<?php

namespace IlBronza\Warehouse\Models;

use IlBronza\CRUD\Models\BaseModel;
use IlBronza\CRUD\Traits\CRUDSluggableTrait;
use IlBronza\CRUD\Traits\Model\PackagedModelsTrait;

class BaseWarehouseModel extends BaseModel
{
	use PackagedModelsTrait;

	use CRUDSluggableTrait;

	public ? string $translationFolderPrefix = 'warehouse';
	static $packageConfigPrefix = 'warehouse';
}