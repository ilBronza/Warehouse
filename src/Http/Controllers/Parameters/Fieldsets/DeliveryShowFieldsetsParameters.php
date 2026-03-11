<?php

namespace IlBronza\Warehouse\Http\Controllers\Parameters\Fieldsets;

use IlBronza\Form\Helpers\FieldsetsProvider\FieldsetParametersFile;
use Auth;

class DeliveryShowFieldsetsParameters extends FieldsetParametersFile
{
    public function _getFieldsetsParameters() : array
    {
	    if(! Auth::user()->hasRole('administrator'))
	    	return [];

	    return [
		    'parameters' => [
				'showLegend' => false,
				'vertical' => true,
			    'fields' => [
				    'delivery_datetime' => [
				    	'type' => 'datetime',
				    	'rules' => 'date|nullable',
						'vertical' => true,
				    ],
				    'vehicle_id' => [
					    'type' => 'select',
						'vertical' => true,
					    'multiple' => false,
					    'rules' => 'string|nullable|exists:' . config('vehicles.models.vehicle.table') . ',id',
					    'relation' => 'vehicle'
				    ]
			    ],
			    'width' => ['medium'],
		    ],
		    'map' => [
			    'showLegend' => false,
			    'fields' => [
			    ],
				'view' => [
					'name' => 'warehouse::deliveries._fetcherDeliveryDestinationsMap',
					'parameters' => [
						'model' => $this->getModel()
					]
				],

			    'width' => ['expand'],
		    ],
	    ];
    }
}
