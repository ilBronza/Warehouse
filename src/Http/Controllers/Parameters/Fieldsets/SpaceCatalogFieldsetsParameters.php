<?php

namespace IlBronza\Warehouse\Http\Controllers\Parameters\Fieldsets;

use IlBronza\Form\Helpers\FieldsetsProvider\FieldsetParametersFile;

class SpaceCatalogFieldsetsParameters extends FieldsetParametersFile
{
	public function _getFieldsetsParameters() : array
	{
		return [
			'general' => [
				'translationPrefix' => 'warehouse::spaces',
				'fields' => [
					'name' => ['text' => 'required|string|max:255'],
					'description' => ['textarea' => 'nullable|string'],
				],
				'width' => ['1-2@m'],
			],
		];
	}
}
