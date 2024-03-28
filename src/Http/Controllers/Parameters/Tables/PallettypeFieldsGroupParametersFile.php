<?php

namespace IlBronza\Warehouse\Http\Controllers\Parameters\Tables;

use IlBronza\Datatables\Providers\FieldsGroupParametersFile;

class PallettypeFieldsGroupParametersFile extends FieldsGroupParametersFile
{
	static function getFieldsGroup() : array
	{
		return [
            'translationPrefix' => 'warehouse::fields',
            'fields' => 
            [
                'mySelfEdit' => 'links.edit',
                'name' => 'flat',
                'slug' => 'flat',

                'width_mm' => 'flat',
                'height_mm' => 'flat',
                'length_mm' => 'flat',
                'weigth_kg' => 'flat',

                'loadable_width_mm' => 'flat',
                'loadable_length_mm' => 'flat',
                'loadable_height_mm' => 'flat',
                'loadable_weigth_kg' => 'flat',

                'mySelfDelete' => 'links.delete'
            ]
        ];
	}
}