<?php

namespace IlBronza\Warehouse\Http\Controllers\Parameters\Fieldsets;

use IlBronza\Form\Helpers\FieldsetsProvider\FieldsetParametersFile;

class ContentDeliveryEditUpdateFieldsetsParameters extends FieldsetParametersFile
{
    public function _getFieldsetsParameters() : array
    {
	    return [
		    'base' => [
			    'fields' => [
				    'quantity_required' => ['number' => 'numeric|required|min:0|max:999999'],
			    ],
			    'width' => ['1-3@l', '1-2@m']
		    ]
	    ];
    }
}
