<?php

namespace IlBronza\Warehouse\Http\Controllers\Unitloads;

use IlBronza\CRUD\Traits\CRUDEditUpdateTrait;
use IlBronza\Warehouse\Http\Controllers\Unitloads\UnitloadsCRUDController;
use Illuminate\Http\Request;

class UnitloadEditUpdateController extends UnitloadsCRUDController
{
    use CRUDEditUpdateTrait;

    public $returnBack = true;
    public $allowedMethods = ['edit', 'update'];

    public function getGenericParametersFile() : ? string
    {
        return config('warehouse.models.unitload.parametersFiles.crud');
    }

    public function edit(string $unitload)
    {
        $unitload = $this->findModel($unitload);

        return $this->_edit($unitload);
    }

    public function update(Request $request, $unitload)
    {
        $unitload = $this->findModel($unitload);

        return $this->_update($request, $unitload);
    }
}
