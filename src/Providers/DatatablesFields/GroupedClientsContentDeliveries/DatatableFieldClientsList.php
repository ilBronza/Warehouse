<?php

namespace IlBronza\Warehouse\Providers\DatatablesFields\GroupedClientsContentDeliveries;

class DatatableFieldClientsList extends DatatableFieldGroupedContentDelivery
{
	public ? string $forcedStandardName = 'clients';

	public function _transformValue($value)
	{
		return $value->first()?->getContent()->getClient()->getName();
	}
}