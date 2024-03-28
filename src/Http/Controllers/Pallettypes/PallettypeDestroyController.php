<?php

namespace IlBronza\Warehouse\Http\Controllers\Pallettypes;

use IlBronza\CRUD\Traits\CRUDDeleteTrait;

class PallettypeDestroyController extends PallettypeCRUD
{
    use CRUDDeleteTrait;

    public $allowedMethods = ['destroy'];

    public function destroy($pallettype)
    {
        $pallettype = $this->findModel($pallettype);

        return $this->_destroy($pallettype);
    }
}