<?php

namespace IlBronza\Warehouse\Http\Controllers\Parameters\Fieldsets;

class RoomTypeFieldsetsParameters extends SpaceCatalogFieldsetsParameters
{
	public function _getFieldsetsParameters() : array
	{
		$parameters = parent::_getFieldsetsParameters();
		$parameters['general']['fields']['room_category_id'] = [
			'type' => 'select',
			'multiple' => false,
			'rules' => 'required|uuid|exists:' . config('warehouse.models.roomCategory.table') . ',id,deleted_at,NULL',
			'relation' => 'roomCategory',
		];

		return $parameters;
	}
}
