<?php

namespace IlBronza\Warehouse\Http\Controllers\Parameters\Tables;

use IlBronza\Datatables\Providers\FieldsGroupParametersFile;

class DeliveryPickableFieldsGroupParametersFile extends FieldsGroupParametersFile
{
	static function getFieldsGroup() : array
	{
		return [
            'translationPrefix' => 'warehouse::fields',
            'fields' => 
            [
                'name' => 'flat',
	            'datetime' => 'dates.date',
	            'vehicle' => 'vehicles::vehicle',
	            'mySelfAddToDelivery' => 'warehouse::deliveries.addOrders'
            ]
        ];
	}
}