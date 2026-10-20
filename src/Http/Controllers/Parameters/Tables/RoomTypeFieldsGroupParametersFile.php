<?php

namespace IlBronza\Warehouse\Http\Controllers\Parameters\Tables;

use IlBronza\Datatables\Providers\FieldsGroupParametersFile;

class RoomTypeFieldsGroupParametersFile extends FieldsGroupParametersFile
{
	static function getFieldsGroup() : array
	{
		return [
			'translationPrefix' => 'warehouse::spaces',
			'fields' => [
				'mySelfSee' => 'links.see',
				'mySelfEdit' => 'links.edit',
				'name' => 'flat',
				'roomCategory.name' => 'flat',
				'description' => 'flat',
				'mySelfDelete' => 'links.delete',
			],
		];
	}
}
