<?php

namespace IlBronza\Warehouse\Http\Controllers\Parameters\Tables;

use IlBronza\Datatables\Providers\FieldsGroupParametersFile;

class DeliveryFieldsGroupParametersFile extends FieldsGroupParametersFile
{
	static function getFieldsGroup() : array
	{
		return [
            'translationPrefix' => 'warehouse::fields',
            'fields' => 
            [
                'mySelfEdit' => 'links.see',
                'name' => 'flat',
                'mySelfDelete' => 'links.delete'
            ]
        ];
	}
}