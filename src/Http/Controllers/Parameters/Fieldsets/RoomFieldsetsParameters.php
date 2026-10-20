<?php

namespace IlBronza\Warehouse\Http\Controllers\Parameters\Fieldsets;

use IlBronza\Form\Helpers\FieldsetsProvider\FieldsetParametersFile;

class RoomFieldsetsParameters extends FieldsetParametersFile
{
	public function _getFieldsetsParameters() : array
	{
		return [
			'general' => [
				'translationPrefix' => 'warehouse::spaces',
				'fields' => [
					'name' => ['text' => 'required|string|max:255'],
					'code' => ['text' => 'nullable|string|max:255'],
					'structure_id' => [
						'type' => 'select',
						'multiple' => false,
						'rules' => 'required|uuid|exists:' . config('warehouse.models.structure.table') . ',id,deleted_at,NULL',
						'relation' => 'structure',
					],
					'room_type_id' => [
						'type' => 'select',
						'multiple' => false,
						'rules' => 'required|uuid|exists:' . config('warehouse.models.roomType.table') . ',id,deleted_at,NULL',
						'relation' => 'roomType',
					],
					'destination_id' => [
						'type' => 'select',
						'multiple' => false,
						'rules' => 'nullable|uuid|exists:' . config('clients.models.destination.table') . ',id,deleted_at,NULL',
						'relation' => 'destination',
					],
				],
				'width' => ['1-2@m'],
			],
			'details' => [
				'translationPrefix' => 'warehouse::spaces',
				'fields' => [
					'floor' => ['text' => 'nullable|string|max:255'],
					'surface_sqm' => ['number' => 'nullable|numeric|min:0|max:9999999999.99'],
					'notes' => ['textarea' => 'nullable|string'],
				],
				'width' => ['1-2@m'],
			],
		];
	}
}
