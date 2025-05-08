<?php

namespace IlBronza\Warehouse\Http\Controllers;

use IlBronza\CRUD\CRUD;
use IlBronza\CRUD\Http\Controllers\BasePackageTrait;

class CRUDWarehousePackageController extends CRUD
{
	use BasePackageTrait;

	static $packageConfigPrefix = 'warehouse';

    public function getRouteBaseNamePrefix() : ? string
    {
        return config('warehouse.routePrefix');
    }

    public function setModelClass()
    {
        if(! config("warehouse.models.{$this->configModelClassName}.class"))
            throw new \Exception("Missing model class in config for {$this->configModelClassName}");
            
        $this->modelClass = config("warehouse.models.{$this->configModelClassName}.class");
    }
}
