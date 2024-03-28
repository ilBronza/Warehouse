<?php

namespace IlBronza\Warehouse\Http\Controllers;

use IlBronza\CRUD\CRUD;

class CRUDWarehousePackageController extends CRUD
{
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
