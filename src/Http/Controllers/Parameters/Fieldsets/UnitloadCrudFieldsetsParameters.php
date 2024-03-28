<?php

namespace IlBronza\Warehouse\Http\Controllers\Parameters\Fieldsets;

use IlBronza\Form\Helpers\FieldsetsProvider\FieldsetParametersFile;
use IlBronza\Warehouse\Models\Pallettype\Pallettype;

class UnitloadCrudFieldsetsParameters extends FieldsetParametersFile
{
    public function _getFieldsetsParameters() : array
    {
        $palletArray = Pallettype::all()->pluck('name', 'id')->toArray();

        return [
            'base' => [
                'fields' => [
                    'quantity' => ['number' => 'numeric|nullable|min:0'],
                    'pallettype_id' => [
                        'type' => 'select',
                        'value' => $this->getModel()?->getPallettype()?->getKey(),
                        'opener' => [
                            'event' => 'change',
                            'targetName' => 'save_pallettype_id_on',
                            'required' => true
                        ],
                        'list' => $palletArray,
                        'rules' => 'string|required|in:' . implode(",", array_keys($palletArray))
                    ],
                ],
                'width' => ["1-3@l", '1-2@m']
            ],
            'loadable' => [
                'fields' => [
                ],
                'width' => ["1-3@l", '1-2@m']
            ]
        ];
    }
}
