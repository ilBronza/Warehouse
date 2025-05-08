<?php

namespace IlBronza\Warehouse\Http\Controllers\Deliveries;

use IlBronza\CRUD\Traits\CRUDCreateStoreTrait;
use IlBronza\CRUD\Traits\CRUDRelationshipTrait;

class DeliveryCreateStoreController extends DeliveryCRUD
{
    use CRUDCreateStoreTrait;
    use CRUDRelationshipTrait;

    public $allowedMethods = ['create', 'store'];

    public function getCreateParametersFile() : ? string
    {
        return config('warehouse.models.delivery.parametersFiles.create');
    }
}
