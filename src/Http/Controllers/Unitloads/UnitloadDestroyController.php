<?php

namespace IlBronza\Warehouse\Http\Controllers\Unitloads;

use IlBronza\CRUD\Traits\CRUDDeleteTrait;
use IlBronza\Warehouse\Http\Controllers\Unitloads\UnitloadsCRUDController;

class UnitloadDestroyController extends UnitloadsCRUDController
{
    use CRUDDeleteTrait;

    public $allowedMethods = ['destroy'];

    public function destroy($unitload)
    {
        $unitload = $this->findModel($unitload);

        return $this->_destroy($unitload);
    }
}