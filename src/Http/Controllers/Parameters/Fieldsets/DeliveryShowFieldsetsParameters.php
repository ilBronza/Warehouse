<?php

namespace IlBronza\Warehouse\Http\Controllers\Parameters\Fieldsets;

use IlBronza\Form\Helpers\FieldsetsProvider\FieldsetParametersFile;

use function config;

class DeliveryShowFieldsetsParameters extends FieldsetParametersFile
{
    public function _getFieldsetsParameters() : array
    {
	    return [
		    'name' => [
				'showLegend' => false,
			    'fields' => [
				    'name' => ['text' => 'string|required|max:255'],
			    ],
			    'width' => ['large'],
		    ],
		    'date' => [
			    'showLegend' => false,
			    'fields' => [
				    'datetime' => ['datetime' => 'date|nullable'],
			    ],
			    'width' => ['large'],
		    ],
		    'vehicle' => [
			    'showLegend' => false,
			    'fields' => [
				    'vehicle_id' => [
					    'type' => 'select',
					    'multiple' => false,
					    'rules' => 'string|nullable|exists:' . config('vehicles.models.vehicle.table') . ',id',
					    'relation' => 'vehicle'
				    ],
			    ],
			    'width' => ['large'],
		    ]
	    ];
    }
}
