<?php

namespace IlBronza\Warehouse\Providers\DatatablesFields\GroupedClientsContentDeliveries;

class DatatableFieldUnitloadsCountList extends DatatableFieldGroupedContentDelivery
{
	public ? string $forcedStandardName = 'unitloadsCount';

	public function _transformValue($value)
	{
		return $value->sum(function($item)
		{
			return $item->getUnitloadsCount();
		});
	}
}