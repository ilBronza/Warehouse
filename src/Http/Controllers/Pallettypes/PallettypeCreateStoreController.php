<?php

namespace IlBronza\Warehouse\Http\Controllers\Pallettypes;

use IlBronza\CRUD\Traits\CRUDCreateStoreTrait;
use IlBronza\CRUD\Traits\CRUDRelationshipTrait;
use IlBronza\CRUD\Traits\CRUDShowTrait;

class PallettypeCreateStoreController extends PallettypeCRUD
{
    use CRUDCreateStoreTrait;
    use CRUDShowTrait;
    use CRUDRelationshipTrait;

    public $allowedMethods = ['create', 'store', 'edit', 'update', 'show'];

    public function getGenericParametersFile() : ? string
    {
        return config('warehouse.models.pallettype.parametersFiles.crud');
    }

    public function getRelationshipsManagerClass()
    {
        return config("warehouse.models.{$this->configModelClassName}.relationshipsManagerClasses.show");
    }

    public function show(string $pallettype)
    {
        $pallettype = $this->findModel($pallettype);

        return $this->_show($pallettype);
    }
}
