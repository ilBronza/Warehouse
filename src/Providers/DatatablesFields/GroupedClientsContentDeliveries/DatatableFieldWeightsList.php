<?php

namespace IlBronza\Warehouse\Providers\DatatablesFields\GroupedClientsContentDeliveries;

use function number_format;

class DatatableFieldWeightsList extends DatatableFieldGroupedContentDelivery
{
	public ? string $forcedStandardName = 'weightsTon';
	public ? string $suffix = 't';

	public function _transformValue($value)
	{
		return number_format($value->sum(function($item)
		{
			return $item->getWeightKg();
		}) / 1000, 2);
	}
}