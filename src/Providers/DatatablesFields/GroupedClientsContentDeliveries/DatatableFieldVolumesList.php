<?php

namespace IlBronza\Warehouse\Providers\DatatablesFields\GroupedClientsContentDeliveries;

use function number_format;

class DatatableFieldVolumesList extends DatatableFieldGroupedContentDelivery
{
	public ? string $forcedStandardName = 'volumes';
	public ? string $suffix = 'm³';

	public function _transformValue($value)
	{
		return number_format($value->sum(function($item)
		{
			return $item->getVolumeMc();
		}), 2);
	}
}