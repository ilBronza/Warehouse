<?php

namespace IlBronza\Warehouse\Http\Controllers\Pallettypes;

use IlBronza\CRUD\Traits\CRUDEditUpdateTrait;
use IlBronza\Warehouse\Models\Pallettype\Pallettype;
use Illuminate\Http\Request;

class PallettypeEditUpdateController extends PallettypeCRUD
{
    use CRUDEditUpdateTrait;

    public $allowedMethods = ['edit', 'update'];

    public function getGenericParametersFile() : ? string
    {
        return config('warehouse.models.pallettype.parametersFiles.crud');
    }

    public function edit(string $pallettype)
    {
        $pallettype = $this->findModel($pallettype);

        return $this->_edit($pallettype);
    }

    public function update(Request $request, $pallettype)
    {
        $pallettype = $this->findModel($pallettype);

        return $this->_update($request, $pallettype);
    }
}
