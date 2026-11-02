<?php

namespace IlBronza\Warehouse\Http\Controllers\Parameters\Tables;

use IlBronza\Datatables\Providers\FieldsGroupParametersFile;

class RoomRelatedByOwnerFieldsGroupParametersFile extends FieldsGroupParametersFile
{
	static function getFieldsGroup() : array
	{
		return [
			'translationPrefix' => 'warehouse::spaces',
			'fields' => [
				'mySelfSee' => 'links.see',
				'mySelfEdit' => 'links.edit',
				'name' => 'flat',
				'code' => 'flat',
				'roomType.name' => 'flat',
				'roomType.roomCategory.name' => 'flat',
				'floor' => 'flat',
				'surface_sqm' => 'flat',
				'mySelfDelete' => 'links.delete',
			],
		];
	}
}
