<?php

namespace IlBronza\Warehouse\Http\Controllers\Unitloads;

use IlBronza\Warehouse\Helpers\Unitloads\UnitloadPrinterHelper;

class UnitloadPrintController extends UnitloadsCRUDController
{
    public $allowedMethods = [
        'print',
        'resetPrintedAt'
    ];

    public function print(string $unitload)
    {
        $unitload = $this->findModel($unitload);

        return UnitloadPrinterHelper::printUnitload($unitload);
    }

    public function resetPrintedAt(string $unitload)
    {
        $unitload = $this->findModel($unitload);

        UnitloadPrinterHelper::resetUnitloadPrintedAt($unitload);

        return back();
    }
}
