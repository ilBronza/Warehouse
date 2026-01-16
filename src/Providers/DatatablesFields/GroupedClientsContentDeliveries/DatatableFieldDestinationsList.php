<?php

namespace IlBronza\Warehouse\Providers\DatatablesFields\GroupedClientsContentDeliveries;

class DatatableFieldDestinationsList extends DatatableFieldGroupedContentDelivery
{
	public ? string $forcedStandardName = 'destinations';

	public function _transformValue($value)
	{
		if(! empty($result = $value->first()?->getContent()?->getOrder()?->getDestination()?->getCity()))
			return $result;

		return ' - ';
	}
}