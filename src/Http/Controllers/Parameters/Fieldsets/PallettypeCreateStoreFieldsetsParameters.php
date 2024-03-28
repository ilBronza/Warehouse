<?php

namespace IlBronza\Warehouse\Http\Controllers\Parameters\Fieldsets;

use IlBronza\Form\Helpers\FieldsetsProvider\FieldsetParametersFile;

class PallettypeCreateStoreFieldsetsParameters extends FieldsetParametersFile
{
    public function _getFieldsetsParameters() : array
    {
        return [
            'base' => [
                'fields' => [
                    'name' => ['text' => 'string|required|max:255'],
                ],
                'width' => ["1-3@l", '1-2@m']
            ],
            'sizes' => [
                'fields' => [
                    'width_mm' => ['number' => 'numeric|nullable|min:0'],
                    'length_mm' => ['number' => 'numeric|nullable|min:0'],
                    'height_mm' => ['number' => 'numeric|nullable|min:0'],
                    'weigth_kg' => ['number' => 'numeric|nullable|min:0'],
                ],
                'width' => ["1-3@l", '1-2@m']
            ],
            'loadable' => [
                'fields' => [
                    'loadable_width_mm' => ['number' => 'numeric|nullable|min:0'],
                    'loadable_length_mm' => ['number' => 'numeric|nullable|min:0'],
                    'loadable_height_mm' => ['number' => 'numeric|nullable|min:0'],
                    'loadable_weigth_kg' => ['number' => 'numeric|nullable|min:0'],
                ],
                'width' => ["1-3@l", '1-2@m']
            ]
        ];
    }
}
