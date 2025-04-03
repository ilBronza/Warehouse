<?php

namespace IlBronza\Warehouse\Models\Pallettype;

use IlBronza\CRUD\Traits\Model\CRUDUseUuidTrait;
use IlBronza\Warehouse\Models\BaseWarehouseModel;

class Pallettype extends BaseWarehouseModel
{
	use CRUDUseUuidTrait;

	static $modelConfigPrefix = 'pallettype';
	protected $keyType = 'string';

	// static $defaultId = "3155906a-fc96-4e35-af6b-406d25bbab93";

	static function getDefault()
	{
		return static::where('slug', 'tipo-eur')->first();
		// return static::findCached(static::$defaultId);
	}

}