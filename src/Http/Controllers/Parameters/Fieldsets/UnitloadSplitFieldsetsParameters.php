<?php

namespace IlBronza\Warehouse\Http\Controllers\Parameters\Fieldsets;

use IlBronza\Form\Helpers\FieldsetsProvider\FieldsetParametersFile;
use IlBronza\Warehouse\Models\Delivery\Delivery;

class UnitloadSplitFieldsetsParameters extends FieldsetParametersFile
{
    public function _getFieldsetsParameters() : array
    {
        return [
            'base' => [
                'fields' => [
                    'quantity' => ['number' => 'numeric|required|min:0|max:' . ($this->getModel()->getQuantity() - 1)],

	                'delivery_id' => [
		                'name' => 'delivery_id',
		                'type' => 'select',
		                'rules' => 'nullable|exists:' . Delivery::gpc()::make()->getTable() . ',id',
		                'relation' => 'delivery'
	                ]
                ],
                'width' => ["1-3@l", '1-2@m']
            ],
        ];
    }
}
