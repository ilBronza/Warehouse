<?php

namespace IlBronza\Warehouse\Http\Controllers\Pallettypes;

use IlBronza\CRUD\Traits\CRUDIndexTrait;
use IlBronza\CRUD\Traits\CRUDPlainIndexTrait;
use IlBronza\Warehouse\Http\Controllers\Warehouse\VehicleCRUD;

class PallettypeIndexController extends PallettypeCRUD
{
    use CRUDPlainIndexTrait;
    use CRUDIndexTrait;

    public $allowedMethods = ['index'];

    public function getIndexFieldsArray()
    {
        return config('warehouse.models.pallettype.fieldsGroupsFiles.index')::getFieldsGroup();
    }

    public function getIndexElements()
    {
        return $this->getModelClass()::all();
    }

}
