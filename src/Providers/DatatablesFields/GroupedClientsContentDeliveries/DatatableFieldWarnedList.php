<?php

namespace IlBronza\Warehouse\Providers\DatatablesFields\GroupedClientsContentDeliveries;

class DatatableFieldWarnedList extends DatatableFieldGroupedContentDelivery
{
	public ? string $forcedStandardName = 'warnedList';
	public $childParameters = [
		'type' => 'boolean',
		'trueIcon' => 'envelope'
	];

	public function _transformValue($value)
	{
		return $value->first()?->hasBeenWarned();
	}
}