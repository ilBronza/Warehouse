<?php

namespace IlBronza\Warehouse\Http\Controllers\Parameters\Tables;

use IlBronza\Datatables\Providers\FieldsGroupParametersFile;

class DeliveryAssociateUnitloadsFieldsGroupParametersFile extends FieldsGroupParametersFile
{
	static function getFieldsGroup() : array
	{
		return [
            'translationPrefix' => 'warehouse::fields',
            'fields' => 
            [
                'name' => 'flat',
	            'datetime' => 'dates.date',
	            'delivery_datetime' => 'vehicles::vehicle',
	            'mySelfAddToDelivery' => 'warehouse::deliveries.addUnitloads'
            ]
        ];
	}
}