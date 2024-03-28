<?php

namespace IlBronza\Warehouse\Http\Controllers\Pallettypes;

use IlBronza\CRUD\Traits\CRUDRelationshipTrait;
use IlBronza\CRUD\Traits\CRUDShowTrait;

class PallettypeShowController extends PallettypeCRUD
{
    use CRUDShowTrait;
    use CRUDRelationshipTrait;

    public $allowedMethods = ['show'];

    public function getGenericParametersFile() : ? string
    {
        return config('warehouse.models.pallettype.parametersFiles.create');
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
