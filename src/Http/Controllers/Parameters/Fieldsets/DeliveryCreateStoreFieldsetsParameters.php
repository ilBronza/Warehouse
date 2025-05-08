<?php

namespace IlBronza\Warehouse\Http\Controllers\Parameters\Fieldsets;

use IlBronza\Form\Helpers\FieldsetsProvider\FieldsetParametersFile;

class DeliveryCreateStoreFieldsetsParameters extends FieldsetParametersFile
{
    public function _getFieldsetsParameters() : array
    {
	    return [
		    'base' => [
			    'fields' => [
				    'name' => ['text' => 'string|required|max:255'],
				    'datetime' => ['datetime' => 'date|nullable'],
				    'vehicle_id' => [
					    'type' => 'select',
					    'multiple' => false,
					    'rules' => 'string|nullable|exists:' . config('vehicles.models.vehicle.table') . ',id',
					    'relation' => 'vehicle'
				    ],
			    ],
			    'width' => ['1-3@l', '1-2@m']
		    ]
	    ];
    }
}
