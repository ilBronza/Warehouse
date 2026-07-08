<?php

namespace IlBronza\Warehouse\Http\Controllers\Pallettypes;

use IlBronza\CRUD\Traits\CRUDEditUpdateTrait;
use Illuminate\Http\Request;

class PallettypeEditUpdateController extends PallettypeCRUD
{
    use CRUDEditUpdateTrait;

    public $allowedMethods = ['edit', 'update'];

    public function getEditParametersFile() : ? string
    {
        return config('warehouse.models.pallettype.parametersFiles.edit');
    }

    public function getUpdateParametersFile() : ? string
    {
        return config('warehouse.models.pallettype.parametersFiles.update')
            ?? $this->getEditParametersFile();
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
