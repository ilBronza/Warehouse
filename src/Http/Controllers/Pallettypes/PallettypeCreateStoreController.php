<?php

namespace IlBronza\Warehouse\Http\Controllers\Pallettypes;

use IlBronza\CRUD\Traits\CRUDCreateStoreTrait;
use IlBronza\CRUD\Traits\CRUDRelationshipTrait;

class PallettypeCreateStoreController extends PallettypeCRUD
{
    use CRUDCreateStoreTrait;
    use CRUDRelationshipTrait;

    public $allowedMethods = ['create', 'store'];

    public function getCreateParametersFile() : ? string
    {
        return config('warehouse.models.pallettype.parametersFiles.create');
    }

    public function getStoreParametersFile() : ? string
    {
        return config('warehouse.models.pallettype.parametersFiles.store')
            ?? $this->getCreateParametersFile();
    }
}
