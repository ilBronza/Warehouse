<?php

namespace IlBronza\Warehouse\Providers\DatatablesFields\GroupedClientsContentDeliveries;

use function number_format;

class DatatableFieldEntiresList extends DatatableFieldGroupedContentDelivery
{
	public ? string $forcedStandardName = 'entires';

	public $childParameters = [
		'type' => 'boolean',
	];

	public function _transformValue($value)
	{
		foreach($value as $item)
			if($item->isPartial())
				return false;

		return true;
	}
}