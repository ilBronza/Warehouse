<?php

namespace IlBronza\Warehouse\Providers\DatatablesFields\GroupedClientsContentDeliveries;

class DatatableFieldZonesList extends DatatableFieldGroupedContentDelivery
{
	public ? string $forcedStandardName = 'zonesList';

	public function _transformValue($value)
	{
		return $value->first()?->getContent()?->getOrder()?->getDestination()?->getZone();
	}
}