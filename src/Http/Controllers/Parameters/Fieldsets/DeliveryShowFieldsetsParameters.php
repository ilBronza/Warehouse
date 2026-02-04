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
		    'name' => [
				'showLegend' => false,
				'vertical' => true,
			    'fields' => [
				    'name' => [
						'type' => 'text',
						'rules' => 'string|required|max:255',
						'vertical' => true,
				    ],
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
				    ],
					'route-distance' => [
						'type' => 'text',
						'value' => 0,
						'disabled' => true,
						'rules' => 'string|nullable|max:255',
						'vertical' => true,
					],
					'route-duration' => [
						'type' => 'text',
						'value' => 0,
						'disabled' => true,
						'rules' => 'string|nullable|max:255',
						'vertical' => true,
					],
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
