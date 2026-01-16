<?php

namespace IlBronza\Warehouse\Providers\DatatablesFields\GroupedClientsContentDeliveries;

use function number_format;

class DatatableFieldPartialsList extends DatatableFieldGroupedContentDelivery
{
	public ? string $forcedStandardName = 'partials';

	public $childParameters = [
		'type' => 'booleanAlarm',
	];

	public function _transformValue($value)
	{
		foreach($value as $item)
			if($item->isPartial())
				return true;

		return false;
	}
}